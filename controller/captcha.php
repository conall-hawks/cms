<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);
/**------------------------------------------------------------------------\
| CAPTCHA generator.                                                       |
\-------------------------------------------------------------------------*/
class Captcha extends Controller {

    /** Width of the image. */
    private $width = 160;

    /** Height of the image. */
    private $height = 80;

    /** Dictionary word file (empty for random text). */
    private $words_file = '';

    /** Path for resource files (fonts, words, etc.). */
    private $resources_path = '';

    /** Min word length (for non-dictionary random text generation) */
    private $min_word_length = 4;

    /** Max word length (for non-dictionary random text generation) */
    private $max_word_length = 4;

    /** Session name to store the original text. */
    private $session_var = 'captcha';

    /** Background color in RGB-array */
    private $background_color = [0, 0, 0];

    /** Foreground colors in RGB-array */
    private $colors = [
        [ 27,  78, 181], // blue
        [ 22, 163,  35], // green
        [214,  36,   7], // red
    ];

    /** Shadow color in RGB-array or null */
    private $shadow_color = [0, 0, 0];

    /** Horizontal line through the text */
    private $line_width = 3;

    /**
     * Font configuration
     *
     * - font: TTF file
     * - spacing: relative pixel space between character
     * - min_size: min font size
     * - max_size: max font size
     */
    private $fonts = [
        'Montserrat-Bold' => [
            'spacing'  => 0,
            'min_size' => 27,
            'max_size' => 27,
            'font'     => ASSET.'/misc/montserrat-bold.ttf'
        ]
    ];

    /** Wave configuration in X and Y axes */
    private $y_period    = 12;
    private $y_amplitude = 14;
    private $x_period    = 11;
    private $x_amplitude = 5;

    /** letter rotation clockwise */
    private $max_rotation = 8;

    /** Image quality/size factor. (1: low, 2: medium, 3: high) */
    private $scale = 3;

    /** Blur effect for better image quality (but slower image processing). */
    private $blur = true;

    /** Debug? */
    public $debug = false;

    /** Image format: jpeg or png */
    private $image_format = 'png';

    /** GD image */
    private $image;

    /**------------------------------------------------------------------------\
    |                                                                          |
    \-------------------------------------------------------------------------*/
    public function __construct(){
        logger('Drawing new CAPTCHA.');

        /* Start a session if one has not already started. */
        if(session_status() !== PHP_SESSION_ACTIVE) session_start();
        #load_controller('user');

        /* Create our CAPTCHA. */
        $this->create_image();

        /* Disable client cache. */
        header('Cache-Control: no-cache');

        /* PNG is in the buffer. We're done here. */
        logger('--------------------------------------------------------------------------------');
        die();
    }

    private function create_image(){
        $ini = microtime(true);

        /** Initialization */
        $this->image_allocate();

        /** Text insertion */
        $text = $this->get_captcha_text();
        $_SESSION[$this->session_var] = $text;
        $fontcfg = $this->fonts[array_rand($this->fonts)];
        $this->write_text($text, $fontcfg);

        /** Transformations */
        if(!empty($this->line_width)){
            $this->write_line();
        }
        $this->wave_image();
        if($this->blur && function_exists('imagefilter')){
            imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);
        }
        $this->reduce_image();

        if($this->debug){
            imagestring($this->image, 1, 1, $this->height - 8,
                "$text {$fontcfg['font']} " . round((microtime(true) - $ini) * 1000) . 'ms',
                $this->GdFgColor
            );
        }

        /** Output */
        $this->write_image();
        $this->cleanup();
    }

    /**
     * Creates the image resources
     */
    private function image_allocate(){
        // Cleanup
        if(!empty($this->image)){
            imagedestroy($this->image);
        }

        $this->image = imagecreatetruecolor($this->width * $this->scale, $this->height * $this->scale);

        // Background color
        $this->GdBgColor = imagecolorallocate($this->image,
            $this->background_color[0],
            $this->background_color[1],
            $this->background_color[2]
        );
        imagefilledrectangle($this->image, 0, 0, $this->width * $this->scale, $this->height * $this->scale, $this->GdBgColor);

        // Foreground color
        $color = $this->colors[mt_rand(0, sizeof($this->colors) - 1)];
        $this->GdFgColor = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);

        // Shadow color
        if(!empty($this->shadow_color) && is_array($this->shadow_color) && sizeof($this->shadow_color) >= 3){
            $this->GdShadowColor = imagecolorallocate($this->image,
                $this->shadow_color[0],
                $this->shadow_color[1],
                $this->shadow_color[2]
            );
        }
    }

    /**
     * Text generation
     *
     * @return string Text
     */
    private function get_captcha_text(){
        $text = $this->get_dictionary_captcha_text();
        if(!$text){
            $text = $this->get_random_captcha_text();
        }
        return $text;
    }

    /**
     * Random text generation
     *
     * @return string Text
     */
    private function get_random_captcha_text($length = null){
        if(empty($length)){
            $length = rand($this->min_word_length, $this->max_word_length);
        }

        $letters = 'abcdefghijlmnopqrstvwyz';
        $vowels = 'aeiou';

        $text = '';
        $vocal = rand(0, 1);
        for ($i = 0; $i < $length; $i++){
            if($vocal){
                $text .= substr($vowels, mt_rand(0, 4), 1);
            }else{
                $text .= substr($letters, mt_rand(0, 22), 1);
            }
            $vocal = !$vocal;
        }
        return $text;
    }

    /**
     * Random dictionary word generation
     *
     * @param boolean $extended Add extended "fake" words
     * @return string Word
     */
    private function get_dictionary_captcha_text($extended = false){
        if(empty($this->words_file)){
            return false;
        }

        // Full path of words file
        if(substr($this->words_file, 0, 1) == '/'){
            $wordsfile = $this->words_file;
        }else{
            $wordsfile = $this->resources_path . '/' . $this->words_file;
        }

        if(!file_exists($wordsfile)){
            return false;
        }

        $fp = fopen($wordsfile, 'r');
        $length = strlen(fgets($fp));
        if(!$length){
            return false;
        }
        $line = rand(1, (filesize($wordsfile) / $length) - 2);
        if(fseek($fp, $length * $line) == -1){
            return false;
        }
        $text = trim(fgets($fp));
        fclose($fp);

        /** Change random vowels */
        if($extended){
            $text = preg_split('//', $text, -1, PREG_SPLIT_NO_EMPTY);
            $vowels = ['a', 'e', 'i', 'o', 'u'];
            foreach ($text as $i => $char){
                if(mt_rand(0, 1) && in_array($char, $vowels)){
                    $text[$i] = $vowels[mt_rand(0, 4)];
                }
            }
            $text = implode('', $text);
        }

        return $text;
    }

    /**
     * Horizontal line insertion
     */
    private function write_line(){
        $x1 = $this->width * $this->scale * .15;
        $x2 = $this->textFinalX;
        $y1 = rand($this->height * $this->scale * .40, $this->height * $this->scale * .65);
        $y2 = rand($this->height * $this->scale * .40, $this->height * $this->scale * .65);
        $width = $this->line_width / 2 * $this->scale;

        for ($i = $width * -1; $i <= $width; $i++){
            imageline($this->image, $x1, $y1 + $i, $x2, $y2 + $i, $this->GdFgColor);
        }
    }

    /**
     * Text insertion
     */
    private function write_text($text, $fontcfg = []){
        if(empty($fontcfg)){
            // Select the font configuration
            $fontcfg = $this->fonts[array_rand($this->fonts)];
        }

        $fontfile = $_SERVER['DOCUMENT_ROOT'].'/'.$fontcfg['font'];
        if(!file_exists($fontfile)) $fontfile = $fontcfg['font'];

        /** Increase font-size for shortest words: 9% for each glyph missing */
        $lettersMissing = $this->max_word_length - strlen($text);
        $fontSizefactor = 1 + ($lettersMissing * 0.09);

        // Text generation (char by char)
        $x = 20 * $this->scale;
        $y = round(($this->height * 27 / 40) * $this->scale);
        $length = strlen($text);
        for ($i = 0; $i < $length; $i++){
            $degree = rand($this->max_rotation * -1, $this->max_rotation);
            $fontsize = rand($fontcfg['min_size'], $fontcfg['max_size']) * $this->scale * $fontSizefactor;
            $letter = substr($text, $i, 1);
            if($this->shadow_color){
                $coords = imagettftext($this->image, $fontsize, $degree,
                    $x + $this->scale, $y + $this->scale,
                    $this->GdShadowColor, $fontfile, $letter);
            }
            $coords = imagettftext($this->image, $fontsize, $degree,
                $x, $y,
                $this->GdFgColor, $fontfile, $letter);
            $x += ($coords[2] - $x) + ($fontcfg['spacing'] * $this->scale);
        }

        $this->textFinalX = $x;
    }

    /**
     * Wave filter
     */
    private function wave_image(){
        // X-axis wave generation
        $xp = $this->scale * $this->x_period * rand(1, 3);
        $k = rand(0, 100);
        for ($i = 0; $i < ($this->width * $this->scale); $i++){
            imagecopy($this->image, $this->image,
                $i - 1, sin($k + $i / $xp) * ($this->scale * $this->x_amplitude),
                $i, 0, 1, $this->height * $this->scale);
        }

        // Y-axis wave generation
        $k = rand(0, 100);
        $yp = $this->scale * $this->y_period * rand(1, 2);
        for ($i = 0; $i < ($this->height * $this->scale); $i++){
            imagecopy($this->image, $this->image,
                sin($k + $i / $yp) * ($this->scale * $this->y_amplitude), $i - 1,
                0, $i, $this->width * $this->scale, 1);
        }
    }

    /**
     * Reduce the image to the final size
     */
    private function reduce_image(){
        $imResampled = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($imResampled, $this->image,
            0, 0, 0, 0,
            $this->width, $this->height,
            $this->width * $this->scale, $this->height * $this->scale
        );
        imagedestroy($this->image);
        $this->image = $imResampled;
    }

    /**
     * File generation
     */
    private function write_image(){
        if($this->image_format == 'png' && function_exists('imagepng')){
            imagealphablending($this->image, true);
            imagesavealpha($this->image, false);
            imagecolortransparent($this->image, $this->GdBgColor);

            header('Content-type: image/png');
            imagepng($this->image);
        }else{
            header('Content-type: image/jpeg');
            imagejpeg($this->image, null, 80);
        }
    }

    /**
     * Cleanup
     */
    private function cleanup(){
        imagedestroy($this->image);
    }
}

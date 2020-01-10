<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<h1>
    <?php $title = explode('/', $uri->path); $title = !empty($title[1]) ? $title[1] :  $uri->class; ?>
    <a class="ajax" href="<?php echo $title; ?>">
        <?php echo htmlspecialchars(mb_ucfirst(urldecode($title))); ?>
    </a>
</h1>

<?php if($this->use_explorer){ ?>
    <div>
        <?php
            function print_tree($inodes){

                /* Use URI library. */
                global $uri;

                /* Erase duplicates. */
                $length = count($inodes) - 1;
                for($i = 0; $i < $length; $i++){

                    $c = current($inodes);
                    $path = is_array($c) ? key($inodes) : $c;
                    $name_1 = pathinfo($path, PATHINFO_FILENAME);

                    $n = next($inodes);
                    $path = is_array($n) ? key($inodes) : $n;
                    $name_2 = pathinfo($path, PATHINFO_FILENAME);

                    if($name_1 === $name_2) unset($inodes[key($inodes)]);
                }

                /* Build list. */
                $output = '<ul class="collapsible">';
                foreach($inodes as $key => $inode){
                    if(is_array($inode)){

                        /* Gather information. */
                        $path = pathinfo($key);
                        $path['dirname'] = trim_prefix($path['dirname'], ASSET.'/share');
                        $path['title'] = title($path['filename']);
                        $href = $path['dirname'].'/'.$path['filename'];
                        $label = abs(crc32($key));

                        /* Build list item. */
                        $output .= '<li class="dir">';
                        $output .= '<input id="'.$label.'" type="checkbox" '.($uri->path === $href || strpos($uri->path, $href."/") === 0 ? ' checked' : '').'/>';
                        $output .= '<label for="'.$label.'" title="'.$path['title'].'"><a class="ajax '.link_active($href).'" href="'.$href.'">'.$path['title'].'</a></label>';
                        $output .= print_tree($inode);
                        $output .= '</li>';
                    }else{

                        /* Gather information. */
                        $path = pathinfo($inode);
                        $path['dirname'] = trim_prefix($path['dirname'], ASSET.'/share');
                        $path['title'] = title($path['filename'] ? $path['filename'] : '.'.$path['extension']);
                        $href = $path['dirname'].'/'.($path['filename'] ? $path['filename'] : '.'.$path['extension']);

                        /* Resolve icon. */
                        $icon = ASSET.'/icon/'.(mb_strtolower($path['extension'] ?? '') ?? '');
                        $icon = glob($icon.'.*')[0] ?? ASSET.'/icon/file.svg';
                        if(!is_file($icon)) $icon = ASSET.'/icon/file.svg';
                        $icon = '/'.$icon;

                        /* Build list item. */
                        $output .= '<li class="file">';

                        /* Resolve URL from .url files. */
                        if(!empty($path['extension']) && $path['extension'] === 'url'){
                            $url = file_get_contents($inode);
                            $match_found = preg_match("((\w+:\/\/)[-a-zA-Z0-9:@;?&=\/%\+\.\*!'\(\),\$_\{\}\^~\[\]`#|]+)", $url, $url);
                            if($match_found && isset($url[0])) $href = htmlspecialchars($url[0]);
                            $output .= '<a href="'.$href.'" style="background-image: url(&quot;'.$icon.'&quot;);" title="'.$path['title'].'" target="_blank">'.$path['title'].'</a>';
                        }else{
                            $output .= '<a class="ajax'.($uri->path === $href || strpos($uri->path, $href."/") === 0 ? ' active' : '').'" href="'.$href.'" style="background-image: url(&quot;'.$icon.'&quot;);" title="'.$path['title'].'">'.$path['title'].'</a>';
                        }

                        $output .= '</li>';
                    }
                }
                return $output.'</ul>';
            }

            /* If we're viewing a directory, use the file explorer. */
            if(is_dir(ASSET.'/share/'.$uri->class)){
                $inodes = rglob(ASSET.'/share/'.$uri->class);

                /* Print file tree. */
                echo print_tree($inodes);
            }
        ?>
    </div>
<?php } ?>

<?php global $_content; if(!empty($_content)){ ?>
    <?php

        /* Prevent negligible errors from polluting HTML output. */
        libxml_use_internal_errors(true);

        /* Parse HTML and generate shortcut links. */
        $articles = new DOMDocument();

        /* $_content comes from the layout.php template. */
        if($_content){
            $articles->loadHTML(mb_convert_encoding($_content, 'HTML-ENTITIES', 'UTF-8'));
            $articles = $articles->getElementsByTagName('article');
            if($articles->length){ ?>
            <ul>
                <?php
                    foreach($articles as $index => $article){

                        // Resolve ID and title.
                        $id = $article->getAttribute('id');
                        if(!$id) continue;
                        $title = '';
                        $header = $article->getElementsByTagName('h1');
                        if($header[0]) $title = htmlspecialchars($header[0]->nodeValue);
                        if(!$title)    $title = htmlspecialchars(title($id));
                        $id = htmlspecialchars($id);
                        if($this->use_explorer && $id === 'explorer') continue;

                        // Print list item.
                        echo '<li><a href="#'.$id.'">'.$title.'</a></li>';
                    }
                ?>
            </ul>
        <?php } ?>
    <?php } ?>
<?php } ?>


<?php if(false/*$this->use_cms*/){ ?>
    <ul>
        <?php foreach($cms->posts as $post){ ?>
            <li><?php echo $post['title']; ?></li>
        <?php } ?>
    </ul>
<?php } ?>

<?php /*-----------------------------------------------------------------------\
| Print out a list of subdirectories.                                          |
\-----------------------------------------------------------------------------*/
//    if(is_dir(TEMPLATE.'/content/'.$uri->path)){
//        $files = glob(TEMPLATE.'/content/'.$uri->path.'/*', GLOB_ONLYDIR);
//        if($files){
//            echo '<ul>';
//            foreach($files as $file){
//                $name = basename($file);
//                echo '<li><a class="ajax" href="'.$uri->path.'/'.$name.'">'.title($name).'</a></li>';
//            }
//            echo '</ul>';
//        }
//        //$files = new FilesystemIterator(TEMPLATE.'/content/'.$uri->path);
//        //if($files->valid()){
//        //    echo '<ul>';
//        //    foreach($files as $file){
//        //        if($file->getExtension() == 'php'){
//        //            $name = $file->getBasename('.'.$file->getExtension());
//        //            echo '<li><a class="ajax" href="'.$uri->path.'/'.$name.'">'.title($name).'</a></li>';
//        //        }
//        //    }
//        //    echo '</ul>';
//        //}
//    }
?>

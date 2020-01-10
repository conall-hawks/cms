<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>

<?php $board = $pix->model->get_board(); if($board){ ?>
    <article class="imageboard-preface">
        <h1><?php echo $board['title']; ?></h1>
        <p><?php echo $board['description']; ?></p>
    </article>
<?php } ?>

<article id="bookmarks">
    <input id="imageboard-toggle" type="checkbox" checked />
    <h1>
        <label for="imageboard-toggle" title="Click to create a new thread.">
            <i class="fa fa-arrow-down"></i>
            Create a New Thread
            <i class="fa fa-arrow-down"></i>
        </label>
    </h1>
    <form id="pix-form">
        <table>
            <tr>
                <td colspan="4">
                    <fieldset class="header-fieldset">
                        <legend>Header</legend>
                        <input type="text"     name="author"   accesskey="n" placeholder="Name"     maxlength="255" pattern="^[a-zA-Z][a-zA-Z0-9.\-_ ]{3,31}$"                          value="<?php if(!empty($_SESSION['pix_post']) && !empty($_SESSION['pix_post']['author']  )){echo $_SESSION['pix_post']['author']  ;unset($_SESSION['pix_post']['author']  );} ?>" />
                        <input type="email"    name="email"    accesskey="e" placeholder="E-mail"   maxlength="255"                                                                     value="<?php if(!empty($_SESSION['pix_post']) && !empty($_SESSION['pix_post']['email']   )){echo $_SESSION['pix_post']['email']   ;unset($_SESSION['pix_post']['email']   );} ?>" />
                        <input type="text"     name="subject"  accesskey="s" placeholder="Subject"  maxlength="255" pattern="^[a-zA-Z0-9.\-_ ]+$"                                       value="<?php if(!empty($_SESSION['pix_post']) && !empty($_SESSION['pix_post']['subject'] )){echo $_SESSION['pix_post']['subject'] ;unset($_SESSION['pix_post']['subject'] );} ?>" />
                        <input type="password" name="password" accesskey="p" placeholder="Password" maxlength="255" title="Password for post management & deletion." autocomplete="off" value="<?php if(!empty($_SESSION['pix_post']) && !empty($_SESSION['pix_post']['password'])){echo $_SESSION['pix_post']['password'];unset($_SESSION['pix_post']['password']);} ?>" />
                    </fieldset>
                </td>
                <td colspan="6" rowspan="2">
                    <fieldset class="content-field">
                        <legend>Message</legend>
                        <textarea name="message" accesskey="m" placeholder="Message" title="A message to be sent along with the post."><?php if(!empty($_SESSION['pix_post']) && !empty($_SESSION['pix_post']['message'])){echo $_SESSION['pix_post']['message'];unset($_SESSION['pix_post']['message']);} ?></textarea>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="4" rowspan="3">
                    <fieldset class="file-field">
                        <legend>File</legend>
                        <?php
                            $types = str_getcsv($board['file_types'] ? $board['file_types'] : 'gif, jpg, png');
                            foreach($types as &$type) $type = mime('/dev/null/example.'.trim($type));
                            $types = implode(', ', $types);
                        ?>
                        <ul>
                            <li><?php echo $types; ?> allowed.</li>
                            <li>Maximum file size allowed is <?php echo format_bytes($board['max_size'] ?? (1048576 * 5)) ; ?>.</li>
                            <li>Images greater than 250x250 will be thumbnailed.</li>
                        </ul>
                        <input name="file" type="file" accesskey="f" accept="<?php echo $types; ?>" />
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="3" rowspan="2">
                    <fieldset class="captcha-field">
                        <legend>Captcha</legend>
                        <label class="captcha" for="captcha" title="Enter this text into the box below."></label>
                        <style nonce="<?php echo $security->nonce(); ?>">
                            label[for="captcha"] {
                                background-image: url("/asset/image/captcha.png?<?php echo bin2hex(random_bytes(16)); ?>");
                                background-size: 100% 100%;
                                float: none;
                            }
                        </style>
                        <input id="captcha" type="text" name="captcha" required minlength="4" maxlength="4" pattern="^.{4}$" autocomplete="off" placeholder="Enter the CAPTCHA above." autofocus />
                    </fieldset>
                </td>
                <td colspan="3">
                    <fieldset class="submit-field">
                        <legend>Submit</legend>
                        <div class="button-wrap">
                            <input name="modify_post" type="submit" value="Create" />
                            <?php if(strlen($pix->feedback ?? '')){ ?>
                                <span id="feedback"><?php print_r($pix->feedback); ?></span>
                            <?php } ?>
                        </div>
                    </fieldset>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <fieldset class="feedback-field">
                        <!-- <legend>Feedback</legend> -->
                        <p id="feedback"><?php print_r($pix->feedback ?? ''); ?></p>
                    </fieldset>
                </td>
            </tr>
        </table>
    </form>
    <noscript>
        <iframe id="frame-select" src="/pix/select" sandbox="allow-forms allow-same-origin"></iframe>
        <iframe id="frame-progress" src="/pix/progress" sandbox="allow-forms"></iframe>
        <style>#pix-form { display: none; }</style>
    </noscript>
</article>

<?php function print_post($post, $board){ ?>
    <?php global $pix, $uri, $security; ?>
    <h1>
        <a class="ajax" href="<?php echo $uri->path.'/'.($post['parent'] ? $post['parent'].'#'.$post['id'] : $post['id']); ?>" title="Link to this post.">&#8470;</a>&nbsp;
        <a class="ajax" href="<?php echo $uri->path.'/'.($post['parent'] ? $post['parent'] : $post['id']).'?quote='.$post['id']; ?>" title="Reply to and quote this post."><?php echo $post['id']; ?></a>&nbsp;
        <span title="Author of this post.">By:&nbsp;<?php echo $post['author'] ? $post['author'] : 'Anonymous'; ?></span>&nbsp;
        <span class="timestamp" title="Date & time of post."><?php echo date('n/j/y', $post['timestamp']).'&nbsp;'.date('G:i:s', $post['timestamp']); ?></span>&nbsp;
        <?php if(!$post['parent']){ ?>
            [<a class="ajax" href="/<?php echo $uri->class.'/'.$post['board'].'/'.$post['id']; ?>" title="Open thread.">Reply</a>]
        <?php } ?>
    </h1>

    <?php $file_name = basename(glob(ASSET.'/upload/'.$uri->class.'/'.$post['file_hash'].'*')[0]) ?? ''; ?>
    <a id="thumb-<?php echo $post['id']; ?>" class="thumb" href="/asset/upload/<?php echo $uri->class.'/'.$file_name; ?>" target="<?php echo $uri->path; ?>"></a>
    <style nonce="<?php echo $security->nonce(); ?>">
        #thumb-<?php echo $post['id']; ?> {
            background-image: url("/asset/upload/<?php echo $uri->class.'/thumb/'.$file_name; ?>");
        }
    </style>

    <p class="message"><?php
        // Split message by line.
        $lines = preg_split('/$\R?^/m', $post['message']);
        foreach($lines as &$line){

            // If message links to another post.
            if(substr($line, 0, 2) === '>>'){
                $link = array_filter(explode(' ', trim(substr($line, 2))))[0] ?? '';
                $link = $pix->model->get_post($link);
                if($link){
                    if($link['board'] === $board['path']){
                        $line = '<a href="/'.$uri->class.'/'.$link['board'].'#'.$link['id'].'">'.$line.'</a>';
                    }else{
                        $line = '<a href="/'.$uri->class.'/'.$link['board'].'/'.$link['id'].'">'.$line.'</a>';
                    }
                }
            }

            // "Greentext" handling.
            elseif(substr($line, 0, 1) === '>'){
                $line = '<span class="green">'.$line.'</span>';
            }
        }
        echo implode(PHP_EOL, $lines);
    ?></p>
<?php } ?>

<?php foreach($pix->model->get_posts() as $post){ ?>
    <article id="<?php echo $post['id']; ?>">
        <?php print_post($post, $board); ?>
        <?php foreach($pix->model->get_replies($post['id']) as $reply){ ?>
            <article id="<?php echo $reply['id']; ?>">
                <?php print_post($reply, $board); ?>
            </article>
        <?php } ?>
    </article>
<?php } ?>

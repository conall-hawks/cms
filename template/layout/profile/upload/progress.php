<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<!DOCTYPE html>
<html>
    <body>
        <?php $key = ini_get("session.upload_progress.prefix").'upload'; $info = !empty($_SESSION[$key]) ? $_SESSION[$key] : false; ?>
        <?php if(!empty($_SESSION[$key]['cancel_upload']) && $_SESSION[$key]['cancel_upload'] === true) $_SESSION[$key] = []; ?>
        <?php if($info && !$info['done']){ ?>
            <?php
                header('Refresh: 1');
                $elapsed   = time() - $info['start_time'];
                $completed = number_format($info['bytes_processed'] / $info['content_length'] * 100);
                $speed     = number_format($info['bytes_processed'] / ($elapsed ? $elapsed : 1) / 1024 / 1024, 2);
            ?>
            <section id="style" hidden>
                <?php $this->include_css($this->css['head']['variables'], true); ?>
                <?php $this->include_css($this->css['layout'], true); ?>
                <style>
                    html {
                        background: none;
                        height: 114px;
                        overflow: hidden;
                    }
                    #progress {
                        padding-bottom: 1px;
                        width: calc(100% - 18px);
                    }
                    .button-wrap > div {
                        background: rgba(0, 255, 0, .125);
                        border-radius: 2px;
                        box-shadow: var(--box-shadow-dark), 0 0 2px rgba(0, 255, 0, .5) inset;
                        height: calc(100% - 8px);
                        left: 4px;
                        position: absolute;
                        top: 50%;
                        transform: translateY(-50%);
                        width: <?php echo $completed; ?>%;
                    }
                    #progress > pre {
                        background: none;
                        float: left;
                        margin: 0 0 0 4px;
                        padding: 0;
                    }
                </style>
            </section>
            <fieldset id="progress">
                <div class="button-wrap"><div></div></div>
<pre>
Completed: <?php echo $completed; ?> %
Speed:     <?php echo $speed; ?> MiB/sec
File Size: <?php echo number_format($info['content_length'] / 1024 / 1024, 2); ?> MiB
</pre>
            </fieldset>
        </body>
    </html>
<?php }else header('Refresh: 3'); ?>

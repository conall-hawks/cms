<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); header('Refresh: 3'); ?>
<!DOCTYPE html>
<html>
    <body>
        <section id="style" hidden>
            <?php $this->include_css($this->css['head']['variables'], true); ?>
            <?php $this->include_css($this->css['layout'], true); ?>
            <?php $this->include_css($this->css['explorer'], true); ?>
            <style>
                html {
                    background: none;
                    height: 114px;
                    overflow: hidden;
                }
                #explorer {
                    border-bottom: var(--border);
                }
            </style>
        </section>
        <?php require(TEMPLATE.'/explorer/profile/upload.php'); ?>
    </body>
</html>

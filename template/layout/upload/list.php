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
                #explorer > h1:first-of-type {
                    display: none;
                }
                .scroll-wrap thead tr div {
                    top: 0 !important;
                }
            </style>
        </section>
        <?php $iframe = true; require(TEMPLATE.'/explorer/upload.php'); ?>
    </body>
</html>

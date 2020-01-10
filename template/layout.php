<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<!DOCTYPE html>
<html>
    <head>
        <?php if($this->html['head']) require($this->html['head']); ?>
    </head>

    <body id="top">
        <section id="style" hidden>
            <?php View::include_css($this->css, true); ?>
        </section>

        <header id="header">
            <?php if($this->html['header']) require($this->html['header']); ?>
        </header>

        <main id="main">
            <?php if($this->html['main']) require($this->html['main']); ?>
        </main>

        <footer id="footer">
            <?php if($this->html['footer']) require($this->html['footer']); ?>
        </footer>

        <section id="misc">
            <?php if($this->html['misc']) require($this->html['misc']); ?>
        </section>

        <section id="navbar" hidden>
            <?php if($this->html['navbar']) require($this->html['navbar']); ?>
        </section>

        <section id="script" hidden>
            <?php View::include_js($this->js, true); ?>
        </section>
    </body>
</html>

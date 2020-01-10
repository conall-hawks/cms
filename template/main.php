<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>

<?php
    // Generated early for aside-left template. $_pre comes from $this->html().
    ob_start();
    if(!empty($_pre)) echo '<article><h1>Debug</h1><pre>'.$_pre.'</pre></article>';
    if($this->use_explorer) $this->template('explorer');
    $this->template('content');
    if($this->use_cms) $this->template('cms');
    global $_content;
    $_content = ob_get_clean();
?>

<?php if($this->html['aside-left']){ ?>
    <aside id="aside-left">
        <?php $this->template('aside-left'); ?>
    </aside>
<?php } ?>

<aside id="aside-right">
    <?php $this->template('aside-right'); ?>
</aside>

<aside id="aside-left-2">
    <?php $this->template('aside-left-2'); ?>
</aside>

<aside id="aside-right-2">
    <?php $this->template('aside-right-2'); ?>
</aside>

<section id="content">
    <?php echo $_content; ?>
</section>

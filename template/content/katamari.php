<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="katamari">
    <h1>Katamari!</h1>
    <?php foreach(new FilesystemIterator(ASSET.'/image/katamari', FilesystemIterator::SKIP_DOTS) as $file){ ?>
        <div><a class="ajax" href="/katamari/<?php echo $file->getFilename(); ?>"><?php echo title($file->getFilename()); ?></a></div>
    <?php } ?>
</article>

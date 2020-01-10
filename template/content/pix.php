<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="bookmarks">
    <h1>Imageboard</h1>
    <p>Forums with a focus on sharing images (and other media).</p>
</article>

<article>
    <h1>Latest Posts</h1>
    <?php print_r($pix->model->get_boards()); ?>
</article>

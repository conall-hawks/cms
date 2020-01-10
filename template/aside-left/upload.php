<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<h1>Upload</h1>
<ul>
    <li><a class="ajax" href="/upload">Create/Browse Uploads</a></li>
    <?php if($user->status()){ ?>
        <li><a class="ajax" href="/upload/password">Protected Uploads</a></li>
    <?php } ?>
    <?php if($user->status()){ ?>
        <li><a class="ajax" href="/upload/private">Private Uploads</a></li>
    <?php } ?>
</ul>

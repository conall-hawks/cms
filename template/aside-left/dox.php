<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<h1>Dox</h1>
<ul>
    <li><a class="ajax" href="/stox">Stocks</a></li>
    <li><a class="ajax" href="/linkz">Links</a></li>
    <li><a class="ajax" href="/store">Store</a></li>
    <li><a class="ajax" href="/tunez">Music</a></li>
    <li><a class="ajax" href="/katamari">Katamari</a></li>
    <li><a class="ajax" href="/about">About</a></li>
    <?php if(ENVIRONMENT === 'development'){ ?>
        <li><a class="ajax" href="/test">Sandbox</a></li>
    <?php } ?>
</ul>

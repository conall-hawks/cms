<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<?php if(ENVIRONMENT === 'development'){ ?>
    <section>
        <article>
            <h1>Session</h1>
            <pre><?php $session = $_SESSION; unset($session['log']); echo str_replace("\0", '', print_r($session, true)); ?></pre>
        </article>
        <article>
            <h1>Post</h1>
            <pre><?php if($_POST) print_r($_POST); ?></pre>
        </article>
    </section>
<?php } ?>

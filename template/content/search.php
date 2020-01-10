<article>
    <h1>Search</h1>
    <form class="ajax">
        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>" required />
        <input type="search" name="search" />
        <div class="button-wrap"><input type="submit" value="Search" /></div>
    </form>
</article>

<?php if(!empty($_POST['search'])){ ?>
        <?php
            $url ='https://www.googleapis.com/customsearch/v1'
                 .'?q='.urlencode($_POST['search'])
                 .'&cx=partner-pub-6772462260567961%3A6385416133'
                 .'&key=AIzaSyDanqRi7aDQhAHx53yKuCaViHNXvdakH6c';
            $query = json_decode(file_get_contents($url), true);
        ?>
        <?php foreach($query['items'] as $result){ ?>
    <article>
            <h1><a href="<?php echo htmlspecialchars($result['link']); ?>" target="_blank">
                <?php echo htmlspecialchars($result['title']); ?>
            </a></h1>

            <img src="<?php echo htmlspecialchars($result['pagemap']['cse_thumbnail'][0]['src']); ?>" />
            <p>
                <?php echo nl2br(htmlspecialchars($result['snippet'])); ?>
            </p>

            <a href="<?php echo parse_url($result['link'], PHP_URL_SCHEME).'://'.htmlspecialchars($result['displayLink']); ?>" target="_blank">
                <?php echo trim_prefix(htmlspecialchars($result['displayLink']), 'www.'); ?>
            </a>
    </article>
        <?php } ?>

    <?php if(ENVIRONMENT === 'development'){ ?>
        <article>
            <h1>Debug</h1>
            <?php echo '<pre>'.print_r($query, true).'</pre>'; ?>
        </article>
    <?php } ?>
<?php } ?>


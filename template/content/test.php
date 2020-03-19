<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="sandbox">
    <h1>Sandbox</h1>
    <code><?php

################################################################################
# TESTING AREA: WHEE! ##########################################################
################################################################################

echo title('hello c sharp lol<br />');


echo css_hex_to_rgb('ffff');
#/* Get data from "API". */
#$url = 'https://finance.yahoo.com/quote/MSFT/key-statistics?p='.$ticker;
#$curl = curl_init();
#curl_setopt($curl, CURLOPT_RETURNTRANSFER, $url);
#curl_setopt($curl, CURLOPT_URL, $url);
#$result = curl_exec($curl);
#curl_close($curl);
#
#/* Prevent negligible errors from polluting HTML output. */
#libxml_use_internal_errors(true);
#
#/* Build DOM */
#$html = new DOMDocument();
#$html->loadHTML($result);
#
#/* Locate value. */
#$elements = $html->getElementsByTagName('span');
#foreach($elements as $element){
#    if(trim($element->nodeValue) === 'Price/Sales'){
#        echo $element->parentNode->nextSibling->nodeValue;
#    }
#}

#$time_target = 0.05;
#$cost = 8;
#do{
#    $cost++;
#    $start = microtime(true);
#    password_hash('test', PASSWORD_BCRYPT, ['cost' => $cost]);
#    $end = microtime(true);
#}while(($end - $start) < $time_target);
#
#echo 'Appropriate Cost Found: ' . $cost;
#unset($time_target, $cost, $start, $end);

#while(ob_get_level()) ob_end_clean();
#die(phpinfo());

################################################################################
################################################################################
################################################################################
?>
    </code>
</article>

<article id="statistics">
    <h1>Statistics</h1>
    <pre>Load time:         <?php echo number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3); ?> sec
Memory usage:      <?php echo format_bytes(memory_get_usage(false)).PHP_EOL; ?>
Peak memory usage: <?php echo format_bytes(memory_get_peak_usage(false)); ?>
</pre>
</article>

<article id="captcha">
    <h1>CAPTCHA</h1>
    <img class="captcha" src="/captcha?<?php echo time(); ?>" alt="Type this text into the box below." />
    <p>A captcha image should appear here.</p>
</article>

<?php if(ENVIRONMENT === 'development'){ ?>
    <article id="header-dump">
        <h1>Header Dump</h1>
        <code><?php print_r(headers_list()); ?></code>
    </article>

    <article id="session-variable-dump" class="content">
        <h1>Session Variable Dump</h1>
        <code><?php htmlspecialchars(print_r($_SESSION)); ?></code>
    </article>

    <article id="server-variable-dump">
        <h1>Server Variable Dump</h1>
        <code><?php include(ASSET.'/share/code/php/variable-dump.php'); ?></code>
    </article>
<?php } ?>

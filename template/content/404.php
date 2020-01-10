<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); http_response_code(404); ?>
<article id="not-found">
    <h1>404 Not Found</h1>
    <p id="not-found-inner">
        We couldn't find the page you're looking for. :(
    </p>
    <style>
        #not-found-inner {
            background-image: url("/<?php echo ASSET.'/image/404-'.rand(1, 3).'.jpg'; ?>");
            background-size: 100% 100%;
            color: white;
            font-size: 2vw;
            font-weight: bold;
            height: 36.5vw;
            line-height: 36.5vw;
            text-align: center;
            text-shadow: 0 0 2px white, 0 0 4px blue, 0 0 8px purple;
        }
    </style>
</article>

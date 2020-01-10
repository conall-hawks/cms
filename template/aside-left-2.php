<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__);

/* Handle POST. */
if(!empty($_POST[$this->form_token('background-color')])){
    $_SESSION['background-color'] = $_POST[$this->form_token('background-color')];
} ?>
<h1>Settings</h1>

<!-- User background customization. -->
<form class="ajax" method="post">
    <fieldset>
        <legend>Background Color</legend>

        <table>
            <tr>
                <td colspan="2">
                    <div class="button-wrap">
                        <input name="<?php echo $this->form_token('background-color'); ?>" type="color" value="#111111" onchange="var e=document.createEvent(&quot;MouseEvents&quot;);e.initMouseEvent(&quot;mousedown&quot;,true,true,window,1,0,0,0,0,false,false,false,false,0,null);document.getElementById(&quot;background-color-submit&quot;).dispatchEvent(e);" required />
                    </div>
                </td>
            </tr>

            <tr>
                <td>
                    <div class="button-wrap">
                        <input type="reset" onclick="var e=document.createEvent(&quot;MouseEvents&quot;);e.initMouseEvent(&quot;mousedown&quot;,true,true,window,1,0,0,0,0,false,false,false,false,0,null);document.getElementById(&quot;background-color-submit&quot;).dispatchEvent(e);" />
                    </div>
                </td>

                <td>
                    <div class="button-wrap">
                        <input id="background-color-submit" type="submit" value="Apply" />
                    </div>
                </td>
            </tr>
        </table>
    </fieldset>

    <style nonce="<?php echo $security->nonce(); ?>">
        html {
            <?php if(!empty($_SESSION['background-color']) && $_SESSION['background-color'] !== '#111111'){ ?>
                background-color: <?php echo htmlspecialchars($_SESSION['background-color']); ?> !important;
            <?php }else{ ?>
                background-color: hsl(<?php echo rand(0, 359); ?>, 100%, 5%) !important;
            <?php } ?>
        }
    </style>
</form>

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
                background-color: hsl(<?php $hue = rand(0, 359); echo $hue; ?>, 100%, 5%) !important;
            <?php } ?>
        }
        /*
        #navbar {
            <?php if(!empty($_SESSION['background-color']) && $_SESSION['background-color'] !== '#111111'){ ?>
                background-color: <?php echo css_hex_to_rgb(htmlspecialchars($_SESSION['background-color']).'d9'); ?> !important;
            <?php }else{ ?>
                background-color: hsla(<?php echo $hue; ?>, 100%, 5%, 0.85) !important;
            <?php } ?>
        }
        */
    </style>
</form>

<?php if(in_array(strtolower(date('M')), ['nov', 'dec', 'jan', 'feb'])){ ?>
    <!-- Snow during the Winter months. -->
    <script async nonce="<?php echo $security->nonce(); ?>" src="https://cdn.jsdelivr.net/npm/jquery-snowfall@latest/dist/snowfall.min.js"></script>
    <script nonce="<?php echo $security->nonce(); ?>">
        if(typeof window.startSnowfall !== "number") window.startSnowfall = setInterval(function(){
            if(typeof document.body === "object" && typeof snowFall === "object" && window.startSnowfall){
                window.startSnowfall = 0;
                clearInterval(window.startSnowfall);
                document.body.style.float = "left";
                document.body.style.width = "100%";
            }
        }, 1000);

        /* Build DOM elements for toggle button. */
        if(!document.querySelector("#snowfall-toggle-fieldset")){
            var fieldset = document.createElement("fieldset");
            fieldset.setAttribute("id", "snowfall-toggle-fieldset");
            document.getElementById("aside-left-2").appendChild(fieldset);
            var legend = document.createElement("legend");
            legend.innerText = "Snowflakes"
            fieldset.appendChild(legend);
            var table = document.createElement("table");
            fieldset.appendChild(table);
            var row = document.createElement("tr");
            table.appendChild(row);
            var cell = document.createElement("td");
            row.appendChild(cell);
            var buttonWrap = document.createElement("div");
            buttonWrap.classList.add("button-wrap");
            cell.appendChild(buttonWrap);
            var button = document.createElement("input");
            button.setAttribute("id", "snowfall-toggle");
            button.setAttribute("type", "button")
            button.value = "Toggle On/Off";
            buttonWrap.appendChild(button);
            window.listen(["mousedown", "keydown"], function(){
                if(document.body.snow && document.body.snow.active){
                    snowFall.snow(document.body, "clear");
                    document.body.snow.active = false;
                }else{
                    try{for(var i = 0; i < 5; i++) snowFall.snow(document.body, "clear")}catch(e){}
                    snowFall.snow(document.body, {flakeColor: "#BBB", flakeCount: 100, maxSpeed: 5, round: true});
                    document.body.snow.active = true;
                }
            }, button);
        }
    </script>
<?php } ?>

<?php if(in_array(strtolower(date('M')), ['mar', 'apr', 'may', 'jun'])){ ?>
    <!-- Sparkles during the Spring months. -->
    <!--<script async nonce="<?php echo $security->nonce(); ?>" src="<?php echo ASSET.'/misc/fairy-dust.js'; ?>"></script>-->
<?php } ?>

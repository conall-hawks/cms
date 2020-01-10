<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<table>
    <tr>
        <td><h1><?php echo TITLE; ?></h1></td>
        <td><div><div class="<?php echo link_active('/index'); ?>"><a class="ajax" href="/">/index</a></div></div></td>
        <td><div><div class="<?php echo link_active('/code'); ?>"><a class="ajax" href="/code">/code</a></div></div></td>
        <td><div><div class="<?php echo link_active('/dox'); ?>"><a class="ajax" href="/dox">/dox</a></div></div></td>
        <td><div><div class="<?php echo link_active('/upload'); ?>"><a class="ajax" href="/upload">/upload</a></div></div></td>

        <!-- Login form. -->
        <td>
            <?php if($user->status()){ ?>
                <div><div class="<?php echo link_active('/profile'); ?>"><a class="ajax" href="/profile">/profile</a></div></div>
            <?php }else{ ?>
                <form class="login">
                    <table>
                        <tr>
                            <td colspan="3">
                                <input id="navbar-login-username" tabindex="5" type="text" placeholder="Username" pattern="^.{4,255}$" minlength="4" maxlength="255" />
                            </td>
                            <td><div><input id="navbar-login-submit" tabindex="7" type="submit" value="Enter" /></div></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <input id="navbar-login-password" tabindex="6" type="password" placeholder="Password" pattern="^.{4,255}$" minlength="4" maxlength="255" autocomplete="off" />
                                <span id="navbar-capslock-notice" class="capslock-notice">CAPS LOCK</span>
                            </td>
                            <td>
                                <div class="slider">
                                    <p>Remember Me:</p>
                                    <input id="navbar-login-remember-me" type="checkbox" checked />
                                    <label for="navbar-login-remember-me" tabindex="8"></label>
                                </div>
                            </td>
                        </tr>
                    </table>
                </form>
            <?php } ?>
        </td>
    </tr>
</table>

<?php if(stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || stripos($_SERVER['HTTP_USER_AGENT'], 'Trident')){ ?>
    <!-- Best-attempt fixes for Internet Explorer. -->
    <style nonce="<?php echo $security->nonce(); ?>">
        @media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
            #navbar td > div {
                height: 44px !important;
                margin-top: 4px !important;
                -ms-transform: translate(-50%, 0) !important;
            }
            #navbar .login td > input, .login td > div {
                height: 19px !important;
                margin-top: 4px !important;
                -ms-transform: translate(-50%, 0) !important;
            }
            #navbar-login-remember-me + label {
                top: 12px;
            }
            #navbar .login input[type="submit"] {
                height: 19px !important;
                top: auto !important;
                margin-top: -1px;
            }
        }
    </style>
<?php } ?>

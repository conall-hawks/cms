<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>

<!-- Default header. -->
<table>
    <tr>
        <td rowspan="3"><a id="header-logo" class="ajax <?php echo link_active('/'); ?>" href="/"></a></td>
        <td><div><div class="<?php echo link_active('/'); ?>"><a class="ajax" href="/">/index</a></div></div></td>
        <td><div><div class="<?php echo link_active('/code'); ?>"><a class="ajax" href="/code">/code</a></div></div></td>
        <td><div><div class="<?php echo link_active('/dox'); ?>"><a class="ajax" href="/dox">/dox</a></div></div></td>
        <td><div><div class="<?php echo link_active('/upload'); ?>"><a class="ajax" href="/upload">/upload</a></div></div></td>
        <td><div><div class="<?php echo link_active('/tool'); ?>"><a class="ajax" href="/tool">/tool</a></div></div></td>
    </tr>

    <tr>
        <td><div><div class="<?php echo link_active('/forum'); ?>"><a class="ajax" href="/forum">/forum</a></div></div></td>

        <!-- Inner header. -->
        <td colspan="3" rowspan="2">
            <table id="header-inner">

                <tr>
                    <!-- Real-time clock. -->
                    <td id="clock">
                        <?php echo date('F j').'<sup>'.date('S').'</sup>'.date(', Y'); ?>
                    </td>

                    <!-- Site title. -->
                    <td id="header-title" colspan="3" rowspan="3">
                        <?php echo TITLE; ?>
                    </td>

                    <!-- User's name. -->
                    <td id="header-username">
                        User:
                        <a class="ajax <?php echo link_active('/profile/'.rawurlencode($user->username())); ?>" href="/profile" title="<?php echo htmlspecialchars($user->username(), ENT_SUBSTITUTE); ?>"><?php echo htmlspecialchars($user->username()); ?></a>
                    </td>
                </tr>

                <tr>
                    <td><a class="ajax <?php echo link_active('/newz'); ?>" href="/newz">/newz</a></td>
                    <td>
                        <?php if($user->status()){ ?>
                            <a class="ajax <?php echo link_active('/inbox'); ?>" href="/inbox">/inbox [0]</a>
                        <?php }else{ ?>
                            <a class="ajax <?php echo link_active('/register'); ?>" href="/register">/register</a>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <!--<a class="<?php echo link_active('/linkz'); ?> ajax" href="/linkz">/linkz</a>-->
                        <a class="<?php echo link_active('/stox'); ?> ajax" href="/stox">/stox</a>
                    </td>
                    <td id="header-encrypted" title="HTTPS helps ensure privacy and integrity of the exchanged data.">
                        Encrypted: <?php echo isset($_SERVER['HTTPS']) ? '<span class="green">Yes</span>' : '<span class="red">No</span>'; ?>
                    </td>
                </tr>
                <tr>
                    <td id="header-forum">Forum: <span class="red">Offline</span></td>
                    <td id="header-subtitle" colspan="3" rowspan="2">
                        <span><?php echo html_subtitle(); ?></span>
                        <span id="loading-notify"></span>
                    </td>
                    <td id="header-websocket" title="WebSocket enables real-time communication.">
                        WebSocket: <label class="red" for="chat">Offline</label>
                    </td>
                </tr>
                <tr>
                    <td id="header-imageboard">Imageboard: <span class="green">Online</span></td>
                    <?php if($user->status()){ ?>
                        <td>
                            <!-- Logout form. -->
                            <form id="logout" class="ajax" method="post">
                                <input id="logout-submit" class="ajax" name="<?php echo $user->view->form_token('logout'); ?>" type="submit" value="Logout" />
                            </form>
                        </td>
                    <?php }else{ ?>
                        <td id="katamari"></td>
                    <?php } ?>
                </tr>
            </table>
        </td>

        <td><div><div class="<?php echo link_active('/email'); ?>"><a class="ajax" href="/email">/email</a></div></div></td>
    </tr>
    <tr>
        <td><div title="Imageboard"><div class="<?php echo link_active('/pix'); ?>"><a class="ajax" href="/pix">/pix</a></div></div></td>

        <!-- Login form. -->
        <td>
            <?php if($user->status()){ ?>
                <div><div class="<?php echo link_active('/profile'); ?>"><a class="ajax" href="/profile">/profile</a></div></div>
            <?php }else{ ?>
                <form id="login" class="login ajax" method="post">
                    <table>
                        <tr>
                            <td colspan="3">
                                <input id="login-username" name="<?php echo $user->view->form_token('username'); ?>" tabindex="1" type="text" placeholder="Username" pattern="^.{4,255}$" required minlength="4" maxlength="255" autofocus />
                                <script nonce="<?php echo $security->nonce(); ?>">focusInput(document.querySelector("input[name=\"<?php echo $user->view->form_token('username'); ?>\"]"));</script>
                            </td>
                            <td><div><input tabindex="3" type="submit" value="Enter" /></div></td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <input id="login-password" name="<?php echo $user->view->form_token('password'); ?>" autocomplete="off" placeholder="Password" tabindex="2" type="password" pattern="^.{4,255}$" required minlength="4" maxlength="255" />
                                <span id="login-capslock-notice" class="capslock-notice">CAPS LOCK</span>
                            </td>
                            <td>
                                <div class="slider">
                                    <p>Remember Me:</p>
                                    <input id="login-remember-me" name="<?php echo $user->view->form_token('remember_me'); ?>" type="checkbox" checked />
                                    <label for="login-remember-me" tabindex="4"></label>
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
            #header {
                height: 154px !important;
            }
            #header-logo {
                height: 144px !important;
                margin-top: 4px !important;
                -ms-transform: translate(-50%, 0) !important;
            }
            #header td > div {
                height: 44px !important;
                margin-top: 4px !important;
                -ms-transform: translate(-50%, 0) !important;
            }
            #header-inner {
                height: 96px !important;
                margin-top: 1px !important;
                -ms-transform: translate(-50%, 0) !important;
            }
            #header #login td > input, #login td > div {
                height: 19px !important;
                margin-top: 4px !important;
                -ms-transform: translate(-50%, 0) !important;
            }
            #header #remember-me + label {
                top: 12px;
            }
            #header #logout input[type="submit"] {
                height: 19px !important;
                top: auto !important;
                margin-top: -1px;
            }
        }
    </style>
<?php } ?>

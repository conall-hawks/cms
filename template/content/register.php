<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="registration">
    <h1>Registration</h1>
    <?php if($user->status()){ ?>
        <p>You are logged in! ;3</p>
    <?php }else{ ?>
        <form class="ajax" method="post">
            <table>
                <tr>
                    <td colspan="4">
                        <fieldset id="credentials">
                            <legend>Credentials:</legend>
                            <?php if(!$user->db) $user->db(); ?>
                            <input accesskey="n" name="username"        placeholder="Username"        required pattern="^.{<?php echo $user->db->username_length_min; ?>,<?php echo $user->db->username_length_max; ?>}$" minlength="<?php echo $user->db->username_length_min; ?>" maxlength="<?php echo $user->db->username_length_max; ?>" tabindex="4" type="text" value="<?php if(!empty($_POST['username'])) echo htmlspecialchars($_POST['username']); ?>" autofocus />
                            <input accesskey="p" name="password"        placeholder="Password"        required pattern="^.{4,255}$" minlength="4" maxlength="255" tabindex="5" type="password" autocomplete="off" />
                            <input accesskey="r" name="password_repeat" placeholder="Password Retype" required pattern="^.{4,255}$" minlength="4" maxlength="255" tabindex="6" type="password" autocomplete="off" />
                        </fieldset>
                    </td>
                    <td colspan="10" rowspan="3">
                        <fieldset id="information">
                            <legend>Information:</legend>
                            <ul>
                                <li>Assign a unique username.</li>
                                <li>Remove captchas.</li>
                                <li>This system is still under development.</li>
                            </ul>
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <fieldset id="captcha">
                            <legend>Captcha:</legend>
                            <img class="captcha" src="/asset/image/captcha.png?time=<?php echo time(); ?>" title="Enter this text into the box below." />
                            <input accesskey="c" autocomplete="off" name="<?php echo $user->view->form_token('captcha'); ?>" placeholder="Enter the CAPTCHA above." required pattern="^.{4}$" minlength="4" maxlength="4" tabindex="7" type="text" value="<?php if(!empty($_POST['captcha'])) echo htmlspecialchars($_POST['captcha']); ?>" />
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <fieldset class="submit">
                            <div class="button-wrap"><input accesskey="s" name="register" tabindex="8" type="submit" value="Submit" /></div>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </form>
    <?php } ?>
</article>

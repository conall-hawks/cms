<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<!DOCTYPE html>
<html>
    <body>
        <section id="style" hidden>
            <?php $this->include_css($this->css['head']['variables'], true); ?>
            <?php $this->include_css($this->css['layout'], true); ?>
            <?php $this->include_css($this->css['main'], true); ?>
            <style>
                html {
                    background: none;
                    height: 114px;
                    overflow: hidden;
                }
            </style>
        </section>
        <article id="new-file">
            <form method="post" enctype="multipart/form-data" id="upload-form">
                <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="upload" />
                <table>
                    <tr>
                        <td>
                            <fieldset>
                                <legend>Privacy</legend>
                                <table>
                                    <tr>
                                        <td><label for="privacy-private">Private:</label></td>
                                        <td colspan="2" <?php if(!$user->status()) echo 'title="You must be logged in for uploading to private storage."'; ?>><div class="button-wrap<?php if(!$user->status()) echo ' disabled'; ?>">
                                            <input type="radio" name="privacy" value="private" id="privacy-private" <?php if(!$user->status()) echo 'disabled '; ?>/>
                                        </div></td>
                                    </tr>
                                    <tr>
                                        <td><label for="privacy-public">Public:</label></td>
                                        <td colspan="2"><div class="button-wrap"><input type="radio" name="privacy" value="public" id="privacy-public" checked /></div></td>
                                    </tr>
                                    <tr>
                                        <td><label for="privacy-password">Password:</label></td>
                                        <td colspan="2"><div class="button-wrap">
                                            <input type="radio"
                                                   name="privacy"
                                                   value="password"
                                                   id="privacy-password"
                                                   class="radio-toggle"
                                                   onclick="focusInput(document.getElementById(&quot;upload-password&quot;));" />
                                            <input type="password" name="upload-password" placeholder="Password" id="upload-password" autocomplete="off" />
                                        </div></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td>
                            <fieldset>
                                <legend>Upload</legend>
                                <table>
                                    <tr><td colspan="2"><input type="file" name="upload" required id="upload-file" /></td></tr>
                                    <tr><td colspan="2"><div class="button-wrap"><input type="submit" value="Upload" id="upload-submit" /></div></td></tr>
                                    <tr>
                                        <td><div class="button-wrap"><label class="button" for="upload-cancel">Cancel</label></div></td>
                                        <td><div class="button-wrap"><input type="reset" value="Reset" /></div></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td>
                            <fieldset>
                                <legend>Captcha</legend>
                                <img class="captcha" src="/asset/image/captcha.png?time=<?php echo time(); ?>" title="Enter this text into the box below." />
                                <input type="text" name="captcha" required minlength="4" maxlength="4" pattern="^.{4}$" autocomplete="off" placeholder="Enter the CAPTCHA above." />
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </form>
            <form method="post" id="upload-form-cancel">
                <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="upload" />
                <input type="submit" name="cancel" id="upload-cancel" style="display: none;" />
            </form>
        </article>
    </body>
</html>




<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<!DOCTYPE html>
<html>
    <body>
        <section id="style" hidden>
            <?php $this->include_css($this->css['head']['variables'], true); ?>
            <?php $this->include_css($this->css['layout'], true); ?>
            <style>
                html {
                    background: none;
                    height: 114px;
                    overflow: hidden;
                }
                #upload {
                    height: 96px;
                }
            </style>
        </section>
        <article>
            <form class="ajax" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>" required />
                <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="upload" />
                <table>
                    <tr>
                        <td>
                            <fieldset>
                                <legend>Privacy</legend>

                                <table>
                                    <tr>
                                        <td colspan="2"><label for="privacy-private">Private:</label></td>
                                        <td colspan="3"><div class="button-wrap"><input type="radio" name="privacy" value="private" id="privacy-private" checked /></div></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="privacy-public">Public:</label></td>
                                        <td colspan="3"><div class="button-wrap"><input type="radio" name="privacy" value="public" id="privacy-public" /></div></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><label for="privacy-password">Password:</label></td>
                                        <td colspan="3"><div class="button-wrap"><input type="radio" name="privacy" value="password" id="privacy-password" /></div></td>
                                    </tr>
                                </table>
                            </fieldset>
                        </td>
                        <td colspan="3">
                            <fieldset id="upload">
                                <legend>Upload</legend>

                                <table>
                                    <tr><td><input type="file" name="upload" multiple /></td></tr>
                                    <tr><td><div class="button-wrap"><input type="submit" value="Upload" /></div></td></tr>
                                    <tr><td><div class="button-wrap"><a class="button ajax" href="<?php echo '/profile/upload/cancel'; ?>">Cancel Upload</a></div></td></tr>
                                </table>
                            </fieldset>
                        </td>
                    </tr>
                </table>
            </form>
        </article>
    </body>
</html>




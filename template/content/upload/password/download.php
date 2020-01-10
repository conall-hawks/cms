<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article>
    <h1>Password Upload</h1>
    <form class="ajax" method="post" download>
        <table>
            <tr>
                <td>
                    <fieldset id="credentials">
                        <legend>Information</legend>
                        <table>
                            <tr><td colspan="2"><p>This is a password-protected file. Please enter a password.</p></td></tr>
                            <tr><td colspan="2"><hr /></td></tr>
                            <?php $upload = $this->model->upload_select(NULL, 'password', $uri->arguments[0])[0]; ?>
                            <tr>
                                <td><p>Name:</p></td>
                                <td><span id="upload-name"><?php echo $upload['name']; ?></span><br /></td>
                            </tr>
                            <tr>
                                <td><p>Size:</p></td>
                                <td><span id="upload-size"><?php echo format_bytes(filesize(ltrim($upload['path'], '/')) ?? 'N/A', NULL, 'long'); ?></span><br /></td>
                            </tr>
                            <tr>
                                <td><p>MIME Type:</p></td>
                                <td><span id="upload-mime"><?php echo mime(ltrim($upload['path'], '/')) ?? 'N/A'; ?></span><br /></td>
                            </tr>
                            <tr>
                                <td><p>Author:</p></td>
                                <td><span id="upload-author"><?php echo $user->model->get_user($upload['user_id'], 'id')['username'] ?? 'N/A'; ?></span><br /></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td>
                    <fieldset id="submit">
                        <legend>Download</legend>
                        <table>
                            <tr>
                                <td><label for="upload-password">Password:</label></td>
                                <td><input  id="upload-password" type="password" name="<?php echo $this->form_token('password'); ?>" placeholder="&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;&#9679;" autofocus /></td>
                            </tr>
                            <tr><td colspan="2"><div class="input-placeholder"></div></td></tr>
                            <tr>
                                <td><div class="button-wrap"><input type="reset" /></div></td>
                                <td><div class="button-wrap"><input type="submit" value="Submit" /></div></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td>
                    <fieldset>
                        <legend>Captcha</legend>
                        <img class="captcha" src="/asset/image/captcha.png?<?php echo bin2hex(random_bytes(16)); ?>" title="Enter this text into the box below." />
                        <input type="text" name="captcha" required minlength="4" maxlength="4" pattern="^.{4}$" autocomplete="off" placeholder="Enter the CAPTCHA above." autofocus />
                    </fieldset>
                </td>
            </tr>
        </table>
    </form>
</article>

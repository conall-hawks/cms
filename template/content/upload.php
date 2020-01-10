<article id="new-file">
    <h1>Upload New File</h1>
    <noscript>
        <iframe id="frame-select" src="/upload/select" sandbox="allow-forms allow-same-origin"></iframe>
        <iframe id="frame-progress" src="/upload/progress" sandbox="allow-forms"></iframe>
        <style>#upload-form { display: none; }</style>
    </noscript>
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
                                    <input type="radio" name="privacy" value="password" id="privacy-password" class="radio-toggle" />
                                    <input type="password" name="password" placeholder="Password" id="upload-password" autocomplete="off" />
                                </div></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td>
                    <fieldset>
                        <legend>Upload</legend>

                        <table>
                            <tr><td colspan="2">
                                <input type="file" name="upload" required id="upload-file" />
                                <script nonce="<?php echo $security->nonce(); ?>">
                                    document.getElementById("upload-file").addEventListener("change", function(){focusInput(document.querySelector("input[name=\"captcha\"]"))});
                                </script>
                            </td></tr>
                            <tr><td colspan="2"><div class="button-wrap"><input type="submit" value="Upload" id="upload-submit" /></div></td></tr>
                            <tr>
                                <td><div class="button-wrap"><input type="button" value="Cancel" id="upload-cancel" /></div></td>
                                <td><div class="button-wrap"><input type="reset" value="Reset" /></div></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td>
                    <fieldset>
                        <legend>Captcha</legend>
                        <label class="captcha" for="captcha" title="Enter this text into the box below."></label>
                        <style nonce="<?php echo $security->nonce(); ?>">
                            label[for="captcha"] {
                                background-image: url("/asset/image/captcha.png?<?php echo bin2hex(random_bytes(16)); ?>");
                                background-size: 100% 100%;
                                float: none;
                            }
                        </style>
                        <input id="captcha" type="text" name="captcha" required minlength="4" maxlength="4" pattern="^.{4}$" autocomplete="off" placeholder="Enter the CAPTCHA above." autofocus />
                    </fieldset>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <fieldset id="progress-fieldset">
                        <legend>Progress</legend>
                        <table>
                            <tr><td colspan="3"><div class="button-wrap"><div id="upload-progress-bar"></div></div></td></tr>
                            <tr>
                                <td><p>Progress:</p></td>
                                <td colspan="2"><span id="upload-progress-percent">N/A</span><br /></td>
                            </tr>
                            <tr>
                                <td><p>Bandwidth:</p></td>
                                <td colspan="2"><span id="upload-progress-bandwidth">0 B/sec</span><br /></td>
                            </tr>
                            <tr>
                                <td><p>Time Remaining:</p></td>
                                <td colspan="2"><span id="upload-progress-time-remaining">N/A</span><br /></td>
                            </tr>
                            <tr>
                                <td><p>Status:&nbsp;</p></td>
                                <td colspan="2"><span id="upload-progress-status">Ready.</span><br /></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
                <td>
                    <fieldset id="information">
                        <legend>Information</legend>
                        <ul>
                            <li>Maximum file size of 10 MiB allowed.</li>
                            <li>Automatically removed after 7 days.</li>
                            <li>By using this service you agree to our <a class="ajax" href="/policy#user-content">Terms of Use</a>.</li>
                        </ul>
                    </fieldset>
                </td>
            </tr>
        </table>
    </form>
</article>

<?php if(!empty($this->log)){ ?>
    <article>
        <h1>Feedback</h1>
        <p><?php echo $this->log; ?></p>
    </article>
<?php } ?>

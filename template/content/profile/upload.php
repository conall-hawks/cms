<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<?php $username_html = htmlspecialchars($this->user['username']); ?>
<?php $username_href = rawurlencode($username_html); ?>
<article id="uploads">
    <h1>Uploads</h1>
    <noscript>
        <iframe id="frame-select" src="/profile/<?php echo $username_href; ?>/upload/select" sandbox="allow-forms"></iframe>
        <iframe id="frame-progress" src="/profile/<?php echo $username_href; ?>/upload/progress" sandbox="allow-forms"></iframe>
        <style>#uploads > form { display: none; }</style>
    </noscript>
    <form id="upload-form" class="ajax" method="post" enctype="multipart/form-data">
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
                            <tr><td colspan="2"><input type="file" name="upload" required /></td></tr>
                            <tr><td colspan="2"><div class="button-wrap"><input type="submit" value="Upload" /></div></td></tr>
                            <tr>
                                <td><div class="button-wrap"><div class="button">Cancel Upload</div></div></td>
                                <td><div class="button-wrap"><input type="reset" value="Reset" /></div></td>
                            </tr>
                        </table>
                    </fieldset>
                </td>
            </tr>
        </table>
    </form>
</article>

<article id="frame-list-wrap">
    <style>#frame-list-wrap { display: none; }</style>
    <noscript>
        <iframe id="frame-list" src="/profile/<?php echo $username_href; ?>/upload/list"></iframe>
        <style>#explorer { display: none; } #frame-list-wrap { display: block; }</style>
    </noscript>
</article>

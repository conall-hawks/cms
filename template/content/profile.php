<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<?php $username = urldecode(explode('/', $uri->path)[2] ?? $user->username()); ?>
<?php $profile = $user->model->get_user($username, 'username'); ?>
<?php #if(empty($profile) && !empty($_SESSION['user'])) $profile = $_SESSION['user']; ?>
<article id="profile">
    <?php $title = htmlspecialchars(substr($profile['username'], -1) !== 's' ? $profile['username']."'s" : $profile['username']."'").' Profile'; ?>
    <h1 title="<?php echo $title; ?>"><?php echo $title; ?></h1>
    <?php if(mb_strtolower($user->username()) === mb_strtolower($profile['username'])){ ?>
        <form class="ajax" method="post" enctype="multipart/form-data">
            <input type="hidden" name="<?php echo ini_get("session.upload_progress.name"); ?>" value="avatar" />
            <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>" required />
            <table>
                <label for="photo-file" title="Click to change your photograph." style="background-image: url('<?php echo htmlspecialchars($profile['photo']); ?>');"></label>
                <input id="photo-file"
                       type="file"
                       name="photo"
                       accept="image/gif, image/jpeg, image/png"
                       onchange="var e = document.createEvent('MouseEvents'); e.initMouseEvent('mousedown', true, true); this.form.querySelector('input[type=\'submit\']').dispatchEvent(e);" />
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <fieldset id="avatar" style="background-image: url('<?php echo htmlspecialchars($profile['avatar']); ?>');">
                            <legend>Avatar:</legend>

                            <label for="avatar-file" title="Click to change your avatar."></label>
                            <input id="avatar-file"
                                   type="file"
                                   name="avatar"
                                   accept="image/gif, image/jpeg, image/png"
                                   onchange="var e = document.createEvent('MouseEvents'); e.initMouseEvent('mousedown', true, true); this.form.querySelector('input[type=\'submit\']').dispatchEvent(e);" />
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2">
                        <fieldset>
                            <legend></legend>
                            <table>
                                <tr>
                                    <td>
                                        <label for="username">Username:</label>
                                    </td>
                                    <td>
                                        <input id="username"
                                               type="text"
                                               name="username"
                                               placeholder="<?php echo htmlspecialchars($profile['username']); ?>"
                                               value="<?php echo htmlspecialchars($profile['username']); ?>"
                                               pattern="^.{<?php echo $user->model->username_length_min; ?>,<?php echo $user->model->username_length_max; ?>}$"
                                               minlength="<?php echo $user->model->username_length_min; ?>"
                                               maxlength="<?php echo $user->model->username_length_max; ?>"
                                               required />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="status">Status:</label>
                                    </td>
                                    <td>
                                        <input id="status"
                                               type="text"
                                               name="status"
                                               placeholder="<?php echo $profile['status'] ? htmlspecialchars($profile['status']) : 'Hysterical, Surprised, Sad, Exhausted, Loved, Frightened, etc.'; ?>"
                                               value="<?php echo htmlspecialchars($profile['status']); ?>"
                                               pattern="^.{0,255}$"
                                               maxlength="255" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="location">Location:</label>
                                    </td>
                                    <td>
                                        <input id="location"
                                               type="text"
                                               name="location"
                                               placeholder="<?php echo htmlspecialchars($profile['location']); ?>"
                                               value="<?php echo htmlspecialchars($profile['location']); ?>"
                                               pattern="^.{0,255}$"
                                               maxlength="255" />
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <div class="button-wrap">
                                            <input type="submit" name="profile" value="Done" />
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </form>
    <?php }else{ ?>
        <div id="profile-wrap">
            <table>
                <label for="photo-file" title="Click to change your photograph." style="background-image: url('<?php echo htmlspecialchars($profile['photo']); ?>');"></label>
                <input id="photo-file" type="file" name="photo" accept="image/gif, image/jpeg, image/png" onchange="var e = document.createEvent('MouseEvents'); e.initMouseEvent('mousedown', true, true); document.querySelector('input[name=\'profile\']').dispatchEvent(e);" />
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <fieldset id="avatar" style="background-image: url('<?php echo htmlspecialchars($profile['avatar']); ?>');">
                            <legend>Avatar:</legend>

                            <label for="avatar-file" title="Click to change your avatar."></label>
                            <input id="avatar-file"
                                   type="file"
                                   name="avatar"
                                   accept="image/gif, image/jpeg, image/png"
                                   onchange="var e = document.createEvent('MouseEvents'); e.initMouseEvent('mousedown', true, true); this.form.querySelector('input[type=\'submit\']').dispatchEvent(e);" />
                        </fieldset>
                    </td>
                </tr>
                <tr>
                    <td rowspan="2">
                        <fieldset disabled>
                            <legend></legend>
                            <table>
                                <tr>
                                    <td>
                                        <label for="username">Username:</label>
                                    </td>
                                    <td>
                                        <input id="username"
                                               type="text"
                                               name="username"
                                               placeholder="<?php echo htmlspecialchars($profile['username']); ?>"
                                               value="<?php echo htmlspecialchars($profile['username']); ?>"
                                               pattern="^.{<?php echo $user->model->username_length_min; ?>,<?php echo $user->model->username_length_max; ?>}$"
                                               minlength="<?php echo $user->model->username_length_min; ?>"
                                               maxlength="<?php echo $user->model->username_length_max; ?>"
                                               required />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="status">Status:</label>
                                    </td>
                                    <td>
                                        <input id="status"
                                               type="text"
                                               name="status"
                                               placeholder="<?php echo $profile['status'] ? htmlspecialchars($profile['status']) : 'Hysterical, Surprised, Sad, Exhausted, Loved, Frightened, etc.'; ?>"
                                               value="<?php echo htmlspecialchars($profile['status']); ?>"
                                               pattern="^.{0,255}$"
                                               maxlength="255" />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <label for="location">Location:</label>
                                    </td>
                                    <td>
                                        <input id="location"
                                               type="text"
                                               name="location"
                                               placeholder="<?php echo htmlspecialchars($profile['location']); ?>"
                                               value="<?php echo htmlspecialchars($profile['location']); ?>"
                                               pattern="^.{0,255}$"
                                               maxlength="255" />
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <div class="button-wrap">
                                            <input type="submit" name="profile" value="Done" />
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>
    <?php } ?>
</article>
<?php if(stripos($_SERVER['HTTP_USER_AGENT'], 'MSIE') || stripos($_SERVER['HTTP_USER_AGENT'], 'Trident')){ ?>
    <!-- Best-attempt fixes for Internet Explorer. -->
    <style>
        #avatar {
            height: 11.3vw !important;
            top: 3px !important;
            -ms-transform: translate(-50%, 0) !important;
        }
    </style>
<?php } ?>

<article id="things">

</article>

<article id="highlights">
    <h1>Highlights</h1>
    <p>

    </p>
</article>

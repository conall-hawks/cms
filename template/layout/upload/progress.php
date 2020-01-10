<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<!DOCTYPE html>
<html>
    <body>
        <?php
            // Get upload info.
            $key  = ini_get('session.upload_progress.prefix').'upload';

            // Clear upload info of canceled uploads.
            if(!empty($_SESSION[$key]['cancel_upload']) && $_SESSION[$key]['cancel_upload'] === true){
                $_SESSION[$key] = [];
            }

            // Defaults.
            header('Refresh: 3');
            $elapsed        = 0;
            $progress       = 0;
            $bandwidth      = 0;
            $time_remaining = 0;
            $status         = 'Ready.';

            // Process upload info.
            $info = !empty($_SESSION[$key]) ? $_SESSION[$key] : false;
            if($info && !$info['done']){
                header('Refresh: 1');
                $progress       = number_format($info['bytes_processed'] / $info['content_length'] * 100);
                $elapsed        = time() - $info['start_time'];
                $bandwidth      = $info['bytes_processed'] / ($elapsed ? $elapsed : 1);
                function format_seconds($seconds){
                    $hours   = str_pad(number_format($seconds / 3600), 2, '0', STR_PAD_LEFT);
                    $minutes = str_pad(number_format(($seconds / 60) % 60), 2, '0', STR_PAD_LEFT);
                    $seconds = str_pad(number_format($seconds % 60), 2, '0', STR_PAD_LEFT);
                    return $hours.':'.$minutes.':'.$seconds;
                }
                $time_remaining = format_seconds($info['content_length'] / ($bandwidth ? $bandwidth : 1));
                $bandwidth      = format_bytes($bandwidth);
                $status         = 'Uploaded '.format_bytes($info['bytes_processed']).' of '.format_bytes($info['content_length']).'.';
            }
        ?>
        <section id="style" hidden>
            <?php $this->include_css($this->css['head']['variables'], true); ?>
            <?php $this->include_css($this->css['layout'], true); ?>
            <?php $this->include_css($this->css['main'], true); ?>
            <style>
                html {
                    background: none;
                    overflow: hidden;
                }
                #progress {
                    top: 0px;
                    height: 100px;
                }
                .button-wrap {
                    width: calc(100% - 8px) !important;
                }
                .button-wrap > div {
                    background: rgba(0, 255, 0, .125);
                    border-radius: 2px;
                    box-shadow: var(--box-shadow-dark), 0 0 2px rgba(0, 255, 0, .5) inset;
                    height: calc(100% - 8px);
                    left: 4px;
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    width: <?php echo $progress; ?>%;
                }
                #progress > pre {
                    background: none;
                    float: left;
                    margin: 0 0 0 4px;
                    padding: 0;
                }
                table {
                    width: calc(100% - 8px);
                }
            </style>
        </section>

        <article id="new-file">
            <table>
                <tr>
                    <td colspan="2">
                        <fieldset id="progress-fieldset">
                            <legend>Progress</legend>
                            <table>
                                <tr><td colspan="3"><div class="button-wrap"><div id="upload-progress-bar"></div></div></td></tr>
                                <tr>
                                    <td><p>Progress:</p></td>
                                    <td colspan="2"><span id="upload-progress-percent"><?php echo $progress ? $progress.' %' : 'N/A'; ?></span><br /></td>
                                </tr>
                                <tr>
                                    <td><p>Bandwidth:</p></td>
                                    <td colspan="2"><span id="upload-progress-bandwidth"><?php echo $bandwidth ? $bandwidth : '0 B'; ?>/sec</span><br /></td>
                                </tr>
                                <tr>
                                    <td><p>Time Remaining:</p></td>
                                    <td colspan="2"><span id="upload-progress-time-remaining"><?php echo $time_remaining ? $time_remaining : 'N/A'; ?></span><br /></td>
                                </tr>
                                <tr>
                                    <td><p>Status:&nbsp;</p></td>
                                    <td colspan="2"><span id="upload-progress-status"><?php echo $status; ?></span><br /></td>
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
        </article>
    </body>
</html>

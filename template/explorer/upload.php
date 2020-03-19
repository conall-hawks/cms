<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="explorer">
    <?php
        // Get privacy.
        $privacy = $uri->method ? $uri->method : 'public';

        // Get uploads.
        $upload = load_controller('upload');
        $upload->db();
        $inodes = $upload->db->upload_select(NULL, $privacy);

        // Filter reported.
        if(!empty($_SESSION['uploads']['reported'])){
            $length = count($inodes);
            for($i = 0; $i < $length; ++$i){
                if(in_array($inodes[$i]['id'], $_SESSION['uploads']['reported'])){
                    unset($inodes[$i]);
                }
            }
        }

        // Show table for uploads.
        if($inodes){ ?>
        <div class="scroll-head"></div>
        <div class="scroll-wrap">
            <table>
                <thead>
                    <tr>
                        <td></td>
                        <td><div>Name</div></td>
                        <td><div>Type</div></td>
                        <td><div>Size</div></td>
                        <td><div>Date Uploaded</div></td>
                        <td><div><?php echo $user->is_admin() ? 'Delete' : 'Report'; ?></div></td>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        /* Sort by name. */
                        #uasort($inodes, function($a, $b){
                        #    return strnatcasecmp($a['name'], $b['name']);
                        #});

                        /* Build entry for each file. */
                        foreach($inodes as $inode){

                            /* Resolve URL. */
                            $path = ltrim($inode['path'], '/');

                            /* Resolve path. */
                            $url = '/upload/'.$inode['privacy'].'/'.$inode['id'].'/'.$inode['name'];

                            /* Get extension. */
                            $ext = mb_strtolower(pathinfo($inode['name'], PATHINFO_EXTENSION));

                            /* Resolve path to icon. */
                            $icon = glob(ASSET.'/icon/'.$ext.'.*')[0] ?? NULL;
                            if(!empty($icon[0])) ASSET.'/icon/'.$icon;
                            else $icon = ASSET.'/icon/file.svg';

                            // Get name
                            $name = htmlspecialchars($inode['name']);

                            // Entry fades as reports go higher.
                            $opacity = max(number_format(1 - ((float)$inode['reported'] / 100), 2), 0.33);

                            /* Build HTML. */ ?>
                            <tr<?php if($opacity < 1) echo ' style="opacity: '.$opacity.';"'?>>
                                <td style="background-image: url(&quot;/<?php echo $icon; ?>&quot;);">
                                    <a href="<?php echo $url; ?>" target="_blank" title="Click to open: &quot;<?php echo $name; ?>&quot;."></a>
                                </td>
                                <td>
                                    <a href="<?php echo $url; ?>" target="_blank" title="Click to download: &quot;<?php echo $name; ?>&quot;." download>
                                        <div><?php echo $name; ?></div>
                                    </a>
                                </td>
                                <td title="MIME type: <?php $mime = mime($inode['name']); $mime = $mime === 'application/octet-stream' ? mime($path) : $mime; echo $mime; ?>">
                                    <?php echo $mime; ?>
                                </td>
                                <td><?php echo format_bytes(filesize($path)); ?></td>
                                <td><?php $mtime = filemtime($path) ; echo gmdate('F d', $mtime).'<sup>'.gmdate('S', $mtime).'</sup>, '.gmdate('o H:i T', $mtime); ?></td>
                                <td>
                                    <form class="ajax" method="post">
                                        <input type="hidden" name="<?php echo $this->form_token('id'); ?>" value="<?php echo $inode['id']; ?>" />
                                        <input type="submit" name="<?php echo $this->form_token('report'); ?>" value="X" title="<?php echo $user->is_admin() ? 'Delete' : 'Report'; ?> this upload; reported <?php echo (int)$inode['reported']; ?> times." />
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    ?>
                </tbody>
            </table>
            <style>
                /*.scroll-wrap thead tr div {
                    top: 25px;
                }*/
                #explorer td:last-of-type {
                    width: 48px;
                }
                #explorer td:last-of-type form {
                    float: left;
                    height: 100%;
                    width: 100%;
                }
                #explorer td:last-of-type input[type="submit"] {
                    border-radius: none;
                    padding: 0;
                    margin: 0;
                    position: relative;
                    top: 0;
                    left: -4px;
                    width: calc(100% + 8px);
                    height: 35px;
                    line-height: 35px;
                }
            </style>
        </div>
    <?php }else{ ?>
        <p>No uploads.</p>
    <?php } ?>
</article>

<?php if(empty($iframe)){ ?>
    <article id="explorer-nojs">
        <h1><?php echo mb_ucfirst($privacy); ?> Uploads</h1>
        <style>#explorer { display: block; } #explorer-nojs { display: none; }</style>
        <noscript>
            <iframe id="frame-list" src="/upload/list"></iframe>
            <style>#explorer { display: none; } #explorer-nojs { display: block; }</style>
        </noscript>
    </article>
<?php } ?>

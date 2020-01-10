<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="explorer">
    <?php $inodes = $profile->db->get_uploads($this->user['id']); if($inodes){ ?>
        <div class="scroll-head"></div>
        <div class="scroll-wrap">
            <table>
                <thead>
                    <tr>
                        <td></td>
                        <td><div>Name</div></td>
                        <td><div>Type</div></td>
                        <td><div>Size</div></td>
                        <td><div>Privacy</div></td>
                        <td><div>Date Uploaded</div></td>
                        <td></td>
                    </tr>
                </thead>

                <tbody>
                    <?php
                        /* Sort by name. */
                        uasort($inodes, function($a, $b){
                            return strnatcasecmp(pathinfo($a['path'], PATHINFO_FILENAME), pathinfo($b['path'], PATHINFO_FILENAME));
                        });

                        /* Erase duplicates. */
                        $length = count($inodes) - 1;
                        for($i = 0; $i < $length; ++$i){
                            $name_1 = pathinfo(current($inodes)['path'], PATHINFO_FILENAME);
                            $name_2 = pathinfo(next($inodes)['path'], PATHINFO_FILENAME);
                            if($name_1 === $name_2){
                                unset($inodes[key($inodes)]);
                            }
                        }

                        /* Build entry for each file. */
                        foreach($inodes as $inode){

                            /* Resolve URL. */
                            $path = ltrim($inode['path'], '/');

                            /* Resolve path. */
                            $url = '/profile/'.$this->user['username'].'/upload/'.$inode['name'];

                            /* Get extension. */
                            $ext = mb_strtolower(pathinfo($inode['path'], PATHINFO_EXTENSION));

                            /* Resolve path to icon. */
                            $icon = glob($ext.'.*');
                            if(!empty($icon[0])) ASSET.'/icon/'.$icon[0];
                            else $icon = ASSET.'/icon/file.svg';

                            ?>
                            <tr>
                                <td style="background-image: url(&quot;/<?php echo $icon; ?>&quot;);">
                                    <a href="<?php echo $url; ?>" target="_blank"></a>
                                </td>
                                <td>
                                    <a href="<?php echo $url; ?>" target="_blank">
                                        <?php echo htmlspecialchars($inode['name']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo mime($path); ?>
                                </td>
                                <td><?php echo format_bytes(filesize($path)); ?></td>
                                <td></td>
                                <td><?php $mtime = filemtime($path) ; echo gmdate('F d', $mtime).'<sup>'.gmdate('S', $mtime).'</sup>, '.gmdate('o H:i T', $mtime); ?></td>
                                <td>
                                    <form class="ajax" method="post">
                                        <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>" required />
                                        <input type="hidden" name="file" value="<?php echo htmlspecialchars($inode['name']); ?>" />
                                        <input type="submit" name="delete" value="X" title="Delete this upload." />
                                    </form>
                                </td>
                            </tr>
                        <?php }
                    ?>
                </tbody>
            </table>
            <style>
                #explorer td:last-of-type {
                    width: 28px;
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
        <h1>Storage</h1>
        <p>No uploads.</p>
    <?php } ?>
</article>

<!--
<article id="storage">
    <h1>Storage</h1>
    <style>#frame-list-wrap { display: none; }</style>
    <noscript>
        <iframe id="frame-list" src="/upload/list"></iframe>
        <style>#explorer { display: none; } #frame-list-wrap { display: block; }</style>
    </noscript>
</article>
-->

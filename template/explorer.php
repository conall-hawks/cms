<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<article id="explorer">
    <?php if(is_dir($this->explorer_path)){ ?>
        <div class="scroll-head"></div>
        <div class="scroll-wrap">
            <table>
                <thead>
                    <tr>
                        <td><div><?php $url = implode('/', array_slice(explode('/', $uri->path), 0, -1)); if($url){ ?>
                            <a class="ajax back" href="<?php echo $url; ?>" title="Go up a directory.">&larr;</a>
                        <?php } ?></div></td>
                        <td><div>Name</div></td>
                        <td title="MIME type."><div>Type</div></td>
                        <td><div>Size</div></td>
                        <td><div>Date Modified</div></td>
                    </tr>
                </thead>
                <tbody>
                    <?php $inodes = new FilesystemIterator($this->explorer_path); if(!iterator_count($inodes)){ ?>
                        <tr><td class="disabled" colspan="5">Directory is empty.</td></tr>
                    <?php }else{

                        /* Convert to associative array. */
                        $inodes = iterator_to_array($inodes);

                        /* Sort by name. */
                        uasort($inodes, function($a, $b){return strnatcasecmp($a->getFilename(), $b->getFilename());});

                        /* Erase duplicates. */
                        $length = count($inodes) - 1;
                        for($i = 0; $i < $length; ++$i){
                            $name_1 = pathinfo(current($inodes), PATHINFO_FILENAME);
                            $name_2 = pathinfo(next($inodes), PATHINFO_FILENAME);
                            if($name_1 === $name_2){
                                unset($inodes[key($inodes)]);
                            }
                        }

                        /* Sort by type. */
                        uasort($inodes, function($a, $b){
                            if($a->isDir() && !$b->isDir()) return -1;
                            if(!$a->isDir() && $b->isDir()) return 1;
                            return strnatcasecmp($a->getFilename(), $b->getFilename());
                        });

                        /* Build entry for each file. */
                        foreach($inodes as $inode){

                            /* Resolve name. */
                            $name = $inode->getBasename('.'.$inode->getExtension());

                            /* Resolve URL. */
                            $url = trim_prefix($inode->getPath().'/'.$name, ASSET.'/share/');

                            /* Get extension. */
                            $ext = mb_strtolower($inode->getExtension());

                            /* Resolve path to icon. */
                            $icon = $inode->isDir() ? 'dir' : $ext;
                            $icon = ASSET.'/icon/'.$icon;
                            $icon = glob($icon.'.*')[0] ?? ASSET.'/icon/file.svg';
                            if(!is_file($icon)) $icon = ASSET.'/icon/file.svg';
                            $icon = "/".$icon;

                            /* Resolve URL and host. */
                            if($ext === 'url'){
                                preg_match("((\w+:\/\/)[-a-zA-Z0-9:@;?&=\/%\+\.\*!'\(\),\$_\{\}\^~\[\]`#|]+)", file_get_contents($inode->getPathname()), $url);
                                $url  = $url[0] ?? '#';
                                $parsed = parse_url($url);
                            }

                            // Prepare for HTML output.
                            $url = htmlspecialchars($url);

                            ?>
                            <tr>
                                <!-- Icon -->
                                <td id="a<?php $id = uniqid(); echo $id; ?>">
                                    <style nonce="<?php echo $security->nonce(); ?>">
                                        #a<?php echo $id; ?> {
                                            background-image: url("<?php echo $icon; ?>");
                                        }
                                    </style>
                                    <?php if($ext === 'url'){ ?>
                                        <a href="<?php echo $url; ?>" target="_blank"></a>
                                    <?php }else{ ?>
                                        <a class="ajax" href="<?php echo $url; ?>"></a>
                                    <?php } ?>
                                </td>

                                <!-- Title -->
                                <?php if($ext === 'url'){ ?>
                                    <td title="<?php echo $url; ?>">
                                        <a href="<?php echo $url; ?>" target="_blank">
                                            <?php echo title($name); ?>
                                        </a>
                                    </td>
                                <?php }else{ ?>
                                    <td<?php if($inode->isFile()) echo ' title="'.$name.($ext ? '.'.$ext : '').'"'; ?>>
                                        <a class="ajax" href="<?php echo $url; ?>">
                                            <?php echo title($name); ?>
                                         </a>
                                    </td>
                                <?php } ?>

                                <!-- Type -->
                                <?php if($ext === 'url'){ ?>
                                    <td>link</td>
                                <?php }else{ ?>
                                    <td title="<?php $mime = mime($inode->getPathname()); echo $mime; ?>"><?php echo $mime; ?></td>
                                <?php } ?>

                                <!-- Size -->
                                <?php if($ext === 'url'){ ?>
                                    <td title="<?php $href = htmlspecialchars($parsed['scheme'].'://'.$parsed['host']); echo $href; ?>">
                                        <a href="<?php echo $href; ?>" target="_blank">
                                            <?php echo htmlspecialchars(trim_prefix($parsed['host'], 'www.')); ?>
                                        </a>
                                    </td>
                                <?php }else{ ?>
                                    <td>
                                        <?php
                                        if($inode->isDir()){
                                            $count = iterator_count(new FilesystemIterator($inode->getPathname()));
                                            echo $count.' file'.(in_array($count, [1, -1]) ? '' : 's');
                                        }else{
                                            $size = $inode->getSize();
                                            echo format_bytes($size);
                                        } ?>
                                    </td>
                                <?php } ?>

                                <!-- Date Modified -->
                                <td class="date-modifed"><?php $mtime = $inode->getMTime(); echo gmdate('F d', $mtime).'<sup>'.gmdate('S', $mtime).'</sup>, '.gmdate('o H:i T', $mtime); ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php }else{

        /* Locate file. */
        #$file = iglob($this->explorer_path.'*')[0] ?? NULL;
        $file = iglob($this->explorer_path)[0] ?? NULL;
        if(!$file) $file = iglob($this->explorer_path.'.*')[0] ?? NULL;

        /* If our path points to a file, try to view it. */
        if(is_file($file)){

            /* Get the file's contents. */
            $content = file_get_contents($file);

            /* Get the file type. */
            $type = pathinfo($file, PATHINFO_EXTENSION);

            /* A list of code files which will be rendered. */
            $code = [
                'asm', 'bat', 'c' , 'cpp', 'cs', 'css', 'html', 'js', 'json',
                'php', 'ps1', 'sh', 'sql', 'txt', 'vb', 'vbs'
            ];

            /* Rendering of code files. The in_array($uri->class, [*]) is for whitelisted controllers which are allowed to render PHP. */
            if(($type !== 'php' /*&& in_array($type, $code)*/) || ($type === 'php' && in_array($uri->class, ['code']))){

                /* Special rules for rendering PHP files. */
                if($type === 'php' && in_array($uri->class, ['code'])){

                    /* Remove opening PHP tag. */
                    $prefix = '<?php'.PHP_EOL;
                    if(substr($content, 0, strlen($prefix)) === $prefix){
                        $content = substr($content, strlen($prefix));
                    }
                }

                /* Get basename. */
                $basename = basename($file);

                /* Coalesce a class name for highlight.js. */
                $class = $type;
                if(mb_ereg('^.*httpd.*conf.*$', $basename)) $class = 'apache';
                if($class === 'asm') $class = 'x86asm';
                if($class === 'bat') $class = 'dos';

                ?>
                <h1>
                    <?php $url = implode('/', array_slice(explode('/', $uri->path), 0, -1)); if($url){ ?>
                        <a class="ajax back" href="<?php echo $url; ?>" title="Go up a directory.">&larr;</a>
                    <?php } ?>
                    <a href="/<?php echo $file; ?>" title="Click to open: <?php echo $basename; ?>"><?php echo title(@end(explode('/', $this->explorer_path))); ?></a>
                    <a href="/<?php echo $file; ?>" title="Click to download: <?php echo $basename; ?>" download>Download</a>
                </h1>
                <code class="<?php echo $class; ?>"><?php echo $content ? htmlspecialchars($content, ENT_SUBSTITUTE) : '<span class="gray">File is empty.</span>'; ?></code>
                <script nonce="<?php echo $security->nonce(); ?>">
                    var elements = document.querySelectorAll("code");
                    if(typeof window.hljs === "object" && window.hljsLanguagesLoading < 1){
                        for(var i = elements.length - 1; i >= 0; i--){
                            window.hljs.highlightBlock(elements[i]);
                        }
                    }
                </script>
            <?php
            }
        }else{ ?>
            <?php http_response_code(404); ?>
            <p>There's nothing here.</p>
        <?php }
    } ?>
</article>

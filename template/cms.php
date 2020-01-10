<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<?php if($user->is_admin()){ ?>
    <article id="cms">
        <h1>CMS</h1>
        <form id="cms-form">
            <table>
                <tr>
                    <td>
                        <fieldset id="cms-content-fieldset">
                            <legend id="cms-content-legend">Content</legend>
                            <textarea id="cms-content" autofocus>&lt;!DOCTYPE html&gt;
&lt;html&gt;
&lt;head&gt;
&lt;/head&gt;
&lt;body&gt;&lt;p&gt;Lorem ipsum dolor sit amet.&lt;/p&gt;
&lt;/body&gt;
&lt;/html&gt;
</textarea>
                        </fieldset>
                    </td>
                </tr>
            </table>
        </form>
        <script nonce="<?php echo $security->nonce(); ?>">
            jsAsync("https://cdn.jsdelivr.net/npm/tinymce@latest/tinymce.min.js", "/asset/misc/tinymce.js", function(){
                tinymce.init({
                    auto_focus: "cms-content",
                    branding: false,
                    content_css: [
                        //"https://cdn.jsdelivr.net/npm/tinymce@latest/skins/ui/oxide-dark/content.min.css",
                        "/asset/misc/tinymce/content.min.css",
                        "/asset/misc/tinymce.css"
                    ],
                    //plugins:      "advlist anchor autolink autoresize autosave bbcode charmap code codesample colorpicker contextmenu directionality fullpage fullscreen help hr image imagetools insertdatetime legacyoutput link lists media nonbreaking noneditable pagebreak paste preview print quickbars save searchreplace spellchecker tabfocus table template textcolor textpattern toc visualblocks visualchars wordcount",
                    //plugins:  "advlist autolink autoresize autosave bbcode charmap code codesample colorpicker contextmenu directionality fullpage fullscreen help hr image imagetools insertdatetime legacyoutput link lists media nonbreaking noneditable pagebreak paste preview print quickbars save searchreplace spellchecker tabfocus table textcolor textpattern visualblocks visualchars wordcount",
                    plugins:  "advlist autolink autoresize bbcode charmap code codesample colorpicker contextmenu directionality fullpage fullscreen help hr image imagetools insertdatetime legacyoutput link lists media nonbreaking noneditable pagebreak paste preview print quickbars save searchreplace spellchecker tabfocus table textcolor textpattern visualblocks visualchars wordcount",
                    selector: "#cms-content",
                    //skin_url: "https://cdn.jsdelivr.net/npm/tinymce@latest/skins/ui/oxide-dark",
                    skin_url: "/asset/misc/tinymce",
                    toolbar:  "undo redo styleselect bold italic alignleft aligncenter alignright bullist numlist outdent indent code",
                });
                document.getElementById("cms-content-legend").textContent = "";
            });
        </script>
    </article>
<?php } ?>

<?php foreach(/*$cms->model->get_posts()*/[] as $post){ ?>
    <article>
        <h1><?php echo $post->title; ?></h1>
        <p><?php echo $post->content; ?></p>
    </article>
<?php } ?>

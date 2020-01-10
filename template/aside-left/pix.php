<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<h1>Boards</h1>



<ul>
    <?php foreach($pix->model->get_boards(20) as $board){ ?>
        <li><a class="ajax" href="/pix/<?php echo urlencode($board['path']); ?>"><?php echo htmlspecialchars($board['title']); ?></a></li>
    <?php } ?>
</ul>

<form id="new-board-form" class="ajax" method="post">
    <fieldset>
        <legend>Goto Board</legend>
        <table>
            <tr>
                <td colspan="2">
                    <p class="input-text">/pix/<input type="search" name="<?php echo $this->form_token('board'); ?>" required /><span class="input-text-background"></span></p>
                </td>
            </tr>
            <tr>
                <td><div class="button-wrap"><input type="reset" /></div></td>
                <td><div class="button-wrap"><input type="submit" value="Go" /></div></td>
            </tr>
        </table>
    </fieldset>
</form>

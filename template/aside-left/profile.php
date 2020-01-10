<?php defined('ROOTPATH') || die('Denied access to: '.__FILE__); ?>
<?php $username_html = htmlspecialchars($profile->user['username']); ?>
<?php $username_href = rawurlencode($username_html); ?>
<h1>Sections</h1>
<ul>
    <?php if(mb_strtolower($user->username()) === mb_strtolower($profile->user['username'])){ ?>
        <li><a class="ajax" href="/profile">Profile</a></li>
        <li><a class="ajax" href="/profile/<?php echo $username_href; ?>/board">Board</a></li>
        <li><a class="ajax" href="/profile/<?php echo $username_href; ?>/upload">Uploads</a></li>
        <li><a class="ajax" href="/profile/<?php echo $username_href; ?>/setting">Settings</a></li>
    <?php }else{ ?>
        <li><a class="ajax" href="/profile/<?php echo $username_href; ?>">Profile</a></li>
        <li><a class="ajax" href="/profile/<?php echo $username_href; ?>/board">Board</a></li>
        <li><a class="ajax" href="/profile/<?php echo $username_href; ?>/upload">Uploads</a></li>
    <?php } ?>
</ul>

<form>
    <input type="hidden" name="csrf" value="<?php echo $_SESSION['csrf']; ?>" />
    <fieldset>
        <legend>Search</legend>
        <label for="search-profile">Search for</label>:<select id="search-profile-select">
            <option selected>User</option>
            <option>Page</option>
        </select>
        <input type="search" name="search-profile" id="search-profile" />
    </fieldset>
</form>

<style>
    #search-profile-select {
        float: none;
        margin-left: 0;
        padding-left: 0;
        width: 88px;
    }
    option {
        background: rgba(0, 0, 0, .9);
    }
</style>

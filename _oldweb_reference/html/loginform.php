
<form name="loginform"
      method="post"
      action="index.php"
      id="loginform"
      onsubmit="return md5form(this);">
    <label for="name">Login:</label>
    <input name="name" type="text" id="name" maxlength="10" size="10"><br />
    <label for="pass">Password:</label>
    <input name="pass" type="password" id="pass" maxlength="10" size="10"><br /><br />
    <input type="hidden" name="challenge" value="<?php echo $chall;  ?>" />
    <input type="submit" name="Submit" value="Log in!">
</form>
<span id="passchange"><a href="index.php?navi=pach&action=init">Change/Recover password</a></span><br />
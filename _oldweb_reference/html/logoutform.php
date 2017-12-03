
<div id='loginform'>
    <form name='logoutform' id='logoutform' method='get' action='index.php'>
        <label for='logout'>Logged: <b><?php echo $_SESSION["user"]; ?></b></label>
        <input type='submit' name='logout' value='logout'>
    </form>
</div>


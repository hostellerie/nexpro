<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head><title>Geeklog Installation: Database Setup</title></head>
<body bgcolor="#ffffff">
    <h1>Geeklog Database Settings (Step 2 of 2)</h1>
    <p>Now we are ready to add the necessary data structures to your Geeklog database.  If you are upgrading, please be sure to select the current version of your Geeklog database below.  If this is a new installation, just hit the 'Next' button. We hope that at this point you have already backed-up your existing database (if you have one).  If you haven't then <b>do so <i>before</i> clicking the 'Next' button below</b>.  You've been warned.</p>
    <form action="install.php" method="post">
    <input type="hidden" name="upgrade" value="{upgrade}">
    <table cellpadding="3" cellspacing="0" border="0" align="center">
        {UPGRADE_OPTIONS}
    </table>
    <input type="hidden" name="page" value="2">
    <input type="hidden" name="geeklog_path" value="{geeklog_path}">
    <input type="hidden" name="install_type" value="{install_type}">
    <p align="center"><input type="submit" name="action" value="&lt;&lt; Back">&nbsp;{NEXT_BUTTON}</p>
    </form>
</body>
</html>

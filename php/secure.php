<?php
session_start();
require_once("class.user.php");
if(!user::checkLogin()){
    header('Location:index.php');
}
/**
 * Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 08/12/2017
 * Time: 12:34
 */

?>

<html>
<head>
    <title>Secure</title>
</head>
<body>
<div class="content">
    <div class="static">
        <div></div>
        <div class="left">
            <h3>MATURA09</h3>
            <p style="font-size: 80%">An address book management system</p>
            <hr>
        </div>
        <div class="right">
        <strong>username:</strong> <?php echo $_SESSION[""]; ?>

        </div>
    </div>
    <table></table>
</div>
</body>

</html>


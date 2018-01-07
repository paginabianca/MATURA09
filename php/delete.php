<?php
 session_start();
 require_once ("class.user.php");
 if(!user::checkLogin()){
     header("Location:index.php");
 }
/**
 * Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 07/01/2018
 * Time: 12:16
 */

?>
<html>
<title>Secure</title>
<style>
    * {
        box-sizing: border-box;
    }

    body {
        display: flex;
        min-height: 100vh;
        flex-direction: row;
        margin: 0;
    }

    .col-1 {
        background: #acc7dc;
        flex: 1;
    }

    .col-2 {
        display: flex;
        flex-direction: column;
        flex: 5;
    }

    .content {
        display: flex;
        flex-direction: row;
    }

    .content > article {
        flex: 4;
        min-height: 60vh;
    }

    header {
        background: #d6dde3;
        height: 15vh;
    }

    header, article, nav {
        padding: 1em;
    }
</style>
<body>
<nav class="col-1">
    <h2> MATURA09</h2>
    <p style="font-size: 80%">Contact management system</p>
    Logged in as: <strong><?php echo $_SESSION["username"]; ?></strong>
    <hr>
    <p><a href="adduser.php">Add contact</a></p>
    <p><a href="delete.php">Delete all</a></p>
    <p><a href="logout.php">Logout</a></p>
</nav>
<div class="col-2">
    <header>
        <h3>Edit</h3>
    </header>
    <main class="content">
    </main>
</div>
</body>
</html>

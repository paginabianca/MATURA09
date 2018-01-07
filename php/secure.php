<?php
session_start();
require_once("class.user.php");
if (!user::checkLogin()) {
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
        <h3>Contacts</h3>
        <form method="post" action="signup.php?page=search">
            <input type="text" name="search" placeholder="Search...">
        </form>
    </header>
    <main class="content">
        <table border="1px" width="100%">
            <thead>
            <tr>
                <th><a href="secure.php?sort=c.name">Name</a></th>
                <th> <a href="secure.php?sort=c.surname">Surname</a></th>
                <th> <a href="secure.php?sort=c.tel">Phone</a></th>
                <th><a href="secure.php?sort=c.email">Email</a></th>
                <th> <a href="secure.php?sort=a.city">City</a></th>
                <th><a href="secure.php?sort=a.zip">ZIP</a></th>
                <th><a href="secure.php?sort=a.street">Street</a></th>
                <th><a href="secure.php?sort=a.nr">Nr.</a></th>
                <th><a href="secure.php?sort=a.land">Land</a></th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            </thead>

        </table>
    </main>
</div>
</body>
</html>


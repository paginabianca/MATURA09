<?php
session_start();
require_once("class.user.php");
require_once("class.dbaccess.php");
if (!user::checkLogin()) {
    header("Location:index.php");
}
error_reporting(0);
$id = $_GET["id"];
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
    <h2><a href="secure.php" style="text-decoration: none;color: black">MATURA09</a></h2>
    <p style="font-size: 80%">Contact management system</p>
    Logged in as: <strong><?php echo $_SESSION["username"]; ?></strong>
    <hr>
    <p><a href="adduser.php">Add contact</a></p>
    <p><a href="delete.php?id=all">Delete all</a></p>
    <p><a href="logout.php">Logout</a></p>
    <p><a href="log.php">Logbook</a></p>
</nav>
<div class="col-2">
    <header>
        <h3>Delete</h3>
    </header>
    <main class="content">
        <?php
        if (!isset($_GET["page"])) {

            ?>
            <p>
            <form>
                Do you really want to delete this contact:
                <strong> <?php echo dbaccess::getContactName($_GET["id"]); ?> </strong>
                <input type="button" value="yes"
                       onclick="window.location.href='delete.php?id=<?php echo $id ?>&page=yes'"/>
                <input type="button" value="no" onclick="window.location.href='secure.php'">
            </form>
            </p>

            <?php
        } else {
            if ($_GET["page"] == "yes") {
                $deleted = dbaccess::deleteThis($id);
            }
            switch ($deleted) {
                case 0:
                    echo "<p>Deleted <strong>" . $id . "</strong> successfully. Back to the <a href='secure.php'>contacts.</a></p>";
                    break;
                case 1:
                    echo "<p>Error <strong>" . $id . "</strong> can't delete contact with ID: <strong>" . $id . "</strong> 
                    because it does not exist. Back to the <a href='secure.php'>contact list.</a></p>";
            }
        }
        ?>

    </main>
</div>
</body>
</html>

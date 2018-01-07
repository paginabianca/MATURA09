<?php
session_start();
require_once("class.user.php");
if (!user::checkLogin()) {
    header('Location:index.php');
}
if($_GET["page"] == "add"){
    //TODO add user funcion here with the parameters passed through the post method
}
/**
 * Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 07/01/2018
 * Time: 12:47
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
        <h3>Add Contact</h3>
    </header>
    <main class="content">
        <form method="post" action="adduser.php?page=add">
            <table>
                <tr>
                    <td>Name:</td>
                    <td><input type="text" name="name"></td>
                </tr>
                <tr>
                    <td>Surname:</td>
                    <td><input type="text" name="surname"></td>
                </tr>
                <tr>
                    <td>Phone:</td>
                    <td><input type="number" name="tel"></td>
                </tr>
                <tr>
                    <td>Email:</td>
                    <td><input type="email" name="email"></td>
                </tr>
                <tr>
                    <td>City</td>
                    <td><input type="text" name="city"></td>
                </tr>
                <tr>
                    <td>ZIP:</td>
                    <td><input type="number" name="zip"></td>
                </tr>
                <tr>
                    <td>Street:</td>
                    <td><input type="text" name="street"></td>
                </tr>
                <tr>
                    <td>Nr.:</td>
                    <td><input type="number" name="nr"></td>
                </tr>
                <tr>
                    <td>Land:</td>
                    <td><input type="text" name="land"></td>
                </tr>
                <tr>
                    <td><input type="reset" name="Reset"></td>
                    <td><input type="submit" name="Add"></td>
                </tr>
            </table>
        </form>
    </main>
</div>
</body>
</html>

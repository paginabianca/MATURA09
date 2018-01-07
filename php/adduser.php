<?php
session_start();
require_once("class.dbaccess.php");
require_once("class.user.php");
if (!user::checkLogin()) {
    header('Location:index.php');
}
error_reporting(0);
$nameErr = $surnamenameErr = $telErr = $emailErr = $cityErr = $zipErr = $streetErr = $nrErr = $landErr = "";
$case = 0;
$valid = true;
if (!isset($_GET["page"])) {
    $case = -1;
} else {
    if ($_GET["page"] == "add") {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (empty($_POST["name"])) {
                $nameErrErr = "Name is required";
                $valid = false;
            } else {
                $name = test_input($_POST["name"]);
            }
            if (empty($_POST["surname"])) {
                $surnamenameErr = "Surname is required";
                $valid = false;
            } else {
                $surname = test_input($_POST["surname"]);
            }
            if (empty($_POST["tel"])) {
                $telErr = "Phone number is required";
                $valid = false;
            } else {
                $tel = test_input($_POST["tel"]);
            }
            if (empty($_POST["email"])) {
                $emailErr = "Email is required";
                $valid = false;
            } else {
                $email = test_input($_POST["email"]);
            }
            if (empty($_POST["city"])) {
                $cityErr = "City is required";
                $valid = false;
            } else {
                $city = test_input($_POST["city"]);
            }
            if (empty($_POST["zip"])) {
                $zipErr = "ZIP is required";
                $valid = false;
            } else {
                $zip = test_input($_POST["zip"]);
            }
            if (empty($_POST["street"])) {
                $streetErr = "Street is required";
                $valid = false;
            } else {
                $street = test_input($_POST["street"]);
            }
            if (empty($_POST["nr"])) {
                $nrErr = "Nr is required";
                $valid = false;
            } else {
                $nr = test_input($_POST["nr"]);
            }
            if (empty($_POST["land"])) {
                $landErr = "Land is required";
                $valid = false;
            } else {
                $land = test_input($_POST["land"]);
            }
        }
        //if all fields are filled in correctly then we try to add the new contact
        if ($valid) {
            $case = dbaccess::addContact($name, $surname, $tel, $email, $city, $zip, $street, $nr, $land);
        } else {
            $case = -1;
        }

    }

}
function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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

    .error {
        color: #F44336;
    }
</style>
<body>
<nav class="col-1">
    <h2><a href="secure.php" style="text-decoration: none;color: black">MATURA09</a></h2>
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
        <?php
        switch ($case) {
            case -1:
                ?>
                <form method="post" action="adduser.php?page=add">
                    <table>
                        <tr>
                            <td></td>
                            <td> <span class="error" style="font-size: 80%"> *required fields</span></td>
                        </tr>
                        <tr>
                            <td>Name:</td>
                            <td><input type="text" name="name"><span class="error">* <?php echo $nameErr; ?></span></td>
                        </tr>
                        <tr>
                            <td>Surname:</td>
                            <td><input type="text" name="surname"><span
                                        class="error">* <?php echo $surnamenameErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Phone:</td>
                            <td><input type="tel" name="tel"><span class="error">* <?php echo $telErr; ?></span></td>
                        </tr>
                        <tr>
                            <td>Email:</td>
                            <td><input type="email" name="email"><span class="error">* <?php echo $emailErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>City</td>
                            <td><input type="text" name="city"><span class="error">* <?php echo $cityErr; ?></span></td>
                        </tr>
                        <tr>
                            <td>ZIP:</td>
                            <td><input type="number" name="zip"><span class="error">* <?php echo $zipErr; ?></span></td>
                        </tr>
                        <tr>
                            <td>Street:</td>
                            <td><input type="text" name="street"><span class="error">* <?php echo $streetErr; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Nr.:</td>
                            <td><input type="number" name="nr"><span class="error">* <?php echo $nrErr; ?></span></td>
                        </tr>
                        <tr>
                            <td>Land:</td>
                            <td><input type="text" name="land"><span class="error">* <?php echo $landErr; ?></span></td>
                        </tr>
                        <tr>
                            <td><input type="reset" name="Reset"></td>
                            <td><input type="submit" name="Add"></td>
                        </tr>
                    </table>
                </form>
                <?php
                break;
            default:
                ?>
            <p>
                Contact was added successfully with the id:<strong><?php echo $case;?></strong>
                <br>
                <br>
                Add <a href="adduser.php"> another</a> or return to <a href="secure.php"> contacts list</a>.
            </p>
                <?php
                break;
        }
        ?>

    </main>
</div>
</body>
</html>

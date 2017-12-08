<?php
require_once("class.user.php");
/** Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 08/12/2017
 * Time: 12:34
 */ ?>

<html>

<head>

</head>
<body>
<h3>Sign up</h3>
<?php
if (!isset($_GET["page"])) {
    ?>
    <form action="signup.php?page=2" method="post">
        Username: <input type="text" name="user"/> <br>
        Password: <input type="password" name="pw"> <br>
        Repeat Password: <input type="password" name="pw2"> <br>
        <input type="submit" value="Sign up">
    </form>
    <?php
}
if (isset($_GET["page"])) {
    if ($_GET["page"] == "2") {
        $user = strtolower($_POST["user"]);
        $pw = md5($_POST["pw"]);
        $pw2 = md5($_POST["pw2"]);

        if ($pw != $pw2) {
            /*
             * check if the entered pws are the same
             * */
            echo "Passwords do not match! Take me <a href='signup.php'>back.</a>";
        } else {
            /*
             * check if username is already in use through static method authUsername()
             * */
            $userauth = user::authUsername($user);
            if ($userauth != 0) {
                echo "Username is already taken. Please choose a different username...<a href='signup.php'>take me back</a>";
            }
        }
    }
}
?>
</body>

</html>

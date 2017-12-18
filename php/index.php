<?php
/**
 * Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 07/12/2017
 * Time: 14:58
 */

session_start();
require_once("class.user.php");
$case = 1;
if (!isset($_SESSION['username']) and !isset($_GET["page"])) {
    $case = 1;
}
if ($_GET["page"] == "log") {
    $user = $_POST['class.user'];
    $password = $_POST['password'];
    $auth = user::authUser($user, $password);

    //correct login
    if ($auth == 0) {
        $_SESSION['username'] = $user;
    }
    $case = $auth;
}
?>
<html>
<head>
    <title>Login</title>
    <?php
    if ($case == 20) {
        ?>
        <meta http-equiv="refresh" content="3; URL=secure.php"/>
        <?php
    }
    ?>
</head>
<body>
<?php
switch ($case) {
    case 1:
        ?>
        Please log in: <br/>
        <form method="post" action="index.php?page=log">
            User: <input type="text" name="user"> <br>
            Password: <input type="password" name="password"><br>
            <input type="submit" value="log in">
        </form>
        <p><a href="signup.php">No account? Sign up now!</a></p>
        <p><a href="reset.php">Forgot username or password?</a></p>
        <?php
        break;
    case 0:
        ?>
        <strong>Succsess!</strong> Login correct. You will be forwarded to the next site...
        <?php
        break;
    case -1:
        ?>
        <strong>Error!</strong> User does not exist! Take me <a href="index.php">back...</a>
        <?php break;
    case -2:
        ?>
        <strong>Error!</strong> User does not exist. <a href="signup.php">Sign up...</a>
        <?php
        break;
    case -3:
        ?>
        <strong>Error!</strong> Database not found. Please make sure that your database is up and running. Take me <a
            href="index.php"> back...</a>
        <?php
        break;
}
?>

</body>
</html>



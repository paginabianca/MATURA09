<?php
/**
 * Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 07/12/2017
 * Time: 14:58
 */

session_start();
require_once ("class.user.php");
$case = 0;
if (!isset($_SESSION['username']) and !isset($_GET["page"])) {
    $case = 0;
}
if ($_GET["page"] == "log") {
    $user = $_POST['class.user'];
    $password = $_POST['password'];
    //correct login
    if ($user == "charlie" and $password == "plaplapla") {
        $_SESSION['username'] = $user;
        $case = 1;
    } //incorrect login
    else {
        $case = 2;
    }
}
?>
<html>
<head>
    <title>Login</title>
    <?php
    if ($case == 1) {
        ?>
        <meta http-equiv="refresh" content="3; URL=secure.php"/>
        <?php
    }
    ?>
</head>
<body>
<?php
switch ($case) {
    case 0:
        ?>
        Please log in: <br/>
        <form method="post" action="index.php?page=log">
            User: <input type="text" name="user"> <br>
            Password: <input type="password" name="password"><br>
            <input type="submit" value="log in">
        </form>
        <p><a href="signup.php">No account? Sign up now!</a></p>
        <?php
        break;
    case 1:
        ?>
        Login correct. You will be forwarded to the next site...
        <?php
        break;
    case 2:
        ?>
        Username or password incorrect. Take me <a href="index.php">back.</a>
        <?php
        break;
}
?>

</body>
</html>



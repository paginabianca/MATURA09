<?php
session_start();
require_once("class.user.php");
if(!user::checkLogin()){
    echo"You are not loged in! Why would you try to sign out!";
}else{
    session_destroy();
    echo "Logout successful. <a href='index.php'>Log back in</a>";
}

/**
 * Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 07/01/2018
 * Time: 12:33
 */

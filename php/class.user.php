<?php

/**
 * Created by PhpStorm.
 * User: Andreas Botzner
 * Date: 08/12/2017
 * Time: 13:15
 */
class user
{
    /**
     * @param $newuser username that has to be checked on availability
     * @return int 0 if username is available, 1 if username is taken
     *
     * checks if username is already in table users@matura09_db
     */
    public static function authUsername($newuser)
    {
        $ret = 0;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $sql = "SELECT uname FROM users WHERE uname = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
            } else {
                $stmt->bind_param('s', $newuser);
                $user = $newuser;
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $ret = $stmt->num_rows;
                }
            }
            $stmt->close();
        }
        $mysqli->close();
        return $ret;
    }

    /**
     * @param $newuser username for the new user
     * @param $pw password for the new user
     * @return int 0 if an error happens, userid if everything goes as planed
     */
    public static function addUser($newuser, $pw)
    {
        $ret = 0;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $sql = "INSERT INTO users(uname,upw)" .
                "VALUES(?,MD5(?))";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
            } else {
                $stmt->bind_param('ss', $newuser, $pw);
                $user = $newuser;
                $stmt->execute();
                if ($mysqli->errno) {
                    echo "DB-ERROR:" . $mysqli->error . " ERRNR:" . $mysqli->errno;
                } else {
                    $ret = $stmt->insert_id;
                }
            }
            $stmt->close();
        }
        $mysqli->close();
        return $ret;
    }

    public static function authUser($uname,$pw){

    }
}
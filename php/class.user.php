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
     * was authUsername
     */
    public static function checkUsername($newuser)
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

    /**
     * @param $uname login name
     * @param $pw password for the corrisponding user
     * @return 0 if authentication was correct
     *  -1 if pws do not match
     *  -2 if user does not exist
     *  -3 if there is a internal error
     */
    public static function authUser($uname, $pw)
    {
        $ret = 0;
        $pwdb = null;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
            error_log("errlog test", "Error");
            $ret = -3;
        } else {
            //check if user exists
            $exists = 0;
            $sql = "SELECT uname FROM users WHERE uname = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB user error:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
            } else {
                $stmt->bind_param('s', $uname);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $exists = $stmt->num_rows;
                }
            }

            //user exists
            if ($exists) {
                $sql = "SELECT upw FROM users WHERE uname = ?";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    echo "<strong>DB pw error:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
                } else {
                    $stmt->bind_param("s", $uname);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($pwdb);
                    $stmt->fetch();
                    //passwords mach
                    if (md5($pw) == $pwdb) {
                        $ret = 0;
                    } else {
                        $ret = -1;
                    }
                }
            } else {
                $ret = -2;
            }
        }
        $stmt->close();
        $mysqli->close();
        return $ret;

        return $ret;
    }

    public static function checkLogin()
    {
        if (empty($_SESSION["username"])) {
            return false;
        }
        return true;
    }
}
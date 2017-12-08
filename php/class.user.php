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
        echo $newuser . "<br>";
        $ret = 0;
        $mysqli = @new \mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $sql = "SELECT uname FROM users WHERE uname ='user'";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
            } else {
                echo "bind param<br>";
               // $stmt->bind_param('s', $newuser);
                $user = $newuser;
                echo "execute<br>";
                $stmt->execute();
                echo "number of datasets: ".$stmt->num_rows;
                if ($stmt->num_rows > 0) {
                    echo "number of datasets: ".$stmt->num_rows;
                    $ret = $stmt->num_rows;
                }
            }
            $stmt->close();
        }
        $mysqli->close();
        return $ret;
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: andreas
 * Date: 20/12/17
 * Time: 08:16
 */

class dbaccess
{
    /**
     * @return the index of the contact
     */
    public static function getContacts($sort, $search)
    {
        if ($search == "") {
            $search = "%";
        } else {
            $search = "%" . $search . "%";
        }
        $ret = 0;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error()
                . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
            $mysqli->autocommit(false);
            $sql = "SELECT c.id ,c.name, c.surname , c.tel, c.email, a.city, a.zip, a.street, a.nr, a.land
            FROM contacts c , adr a 
            WHERE (c.name LIKE ? AND  c.adr = a.id) OR 
                  (c.surname LIKE ? AND  c.adr = a.id) OR 
                  (c.tel LIKE ? AND  c.adr = a.id) OR 
                  (c.email LIKE ? AND c.adr = a.id) OR 
                  (a.city LIKE ? AND  c.adr = a.id)
            OR (a.zip LIKE ? AND  c.adr = a.id) 
            OR (a.street LIKE ? AND  c.adr = a.id) OR (a.nr LIKE ? AND  c.adr = a.id) OR (a.land LIKE ? AND  c.adr = a.id)             
            ORDER  BY ? ASC";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error: </strong>" . $mysqli->error . " <br><strong> errornr: </strong>" . $mysqli->errno;
            } else {
                $stmt->bind_param("sssssssssi", $search, $search, $search, $search, $search, $search, $search, $search, $search, $sort);
                $stmt->execute();
                $stmt->store_result();
                $ret = $stmt->num_rows();
                $stmt->bind_result($id, $name, $surname, $tel, $email, $city, $zip, $street, $nr, $land);
                //print out the contacts and edit links
                while ($stmt->fetch()) {
                    echo "<tr><td>$name</td>
                            <td>$surname</td>
                            <td>$tel</td>
                            <td>$email</td>
                            <td>$city</td>
                            <td>$zip</td>
                            <td>$street</td>
                            <td>$nr</td>
                            <td>$land</td>
                            <td><a href='edit.php?id=$id'>edit</a></td>
                            <td><a href='delete.php?id=$id'>delete</td>";
                }
                $stmt->close();
            }
            $mysqli->commit();
        }
        $mysqli->close();
        return $ret;
    }

    /**
     * @param $name
     * @param $surname
     * @param $tel
     * @param $email
     * @param $city
     * @param $zip
     * @param $street
     * @param $nr
     * @param $land
     * @return int 0 if everything went well
     *             -1 not able to add address
     */
    public static function addContact($name, $surname, $tel, $email, $city, $zip, $street, $nr, $land)
    {
        $ret = 2;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error()
                . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_WRITE);
            $mysqli->autocommit(false);
            //add the address
            $sql = "INSERT INTO adr(city, zip, street,nr,land) VALUES (?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB adr error: </strong>" . $mysqli->error . " <br><strong> errornr: </strong>" . $mysqli->errno;
            } else {
                $stmt->bind_param("sisis", $city, $zip, $street, $nr, $land);
                $stmt->execute();
                $id = $mysqli->insert_id;
                $rows = $stmt->affected_rows;
                // echo "Number of affected rows: ". $rows;
            }
            $stmt->close();
            if ($rows > 0) {
                //add the contact
                $sql = "INSERT INTO contacts(name,surname,tel,email,adr) VALUES (?,?,?,?,?)";
                $stmt = $mysqli->prepare($sql);
                if (!$stmt) {
                    echo "<strong>DB contact error: </strong>" . $mysqli->error . " <br><strong> errornr: </strong>" . $mysqli->errno;
                } else {
                    $stmt->bind_param("ssisi", $name, $surname, $tel, $email, $id);
                    $stmt->execute();
                    $ret = $mysqli->insert_id;
                    $stmt->close();
                }
            }
            $mysqli->query("INSERT INTO log(user,type) VALUES ('" . $_SESSION["username"] . "','add')");
            $mysqli->commit();
        }
        $mysqli->close();
        return $ret;
    }

    /**
     * @param $id id of the contact to delete or all
     * @return int  0 if deletet
     *              -1 if the contact doesn't exist
     *
     */
    public static function deleteThis($id)
    {
        $ret = 0;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->autocommit(false);
            $mysqli->query("START TRANSACTION");
            if ($id == "all") {
                $sql = "SELECT * FROM contacts FOR UPDATE ";
            } else {
                $sql = "SELECT FROM contacts WHERE id=?";
            }
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error while creating delete lock statement:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
                $stmt->close();
                $mysqli->rollback();
            } else {
                if ($id != "all") {
                    $stmt->bind_param("i", $id);
                }
                $stmt->execute();
                if ($mysqli->errno) {
                    echo "DB error while locking the contact that was about to be deleted:" . $mysqli->error . " ERRNR:" . $mysqli->errno;
                    $stmt->close();
                    $mysqli->rollback();
                } else {
                    if ($stmt->affected_rows = 0) {
                        $ret = -1;
                        $stmt->close();
                        $mysqli->rollback();
                    } else {
                        if ($id == "all") {
                            $sql = "DELETE * FROM contacts ";
                        } else {
                            $sql = "DELETE FROM contacts WHERE id=?";
                        }
                        $stmt = $mysqli->prepare($sql);
                        if (!$stmt) {
                            echo "<strong>DB error while creating delete statement:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
                            $stmt->close();
                            $mysqli->rollback();
                        } else {
                            if ($id != "all") {
                                $stmt->bind_param('i', $id);
                            }
                            $stmt->execute();
                            if ($mysqli->errno) {
                                echo "DB-ERROR:" . $mysqli->error . " ERRNR:" . $mysqli->errno;
                                $stmt->close();
                                $mysqli->rollback();
                            } else {
                                $stmt->close();
                                $mysqli->query("INSERT INTO log(user,type) VALUES ('" . $_SESSION["username"] . "','delete')");
                                $mysqli->commit();
                            }
                        }
                    }
                }
            }
        }
        $mysqli->close();
        return $ret;
    }

    /**
     * @param $id all other @params are the new values for the contact this id
     * @param $name
     * @param $surname
     * @param $tel
     * @param $email
     * @param $city
     * @param $zip
     * @param $street
     * @param $nr
     * @param $land
     * @return int 0 if everything worked. Otherwise a error msg will be displayed
     */
    public static function editThis($id, $name, $surname, $tel, $email, $city, $zip, $street, $nr, $land)
    {
        $ret = 0;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->autocommit(false);
            //setting the isolation level to READ COMMITTED
            $mysqli->query("SET SESSION TRANSACTION ISOLATION LEVEL READ COMMITTED");
            //starting transaction
            $mysqli->query("START TRANSACTION");
            //lock the contact that is about to be edited
            $sql = "SELECT id FROM contacts WHERE id = ? FOR UPDATE ";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB ERROR while creating the statement to lock a contact: </strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
                $stmt->close();
                $mysqli->rollback();
            } else {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->store_result();
                if ($mysqli->errno) {
                    echo "DB ERROR while trying to lock contact id: <strong>" . $id . "</strong> :" . $mysqli->error . " ERRNR:" . $mysqli->errno;
                    $stmt->close();
                    $mysqli->rollback();
                } else {
                    if ($stmt->affected_rows > 0) {
                        $stmt->close();
                        $sql = "UPDATE contacts SET name=?, surname=?, tel=?,email=? WHERE id = ?";
                        $stmt = $mysqli->prepare($sql);
                        if (!$stmt) {
                            echo "<strong>DB ERROR while cerating the statement to update a contact: </strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
                            $stmt->close();
                            $mysqli->rollback();
                        } else {
                            $stmt->bind_param("ssisi", $name, $surname, $tel, $email, $id);
                            $stmt->execute();
                            if ($mysqli->errno) {
                                echo "DB ERROR while updating the contact: " . $mysqli->error . " ERRNR:" . $mysqli->errno;
                                $stmt->close();
                                $mysqli->rollback();
                            } else {
                                //locking the address
                                $stmt->close();
                                //get the adr id of the contact
                                $sql = "SELECT adr FROM contacts WHERE contacts.id = ? FOR UPDATE ";
                                $stmt = $mysqli->prepare($sql);
                                $adr = null;
                                if (!$stmt) {
                                    echo "<strong>DB get adr error:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
                                    $stmt->close();
                                    $mysqli->rollback();
                                } else {
                                    $stmt->bind_param("i", $id);
                                    $stmt->execute();
                                    if ($mysqli->errno) {
                                        echo "DB ERROR while locking the address: " . $mysqli->error . " ERRNR:" . $mysqli->errno;
                                        $stmt->close();
                                        $mysqli->rollback();
                                    } else {
                                        $stmt->store_result();
                                        $stmt->bind_result($adr);
                                        $stmt->fetch();
                                        $val = $stmt->affected_rows;
                                        $stmt->close();
                                    }
                                }
                                //update address
                                if ($val) {
                                    $sql = "UPDATE adr SET city=?, zip=?, street=?,nr=?,land=? WHERE id = ?";
                                    $stmt = $mysqli->prepare($sql);
                                    if (!$stmt) {
                                        echo "<strong>DB ERROR while creating a statement to update the address:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
                                        $stmt->close();
                                        $mysqli->rollback();
                                    } else {
                                        $stmt->bind_param("sisisi", $city, $zip, $street, $nr, $land, $adr);
                                        $stmt->execute();
                                        if ($mysqli->errno) {
                                            echo "<strong>DB ERRROR while updating the address:</strong>" . $mysqli->error . " <br><strong>nr.:</strong>" . $mysqli->errno;
                                            $stmt->close();
                                            $mysqli->rollback();
                                        } else {
                                            $stmt->close();
                                            //if EVERYTHING went well a log is created and the actions get committed to the database
                                            $mysqli->query("INSERT INTO log(user,type) VALUES ('" . $_SESSION["username"] . "','edit')");
                                            $mysqli->commit();
                                        }
                                    }

                                }
                            }
                        }
                    } else {
                        $stmt->close();
                        $mysqli->rollback();
                    }
                }
            }
        }
        $mysqli->close();
        return $ret;
    }

    /**
     * @param $id of the contacts name that is asked
     * @return int|string -1 if an error occurred
     *                     name of the contact in form "name surname"
     */
    public static function getContactName($id)
    {
        $ret = -1;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error()
                . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
            $mysqli->autocommit(false);
            $sql = "SELECT c.name, c.surname
            FROM contacts c
            WHERE c.id = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error: </strong>" . $mysqli->error . " <br><strong> errornr: </strong>" . $mysqli->errno;
            } else {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->store_result();
                $ret = $stmt->num_rows();
                $stmt->bind_result($name, $surname);
                //print out the contacts and edit links
                while ($stmt->fetch()) {
                    $ret = $name . " " . $surname . " ";
                }
                $stmt->close();
            }
            $mysqli->commit();
        }
        $mysqli->close();
        return $ret;
    }

    /**
     * @param $id
     * @return array|int    -1 if a error happens while creating the statement
     *                      -2 if a error happens while executing the statement
     *                      0  if contact with the given id does not exit
     *                      array with the contacts metadata
     */
    public static function getContact($id)
    {
        $ret = null;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error()
                . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
            $ret = -1;
        } else {
            $mysqli->autocommit(false);
            $mysqli->query("START TRANSACTION");
            $mysqli->query("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
            $sql = "SELECT c.id ,c.name, c.surname , c.tel, c.email, a.city, a.zip, a.street, a.nr, a.land
            FROM contacts c , adr a 
            WHERE c.adr = a.id AND c.id = ?";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error: </strong>" . $mysqli->error . " <br><strong> errornr: </strong>" . $mysqli->errno;
                $ret = -2;
            } else {
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->affected_rows > 0) {
                    $stmt->bind_result($id, $name, $surname, $tel, $email, $city, $zip, $street, $nr, $land);
                    //print out the contacts and edit links
                    while ($stmt->fetch()) {
                        $ret = array(
                            "name" => $name,
                            "surname" => $surname,
                            "tel" => $tel,
                            "email" => $email,
                            "city" => $city,
                            "zip" => $zip,
                            "street" => $street,
                            "nr" => $nr,
                            "land" => $land
                        );
                    }
                } else {
                    $ret = $stmt->affected_rows;
                }

                $stmt->close();
            }
            $mysqli->commit();
        }
        $mysqli->close();
        return $ret;
    }

    public static function getLog()
    {
        $ret = 0;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error()
                . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
            $mysqli->autocommit(false);
            $sql = "SELECT l.time, l.user, l.type
            FROM log l             
            ORDER  BY 1 ASC";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error: </strong>" . $mysqli->error . " <br><strong> errornr: </strong>" . $mysqli->errno;
            } else {
                $stmt->execute();
                $stmt->store_result();
                $ret = $stmt->num_rows();
                $stmt->bind_result($time, $user, $type);
                //print out the contacts and edit links
                while ($stmt->fetch()) {
                    echo "<tr><td>$time</td>
                            <td>$user</td>
                            <td>$type</td>";
                }
                $stmt->close();
            }
            $mysqli->query("INSERT INTO log(user,type) VALUES ('" . $_SESSION["username"] . "','log')");
            $mysqli->commit();
        }
        $mysqli->close();
        return $ret;
    }
}

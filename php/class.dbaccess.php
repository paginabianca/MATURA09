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
     * @return the number of contacts
     */
    public function numberContacts()
    {
        $ret = 0;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "mature09_db");
        if (mysqli_connect_errno()) {
            "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
            //setting isolation level to serializable to read from a snapshot of the db
            $mysqli->query("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
            $result = $mysqli->query("SELECT SUM(id) FROM contacts", MYSQLI_STORE_RESULT);
            $ret = $result->fetch_all(MYSQL_ASSOC);
            var_dump($ret);
        }
        $mysqli->commit();
        //todo fix return type to int not an array
        return ret;
    }

    public function get15($number, $offset)
    {
        $ret = null;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error() . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
            //setting isolation level to serializable to read from a snapshot of the db
            $mysqli->query("SET SESSION TRANSACTION ISOLATION LEVEL REPEATABLE READ");
            $sql = "SELECT c.vmane , c.name, c.tnr, c.email, o.city, o.zip, a.adr, a.hnr,a.land 
                    FROM contacts c, ort o , addresses a
                    ORDER BY 1, 2
                    ROWS ? to ?
                    ";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error:</strong> " . $mysqli->error . " <br><strong>nr.:</strong> " . $mysqli->errno;
            } else {
                $stmt->bind_param("ii", $offset, $number);
                $stmt->execute();
                $stmt->store_result();
                $stmt->bind_result($vname, $name, $tnr, $email, $ort);//todo the rest of the attributes
                $stmt->fetch();
                //todo create database and check return type of this method
                //todo Also check how oto create a button to add more results to the list.
                //todo add a php file to the project that displays the datasets
                //todo add a edit button to every dataset
                //todo create a php file that enabables the user to edit a specific dataset (only one dataset at the time)
            }
            $mysqli->commit();
        }

        return $ret;
    }

    public function getContacts($sort)
    {
        $ret = null;
        $mysqli = @new mysqli("localhost", "root", "masterkey", "matura09_db");
        if (mysqli_connect_errno()) {
            echo "<strong>DB connection error: </strong>" . mysqli_connect_error()
                . " <br><strong>errornr: </strong>" . mysqli_connect_errno();
        } else {
            $mysqli->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
            $mysqli->autocommit(false);
            $sql = "SELECT c.id c.name, c.surname , c.tel, c.email, a.city, a.zip, a.street, a.nr, a.land
            FROM contacts c , adr a 
            ORDER  BY ? DESC";
            $stmt = $mysqli->prepare($sql);
            if (!$stmt) {
                echo "<strong>DB error: </strong>" . $mysqli->error() . " <br><strong> errornr: </strong>" . $mysqli->errno();
            } else {
                $stmt->bind_param("s", $sort);
                $stmt->execute();
                $stmt->store_result();
                $ret = $stmt->num_rows();
                $stmt->bind_result($id ,$name,$surname,$tel,$email,$city,$zip,$street,$nr,$land);
                //print out the contacts and edit links
                while ($stmt->fetch()){
                    echo "<tr><td>$name</td>
                            <td>$surname</td>
                            <td>$tel</td>
                            <td>$email</td>
                            <td>$city</td>
                            <td>$zip</td>
                            <td>$street</td>
                            <td>$nr</td>
                            <td>$land</td>
                            <td><a href='edit.php?name=$name&surname=$surname&tel=$tel&email=$email&city=$city&zip=$zip&street=$street&nr=$nr&land=$land'></a>edit</td>
                            <td><a href='delete.php?id=$id'>delete</td>";
                }
                $stmt->close();
            }
            $mysqli->commit();
            $mysqli->close();

        }
        return ret;
    }

}
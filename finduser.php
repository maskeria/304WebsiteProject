<?php

include "include/dbConfig.php";
//connect to database
$connection = mysqli_connect($host, $user, $pass, $database);

//handle connection errors
$error = mysqli_connect_error();
if($error != null)
{
  $output = "<p>Unable to connect to database!</p>";
  exit($output);
}
else
{
    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = "";
        $firstname = "";
        $lastname = "";
        $email = "";
        $userID = "";
        $type = "";
        $image = "";

        if(!empty($_POST["username"])) {
            $username = $_POST["username"];
        }
        //Execute sql
        $sql = "SELECT * FROM users WHERE username = \"$username\";";

        $results = mysqli_query($connection, $sql);

        if (mysqli_num_rows($results) == 0) {
            echo "<h1>Username is invalid</h1>";
        } else {
            //process results
            $found = false;
            while ($row = mysqli_fetch_assoc($results)) {
                //echo $row['username'] . " " . $row['firstName'] . " " . $row['lastName'] . " " . $row['email'] . " " . $row['password'] . "<br/>";
                $firstname = $row["firstName"];
                $lastname = $row["lastName"];
                $email = $row["email"];
                $userID = $row["userID"];
            }

            $sql = "SELECT contentType, image FROM userImages WHERE userID=?";
            $stmt = mysqli_stmt_init($connection);
            mysqli_stmt_prepare($stmt, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userID);
            $result = mysqli_stmt_execute($stmt) or die(mysqli_stmt_errno($stmt));
            mysqli_stmt_bind_result($stmt, $type, $image);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

        echo "<fieldset>";
        echo "<legend>User: $username</legend>";
        echo "<table>";
        echo "<tbody>";
        echo "<tr><td>First Name:</td> <td>$firstname</td></tr>";
        echo "<tr><td>Last Name:</td> <td>$lastname</td></tr>";
        echo "<tr><td>Email:</td> <td>$email</td></tr>";
        echo "<tr><td>userID:</td> <td>$userID</td></tr>";
        echo "</tbody>";
        echo "</table>";
        echo "</fieldset>";
        echo '<img src="data:image/'.$type.';base64,'.base64_encode($image).'"/>';
    }

    //free resources and close connection
    mysqli_free_result($results);
    mysqli_close($connection);
}

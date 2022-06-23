<?php

session_start();

if(isset($_SESSION["username"])){
    header('Location: home.php');
    exit();
}

include 'include/dbConfig.php';
//make database connection
$connection = mysqli_connect($host, $user, $pass, $database);

//handle errors
$error = mysqli_connect_error();
if ($error != null) {
    $output = "<p>Unable to connect to database!</p>";
    exit($output);
} else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["username"] && !empty($_POST["password"]))) {
            $username = $_POST["username"];
            $password = $_POST["password"];
        }

        //Execute sql
        $sql = "SELECT * FROM users WHERE username = \"$username\";";

        $results = mysqli_query($connection, $sql);

        if (mysqli_num_rows($results) == 0) {
            echo "<h1>Username or password are invalid</h1>";
        } else {
            //process results
            $found = false;
            while ($row = mysqli_fetch_assoc($results)) {
                //echo $row['username'] . " " . $row['firstName'] . " " . $row['lastName'] . " " . $row['email'] . " " . $row['password'] . "<br/>";
                if($row["password"] == md5($password)){
                    $found = true;
                    echo "<h1>User ".$username." has a valid account</h1>";
                    //User is valid create session superglobal
                    $_SESSION["username"] = $username;
                    header('Location: home.php');
                    exit();
                }
            }
            if(!$found) {
                echo "<h1>Username or password are invalid</h1>";
                header('Location: login.php');
            }
        }
    }

    else if ($_SERVER["REQUEST_METHOD"] == "GET") {
        header('Location: login.php');
    }
    //free resources and close connection
    mysqli_free_result($results);
    mysqli_close($connection);
}

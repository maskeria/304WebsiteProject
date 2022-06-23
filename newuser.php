<?php

include "include/dbConfig.php";

//Connect to db
$connection = mysqli_connect($host, $user, $pass, $database);

//Handle connection errors
$error = mysqli_connect_error();
if ($error != null) {
    $output = "<p>Unable to connect to database!</p>";
    exit($output);
} else {
    $firstname = "";
    $lastname = "";
    $username  = "";
    $email = "";
    $password = "";
    $userID = "";
    $target_dir = "uploads/";
    $target_file = "";
    $imageFileType = "";
    $uploadOk = 1;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_POST["firstname"]) && !empty($_POST["lastname"]) && !empty($_POST["username"]) && !empty($_POST["email"]) && !empty($_POST["password"])) {
            $firstname = $_POST["firstname"];
            $lastname = $_POST["lastname"];
            $username = $_POST["username"];
            $email = $_POST["email"];
            $password = $_POST["password"];
        }

        //Image upload
        $target_file = $target_dir . basename($_FILES["userImage"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        // Check if image file is a actual image or fake image
        if (isset($_POST["submit"])) {
            $check = getimagesize($_FILES["userImage"]["tmp_name"]);
            if ($check !== false) {
                echo "File is an image - " . $check["mime"] . ".";
                $uploadOk = 1;
            } else {
                echo "File is not an image.";
                $uploadOk = 0;
            }
        }
        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["userImage"]["size"] > 100000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "gif") {
            echo "Sorry, only JPG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($_FILES["userImage"]["tmp_name"], $target_file)) {
                echo "The file " . htmlspecialchars(basename($_FILES["userImage"]["name"])) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }


        $users = array();
        //Execute sql query
        $sql = "SELECT * FROM users;";

        $results = mysqli_query($connection, $sql);

        //process results
        while ($row = mysqli_fetch_assoc($results)) {
            $currentUser = $row["username"];
            $users["$currentUser"] = array(
                "firstname" => $row["firstName"],
                "lastname" => $row["lastName"],
                "email" => $row["email"],
                "password" => $row["password"]
            );
        }
        //print_r($users);

        if (isset($users["$username"]) || (isset($users["$username"]["email"]) && strcmp($users["$username"]["email"], $email) == 0)) {
            //username or email already exists
            echo "<h1>Username or email already exists</h1>";
        } else {
            //insert user into db
            $password = md5($password);
            $insert = "INSERT INTO users (firstName, lastName, username, email, password) VALUES (\"$firstname\", \"$lastname\", \"$username\", \"$email\", \"$password\"); ";

            if (mysqli_query($connection, $insert)) {
                $count = mysqli_affected_rows($connection);
                echo "<p>Account has been created for " . $firstname . "</p>";
            }

            $sql = "SELECT userID FROM users WHERE username = \"$username\"";
            $results = mysqli_query($connection, $sql);

            //process results
            while($row = mysqli_fetch_assoc($results)) {
                $userID = $row["userID"];
            }
            echo "the user Id is ".$userID;

            //store file content in memory for upload
            $imagedata = file_get_contents($target_file);
            //create prepared statement
            $sql = "INSERT INTO userImages (userID, contentType, image) VALUES(?, ?, ?)";
            //init prepared statement object
            $stmt = mysqli_stmt_init($connection);
            //register the query
            mysqli_stmt_prepare($stmt, $sql);

            $null = NULL;
            mysqli_stmt_bind_param($stmt, "isb", $userID, $imageFileType, $null);
            mysqli_stmt_send_long_data($stmt, 2, $imagedata);
            $result = mysqli_stmt_execute($stmt) or die(mysqli_stmt_error($stmt));

            mysqli_stmt_close($stmt);
        }
    }
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        echo "<p>Bad Data</p>";
    }

    //free resources and close connection
    mysqli_free_result($results);
    mysqli_close($connection);
}

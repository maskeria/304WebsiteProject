<?php
    session_start();
    print_r($_SESSION);
    if (isset($_SESSION["username"])) {
        header('Location: home.php');
    } else {
        header('Location: login.html');
    }
?>
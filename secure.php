<?php
    session_start();

    if(isset($_SESSION['username'])) {
        echo "<h1>This is really secure data</h1>
              <p><a href=\"logout.php\">Logout</a></p>";

    } else {
        echo "<h1>Sorry, only users can access this page</h1>
              <p><a href=\"login.php\">Login</a></p>";
    }
        
?>
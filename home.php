<?php
    session_start();

    if(isset($_SESSION['username'])) {
        echo "<p>Welcome to the test site</p>
              <p><a href=\"secure.php\">Secure Data Page</a></p>
              <p><a href=\"logout.php\">Logout</a></p>";
    } else {
        echo "nope not working";
    }
?>
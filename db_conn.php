<?php

    $connection = mysqli_connect("localhost", "root", "enter password here", "instdb");

    if (mysqli_connect_errno()) {
      echo "Failed to connect to MySQL: ".mysqli_connect_error();
    }

?>

<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "logisticdb";
    $conn = "";

    try {
        $conn = mysqli_connect(
        $db_server,
        $db_user,
        $db_pass,
        $db_name,
        3307);
    }catch (mysqli_sql_exception) {
            echo "Could not connect<br>";
    }
    // if($conn) {
    //     echo"You are connected <br>";
    // }
?>
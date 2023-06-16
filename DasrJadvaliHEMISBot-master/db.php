<?php

 $server ="us-cdbr-east-06.cleardb.net";
 $username = "b83f3ddb414d96";
    $password = "73f44bd2";
    $db = "heroku_f4c38d62b31c53f";
    $conn = new mysqli($server, $username, $password, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //set charset to utf8 general ci
    $conn->set_charset("utf8mb4");

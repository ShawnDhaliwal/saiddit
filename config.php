<?php

    $user = 'root';
    $password = 'root';
    $db = 'saiddit_users';
    $host = 'localhost';
    $port = 8889;

    $conn = mysqli_connect("$host:$port",$user,$password,$db);

    if(!$conn){
        echo"failed connection to server ";
        die();
    }

?>
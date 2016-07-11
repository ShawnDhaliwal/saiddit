<?php

    $user = 'root';
    $password = 'root';
    //database name
    $db = 'saiddit_users';
    $host = 'localhost';
    //the post the sql server is on
    $port = 8889;
    //connect
    $conn = mysqli_connect("$host:$port",$user,$password,$db);

    if(!$conn){
        echo"failed connection to server ";
        die();
    }

?>
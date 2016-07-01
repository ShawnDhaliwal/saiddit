<?php 

    session_start();

     if(isset($_SESSION['User_log_in'])){
        return true;
     }else {
        return false;
     }
     

 ?>
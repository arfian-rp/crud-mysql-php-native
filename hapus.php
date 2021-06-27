<?php 

require "init.php";

if( deleteData($_GET['id']) > 0){
    header('location: /');
    } else{
        echo "<script>alert('gagal')</script>";
    }

?>
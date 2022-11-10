<?php
    $host = 'localhost';
    $dbuser ='root';
    $dbpassword = '';
    $dbname = 'test';
    $link = mysqli_connect($host,$dbuser,$dbpassword,$dbname);
    if(!$link){
        echo "不正確連接資料庫</br>" . mysqli_connect_error();
    }
    else{
        $link->query('SET NAMES UTF8'); // 設定編碼
        $link->query('SET time_zone = "+8:00"'); // 設定台灣時間
    }
?>
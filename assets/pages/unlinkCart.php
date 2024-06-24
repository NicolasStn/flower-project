<?php

$filename = glob("cart/user/visitor*.json");

foreach ($filename as $file) {

    $date =  date("F d Y H:i:s.", filemtime($file));
    $deletedate = date('Y-m-d', strtotime($date. ' + 30 days'));
    $deletedateTime = strtotime($deletedate);
    $currentdate = date('Y-m-d');
    $currentdateTime = strtotime($currentdate);

    if ($deletedateTime < $currentdateTime) {
        unlink($file);
    }

}
<?php

if (isset($_COOKIE['user'])) {

    setcookie("user", '', time() - 864000 * 30, "/");
    $cookie_name = "visitor";
    $cookie_value = "visitor". uniqid();
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
    header("Location: ../../index.php");

} else {
    header('Location: ../../index.php');
}

?>
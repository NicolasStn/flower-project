<?php

if ($_COOKIE == null) {

    // Mettez en place la logique pour définir un cookie qui marque l'acceptation des cookies.
    $cookie_name = "visitor";
    $cookie_value = "visitor". uniqid();
    setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/");
    header("Location: ".$_SERVER['HTTP_REFERER']); // Redirigez l'utilisateur vers la page précédente.

} else {
    header('Location:../../index.php');
}
?>


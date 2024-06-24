<?php

require_once('../connexion.php');

if (isset($_COOKIE['user'])) {

    $username = $_COOKIE['user'];

    $sqlClients = "SELECT username, admin FROM client
    WHERE username = :username";
    $queryClients = $db->prepare($sqlClients);
    $queryClients->execute([
        "username" => $username
    ]);

    $checkClients = $queryClients->fetch();

    if ($checkClients['admin'] === '1') {

        $sql = "SELECT username, admin FROM client
                ORDER BY id_client DESC";
        $query = $db->prepare($sql);
        $query->execute();

        $allClients = $query->fetchAll();

        // Delete

        if (isset($_POST['submitDelete']) && isset($_POST['id']) && intval($_POST['id']) != 0 ) {

            $id = $_POST['id'];
            /* requete pour récupérer les informations d'un client */
            $sql = "SELECT username FROM demo_fleuriste.`client` WHERE id_client = :id;";
            $query = $db->prepare($sql);
            $query->execute([
                'id' => $id
            ]);

            $client = $query->fetch();

            /* je renvois l'utilisateur à la page index si le client n'existe pas en base */
            if ($client === false){
                header('Location:clients.php');
            }

            /* requête pour récupérer les commandes du client */
            $sqlCommandes = "SELECT commande.id_commande, commande.date_commande, total FROM demo_fleuriste.commande
                INNER JOIN demo_fleuriste.client ON commande.id_client = client.id_client
                INNER JOIN demo_fleuriste.fleur_commande ON commande.id_commande = fleur_commande.num_commande
                INNER JOIN demo_fleuriste.fleur ON fleur_commande.id_fleur = fleur.id_fleur
                WHERE client.id_client = :id
                GROUP BY commande.id_commande;";
            $queryCommandes = $db->prepare($sqlCommandes);
            $queryCommandes->execute([
                "id" => $id
            ]);

            $commandes = $queryCommandes->fetchAll();

            $password = htmlspecialchars($_POST['password']);

            if ($clients['username'] && password_verify($password, $clients['password']) || $clients['email'] && password_verify($password, $clients['password'])) {

                $id = intval(trim($_POST['id']));

                foreach($commandes as $commande) {
                    $sqlDeleteLignes = "DELETE FROM demo_fleuriste.fleur_commande WHERE num_commande = :num";
                    $sqlDeleteCommande = "DELETE FROM demo_fleuriste.commande WHERE num_commande = :num";

                    $queryDeleteLignes = $db->prepare($sqlDeleteLignes);
                    $queryDeleteLignes->execute([
                        "num" => $commande['num_commande']
                    ]);

                    $queryDeleteCommande = $db->prepare($sqlDeleteCommande);
                    $queryDeleteCommande->execute([
                        "num" => $commande['num_commande']
                    ]);
                }

                $sqlDelete = "DELETE FROM demo_fleuriste.client WHERE id_client = :id";
                $queryDelete = $db->prepare($sqlDelete);
                $queryDelete->execute([
                    'id' => $id
                ]);

                header('Location:allClients.php');
            } else {
                header("Location: ".$_SERVER['HTTP_REFERER']);
                echo "<script>alert('Mot de passe incorrect');</script>";
            }
        } else {
            header("Location: ".$_SERVER['HTTP_REFERER']);
        }
    } else {
        header("Location: ".$_SERVER['HTTP_REFERER']);
    }
} else {
    header("Location: ".$_SERVER['HTTP_REFERER']);
}

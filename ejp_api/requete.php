<?php


// requete.php

// Obtenir la date du jour au format YYYY-MM-DD
$dateDuJour = date('Y-m-d');

// Cr�er un nouvel objet DateTime pour aujourd'hui
$aujourdHui = new DateTime();

// Ajouter un jour pour obtenir la date de demain
$demain = clone $aujourdHui;
$demain->add(new DateInterval('P1D'));

// Formater la date de demain au format YYYY-MM-DD
$dateDemain = $demain->format('Y-m-d');

// URL de l'API
$apiUrl = "https://api-commerce.edf.fr/commerce/activet/v1/calendrier-jours-effacement?option=EJP&dateApplicationBorneInf=$dateDuJour&dateApplicationBorneSup=$dateDemain&identifiantConsommateur=src";

// Initialiser une nouvelle session cURL
$ch = curl_init($apiUrl);

// Configurer les options cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner le r�sultat sous forme de cha�ne
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json', // Accepter les r�ponses JSON
]);

// Ex�cuter la requ�te et r�cup�rer la r�ponse
$response = curl_exec($ch);

// V�rifier les erreurs cURL
if (curl_errno($ch)) {
    echo 'Erreur cURL : ' . curl_error($ch);
}

// Fermer la session cURL
curl_close($ch);

// D�coder la r�ponse JSON
$data = json_decode($response, true);


// V�rifier si le d�codage JSON a r�ussi
if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'Erreur de d�codage JSON : ' . json_last_error_msg();
} else {
    // Traiter les donn�es JSON
    // Convertir le tableau en cha�ne JSON
    $jsonString = json_encode($data, JSON_PRETTY_PRINT);


    $pattern = '/"statut"\s*:\s*"([^"]+)"/';
    preg_match($pattern, $jsonString, $matches);

    // Afficher le premier statut
    if (!empty($matches)) {
        echo $matches[1]. "\n"; // Cela affichera : HORS_PERIODE_EJP
        
        // Obtenir l'heure actuelle au format "H" (heures sans les minutes)
        $heureActuelle = date('H');

        // Cr�er le nom du fichier
        $nomFichier = 'ejp' . $heureActuelle . '.txt';

        // Enregistrer le contenu de $matches[1] dans le fichier
        file_put_contents($nomFichier, $matches[1]);
    } else {
        echo "Aucun statut trouv�.";
    }
}




?>
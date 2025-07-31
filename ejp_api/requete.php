<?php
// requete.php

// on positionne la TZ pour avoir la bonne date & heure
date_default_timezone_set('Europe/Paris');

// Obtenir la date du jour au format YYYY-MM-DD
$dateDuJour = date('Y-m-d');

// Créer un nouvel objet DateTime pour aujourd'hui
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
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner le résultat sous forme de chaîne
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json', // Accepter les réponses JSON
]);

// Pour débugguer localement sans SSL :)
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);	

// Exécuter la requête et récupérer la réponse
$response = curl_exec($ch);

// Vérifier les erreurs cURL
if (curl_errno($ch)) {
    echo 'Erreur cURL : ' . curl_error($ch);
} else {
    // Décoder la réponse JSON
    $data = json_decode($response, true);
    
    
    // Vérifier si le décodage JSON a réussi
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'Erreur de décodage JSON : ' . json_last_error_msg();
    } else {
        // Traiter les données JSON
        // Convertir le tableau en chaîne JSON
        $jsonString = json_encode($data, JSON_PRETTY_PRINT);
    
    
        $pattern = '/"statut"\s*:\s*"([^"]+)"/';
        preg_match($pattern, $jsonString, $matches);
    
        // Afficher le premier statut
        if (!empty($matches)) {
            echo $matches[1]. "\n"; // Cela affichera : HORS_PERIODE_EJP
            
            // Obtenir l'heure actuelle au format "H" (heures sans les minutes)
            $heureActuelle = date('H');
    
            // Créer le nom du fichier
            $nomFichier = 'ejp' . $heureActuelle . '.txt';
    
            // Enregistrer le contenu de $matches[1] dans le fichier
            file_put_contents($nomFichier, $matches[1]);
        } else {
            echo "Aucun statut trouvé.";
        }
    }
}

// Fermer la session cURL
curl_close($ch);

?>

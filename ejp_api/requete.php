<?php
// requete.php

// on positionne la TZ pour avoir la bonne date & heure
date_default_timezone_set('Europe/Paris');

function logMessage(string $filename, string $message): void {
	// Format de la date/heure
	$timestamp = date('Y-m-d H:i:s');
	
	// Force l'encodage UTF-8 du message
	$message = mb_convert_encoding($message, 'UTF-8', 'auto');
	
	// Ligne à écrire dans le fichier CSV
	$line = [$timestamp, $message];
	
	// Ouvre le fichier en mode ajout (créé s'il n'existe pas)
	$file = fopen($filename, 'a');
	
	if ($file) {
		// Écrit la ligne en format CSV
		fputcsv($file, $line, ';');
		fclose($file);
	} else {
		// erreur ? on ne tracera pas alors...
	}
}

// Obtenir la date du jour au format YYYY-MM-DD
$dateDuJour = date('Y-m-d');

// Obtenir la date de demain au format YYYY-MM-DD
$dateDemain = date('Y-m-d', strtotime('+1 day'));

// URL de l'API
$apiUrl = "https://api-commerce.edf.fr/commerce/activet/v1/calendrier-jours-effacement?option=EJP&dateApplicationBorneInf=$dateDuJour&dateApplicationBorneSup=$dateDemain&identifiantConsommateur=src";

// Bouchon pour tests locaux
// nb: si on utilise directement php sans serveur web, il faut lancer un autre php sur un autre port d'écoute, sinon ça bloque (monothread) !
// $apiUrl = "http://localhost:8081/ejp_api/bouchon.php";

// Initialiser une nouvelle session cURL
$ch = curl_init($apiUrl);

// Configurer les options cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Retourner le résultat sous forme de chaîne
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Pour gérer d'éventuelles redirections HTTP30x
curl_setopt($ch, CURLOPT_ENCODING, ''); // décompression automatique
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
	logMessage('history.log', 'Erreur cURL');
} else {
	// Décoder la réponse JSON
	$data = json_decode($response, true);
	
	// Vérifier si le décodage JSON a réussi
	if (json_last_error() !== JSON_ERROR_NONE) {
		echo 'Erreur de décodage JSON : ' . json_last_error_msg();
		logMessage('history.log', 'Erreur de décodage JSON');
	} else {
		// Récupération du code HTTP
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		echo "L'appel à <a href=\"$apiUrl\">$apiUrl</a> a renvoyé un code HTTP $httpCode <br />\n";
		echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";			
		// si autre chose que HTTP200, on aura affiché le contenu avec les 2 lignes précédentes... on s'arrête donc là dans ce cas.
		if ($httpCode == 200){
			// Initialiser le statut
			$statutDuJour = null;
		
			// Parcourir le calendrier pour trouver la bonne date
			if (isset($data['content']['options'][0]['calendrier'])) {
				foreach ($data['content']['options'][0]['calendrier'] as $jour) {
					if ($jour['dateApplication'] === $dateDemain) {
						$statutDuJour = $jour['statut'];
						break;
					}
				}
			}
			
			// Affichage du résultat
			if ($statutDuJour !== null) {
				echo "Statut pour la date $dateDemain : $statutDuJour";
				
				// Obtenir l'heure actuelle au format "H" (heures sans les minutes)
				$heureActuelle = date('H');
		
				// Créer le nom du fichier
				$nomFichier = 'ejp' . $heureActuelle . '.txt';
		
				// Enregistrer le contenu de $matches[1] dans le fichier
				file_put_contents($nomFichier, $statutDuJour);
				logMessage('history.log', "Statut pour la date $dateDemain : $statutDuJour");
			} else {
				echo "Aucun statut trouvé pour la date $dateDemain.";
				logMessage('history.log', "Aucun statut trouvé pour la date $dateDemain.");
			}			
		} else {
			logMessage('history.log', "L'API a renvoyé le code HTTP $httpCode");			
		}
	}
}

// Fermer la session cURL
curl_close($ch);

?>


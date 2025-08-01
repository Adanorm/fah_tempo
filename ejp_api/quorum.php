<?php

// Tableau associatif pour stocker les contenus des fichiers
$ejp = array();

// Utiliser glob pour trouver tous les fichiers correspondant au motif ejp*.txt
$fichiers = glob('ejp*.txt');

// Tableau pour compter les occurrences de chaque valeur
$occ = array();

// Variables pour trouver la valeur avec le plus grand nombre d'occurrences
$consensus = null;
$maxOccurrences = 0;

// Parcourir chaque fichier trouvé
foreach ($fichiers as $fichier) {
	// Lire le contenu du fichier
    $contenu = file_get_contents($fichier);

	// Compter les occurences d'un même contenu
    if (isset($occ[$contenu])) {
        $occ[$contenu]++;
    } else {
        $occ[$contenu] = 1;
    }
	// Identifier si c'est le nouveau max.
    if ($occ[$contenu] > $maxOccurrences){
        $maxOccurrences = $occ[$contenu];
        $consensus = $contenu;
	}
}

// Afficher le tableau associatif pour vérification
echo "<pre>";
print_r($occ);
echo "</pre>";			

// Afficher la valeur de consensus dans la console
echo "La valeur de consensus est : " . $consensus . "\n";

// Écrire le contenu de $consensus dans un fichier
file_put_contents('consensus_ejp.txt', $consensus);

?>

<?php

// Tableau associatif pour stocker les contenus des fichiers
$ejp = array();

// Utiliser glob pour trouver tous les fichiers correspondant au motif ejp*.txt
$fichiers = glob('ejp*.txt');

// Parcourir chaque fichier trouvé
foreach ($fichiers as $fichier) {
    // Extraire XX du nom du fichier
    $nomFichier = basename($fichier, '.txt'); // Supprimer l'extension .txt
    $cle = substr($nomFichier, 3); // Extraire XX en supprimant 'ejp'

    // Lire le contenu du fichier
    $contenu = file_get_contents($fichier);

    // Stocker le contenu dans le tableau associatif
    $ejp[$cle] = $contenu;
}

// Afficher le tableau associatif pour vérification
echo "<pre>";
print_r($ejp);
echo "</pre>";

// Tableau pour compter les occurrences de chaque valeur
$occurrences = array();

// Parcourir le tableau $ejp et compter les occurrences
foreach ($ejp as $cle => $valeur) {
    if (isset($occurrences[$valeur])) {
        $occurrences[$valeur]++;
    } else {
        $occurrences[$valeur] = 1;
    }
}

// Trouver la valeur avec le plus grand nombre d'occurrences
$consensus = null;
$maxOccurrences = 0;

foreach ($occurrences as $valeur => $nombre) {
    if ($nombre > $maxOccurrences) {
        $maxOccurrences = $nombre;
        $consensus = $valeur;
    }
}

// Afficher la valeur de consensus dans la console
echo "La valeur de consensus est : " . $consensus . "\n";

// Écrire le contenu de $consensus dans un fichier
file_put_contents('consensus_ejp.txt', $consensus);

?>

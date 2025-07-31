<?php

// Utiliser glob pour trouver tous les fichiers correspondant au motif ejp[0-9][0-9].txt
$fichiers = glob('ejp[0-9][0-9].txt');

// Parcourir chaque fichier trouvé et le supprimer
foreach ($fichiers as $fichier) {
    if (is_file($fichier)) {
        unlink($fichier);
        echo "Suppression du fichier : $fichier\n";
    }
}

echo "Tous les fichiers ejpXX.txt ont été supprimés.\n";

?>

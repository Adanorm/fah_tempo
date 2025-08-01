<?php
// Définir le bon Content-Type
header('Content-Type: application/json');

// Vérifier si un paramètre "code" est passé
$code = isset($_GET['code']) ? intval($_GET['code']) : 200;

if ($code === 400) {
    // Définir le code HTTP
    http_response_code(400);

	// Simuler la réponse JSON
	$response = [
        "errors" => [
            "0" => [
                "code" => "ATM_HTTP_400",
                "description" => "La syntaxe de la requête est erronée.",
                "severity" => "ERROR",
                "type" => "TECHNICAL"
            ],
            "content" => null
        ]
    ];
	
	
} else {
    // Réponse normale
    http_response_code(200);


	// Date d'aujourd'hui
	$aujourdhui = date('Y-m-d');

	// Date de demain
	$demain = date('Y-m-d', strtotime('+1 day'));
	
	
	// Simuler la réponse JSON
	$response = [
		"errors" => [],
		"content" => [
			"dateApplicationBorneInf" => "$aujourdhui",
			"dateApplicationBorneSup" => "$aujourdhui",
			"dateHeureTraitementActivET" => $aujourdhui."T22:42:24Z",
			"options" => [
				[
					"option" => "EJP",
					"calendrier" => [
						[
							"dateApplication" => "$aujourdhui",
							"statut" => "HORS_PERIODE_EJP"
						],
						[
							"dateApplication" => "$demain",
							"statut" => "HORS_PERIODE_EJP"
						]
					]
				]
			]
		]
	];
}

// Afficher le JSON formaté
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


?>

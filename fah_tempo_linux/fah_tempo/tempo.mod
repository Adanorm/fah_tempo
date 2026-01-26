#!/bin/sh

. ~/fah_tempo/config.cfg

cd $chemin_local

curl -X 'GET' \
  'https://www.api-couleur-tempo.fr/api/jourTempo/today' \
  -H 'accept: application/json' | python3 -c "import sys, json; print(json.load(sys.stdin)['codeJour'])" > tempo_jour.txt 2>> logs.log

. ~/fah_tempo/config.cfg

aujourdhui=$(date '+%Y-%m-%d')
demain=$(date --date="next day" +%Y-%m-%d)

echo  "https://api-commerce.edf.fr/commerce/activet/v1/calendrier-jours-effacement?option=EJP&dateApplicationBorneInf=$aujourdhui&dateApplicationBorneSup=$demain&identifiantConsommateur=src" >> $logfile

curl -X 'GET' "https://api-commerce.edf.fr/commerce/activet/v1/calendrier-jours-effacement?option=EJP&dateApplicationBorneInf=$aujourdhui&dateApplicationBorneSup=$demain&identifiantConsommateur=src" -H 'accept: application/json' --output ejp.json 

ejp=`sed 's/,/\n/g' ejp.json | grep statut | head -n 1 | grep -e NON_EJP -e HORS_PERIODE_EJP | wc -l` 

if [ $ejp -eq 1 ]
then
	echo 1 > $chemin_local/tempo_jour.txt
else
	echo 3 > $chemin_local/tempo_jour.txt
fi



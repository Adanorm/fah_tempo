#!/bin/sh
. ~/fah_tempo/config.cfg

echo "Lancement du module $module" >> $logfile
bash "$chemin_local/$module".mod
echo "Module chargé" >> $logfile

codeJour=`cat $chemin_local/tempo_jour.txt`
echo "Valeur du jour $codeJour" >> $logfile

if [ $codeJour -ge $seuil_coupure_tempo ]
then
	echo "Coupure FAHv8" >> $logfile
	lufah finish

	if [ $coupure_apres_delais -eq 1 ]
        then
                echo "Attente $delais_extinction avant de couper le PC" >> $logfile
                sleep $delais_extinction
                echo "Arret slot GPU" >> $logfile
                lufah pause
	fi			
	if [ $eteindre_apres_delais -eq 1 ]
	then
		echo "Attente $delais_extinction avant de couper le PC" >> $logfile
		sleep $delais_extinction
		echo "Extinction PC" >> $logfile
		shutdown -h now
	fi
fi

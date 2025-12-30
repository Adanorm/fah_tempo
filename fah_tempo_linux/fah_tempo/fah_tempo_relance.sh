#!/bin/sh
. ~/fah_tempo/config.cfg

codeJour=`cat $chemin_local/tempo_jour.txt`
echo "Code du jour : $codeJour" >> $logfile

if [ $codeJour -ge $seuil_coupure_tempo ]
then
	echo "Relance FAHv8" >> $logfile
        lufah fold
fi


NOWF=$(date +"%F")
NOWT=$(date +"%T")
 
php iniciativas/index.php > iniciativas/logs/iniciativas-$NOWF-$NOWT.log &
#tail -f iniciativas/logs/iniciativas-$NOWF-$NOWT.log

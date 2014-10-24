NOWF=$(date +"%F")
NOWT=$(date +"%T")
 
php /var/www/scraping/iniciativas/index.php > /var/www/scraping/iniciativas/logs/iniciativas-$NOWF-$NOWT.log &
#tail -f /var/www/scraping/iniciativas/logs/iniciativas-$NOWF-$NOWT.log

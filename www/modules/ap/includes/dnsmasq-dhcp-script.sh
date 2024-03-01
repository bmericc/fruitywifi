#!/bin/bash

LOG="/usr/share/fruitywifi/logs/clients.log"
DATE=`date +"%Y-%m-%d %H:%M:%S"` 

echo "$DATE $2 $3 $4 ($1)" >> $LOG

python /usr/share/FruityWifi/www/modules/faraday/includes/faraday-client.py -s "127.0.0.1" -p 9876 -f createHostAndInterface -d "$3|$2|$4|3"

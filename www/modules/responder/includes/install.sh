#!/bin/bash
echo
echo "installing Responder..."
echo

apt-get -y install python-openssl
wget https://github.com/lgandx/Responder/archive/master.zip
unzip master.zip

echo
echo "Settings for Responder..."
echo

/bin/sed -i 's/^SQL.*/SQL = On/g' Responder-master/Responder.conf
/bin/sed -i 's/^SMB.*/SMB = On/g' Responder-master/Responder.conf
/bin/sed -i 's/^FTP.*/FTP = On/g' Responder-master/Responder.conf
/bin/sed -i 's/^LDAP.*/LDAP = On/g' Responder-master/Responder.conf
/bin/sed -i 's/^HTTP .*/HTTP = Off/g' Responder-master/Responder.conf
/bin/sed -i 's/^HTTPS.*/HTTPS = Off/g' Responder-master/Responder.conf
/bin/sed -i 's/^DNS.*/DNS = Off/g' Responder-master/Responder.conf
cat /dev/null > /usr/share/fruitywifi/logs/responder.log
/bin/sed -i 's/^SessionLog.*/SessionLog = \/usr\/share\/fruitywifi\/logs\/responder.log/g' Responder-master/Responder.conf

chmod 755 Responder-master/Responder.py

/bin/sed -i 's/#! \/usr\/bin\/env python/#!\/usr\/bin\/python/g' Responder-master/Responder.py

echo
echo "Cleaning Up..."
echo

rm -r master.zip

echo "..DONE.."
exit

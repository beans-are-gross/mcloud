if [ "$(id -u)" != "0" ]; then
   printf "\033[0;31m! This script must be run as root!\033[0m \n" 1>&2
   exit 1
fi

clear

printf "\033[0;32m> Updating apt and intalling required files.\033[0m \n"
sudo apt update && sudo apt install php libapache2-mod-php php-mysql apache2 mysql-server curl -y

clear

fileCount=$(find "/var/www/html" -maxdepth 1 -type f | wc -l)

if [ "$fileCount" -gt 1 ]; then
   printf "\033[0;33m! Your /var/www/html/ folder will be cleared, enter Y to continue.\033[0m \n"
   printf "> "
   read confirmClearFolder
   
   confirmClearFolder=$(echo "$confirmClearFolder" | tr '[:upper:]' '[:lower:]')
   
   printf "\n";
   
   if [ "$confirmClearFolder" != "y" ]; then
      clear
      printf "\033[0;31m! Script canceled\033[0m \n" 1>&2
      exit 1
   fi
fi

printf "\033[0;32m> Deleting html folder and moving html folder to /var/www.\033[0m \n"
rm -r /var/www/html
mv html /var/www

printf "\033[0;34m? What would you like your admin password to be?\033[0m \n"
printf "\033[0;31m! Spaces will be removed!\033[0m \n"
printf "> "
read dbPassword
dbPassword=$(echo -n $dbPassword | tr -d '\n')
hashedPwd=$(php /var/www/html/password.php $dbPassword)
printf "\033[0;32m> Password hash recieved.\033[0m \n"

printf "\033[0;32m> Creating mCloud root folder.\033[0m \n"
mkdir /mcloud
mkdir /mcloud/uploads
chown www-data:root /mcloud/uploads

echo "$dbPassword" > /mcloud/mcloud.key
printf "\033[0;32m> Key saved successfully.\033[0m \n"


printf "\033[0;32m> Starting MySQL.\033[0m \n"
sudo systemctl start mysql.service
printf "\033[0;32m> Please ignore the upcoming password errors.\033[0m \n"
mysql --user="root" --execute="ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$dbPassword';"
mysql --user="root" --password="$dbPassword" --execute="CREATE USER 'mcloud'@'localhost' IDENTIFIED WITH mysql_native_password BY '$dbPassword';"
mysql --user="root" --password="$dbPassword" --execute="CREATE DATABASE mcloud;"
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute='CREATE TABLE files (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` TEXT NOT NULL,
    `externalDir` VARCHAR(50) NOT NULL,
    `internalDir` TEXT NOT NULL,
    `dateAdded` DATETIME NOT NULL DEFAULT NOW(),
    `lastUpdated` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
    `type` TEXT NOT NULL,
    `icon` TEXT NOT NULL,
    `userId` TEXT NOT NULL,
    PRIMARY KEY (`id`)
);'
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute='CREATE TABLE login (
    `id` INT NOT NULL AUTO_INCREMENT,
    `uid` TEXT NOT NULL,
    `pwd` VARCHAR(255) NOT NULL,
    `firstName` TEXT NOT NULL,
    `lastName` TEXT NOT NULL,
    `cookie` VARCHAR(16) NOT NULL,
    PRIMARY KEY (`id`)
);'
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute="INSERT INTO login (uid, pwd, firstName, lastName, cookie) VALUES ('admin', '$hashedPwd', 'Admin', 'User', '');"
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute="GRANT SELECT, INSERT, UPDATE, DELETE ON mcloud.files TO 'mcloud'@'localhost';"
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute="GRANT SELECT, INSERT, UPDATE, DELETE ON mcloud.login TO 'mcloud'@'localhost';"
mysql --user="root" --password="$dbPassword" --execute="FLUSH PRIVILEGES;"
clear

printf "\033[0;35m> Setup complete! \033[0m \n"

if [ "$(id -u)" != "0" ]; then
   printf "\033[0;31m! This script must be run as root!\033[0m \n" 1>&2
   exit 1
fi

clear

printf "\033[0;32m> Updating apt and intalling required files.\033[0m \n"
sudo apt update && sudo apt install php libapache2-mod-php php-mysql apache2 mysql-server curl -y

clear

printf "\033[0;32m> Moving html folder to /var/www.\033[0m \n"
rm -r /var/www/html
mv html /var/www

printf "\033[0;34m? What would you like your admin password to be?\033[0m \n"
printf "\033[0;31m! Spaces will be removed!\033[0m \n"
printf "> "
read dbPassword
dbPassword=$(echo -n $dbPassword | tr -d '\n')
hashedPwd="php /var/www/html/password.php -pwd=$dbPassword"
printf "\033[0;32m> Password hash recieved.\033[0m \n"


echo "$dbPassword" > /mcloud.key
printf "\033[0;32m> Key saved successfully.\033[0m \n"


printf "\033[0;32m> Starting MySQL.\033[0m \n"
sudo systemctl start mysql.service
printf "\033[0;32m> Please ignore the upcoming password errors.\033[0m \n"
mysql --user="root" --execute="ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '$dbPassword';"
mysql --user="root" --password="$dbPassword" --execute="CREATE USER 'mcloud'@'%' IDENTIFIED WITH mysql_native_password BY '$dbPassword';"
mysql --user="root" --password="$dbPassword" --execute="CREATE DATABASE mcloud;"
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute="GRANT SELECT, INSERT, UPDATE, DELETE ON files TO 'mcloud'@'%';"
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute="GRANT SELECT, INSERT, UPDATE, DELETE ON login TO 'mcloud'@'%';"
mysql --user="root" --password="$dbPassword" --execute="FLUSH PRIVILEGES;"
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute='CREATE TABLE files (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` TEXT NOT NULL,
    `externalDir` VARCHAR(50) NOT NULL,
    `internalDir` TEXT NOT NULL,
    `dateAdded` DATETIME NOT NULL DEFAULT NOW(),
    `lastUpdated` DATETIME NOT NULL DEFAULT NOW() ON UPDATE NOW(),
    `type` TEXT NOT NULL,
    `accountCookie` TEXT NOT NULL,
    PRIMARY KEY (`id`)
);'
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute='CREATE TABLE login (
    `id` INT NOT NULL AUTO_INCREMENT,
    `uid` TEXT NOT NULL,
    `pwd` VARCHAR(255) NOT NULL,
    `firstName` TEXT NOT NULL,
    `lastName` TEXT NOT NULL,
    PRIMARY KEY (`id`)
);'
mysql --user="root" --password="$dbPassword" --database="mcloud" --execute="INSERT INTO login (uid, pwd, firstName, lastName) VALUES ('admin', '$hashedPWD', 'Admin', 'User');"

clear

printf "\033[0;35m> Setup complete! \033[0m \n"

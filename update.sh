if [ "$(id -u)" != "0" ]; then
   printf "\033[0;31m! This script must be run as root!\033[0m \n" 1>&2
   exit 1
fi

if [$(test -d /tmp/test) == 1]; then
   printf "\033[0;31m! Please follow the update commands at https://github.com/beans-are-gross/mcloud\033[0m \n" 1>&2
   exit 1
fi
clear

printf "\033[0;32m> Moving html folder to /var/www.\033[0m \n"
rm -r /var/www/html
mv html /var/www

printf "\033[0;35m> Update complete! \033[0m \n"
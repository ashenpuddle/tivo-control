cp ../config/google-assistant-tivo.com.conf /etc/apache2/sites-available
cp ../google-assistant-tivo /var/www

a2ensite /etc/apache2/sites-available/google-assistant-tivo.com.conf
service apache2 reload
service apache2 restart

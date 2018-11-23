To use this library: 

**/etc/apache2/sites-available** and **/etc/apache2/sites-enabled** needs to be **owned** by **www-data:www-data**

Add the following to your /etc/sudoers file:

**Cmnd_Alias      RELOAD_APACHE =  /etc/init.d/apache2 reload**

**www-data ALL=NOPASSWD: RELOAD_APACHE**

***Use This Library at your own risk!***

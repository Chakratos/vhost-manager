To use this library: 

**/etc/apache2/sites-available** and **/etc/apache2/sites-enabled** needs to be **owned** by **www-data:www-data**

Add the following to your /etc/sudoers file:

**Cmnd_Alias      RELOAD_APACHE =  /etc/init.d/apache2 reload**

**www-data ALL=NOPASSWD: RELOAD_APACHE**

***Use This Library at your own risk!***

*-*Usage*-*

    $proxyPass = new \VhostManager\ProxyPass();
    $proxyPass->setRedirectFrom('/')
        ->setRedirectTo('127.0.0.1:8088')
        ->setReverseRedirectFrom('/')
        ->setReverseRedirectTo('127.0.0.1:8088');

    $vhost = new \VhostManager\Vhost();
    $vhost->setProxyPass($proxyPass)
        ->setPort(80)
        ->setServerAdmin('Benjamin.Schaffrath@jtl-software.com')
        ->setServerName('first.test')
        ->setServerAlias('www.first.test')
        ->save(true);

    $vhost->activate();

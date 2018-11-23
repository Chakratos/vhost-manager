<?php

$proxyPass = new \VhostManager\ProxyPass();
$proxyPass->setRedirectFrom("/");
$proxyPass->setRedirectTo("127.0.0.1:8088");

$proxyPassReverse = new \VhostManager\ProxyPassReverse();
$proxyPassReverse->setRedirectFrom("/");
$proxyPassReverse->setRedirectTo("127.0.0.1:8088");

$vhost = new \VhostManager\Vhost();
$vhost->setProxyPass($proxyPass)
    ->setProxyPassReverse($proxyPassReverse)
    ->setPort(80)
    ->setServerAdmin("Benjamin.Schaffrath@jtl-software.com")
    ->setServerName("first.test")
    ->setServerAlias("www.first.test")
    ->save(true);

$vhost->activate();

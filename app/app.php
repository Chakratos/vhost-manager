<?php
/*
 * $vhost = new VHost();
 */
$proxyPass = new \VhostManager\ProxyPass();
$proxyPass->setRedirectFrom("/");
$proxyPass->setRedirectTo("localhost:8088");

$proxyPassReverse = new \VhostManager\ProxyPassReverse();
$proxyPassReverse->setRedirectFrom("/");
$proxyPassReverse->setRedirectTo("localhost:8088");

$vhost = new \VhostManager\Vhost();
$vhost->setProxyPass($proxyPass)
    ->setProxyPassReverse($proxyPassReverse)
    ->setPort(80)
    ->setServerAdmin("Benjamin.Schaffrath@jtl-software.com")
    ->setServerName("docker-vhost.test")
    ->setServerAlias("www.docker-vhost.test");

echo $vhost;

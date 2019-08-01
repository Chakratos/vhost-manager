<?php
namespace VhostManager;

class Vhost
{
    /** @var int */
    protected $port = 80;
    /** @var string */
    protected $serverName = '';
    /** @var string */
    protected $serverAlias = '';
    /** @var string */
    protected $serverAdmin = '';
    /** @var string */
    protected $documentRoot = '';
    /** @var string */
    protected $phpVersion = '';
    /** @var ProxyPass */
    protected $proxyPass;
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->build();
    }
    
    /**
     * @param bool $force
     * @return bool
     */
    public function save(bool $force = true): bool
    {
        $filePath = sprintf('/etc/apache2/sites-available/%s.conf',
            $this->serverName
        );
        
        if (empty($this->serverName) || (file_exists($filePath  && $force == false))) {
            return false;
        }
        
        $file = fopen($filePath, 'w+');
        
        if (!$file || !fwrite($file, $this->build())) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @param bool $force
     * @return bool
     */
    public function activate(bool $force = true): bool
    {
        $filePath = sprintf('/etc/apache2/sites-available/%s.conf',
            $this->serverName
        );
        $symlinkPath = sprintf('/etc/apache2/sites-enabled/%s.conf',
            $this->serverName
        );
        
        if (empty($this->serverName) || (!file_exists($filePath))) {
            return false;
        }
    
        if (file_exists($symlinkPath)) {
            if (!$force) {
                exec('sudo /etc/init.d/apache2 reload');
                
                return false;
            }
    
            unlink($symlinkPath);
        }
        
        symlink($filePath, $symlinkPath);
        shell_exec('sudo /etc/init.d/apache2 reload');
        
        return true;
    }
    
    public static function enable($serverName): bool
    {
        $filePath = sprintf('/etc/apache2/sites-available/%s.conf',
            $serverName
        );
        $symlinkPath = sprintf('/etc/apache2/sites-enabled/%s.conf',
            $serverName
        );
        
        if (empty($serverName) || (!file_exists($filePath)) || file_exists($symlinkPath)) {
            return false;
        }
        
        symlink($filePath, $symlinkPath);
        shell_exec('sudo /etc/init.d/apache2 reload');
        
        return true;
    }
    
    public static function disable($serverName): bool
    {
        $filePath = sprintf('/etc/apache2/sites-enabled/%s.conf',
            $serverName
        );
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        unlink($filePath);
        shell_exec('sudo /etc/init.d/apache2 reload');
        
        return true;
    }
    
    public static function delete(string $serverName, bool $deactivate) {
        $filePath = sprintf('/etc/apache2/sites-available/%s.conf',
            $serverName
        );
        
        if (!file_exists($filePath)) {
            return false;
        }
        
        if ($deactivate) {
            self::disable($serverName);
        }
        
        unlink($filePath);
        shell_exec('sudo /etc/init.d/apache2 reload');
        
        return true;
    }
    
    public static function getAvailableVhosts(): array
    {
        $hosts = glob('/etc/apache2/sites-available/*');
        $result =[];
        
        foreach ($hosts as $host) {
            $result[] = str_replace('/etc/apache2/sites-available/', '', $host);
        }
        
        return $result;
    }
    
    public static function getEnabledVhosts(): array
    {
        $hosts = glob('/etc/apache2/sites-enabled/*');
        $result =[];
    
        foreach ($hosts as $host) {
            $result[] = str_replace('/etc/apache2/sites-enabled/', '', $host);
        }
    
        return $result;
    }
    
    public static function getDisabledVhosts(): array
    {
        return array_diff(self::getAvailableVhosts(), self::getEnabledVhosts());
    }
    
    /**
     * @return string
     */
    public function build() {
        $serverName = '';
        $serverAlias = '';
        $serverAdmin = '';
        $documentRoot = '';
        $phpVersion = '';
        $proxyPass = '';
        $proxyPassReverse = '';
        
        if (!empty($this->serverName)) {
            $serverName = 'ServerName ' . $this->serverName;
        }
        if (!empty($this->serverAlias)) {
            $serverAlias = 'ServerAlias ' . $this->serverAlias;
        }
        if (!empty($this->serverAdmin)) {
            $serverAdmin = 'ServerAdmin ' . $this->serverAdmin;
        }
        if (!empty($this->documentRoot)) {
            $documentRoot = 'documentRoot ' . $this->documentRoot;
        }
        if (preg_match('^\d\.\d^', $this->phpVersion)) {
            $phpVersion = sprintf(
                'Include conf-available/php%s-fpm.conf',
                $this->phpVersion
            );
        }
        if (!empty($this->proxyPass)) {
            $proxyPass = sprintf('
                DefaultType none
                RewriteEngine on
                AllowEncodedSlashes on
                RequestHeader set X-Forwarded-Proto "http"
                RewriteCond %%{QUERY_STRING} transport=polling
                RewriteRule /(.*)$ %s/$1 [P]
                ProxyRequests off
                ProxyPreserveHost On
                ProxyAddHeaders Off
                ProxyPass "%s" "%s/"',
                $this->proxyPass->getRedirectTo(),
                $this->proxyPass->getRedirectFrom(),
                $this->proxyPass->getRedirectTo()
            );
        }
        if (isset($this->proxyPass) && !empty($this->proxyPass->getReverseRedirectFrom()) && !empty($this->proxyPass->getReverseRedirectTo())) {
            $proxyPassReverse = sprintf('ProxyPassReverse "%s" "%s/"',
                $this->proxyPass->getReverseRedirectFrom(),
                $this->proxyPass->getReverseRedirectTo()
            );
        }
        
        $vhost = sprintf('
            <VirtualHost %s:%s>
                # Generated by Chakratos\VHost-Manager
                %s
                %s
                %s
                %s
                %s
                %s
                %s
            </VirtualHost>',
            $this->serverName,
            $this->port,
            $serverName,
            $serverAlias,
            $serverAdmin,
            $documentRoot,
            $phpVersion,
            $proxyPass,
            $proxyPassReverse
        );
        
        return $vhost;
    }
    
    /**
     * @return string
     */
    public function getServerAdmin(): string
    {
        return $this->serverAdmin;
    }
    
    /**
     * @param string $serverAdmin
     * @return Vhost
     */
    public function setServerAdmin(string $serverAdmin): Vhost
    {
        $this->serverAdmin = $serverAdmin;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getDocumentRoot(): string
    {
        return $this->documentRoot;
    }
    
    /**
     * @param string $documentRoot
     * @return Vhost
     */
    public function setDocumentRoot(string $documentRoot): Vhost
    {
        $this->documentRoot = $documentRoot;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }
    
    /**
     * @param int $port
     * @return Vhost
     */
    public function setPort(int $port): Vhost
    {
        $this->port = $port;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getServerName(): string
    {
        return $this->serverName;
    }
    
    /**
     * @param string $serverName
     * @return Vhost
     */
    public function setServerName(string $serverName): Vhost
    {
        $this->serverName = $serverName;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getServerAlias(): string
    {
        return $this->serverAlias;
    }
    
    /**
     * @param string $serverAlias
     * @return Vhost
     */
    public function setServerAlias(string $serverAlias): Vhost
    {
        $this->serverAlias = $serverAlias;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPhpVersion() : string
    {
        return $this->phpVersion;
    }
    
    /**
     * @param string $phpVersion
     * @return Vhost
     */
    public function setPhpVersion(string $phpVersion) : Vhost
    {
        $this->phpVersion = $phpVersion;
        
        return $this;
    }
    
    /**
     * @return ProxyPass
     */
    public function getProxyPass(): ProxyPass
    {
        return $this->proxyPass;
    }
    
    /**
     * @param ProxyPass $proxyPass
     * @return Vhost
     */
    public function setProxyPass(ProxyPass $proxyPass): Vhost
    {
        if (empty($proxyPass->getRedirectTo()) || empty($proxyPass->getRedirectFrom())) {
            throw new \InvalidArgumentException('ProxyPass needs to be filled!');
        }
        $this->proxyPass = $proxyPass;
        
        return $this;
    }
    
    /**
     * @param string $fileName
     * @param array $vhosts
     * @param bool $force
     * @param bool $activate
     * @return bool
     */
    public static function writeMultipleVhosts(string $fileName, array $vhosts, bool $force, bool $activate = true)
    {
        $combinedVhost = "";
        foreach ($vhosts as $vhost) {
            $combinedVhost .= $vhost->build();
        }
    
        $filePath = sprintf('/etc/apache2/sites-available/%s.conf',
            $fileName
        );
    
        if (file_exists($filePath  && $force == false)) {
            return false;
        }
    
        $file = fopen($filePath, 'w+');
    
        if (!$file || !fwrite($file, $combinedVhost)) {
            return false;
        }
        
        if (!$activate === true) {
            return true;
        }
        
        $symlinkPath = sprintf('/etc/apache2/sites-enabled/%s.conf',
            $fileName
        );
    
        if (file_exists($symlinkPath)) {
            if (!$force) {
                exec('sudo /etc/init.d/apache2 reload');
            
                return false;
            }
        
            unlink($symlinkPath);
        }
    
        symlink($filePath, $symlinkPath);
        shell_exec('sudo /etc/init.d/apache2 reload');
    
        return true;
    }
}

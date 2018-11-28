<?php

namespace VhostManager;

class ProxyPass
{
    /** @var string */
    protected $redirectFrom = '';
    /** @var string */
    protected $redirectTo = '';
    /** @var string */
    protected $reverseRedirectFrom = '';
    /** @var string */
    protected $reverseRedirectTo = '';
    /** @var bool */
    protected $https = false;
    
    /**
     * @return string
     */
    public function getRedirectFrom(): string
    {
        return $this->redirectFrom;
    }
    
    /**
     * @param string $redirectFrom
     * @return ProxyPass
     */
    public function setRedirectFrom(string $redirectFrom): ProxyPass
    {
        $this->redirectFrom = $redirectFrom;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getRedirectTo(): string
    {
        return ($this->https ? 'https://' : 'http://') . $this->redirectTo;
    }
    
    /**
     * @param string $redirectTo
     * @return ProxyPass
     */
    public function setRedirectTo(string $redirectTo): ProxyPass
    {
        $this->redirectTo = $redirectTo;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getReverseRedirectFrom(): string
    {
        return $this->reverseRedirectFrom;
    }
    
    /**
     * @param string $redirectFrom
     * @return ProxyPass
     */
    public function setReverseRedirectFrom(string $redirectFrom): ProxyPass
    {
        $this->reverseRedirectFrom = $redirectFrom;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getReverseRedirectTo(): string
    {
        return ($this->https ? 'https://' : 'http://') . $this->reverseRedirectTo;
    }
    
    /**
     * @param string $redirectTo
     * @return ProxyPass
     */
    public function setReverseRedirectTo(string $redirectTo): ProxyPass
    {
        $this->reverseRedirectTo = $redirectTo;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isHttps(): bool
    {
        return $this->https;
    }
    
    /**
     * @param bool $https
     * @return ProxyPass
     */
    public function useHttps(bool $https): ProxyPass
    {
        $this->https = $https;
        
        return $this;
    }
    
    
}

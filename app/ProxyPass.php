<?php

namespace VhostManager;

class ProxyPass
{
    protected $redirectFrom = "";
    protected $redirectTo = "";
    
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
        return $this->redirectTo;
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
    
}

<?php declare(strict_types=1);

/******************************************************************************
 *
 * This file is part of ILIAS, a powerful learning management system.
 *
 * ILIAS is licensed with the GPL-3.0, you should have received a copy
 * of said license along with the source code.
 *
 * If this is not the case or you just want to try ILIAS, you'll find
 * us at:
 *      https://www.ilias.de
 *      https://github.com/ILIAS-eLearning
 *
 *****************************************************************************/

/**
 * Representation of an HTTP cookie
 *
 * @author Stefan Meyer <smeyer.ilias@gmx.de>
 *
 */
class ilCookie
{
    private $name = '';
    private $value = '';
    private $expire = 0;
    private $path = '';
    private $domain = '';
    private $secure = false;
    private $http_only = false;
    
    public function __construct($a_name)
    {
        $this->setName($a_name);
    }
    
    public function setName($a_name)
    {
        $this->name = $a_name;
    }
    
    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Currently no restriction on cookie length.
     * RFC 2965 suggests a minimum of 4096 bytes
     * @param string $a_value
     */
    public function setValue($a_value)
    {
        $this->value = $a_value;
    }
    
    public function getValue()
    {
        return $this->value;
    }
    
    public function setExpire($a_expire)
    {
        $this->expire = (int) $a_expire;
    }
    
    public function getExpire()
    {
        return $this->expire;
    }
    
    public function setPath($a_path)
    {
        $this->path = $a_path;
    }
    
    public function getPath()
    {
        return $this->path;
    }
    
    public function setDomain($a_domain)
    {
        $this->domain = $a_domain;
    }
    
    public function getDomain()
    {
        return $this->domain;
    }
    
    public function setSecure($a_status)
    {
        $this->secure = (bool) $a_status;
    }
    
    public function isSecure()
    {
        return $this->secure;
    }
    
    public function setHttpOnly($a_http_only)
    {
        $this->http_only = $a_http_only;
    }
    
    public function isHttpOnly()
    {
        return $this->http_only;
    }
}

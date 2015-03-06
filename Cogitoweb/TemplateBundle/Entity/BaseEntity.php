<?php

namespace Cogitoweb\TemplateBundle\Entity;

/**
 * Description of BaseEntity
 *
 * @author 2z
 */
class BaseEntity {
    
    public function __toString() {
        
        $out = '';
        
        if(method_exists($this, 'getName'))
        {
            $out .= ($out) ? ' - '.$this->getName() : $this->getName();
        }
        
        if(method_exists($this, 'getCode'))
        {
            $out .= $this->getCode();
        }
        
        if(!$out) {
            $out .= ($this->getId()) ? $this->getId() : '-';
        }
        
        return $out;
    }
    
    /**
     * Placeholder per i locales
     * 
     * @return array()
     */
    public function getLocales() {
        
        return array();
        
    }
    
    /**
     * fake set id to allow print hidden id field
     * 
     * @param integer
     * @return stdClass 
     */
    public function setId($id)
    {
        return $this;
    }
    
}

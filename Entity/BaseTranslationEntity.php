<?php

namespace Cogitoweb\TemplateBundle\Entity;

/**
 * Description of BaseEntity
 *
 * @author 2z
 */
class BaseTranslationEntity {
    
    
    public function getTranslatableId()
    {
        return $this->translatable->getId();
    }
    
    public function __toString() {
        
        $out = $this->getLocale();
        
        if(method_exists($this, 'getName'))
        {
            $out .= ($out) ? ' - '.$this->getName() : $this->getName();
        }

        
        return $out;
    }
    
    /**
     * 
     * @return boolean
     */
    public function getIsNew() {
        return ($this->getId()) ? false : true;
    }
    
    private $to_validate = false;
    
    /**
     * 
     * @return boolean
     */
    public function getToValidate() {
        return ($this->to_validate) ? $this->to_validate : !$this->getIsNew();
    }
    
    /**
     * 
     * @param boolean $v
     * @return \Cogitoweb\TemplateBundle\Entity\BaseTranslationEntity
     */
    public function setToValidate($v) {
        $this->to_validate = $v;
        
        return $this;
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

<?php

namespace Cogitoweb\TemplateBundle\Entity;

use JMS\Serializer\Annotation\Exclude;

/**
 * Description of BaseEntity
 *
 * @author 2z
 */
class BaseTranslatedEntity extends \Cogitoweb\TemplateBundle\Entity\BaseEntity {
    
    
    /**
     * @Exclude
     * @var type 
     */
    private $myDefaultLocale = 'it';
    
    /**
     * 
     * @return string
     */
    public function getMyDefaultLocale(){
      return $this->myDefaultLocale;
    }
  
    public function __toString() {
        
      if(!$this->getId()) return '-';               
      
      if($this->getName()){
          return $this->getName();
      }
      else{
        if($this->translate($this->getMyDefaultLocale())->getName()){
          return $this->translate($this->getMyDefaultLocale())->getName();
        }
        else{
          return (string) $this->getId();
        }
      }     
    }  
    
    /**
     * 2z -> fluid interface
     * 
     * @param mixed $locale the current locale
     */
    public function setCurrentLocaleFuild($locale)
    {
        $this->currentLocale = $locale;
        
        return $this;
    }
 
}

<?php

namespace Cogitoweb\TemplateBundle\Entity;

/**
 * Description of BaseTranslationEntity
 *
 * @author 2z
 */
class BaseTranslationEntity
{
	/**
	 * 
	 * @var boolean
	 */
	protected $toValidate = false;

	/**
	 * To string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$out = $this->getLocale();

		if (method_exists($this, 'getName')) {
			$out .= ($out) ? ' - ' . $this->getName() : $this->getName();
		}

		return $out;
	}

	/**
	 * Get translatable id
	 * 
	 * @return integer
	 */
	public function getTranslatableId()
	{
		return $this->translatable->getId();
	}

	/**
	 * Is entity new?
	 * 
	 * @return boolean
	 */
	public function getIsNew()
	{
		return ($this->getId()) ? false : true;
	}

	/**
	 * Get toValidate
	 * 
	 * @return boolean
	 */
	public function getToValidate()
	{
		return ($this->toValidate) ? $this->toValidate : !$this->getIsNew();
	}

	/**
	 * Set toValidate
	 * 
	 * @param  boolean $toValidate
	 * 
	 * @return BaseTranslationEntity
	 */
	public function setToValidate($toValidate)
	{
		$this->toValidate = $toValidate;

		return $this;
	}

	/**
	 * Fake setId to allow printing of hidden id field
	 *
	 * @param  integer
	 * 
	 * @return object
	 */
	public function setId($id)
	{
		return $this;
	}
}
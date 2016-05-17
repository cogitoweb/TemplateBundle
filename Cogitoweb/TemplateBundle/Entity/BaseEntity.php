<?php

namespace Cogitoweb\TemplateBundle\Entity;

/**
 * Description of BaseEntity
 *
 * @author 2z
 */
class BaseEntity
{
	/**
	 * To string
	 * 
	 * @return string
	 */
	public function __toString()
	{
		$out = '';

		if (method_exists($this, 'getName')) {
			$out .= ($out) ? ' - ' . $this->getName() : $this->getName();
		}

		if (method_exists($this, 'getCode')) {
			$out .= $this->getCode();
		}

		if (!$out) {
			$out .= ($this->getId()) ? $this->getId() : '-';
		}

		return $out;
	}

	/**
	 * Placeholder for locales
	 * 
	 * @return array
	 */
	public function getLocales()
	{
		return array();
	}

	/**
	 * Fake setId to allow printing of hidden id field
	 * 
	 * @param integer
	 * 
	 * @return objet
	 */
	public function setId($id)
	{
		return $this;
	}
}
<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Twig\Extension;

use Sonata\IntlBundle\Templating\Helper\DateTimeHelper;
use Sonata\IntlBundle\Twig\Extension\DateTimeExtension as SonataDateTimeExtension;

/**
 * Override SonataIntlBundle's DateTimeExtension to load default patterns from configuration
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class DateTimeExtension extends SonataDateTimeExtension
{
	/**
	 *
	 * @var string
	 */
	protected $datePattern, $timePattern, $datetimePattern;

	/**
	 * Constructor
	 * 
	 * @param DateTimeHelper $helper
	 * @param string|null    $datePattern
	 * @param string|null    $timePattern
	 * @param string|null    $datetimePattern
	 */
	public function __construct(DateTimeHelper $helper, $datePattern = null, $timePattern = null, $datetimePattern = null)
	{
		parent::__construct($helper);

		$this->datePattern     = $datePattern;
		$this->timePattern     = $timePattern;
		$this->datetimePattern = $datetimePattern;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getFilters()
	{
		return parent::getFilters();
	}

	/**
	 * {@inheritdoc}
	 */
	public function formatDate($date, $pattern = null, $locale = null, $timezone = null, $dateType = null)
	{
		$pattern = $pattern ?: $this->datePattern;

		return parent::formatDate($date, $pattern, $locale, $timezone, $dateType);
	}

	/**
	 * {@inheritdoc}
	 */
	public function formatTime($time, $pattern = null, $locale = null, $timezone = null, $timeType = null)
	{
		$pattern = $pattern ?: $this->timePattern;

		return parent::formatTime($time, $pattern, $locale, $timezone, $timeType);
	}

	/**
	 * {@inheritdoc}
	 */
	public function formatDatetime($time, $pattern = null, $locale = null, $timezone = null, $dateType = null, $timeType = null)
	{
		$pattern = $pattern ?: $this->datetimePattern;

		return parent::formatDatetime($time, $pattern, $locale, $timezone, $dateType, $timeType);
	}
}
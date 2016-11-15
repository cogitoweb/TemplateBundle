<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Form\Type;

use Sonata\CoreBundle\Date\MomentFormatConverter;
use Sonata\CoreBundle\Form\Type\DatePickerType as SonataDatePickerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Description of DatePickerType
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class DatePickerType extends SonataDatePickerType
{
	/**
	 *
	 * @var string
	 */
	protected $datePattern;

	/**
	 * Constructor
	 * 
     * @param MomentFormatConverter    $formatConverter
	 * @param TranslatorInterface|null $translator
	 * @param string|null              $datePattern
	 */
	public function __construct(MomentFormatConverter $formatConverter, TranslatorInterface $translator = null, $datePattern = null)
	{
		parent::__construct($formatConverter, $translator);

		$this->datePattern = $datePattern;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array_merge($this->getCommonDefaults(), [
            'dp_pick_time' => false,
            'format'       => $this->datePattern ?: DateType::DEFAULT_FORMAT,
        ]));
	}
}

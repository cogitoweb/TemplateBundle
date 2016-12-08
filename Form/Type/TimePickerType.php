<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Form\Type;

use Sonata\CoreBundle\Date\MomentFormatConverter;
use Sonata\CoreBundle\Form\Type\DateTimePickerType as SonataDateTimePickerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Description of TimePickerType
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class TimePickerType extends SonataDateTimePickerType
{
	/**
	 *
	 * @var string
	 */
	protected $timePattern;

	/**
	 * Constructor
	 * 
	 * @param MomentFormatConverter    $formatConverter
	 * @param TranslatorInterface|null $translator
	 * @param string|null              $timePattern
	 */
	public function __construct(MomentFormatConverter $formatConverter, TranslatorInterface $translator = null, $timePattern = null)
	{
		parent::__construct($formatConverter, $translator);

		$this->timePattern = $timePattern;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults(array_merge($this->getCommonDefaults(), [
			'dp_pick_date' => false,
			'format'       => $this->timePattern ?: DateTimeType::DEFAULT_TIME_FORMAT,
		]));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'sonata_type_time_picker';
	}
}
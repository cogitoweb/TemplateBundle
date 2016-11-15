<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cogitoweb\TemplateBundle\Form\Type;

use Sonata\CoreBundle\Form\Type\DateRangePickerType as SonataDateRangePickerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Description of DateRangePickerType
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class DateRangePickerType extends SonataDateRangePickerType
{
	/**
	 *
	 * @var string
	 */
	protected $datePattern;

	/**
	 * Constructor
	 * 
	 * @param TranslatorInterface|null $translator
	 * @param string|null              $datePattern
	 */
	public function __construct(TranslatorInterface $translator = null, $datePattern = null)
	{
		parent::__construct($translator);

		$this->datePattern = $datePattern;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		if (null !== $this->datePattern) {
			$fieldOptions = ['format' => $this->datePattern];
		} else {
			$fieldOptions = [];
		}

		$resolver->setDefaults([
			'field_options'       => $fieldOptions,
            'field_options_start' => [],
            'field_options_end'   => [],
            // NEXT_MAJOR: Remove ternary and keep 'Sonata\CoreBundle\Form\Type\DatePickerType'
            // (when requirement of Symfony is >= 2.8)
            'field_type'          => method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')
                ? 'Sonata\CoreBundle\Form\Type\DatePickerType'
                : 'sonata_type_date_picker'
		]);
	}
}

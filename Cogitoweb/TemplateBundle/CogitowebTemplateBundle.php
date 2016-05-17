<?php

namespace Cogitoweb\TemplateBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CogitowebTemplateBundle extends Bundle
{
	/**
	 * {@inheritdoc}
	 */
	public function getParent()
	{
		return 'SonataAdminBundle';
	}
}

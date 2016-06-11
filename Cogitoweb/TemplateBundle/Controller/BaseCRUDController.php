<?php

namespace Cogitoweb\TemplateBundle\Controller;

use Sonata\AdminBundle\Controller\CogitowebCRUDController;

use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Description of BaseCRUDController
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 * 
 * @deprecated since version 2.3. This class is kept for backward compatibility only. Use {@link Sonata\AdminBundle\Controller\CogitowebCRUDController} instead.
 */
class BaseCRUDController extends CogitowebCRUDController
{
	/**
	 * {@inheritdoc}
	 */
	protected function redirectTo($object)
	{
		$request = $this->getRequest();

		$url = false;

		if (null !== $request->get('btn_update_and_list')) {
			$url = $this->admin->generateUrl('list');
		}
		if (null !== $request->get('btn_create_and_list')) {
			$url = $this->admin->generateUrl('list');
		}

		if (null !== $request->get('btn_create_and_create')) {
			$params = array();
			if ($this->admin->hasActiveSubClass()) {
				$params['subclass'] = $request->get('subclass');
			}
			$url = $this->admin->generateUrl('create', $params);
		}

		if ($this->getRestMethod() === 'DELETE') {
			$url = $this->admin->generateUrl('list');
		}

		if (!$url) {
			// Cogitoweb: redirect to show by default
//			$url = $this->admin->generateObjectUrl('edit', $object);
			$url = $this->admin->generateObjectUrl('show', $object);
		}

		return new RedirectResponse($url);
	}
}
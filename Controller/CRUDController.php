<?php

namespace Cogitoweb\TemplateBundle\Controller;

use Cogitoweb\MultiLevelAdminBundle\Controller\CRUDController as CogitowebCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Description of CRUDController
 *
 * @author Daniele Artico <daniele.artico@cogitoweb.it>
 */
class CRUDController extends CogitowebCRUDController
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
//			foreach (array('edit', 'show') as $route) {
			foreach (array('show', 'edit') as $route) {
				if ($this->admin->hasRoute($route) && $this->admin->isGranted(strtoupper($route), $object)) {
					$url = $this->admin->generateObjectUrl($route, $object);
					break;
				}
			}
		}

		if (!$url) {
			$url = $this->admin->generateUrl('list');
		}

		return new RedirectResponse($url);
	}
}
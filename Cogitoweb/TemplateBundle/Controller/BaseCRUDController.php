<?php

namespace Cogitoweb\TemplateBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Patch;
use FOS\RestBundle\Controller\Annotations\Post;

use FOS\RestBundle\Controller\Annotations\NoRoute;

class BaseCRUDController extends CRUDController
{
    
    
    /**
     * redirect the user depend on this choice
     *
     * @param object $object
     *
     * @return Response
     */
    protected function redirectTo($object)
    {
        $url = false;

        if ($this->get('request')->get('btn_update_and_list')) {
            $url = $this->admin->generateUrl('list');
        }
        if ($this->get('request')->get('btn_create_and_list')) {
            $url = $this->admin->generateUrl('list');
        }

        if ($this->get('request')->get('btn_create_and_create')) {
            $params = array();
            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $this->get('request')->get('subclass');
            }
            $url = $this->admin->generateUrl('create', $params);
        }

        if (!$url) {
            $url = $this->admin->generateObjectUrl('show', $object);
        }

        return new RedirectResponse($url);
    }
    
    
    ////////////////////////////
    /////////////////////API SPECIFIC
    ////////////////////////////
    
    /**
     * return the Response object associated to the list action
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @View(serializerGroups={"adminlist","Default"})
     * @return Response
     */
    public function listAction()
    {
        
        if(method_exists($this->admin, 'isAPI') && $this->admin->isAPI())
        {
            if (false === $this->admin->isGranted('LIST')) {
                throw new AccessDeniedException();
            }

            return $this->admin->getDatagrid()->getResults();
        }
        
        return parent::listAction();
       
    }
    
    /**
     * return the Response object associated to the view action
     *
     * @Get
     * @param null $ix
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @View(serializerGroups={"adminshow","Default"})
     * @return Response
     */
    public function showAction($ix = null)
    {
        if(method_exists($this->admin, 'isAPI') && $this->admin->isAPI())
        {
            $id = $this->get('request')->get($this->admin->getIdParameter());
            $object = $this->admin->getObject($id);

            if (!$object) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
            }
        
            if (false === $this->admin->isGranted('VIEW', $object)) {
                throw new AccessDeniedException();
            }

            return $object;
        }

        return parent::showAction($ix);
        
    }
    
     /**
     * return the Response object associated to the create action
     * 
     * EXAMPLE
     * {"code":"apipackage","locLocation":{"identifiers":["2718"]},
     *  "depositperc":"0","enabled":"1","translations":{"enabled_locales":["it"],"it":{"name":"aa"}}}
     * 
     * @Post
     * @View(serializerGroups={"adminshow","Default"})
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @return Response
     */
    public function createAction()
    {
        
        if(method_exists($this->admin, 'isAPI') && $this->admin->isAPI())
        {
            if (false === $this->admin->isGranted('CREATE')) {
                throw new AccessDeniedException();
            }

            $object = $this->admin->getNewInstance();

            $this->admin->setSubject($object);

            /** @var $form \Symfony\Component\Form\Form */
            $form = $this->admin->getForm();
            $form->setData($object);
            
            // 2z -> normalizza i dati della request
            $form->bind($this->normalizeAPIData($this->get('request')));
            //$form->bind($this->get('request'));

            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid) {
                $this->admin->create($object);

                return $object;
            }
            else 
            {
                return array('result' => 'error', 'message' => 'validation error', 'details' => $this->getErrorMessages($form));
            }  
        }

        return parent::createAction();
    }
    
    /**
     * return the Response object associated to the edit action
     *
     * @Patch
     * @param mixed $ix
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @View(serializerGroups={"adminshow","Default"})
     * @return Response
     */
    public function editAction($ix = null)
    {
        
        if(method_exists($this->admin, 'isAPI') && $this->admin->isAPI())
        {
            $id = $this->get('request')->get($this->admin->getIdParameter());
            $object = $this->admin->getObject($id);

            if (!$object) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
            }
            
            if (false === $this->admin->isGranted('EDIT', $object)) {
                throw new AccessDeniedException();
            } 
            
            $this->admin->setSubject($object);

            /** @var $form \Symfony\Component\Form\Form */
            $form = $this->admin->getForm();
            $form->setData($object);

            // 2z -> normalizza i dati della request
            $form->bind($this->normalizeAPIData($this->get('request')));
            //$form->bind($this->get('request'));
            
            $isFormValid = $form->isValid();

            // persist if the form was valid and if in preview mode the preview was approved
            if ($isFormValid) {
                $this->admin->update($object);

                return $object;
            }
            else 
            {
                return array('result' => 'error', 'message' => 'validation error', 'details' => $this->getErrorMessages($form));
            }
        }

        return parent::editAction($ix);
    }
    
    /**
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @param mixed $ix
     *
     * @return Response|RedirectResponse
     */
    public function deleteAction($ix = null)
    {
        
        if(method_exists($this->admin, 'isAPI') && $this->admin->isAPI())
        {
            $id = $this->get('request')->get($this->admin->getIdParameter());
            $object = $this->admin->getObject($id);
            
            if (!$object) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
            }

            if (false === $this->admin->isGranted('DELETE', $object)) {
                throw new AccessDeniedException();
            }

            if ($this->getRestMethod() == 'DELETE') {

                try {
                    $this->admin->delete($object);

                    return array('result' => 'ok');

                } catch (ModelManagerException $e) {

                    return array('result' => 'error');
                }
                
            }
        }
        
        return parent::deleteAction($ix);        
    }
    
    /**
     * 
     * @Get
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException|\Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @param mixed $ix
     *
     * @return Response
     */
    public function historyAction($ix = null)
    {
        
        if(method_exists($this->admin, 'isAPI') && $this->admin->isAPI())
        {
            if (false === $this->admin->isGranted('EDIT')) {
                throw new AccessDeniedException();
            }

            $id = $this->get('request')->get($this->admin->getIdParameter());

            $object = $this->admin->getObject($id);

            if (!$object) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
            }

            $manager = $this->get('sonata.admin.audit.manager');

            if (!$manager->hasReader($this->admin->getClass())) {
                throw new NotFoundHttpException(sprintf('unable to find the audit reader for class : %s', $this->admin->getClass()));
            }

            $reader = $manager->getReader($this->admin->getClass());

            $revisions = $reader->findRevisions($this->admin->getClass(), $id);

            return $revisions;
        }
        

        return parent::historyAction($ix);
    }
    
    /**
     * 
     * @Get
     * @param null   $ix
     * @param string $revision
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    public function historyViewRevisionAction($ix = null, $revision = null)
    {
        
        if(method_exists($this->admin, 'isAPI') && $this->admin->isAPI())
        {
            if (false === $this->admin->isGranted('EDIT')) {
                throw new AccessDeniedException();
            }

            $id = $this->get('request')->get($this->admin->getIdParameter());

            $object = $this->admin->getObject($id);

            if (!$object) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
            }

            $manager = $this->get('sonata.admin.audit.manager');

            if (!$manager->hasReader($this->admin->getClass())) {
                throw new NotFoundHttpException(sprintf('unable to find the audit reader for class : %s', $this->admin->getClass()));
            }

            $reader = $manager->getReader($this->admin->getClass());

            // retrieve the revisioned object
            $object = $reader->find($this->admin->getClass(), $id, $revision);

            if (!$object) {
                throw new NotFoundHttpException(sprintf('unable to find the targeted object `%s` from the revision `%s` with classname : `%s`', $id, $revision, $this->admin->getClass()));
            }
            
            return $object;
        }

        return parent::historyViewRevisionAction($ix, $revision);
    }
    
    /**
     * espone in modo coerente gli errori della form
     * 
     * @param \Symfony\Component\Form\Form $form
     * @return array
     */
    public function getErrorMessages(\Symfony\Component\Form\Form $form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
                $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
    
    /**
     * Normalizza il formato di dati della request provenienti dalla API
     * 
     * @param Symfony\Component\HttpFoundation\Request $req
     * @return array
     */
    private function normalizeAPIData($req) {
        
        $normalizedData = $req->request->all();
        
        // aggiungo alla request uno unique id
        if(!$req->query->has('uniqid'))
        {
            $req->query->add(array('uniqid' => uniqid()));
        }

        return $normalizedData;
    }
    
    ////
    ///////////////////////////////// DISABILITATE DA API
    ////
    
    /**
     * return the Response object associated to the batch action
     * 
     * @NoRoute
     * @throws \RuntimeException
     * @return Response
     */
    public function batchAction()
    {
        return parent::batchAction();
    }
    
    /**
     * execute a batch delete
     * 
     * @NoRoute
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @param \Sonata\AdminBundle\Datagrid\ProxyQueryInterface $query
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function batchActionDelete(ProxyQueryInterface $query)
    {
        return parent::batchActionDelete($query);
    }
    
    /**
     * return the Response object associated to the acl action
     *
     * @NoRoute
     * @param null $id
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    public function aclAction($id = null)
    {
        return parent::aclAction($id);
    }
    
    /**
     * @NoRoute
     * @param Request $request
     *
     * @throws \RuntimeException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     *
     * @return Response
     */
    public function exportAction(Request $request)
    {
        return parent::exportAction($request);
    }

}

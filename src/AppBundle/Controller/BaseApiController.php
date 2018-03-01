<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\ObjectConstructionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationList;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class BaseApiController extends FOSRestController implements ClassResourceInterface
{
    protected $entityClass;
    protected $cgetContext = ['cget'];
    protected $getContext = ['get'];
    protected $postContext = ['post'];
    protected $putContext = ['put'];

    protected function applyContext($view, $contextArray)
    {
        $context = new Context();
        $context->setSerializeNull(true);
        foreach ($contextArray as $item) {
            $context->addGroup($item);
        }
        $view->setContext($context);

        return $view;
    }

    protected function mergePost(Request $request, $usedContext = null)
    {
        $context = new DeserializationContext();
        if ($usedContext) {
            $context->setGroups($usedContext);
        } else {
            $context->setGroups($this->postContext);
        }
        $entity = $this->get('jms_serializer')->deserialize($request->getContent(), $this->entityClass, 'json', $context);

        return $entity;
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Put a resource",
     *  statusCodes={
     *      200="Returned when successful"
     *  },
     *  input={
     *      "class"="AppBundle\Entity\User",
     *      "groups"={"put-user"}
     *  },
     *  output={
     *      "class"="AppBundle\Entity\User",
     *      "groups"={"get-user"}
     *  }
     * )
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function putAction(Request $request, $id)
    {
        try {
            $data = $this->mergePost($request, $this->putContext);

            if (!$data) {
                return $this->handleView($this->view('Not Found', Response::HTTP_NOT_FOUND));
            }

            if ($data->getId() !== (int) $id) {
                return $this->returnResponse('wrong id', 403);
            }

            $em = $this->getDoctrine()->getManager();
            $data = $em->merge($data);

            if ($invalid = $this->validateObject($data, $this->putContext)) {
                return $invalid;
            }

            $em->flush();

            $view = $this->view($data, 200);
            $this->applyContext($view, $this->getContext);

            return $this->handleView($view);
        } catch (ObjectConstructionException $e) {
            return $this->handleView($this->view('Not Found', Response::HTTP_NOT_FOUND));
        }
    }

    /**
     * @var Request
     *
     * @return View|array
     */
    public function postAction(Request $request)
    {
        $entity = $this->mergePost($request);

        if ($invalid = $this->validateObject($entity, $this->postContext)) {
            return $invalid;
        }

        $this->getDoctrine()->getManager()->persist($entity);
        $this->getDoctrine()->getManager()->flush();

        $view = $this->view($entity, 200);
        $this->applyContext($view, $this->getContext);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Delete single resource",
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     */
    public function deleteAction(Request $request, $id)
    {
        $data = $this->getDoctrine()->getRepository($this->entityClass)->find($id);

        if (!$data) {
            return $this->handleView($this->view('Not Found', Response::HTTP_NOT_FOUND));
        }

        $this->getDoctrine()->getManager()->remove($data);
        $this->getDoctrine()->getManager()->flush();

        $view = $this->view($this->getUser(), 200);
        $this->applyContext($view, ['get-me']);

        return new JsonResponse(null, 200);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Get single resource",
     *  statusCodes={
     *      200="Returned when successful"
     *  }
     * )
     */
    public function getAction(Request $request, $id)
    {
        $data = $this->getDoctrine()->getRepository($this->entityClass)->find($id);

        if (!$data) {
            return $this->handleView($this->view('Not Found', Response::HTTP_NOT_FOUND));
        }

        $view = $this->view($data, 200);

        $this->applyContext($view, $this->getContext);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Get collection of resources",
     *  filters={
     *      {"name"="limit", "dataType"="integer", "description"="Limit the number of results", "default"="10"},
     *      {"name"="page", "dataType"="integer", "description"="PageNr", "default"="1"},
     *      {"name"="s", "dataType"="string", "description"=".", "default"="createdAt"},
     *      {"name"="o", "dataType"="string", "description"=".", "default"="DESC"}
     *  }
     * )
     */
    public function cgetAction(Request $request)
    {
        $query = $this->createCgetQb($request);

        $pagination = $this->createPaginator($query, $request);

        $result = array(
            'data' => $pagination->getItems(),
            'meta' => $pagination->getPaginationData(), );

        $view = $this->view($result, 200);

        $this->applyContext($view, $this->cgetContext);

        return $this->handleView($view);
    }

    /**
     *
     * @param Request $request
     *
     * @return mixed
     */
    protected function createCgetQb(Request $request)
    {
        $sort_property = $request->query->getAlnum('s', 'createdAt');
        $sort_order = $request->query->getAlnum('o', 'DESC');

        $query = $this->getDoctrine()->getRepository($this->entityClass)->createQueryBuilder('x');

        if (('createdAt' !== $sort_property) || property_exists($this->entityClass, 'createdAt')) {
            $query->addOrderBy('x.'.$sort_property, $sort_order);
        }

        return $query;
    }

    protected function createPaginator($query, $request)
    {
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);
        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $query,
            $page,
            $limit
        );

        return $pagination;
    }

    protected function returnResponse($data, $responseCode = 200, $errorCode = null)
    {
        if (200 === $responseCode) {
            return $data;
        } else {
            return new JsonResponse(['error' => true, 'errorType' => $errorCode, 'errorMessage' => $data], $responseCode);
        }
    }

    protected function validateObject($object, $groups = null)
    {
        $validator = $this->get('validator');
        $errors = $validator->validate($object, null, $groups);

        if (count($errors) > 0) {
            return $this->returnValidationErrors($errors);
        }

        return false;
    }

    private function returnValidationErrors(ConstraintViolationList $data, $responseCode = 200)
    {
        $errors = [];
        foreach ($data as $violation) {
            $errors[$violation->getPropertyPath()] = [];
            $errors[$violation->getPropertyPath()]['message'] = $violation->getMessage();
        }

        return new JsonResponse(['error' => true, 'errorType' => 'validation_error', 'errorMessage' => $errors], $responseCode);
    }
}

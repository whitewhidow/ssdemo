<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use AppBundle\Entity\User;

/**
 * Class UserController.
 *
 * @RouteResource("User")
 */
class UserController extends BaseApiController
{
    protected $entityClass = User::class;
    protected $cgetContext = ['cget-user'];
    protected $getContext = ['get-user'];
    protected $postContext = ['post-user'];
    protected $putContext = ['put-user'];



    /**
     * @ApiDoc(
     *  resource=true,
     *  description="Create a resource",
     *  statusCodes={
     *      200="Returned when successful"
     *  },
     *  input={
     *      "class"="AppBundle\Entity\User",
     *      "groups"={"post-user"}
     *  },
     *  output={
     *      "class"="AppBundle\Entity\User",
     *      "groups"={"get-user"}
     *  }
     * )
     *
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function postAction(Request $request)
    {

        return parent::postAction($request);

    }
}

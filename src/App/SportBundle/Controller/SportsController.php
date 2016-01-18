<?php

namespace App\SportBundle\Controller;

use App\SportBundle\Entity\Sport;
use App\Util\Controller\AbstractRestController as Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\Serializer\SerializerBuilder;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Sports resource.
 *
 * @author Robin Chalas <rchalas@sutunam.com>
 */
class SportsController extends Controller
{
    /**
     * Get sports.
     *
     * @Rest\Get("/sports")
     * @ApiDoc(
     *   section="Sport",
     * 	 resource=true,
     * 	 statusCodes={
     * 	   200="OK",
     * 	   401="Unauthorized"
     * 	 },
     * )
     * @Rest\View
     *
     * @return array
     */
    public function getListAction()
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('AppSportBundle:Sport');
        $entities = $repo->findBy(['isActive' => 1]);

        return $entities;
    }

    /**
     * Creates a new Sport entity.
     *
     * @Rest\Post("/sports")
     * @Rest\RequestParam(name="name", requirements="[^/]+", allowBlank=false, description="Name")
     * @Rest\RequestParam(name="isActive", requirements="true|false", nullable=true, description="Active")
     * @Rest\RequestParam(name="icon", requirements="[^/]+", nullable=true, description="Icon")
     * @ApiDoc(
     *   section="Sport",
     * 	 resource=true,
     * 	 statusCodes={
     * 	   201="Created",
     * 	   400="Bad Request",
     * 	   401="Unauthorized",
     * 	 },
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return JsonResponse
     */
    public function createAction(ParamFetcher $paramFetcher)
    {
        $em = $this->getEntityManager();
        $repo = $em->getRepository('AppSportBundle:Sport');
        $sport = ['name' => $paramFetcher->get('name')];
        $isActive = $paramFetcher->get('isActive');
        $icon = $paramFetcher->get('icon');

        $repo->findOneByAndFail($sport);

        if (true == $isActive) {
            $sport['isActive'] = true;
        }

        if ($icon) {
            $sport->setIcon($icon);
        }

        return new Response($this->serialize($repo->create($sport)), 201);
    }

    /**
     * Get Sport entity.
     *
     * @Rest\Get("/sports/{id}")
     *
     * @ApiDoc(
     *   section="Sport",
     * 	 resource=true,
     * 	 statusCodes={
     * 	   200="OK",
     * 	   401="Unauthorized"
     * 	 },
     * )
     *
     * @param int $id Sport entity
     *
     * @return array
     */
    public function getAction($id)
    {
        $em = $this->getEntityManager();
        $entity = $em->getRepository('AppSportBundle:Sport')->findOrFail($id);

        return $entity;
    }

    /**
     * Update an existing entity.
     *
     * @Rest\Patch("/sports/{id}")
     * @Rest\RequestParam(name="name", requirements="[^/]+", nullable=true, description="Name")
     * @Rest\RequestParam(name="isActive", requirements="[^/]+", nullable=true, description="Name")
     * @Rest\RequestParam(name="icon", requirements="[^/]+", nullable=true, description="Name")
     * @ApiDoc(
     *   section="Sport",
     * 	 resource=true,
     * 	 statusCodes={
     * 	   200="OK",
     * 	   401="Unauthorized"
     * 	 },
     * )
     *
     * @param int          $id
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function updateAction($id, ParamFetcher $paramFetcher)
    {
        $repo = $this
            ->getEntityManager()
            ->getRepository('AppSportBundle:Sport')
        ;
        $changes = [];
        $entity = $repo->findOrFail($id);
        $name = $paramFetcher->get('name');
        $isActive = $paramFetcher->get('isActive');

        if ($isActive) {
            $changes['isActive'] = 'false' == $isActive ? false : true;
        }

        if ($name) {
            if ($name == $entity->getName()) {
                return $entity;
            }
            
            $changes['name'] = $name;
        }

        $repo->findOneByAndFail($changes);

        return $repo->update($entity, $changes);;
    }

    /**
     * Delete a Sport entity.
     *
     * @Rest\Delete("/sports/{id}")
     * @ApiDoc(
     *   section="Category",
     * 	 resource=true,
     * 	 statusCodes={
     * 	   200="OK",
     * 	   401="Unauthorized"
     * 	 },
     * )
     *
     * @param int $id Sport entity
     *
     * @return array
     */
    public function deleteCategoryAction($id)
    {
        $repo = $this
            ->getEntityManager()
            ->getRepository('AppSportBundle:Sport')
        ;

        $sport = $repo->findOrFail($id);
        $repo->delete($sport);

        return ['success' => true];
    }

    /**
     * Get Icon image from Sport entity.
     *
     * @Rest\Get("/sports/{sport}/icon")
     * @ApiDoc(
     *   section="Sport",
     * 	 resource=true,
     * 	 statusCodes={
     * 	   200="OK",
     * 	   401="Unauthorized"
     * 	 },
     * )
     *
     * @param string|int $sport Sport entity
     *
     * @return Response
     */
    public function getIconBySportAction($sport)
    {
        return $this->forward('AppAdminBundle:SportAdmin:showIcon', array(
            'sport'          => $sport,
            '_sonata_admin'  => 'sonata.admin.sports',
        ));
    }
}
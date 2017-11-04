<?php

namespace AppBundle\Controller;

use AppBundle\Exception\APIException;
use AppBundle\Service\PersonService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonController extends Controller
{
    /**
     * @var PersonService
     */
    protected $personService;

    /**
     * @Route("/people", name="create_person", methods={"POST"})
     */
    public function createAction(Request $request)
    {
        $name = $request->get('name');
        $birthday = $request->get('birthday');

        try {
            $this->getPersonService()->createPerson($name, $birthday);
        } catch (APIException $e) {
            return $this->json([
                'success' => false,
                'error' => [
                    'code' => $e->getErrorCode(),
                    'message' => $e->getMessage(),
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json(['success' => true]);
    }

    /**
     * @Route("/people/dummies", name="generate_people", methods={"POST"})
     */
    public function dummiesAction(Request $request)
    {
        $total = $request->request->getInt('total', 100);

        $this->getPersonService()->loadDummies($total);
        return $this->json(['success' => true]);
    }

    /**
     * @Route("/people", name="get_people", methods={"GET"})
     */
    public function getAllAction()
    {
        $people = $this->getPersonService()->getAll();
        return $this->json([
            'success' => true,
            'data' => [
                'total' => count($people),
                'results' => $people,
            ],
        ]);
    }

    /**
     * @Route("/people/{id}", name="get_person", methods={"GET"})
     */
    public function getOneAction($id)
    {
        $people = $this->getPersonService()->getOne($id);
        if (!$people) {
            return $this->json([
                'success' => false,
                'error' => [
                    'code' => 'not_found',
                    'message' => sprintf('Not found people with id[%s]', $id),
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data' => $people,
        ]);
    }

    /**
     * @Route("/people/{id}/send", name="send_person", methods={"POST"})
     */
    public function sendAction($id)
    {
        $sent = $this->getPersonService()->send($id);
        if (!$sent) {
            return $this->json([
                'success' => false,
                'error' => [
                    'code' => 'not_found',
                    'message' => sprintf('Not found people with id[%s]', $id),
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['success' => true]);
    }

    /**
     * @return PersonService
     */
    public function getPersonService()
    {
        return $this->personService;
    }

    /**
     * @param PersonService $personService
     * @return PersonController
     */
    public function setPersonService($personService)
    {
        $this->personService = $personService;
        return $this;
    }
}

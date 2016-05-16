<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\TasksManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    public function indexAction()
    {

        $dummyProfileWithReviewConfirmationRequest = new BusinessProfile();
        $dummyProfileWithProfileCreateRequest = new BusinessProfile();
        $dummyProfileWithProfileUpdateRequest = new BusinessProfile();
        $dummyProfileWithProfileCloseRequest = new BusinessProfile();

        $em = $this->getDoctrine()->getManager();

        $em->persist($dummyProfileWithReviewConfirmationRequest);
        $em->persist($dummyProfileWithProfileCreateRequest);
        $em->persist($dummyProfileWithProfileUpdateRequest);
        $em->persist($dummyProfileWithProfileCloseRequest);

        $em->flush();

        /** @var TasksManager $tasksManager */
        $tasksManager = $this->get('domain_business.manager.tasks');

        $response = $tasksManager->createBusinessReviewConfirmationRequest($dummyProfileWithReviewConfirmationRequest);
        var_dump($response);

        $tasksManager->createNewProfileConfirmationRequest($dummyProfileWithProfileCreateRequest);
        $tasksManager->createUpdateProfileConfirmationRequest($dummyProfileWithProfileUpdateRequest);
        $tasksManager->createCloseProfileConfirmationRequest($dummyProfileWithProfileCloseRequest);

        return $this->render('DomainBusinessBundle:Default:index.html.twig');
    }
}

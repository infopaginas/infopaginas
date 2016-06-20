<?php

namespace Domain\SiteBundle\Security\Core\User;

use Doctrine\ORM\EntityManager;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Oxa\Sonata\UserBundle\Entity\Group;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    protected $userManager;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @param UserInterface $user
     * @param UserResponseInterface $response
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $email = $response->getEmail();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array('email' => $email))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($email);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * @param UserResponseInterface $response
     * @return \FOS\UserBundle\Model\UserInterface|UserInterface
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        $email = $response->getEmail();

        $user = $this->userManager->findUserBy([
            'email' => $email
        ]);

        //when the user is registrating
        if ($user === null) {
            $service = $response->getResourceOwner()->getName();

            $setter = 'set'.ucfirst($service);
            $setter_id = $setter.'Id';
            $setter_token = $setter.'AccessToken';

            // create new user here
            $user = $this->userManager->createUser();
            $user->$setter_id($email);
            $user->$setter_token($response->getAccessToken());

            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($email);
            $user->setEmail($email);
            $user->setPlainPassword($email);
            $user->setEnabled(true);
            $user->setFirstName($response->getFirstName());
            $user->setLastName($response->getLastName());

            $group = $this->em->getRepository('OxaSonataUserBundle:Group')->findOneBy(['code' => Group::CODE_CONSUMER]);

            $user->addGroup($group);
            $user->setRole($group);

            $this->userManager->updateUser($user);

            return $user;
        }

        //if user exists - go with the HWIOAuth way
        $user = $this->userManager->findUserBy([
            'email' => $email,
        ]);

        if (null === $user || null === $email) {
            throw new AccountNotLinkedException(sprintf("User '%s' not found.", $email));
        }

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }
}

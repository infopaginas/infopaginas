<?php

namespace Oxa\Sonata\UserBundle\Security\Core\User;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\Exception\AccountNotLinkedException;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Oxa\Sonata\UserBundle\Entity\Group;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{
    /**
     * @var UserManagerInterface
     */
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
        $setter = 'set' . ucfirst($service);
        $setterId = $setter . 'Id';
        $setterToken = $setter . 'AccessToken';

        $previousUser = $this->userManager->findUserBy(['email' => $email]);

        //we "disconnect" previously connected users
        if ($previousUser !== null) {
            $previousUser->$setterId(null);
            $previousUser->$setterToken(null);

            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setterId($email);
        $user->$setterToken($response->getAccessToken());

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

            $setter = 'set' . ucfirst($service);
            $setterId = $setter . 'Id';
            $setterToken = $setter . 'AccessToken';

            // create new user here
            $user = $this->userManager->createUser();
            $user->$setterId($email);
            $user->$setterToken($response->getAccessToken());

            //I have set all requested data with the user's username
            //modify here with relevant data
            $user->setUsername($email);
            $user->setEmail($email);
            $user->setPlainPassword($email);
            $user->setEnabled(true);
            $user->setFirstName($response->getFirstName());
            $user->setLastName($response->getLastName());

            $group = $this->getGroupsRepository()->findOneBy(['code' => Group::CODE_CONSUMER]);

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

    /**
     * @return EntityRepository
     */
    private function getGroupsRepository() : EntityRepository
    {
        return $this->em->getRepository(Group::class);
    }
}

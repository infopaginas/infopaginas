<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.06.16
 * Time: 12:49
 */

namespace Domain\SiteBundle\Mailer;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use FOS\UserBundle\Model\UserInterface;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\ConfigBundle\Service\Config;
use Oxa\Sonata\UserBundle\Entity\User;
use \Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class Mailer
 * @package Domain\SiteBundle\Mailer
 */
class Mailer
{
    const REGISTRATION_MAIL_SUBJECT   = 'New account created!';
    const RESET_PASSWORD_MAIL_SUBJECT = 'Reset password';

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var Config */
    private $configService;

    /** @var Router */
    private $router;

    /**
     * Mailer constructor.
     * @param \Swift_Mailer $mailer
     * @param Config $configService
     * @param Router $router
     */
    public function __construct(\Swift_Mailer $mailer, Config $configService, Router $router = null)
    {
        $this->mailer        = $mailer;
        $this->configService = $configService;
        $this->router        = $router;
    }

    /**
     * @param UserInterface $user
     */
    public function sendRegistrationCompleteEmailMessage(UserInterface $user)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_REGISTRATION_TEMPLATE);
        $message = str_replace('{NAME}', $user->getFirstName() . ' ' . $user->getLastName(), $message);
        $message = str_replace('{EMAIL}', $user->getEmail(), $message);

        $contentType = 'text/html';

        $this->send($user->getEmail(), self::REGISTRATION_MAIL_SUBJECT, $message, $contentType);
    }

    /**
     * @param UserInterface $user
     * @param $password
     */
    public function sendMerchantRegisteredEmailMessage(UserInterface $user, $password)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_NEW_MERCHANT_TEMPLATE);
        $message = str_replace('{NAME}', $user->getFirstName() . ' ' . $user->getLastName(), $message);
        $message = str_replace('{EMAIL}', $user->getEmail(), $message);
        $message = str_replace('{PASSWORD}', $password, $message);

        $contentType = 'text/html';

        $this->send($user->getEmail(), self::REGISTRATION_MAIL_SUBJECT, $message, $contentType);
    }

    /**
     * @param UserInterface $user
     */
    public function sendResetPasswordEmailMessage(UserInterface $user)
    {
        $url = $this->getRouter()->generate(
            'fos_user_resetting_reset',
            ['token' => $user->getConfirmationToken()],
            true
        );

        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_RESET_PASSWORD_TEMPLATE);
        $message = str_replace('{NAME}', $user->getFirstName() . ' ' . $user->getLastName(), $message);
        $message = str_replace('{LINK}', $url, $message);

        $contentType = 'text/html';

        $this->send($user->getEmail(), self::RESET_PASSWORD_MAIL_SUBJECT, $message, $contentType);
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string $reason
     */
    public function sendBusinessProfileCreateRejectEmailMessage(BusinessProfile $businessProfile, string $reason)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_CHANGE_WAS_REJECTED);
        $message = str_replace('{REASON}', $reason, $message);

        $contentType = 'text/html';

        $subject = 'BUSINESS PROFILE CREATE [' . $businessProfile->getName() . '] - Rejected';

        $email = $businessProfile->getUser() !== null ? $businessProfile->getUser()->getEmail()
            : $businessProfile->getEmail();

        if ($email !== null) {
            $this->send($email, $subject, $message, $contentType);
        }
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string $reason
     */
    public function sendBusinessProfileUpdateRejectEmailMessage(BusinessProfile $businessProfile, string $reason)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_CHANGE_WAS_REJECTED);
        $message = str_replace('{REASON}', $reason, $message);

        $contentType = 'text/html';

        $subject = 'BUSINESS PROFILE UPDATE [' . $businessProfile->getName() . '] - Rejected';

        $email = $businessProfile->getUser() !== null ? $businessProfile->getUser()->getEmail()
            : $businessProfile->getEmail();

        if ($email !== null) {
            $this->send($email, $subject, $message, $contentType);
        }
    }

    /**
     * @param BusinessProfile $businessProfile
     * @param string $reason
     */
    public function sendBusinessProfileCloseRejectEmailMessage(BusinessProfile $businessProfile, string $reason)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_CHANGE_WAS_REJECTED);
        $message = str_replace('{REASON}', $reason, $message);

        $contentType = 'text/html';

        $subject = 'BUSINESS PROFILE CLOSE [' . $businessProfile->getName() . '] - Rejected';

        $email = $businessProfile->getUser() !== null ? $businessProfile->getUser()->getEmail()
            : $businessProfile->getEmail();

        if ($email !== null) {
            $this->send($email, $subject, $message, $contentType);
        }
    }

    /**
     * @param BusinessReview $review
     * @param string $reason
     */
    public function sendBusinessProfileReviewRejectEmailMessage(BusinessReview $review, string $reason)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_CHANGE_WAS_REJECTED);
        $message = str_replace('{REASON}', $reason, $message);

        $contentType = 'text/html';

        $subject = 'BUSINESS PROFILE REVIEW [' . $review->getBusinessProfile()->getName() . '] - Rejected';

        $this->send($review->getUser()->getEmail(), $subject, $message, $contentType);
    }

    /**
     * @param string $reason
     * @param User[] $users
     */
    public function sendYoutubeTokenErrorEmailMessage($reason, $users)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::YOUTUBE_ERROR_EMAIL_TEMPLATE);
        $link    = $this->getRouter()->generate(
            'oxa_youtube_oauth_notify',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $message = str_replace('{REASON}', $reason, $message);
        $message = str_replace('{LINK}', $link, $message);

        $contentType = 'text/html';

        $subject = 'Youtube token error';

        $emails = [];

        foreach ($users as $user) {
            $email = $user->getEmail();

            if ($email and filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }

        if ($emails) {
            $this->send($emails, $subject, $message, $contentType);
        }
    }

    /**
     * @param mixed $toEmail
     * @param string $subject
     * @param string $body
     * @param string $contentType
     */
    protected function send($toEmail, string $subject, string $body, string $contentType = 'text/plain')
    {
        $fromEmail = $this->getConfigService()->getValue(ConfigInterface::DEFAULT_EMAIL_ADDRESS);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($fromEmail)
            ->setTo($toEmail)
            ->setBody($body)
            ->setContentType($contentType)
        ;

        $this->mailer->send($message);
    }

    /**
     * @return Config
     */
    private function getConfigService() : Config
    {
        return $this->configService;
    }

    /**
     * @return \Swift_Mailer
     */
    private function getMailer()
    {
        return $this->mailer;
    }

    /**
     * @return Router
     */
    private function getRouter() : Router
    {
        return $this->router;
    }
}

<?php

namespace Domain\SiteBundle\Mailer;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Entity\Task;
use Domain\ReportBundle\Entity\ExportReport;
use FOS\UserBundle\Model\UserInterface;
use Oxa\ConfigBundle\Model\ConfigInterface;
use Oxa\ConfigBundle\Service\Config;
use Oxa\Sonata\UserBundle\Entity\User;
use \Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class Mailer
 * @package Domain\SiteBundle\Mailer
 */
class Mailer
{
    const CONTENT_TYPE_HTML = 'text/html';
    const REGISTRATION_MAIL_SUBJECT   = 'New account created!';
    const RESET_PASSWORD_MAIL_SUBJECT = 'Reset password';

    /** @var \Swift_Mailer */
    private $mailer;

    /** @var Config */
    private $configService;

    /** @var Router */
    private $router;

    /**
     * @var EngineInterface $templateEngine
     */
    protected $templateEngine;

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
     * @param EngineInterface $service
     */
    public function setTemplateEngine(EngineInterface $service)
    {
        $this->templateEngine = $service;
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
     * @param Task   $task
     * @param string $reason
     */
    public function sendBusinessProfileClaimRejectEmailMessage($task, $reason)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_CHANGE_WAS_REJECTED);
        $message = str_replace('{REASON}', $reason, $message);

        $contentType = 'text/html';

        $subject = 'BUSINESS PROFILE CLAIM [' . $task->getBusinessProfile()->getName() . '] - Rejected';

        $this->send($task->getCreatedUser()->getEmail(), $subject, $message, $contentType);
    }

    /**
     * @param ExportReport $export
     */
    public function sendReportExportProcessedEmailMessage($export)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::MAIL_REPORT_EXPORT_PROCESSED);

        $link = $this->router->generate(
            'admin_domain_report_exportreport_show',
            [
                'id' => $export->getId(),
            ],
            Router::ABSOLUTE_URL
        );

        $message = str_replace('{LINK}', $link, $message);

        $contentType = 'text/html';

        $subject = 'REPORT ' . strtoupper($export->getType()) . ' ' . strtoupper($export->getStatus());

        $this->send($export->getUser()->getEmail(), $subject, $message, $contentType);
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
     * @param string $reason
     * @param User[] $users
     */
    public function sendArticlesApiErrorEmailMessage($reason, $users)
    {
        $message = $this->getConfigService()->getValue(ConfigInterface::ARTICLE_API_ERROR_EMAIL_TEMPLATE);

        $message = str_replace('{REASON}', $reason, $message);

        $contentType = 'text/html';

        $subject = 'Articles API error';

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
     * @param array $data
     */
    public function sendFeedbackEmailMessage($data)
    {
        $email   = $this->getConfigService()->getValue(ConfigInterface::FEEDBACK_EMAIL_ADDRESS);
        $subject = $this->getConfigService()->getValue(ConfigInterface::FEEDBACK_EMAIL_SUBJECT);

        if ($email and $subject) {
            $message = $this->templateEngine->render(
                'OxaConfigBundle:Fixtures:mail_feedback.html.twig',
                $data
            );

            $this->send($email, $subject, $message, self::CONTENT_TYPE_HTML);
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

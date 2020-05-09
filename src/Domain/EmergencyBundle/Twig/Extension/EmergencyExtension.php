<?php

namespace Domain\EmergencyBundle\Twig\Extension;

use Symfony\Component\HttpFoundation\Session\Session;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class EmergencyExtension
 * @package Domain\EmergencyBundle\Twig\Extension
 */
class EmergencyExtension extends AbstractExtension
{
    const EMERGENCY_POP_UP_FREQUENCY = 86400;   // delay between emergency pop up displaying in seconds

    /** @var Session */
    private $session;

    /**
     * @param Session $session
     */
    public function setSession(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('get_emergency_pop_up_allowed', [$this, 'getEmergencyPopupAllowed']),
        ];
    }

    /**
     * @return bool
     */
    public function getEmergencyPopupAllowed()
    {
        $lastDisplayed = $this->session->get('emergencyPopupLastDisplayed', 0);

        $now = new \DateTime();
        $currentTimestamp = $now->getTimestamp();

        $popupAllowed = false;

        if (!$lastDisplayed or $currentTimestamp - $lastDisplayed > self::EMERGENCY_POP_UP_FREQUENCY) {
            $this->session->set('emergencyPopupLastDisplayed', $currentTimestamp);
            $popupAllowed = true;
        }

        return $popupAllowed;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'emergency_extension';
    }
}

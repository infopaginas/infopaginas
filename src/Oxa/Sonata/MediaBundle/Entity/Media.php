<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Oxa\Sonata\MediaBundle\Entity;

use Oxa\Sonata\AdminBundle\Model\DefaultEntityInterface;
use Oxa\Sonata\AdminBundle\Util\Traits\DefaultEntityTrait;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\MediaBundle\Entity\BaseMedia as BaseMedia;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Table(name="media__media")
 * @ORM\Entity(repositoryClass="Oxa\Sonata\MediaBundle\Repository\MediaRepository")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\HasLifecycleCallbacks
 */
class Media extends BaseMedia implements OxaMediaInterface, DefaultEntityInterface
{
    use DefaultEntityTrait;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Available contexts
     *
     * @return array
     */
    public static function getContexts() : array
    {
        return [
            self::CONTEXT_DEFAULT                   => self::CONTEXT_DEFAULT,
            self::CONTEXT_BUSINESS_PROFILE_IMAGES   => self::CONTEXT_BUSINESS_PROFILE_IMAGES,
            self::CONTEXT_BUSINESS_PROFILE_LOGO     => self::CONTEXT_BUSINESS_PROFILE_LOGO,
            self::CONTEXT_BANNER                    => self::CONTEXT_BANNER,
            self::CONTEXT_PAGE                      => self::CONTEXT_PAGE,
        ];
    }

    /**
     * Available providers
     *
     * @return array
     */
    public static function getProviders() : array
    {
        return [
            self::PROVIDER_IMAGE    => self::PROVIDER_IMAGE,
            self::PROVIDER_FILE     => self::PROVIDER_FILE,
        ];
    }
}


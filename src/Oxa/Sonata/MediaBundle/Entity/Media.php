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
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", nullable=true)
     * @Assert\Valid()
     */
    protected $url;

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
     * Sets createdAt.
     *
     * @param  \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt = null)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Sets updatedAt.
     *
     * @param  \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt = null)
    {
        $this->updatedAt = $updatedAt;

        return $this;
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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->galleryHasMedias = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Media
     */
    public function setUrl($url)
    {
        try {
            $file = file_get_contents($url);

            if ($file) {
                $urlParts = explode('/', $url);
                $fileName = array_pop($urlParts);

                if (!empty($fileName)) {
                    $fullFilePath = 'uploads/' . $fileName;

                    file_put_contents($fullFilePath, $file);

                    $this->setBinaryContent($fullFilePath);
                }
            }
        } catch (\Exception $e) {
        }

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}

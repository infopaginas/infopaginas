<?php


namespace Domain\BusinessBundle\VO;

use Domain\BusinessBundle\DBAL\Types\UrlType;
use Symfony\Component\Validator\Constraints as Assert;

class Url
{
    /**
     * @var string
     * @Assert\Url()
     * @Assert\Length(max=1000, maxMessage="business_profile.max_length")
     */
    private $url;

    /**
     * @var bool
     */
    private $relNoFollow = true;

    /**
     * @var bool
     */
    private $relNoOpener = true;

    /**
     * @var bool
     */
    private $relNoReferrer = true;

    /**
     * @var bool
     */
    private $relSponsored = false;

    /**
     * @var bool
     */
    private $relUGC = false;

    /**
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return bool|null
     */
    public function isRelNoFollow()
    {
        return $this->relNoFollow;
    }

    /**
     * @param bool $relNoFollow
     */
    public function setRelNoFollow($relNoFollow)
    {
        $this->relNoFollow = $relNoFollow;
    }

    /**
     * @return bool|null
     */
    public function isRelNoOpener()
    {
        return $this->relNoOpener;
    }

    /**
     * @param bool $relNoOpener
     */
    public function setRelNoOpener($relNoOpener)
    {
        $this->relNoOpener = $relNoOpener;
    }

    /**
     * @return bool|null
     */
    public function isRelNoReferrer()
    {
        return $this->relNoReferrer;
    }

    /**
     * @param bool $relNoReferrer
     */
    public function setRelNoReferrer($relNoReferrer)
    {
        $this->relNoReferrer = $relNoReferrer;
    }

    /**
     * @return bool
     */
    public function isRelSponsored()
    {
        return $this->relSponsored;
    }

    /**
     * @param bool $relSponsored
     * @return Url
     */
    public function setRelSponsored(bool $relSponsored)
    {
        $this->relSponsored = $relSponsored;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRelUGC()
    {
        return $this->relUGC;
    }

    /**
     * @param bool $relUGC
     * @return Url
     */
    public function setRelUGC(bool $relUGC)
    {
        $this->relUGC = $relUGC;
        return $this;
    }

    public function toArray()
    {
        return [
            UrlType::URL_NAME        => $this->getUrl(),
            UrlType::REL_NO_FOLLOW   => $this->isRelNoFollow(),
            UrlType::REL_NO_OPENER   => $this->isRelNoOpener(),
            UrlType::REL_NO_REFERRER => $this->isRelNoReferrer(),
            UrlType::REL_SPONSORED   => $this->isRelSponsored(),
            UrlType::REL_UGC         => $this->isRelUGC(),
        ];
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }
}

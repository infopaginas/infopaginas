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

    public function toArray()
    {
        return [
            UrlType::URL_NAME        => $this->getUrl(),
            UrlType::REL_NO_FOLLOW   => $this->isRelNoFollow(),
            UrlType::REL_NO_OPENER   => $this->isRelNoOpener(),
            UrlType::REL_NO_REFERRER => $this->isRelNoReferrer(),
        ];
    }

    public function __toString()
    {
        return json_encode($this->toArray());
    }
}

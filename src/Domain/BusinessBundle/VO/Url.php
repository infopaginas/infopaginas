<?php


namespace Domain\BusinessBundle\VO;


class Url
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var bool
     */
    private $relNoFollow;

    /**
     * @var bool
     */
    private $relNoOpener;

    /**
     * @var bool
     */
    private $relNoReferrer;

    public function __construct($url, $relNoFollow, $relNoOpener, $relNoReferrer)
    {
        $this->url  = $url;
        $this->relNoFollow = $relNoFollow;
        $this->relNoOpener = $relNoOpener;
        $this->relNoReferrer = $relNoReferrer;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return bool
     */
    public function isRelNoFollow(): bool
    {
        return $this->relNoFollow;
    }

    /**
     * @param bool $relNoFollow
     */
    public function setRelNoFollow(bool $relNoFollow)
    {
        $this->relNoFollow = $relNoFollow;
    }

    /**
     * @return bool
     */
    public function isRelNoOpener(): bool
    {
        return $this->relNoOpener;
    }

    /**
     * @param bool $relNoOpener
     */
    public function setRelNoOpener(bool $relNoOpener)
    {
        $this->relNoOpener = $relNoOpener;
    }

    /**
     * @return bool
     */
    public function isRelNoReferrer(): bool
    {
        return $this->relNoReferrer;
    }

    /**
     * @param bool $relNoReferrer
     */
    public function setRelNoReferrer(bool $relNoReferrer)
    {
        $this->relNoReferrer = $relNoReferrer;
    }

}
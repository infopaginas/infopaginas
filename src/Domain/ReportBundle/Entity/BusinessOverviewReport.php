<?php

namespace Domain\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Domain\ReportBundle\Model\ReportInterface;

/**
 * BusinessOverviewReport
 *
 * @ORM\Table(name="business_overview_report")
 * @ORM\Entity(repositoryClass="Domain\ReportBundle\Repository\BusinessOverviewReportRepository")
 */
class BusinessOverviewReport implements ReportInterface
{
    const TYPE_CODE_IMPRESSION = 'impressions';
    const TYPE_CODE_VIEW       = 'views';

    const TYPE_CODE_DIRECTION_BUTTON       = 'directionButton';
    const TYPE_CODE_MAP_SHOW_BUTTON        = 'mapShowButton';
    const TYPE_CODE_MAP_MARKER_BUTTON      = 'mapMarkerButton';
    const TYPE_CODE_WEB_BUTTON             = 'webButton';
    const TYPE_CODE_CALL_MOB_BUTTON        = 'callMobButton';
    const TYPE_CODE_CALL_DESK_BUTTON       = 'callDeskButton';
    const TYPE_CODE_ADD_COMPARE_BUTTON     = 'addCompareButton';
    const TYPE_CODE_REMOVE_COMPARE_BUTTON  = 'removeCompareButton';

    const TYPE_CODE_FACEBOOK_SHARE  = 'facebookShare';
    const TYPE_CODE_TWITTER_SHARE   = 'twitterShare';

    const TYPE_CODE_FACEBOOK_VISIT  = 'facebookVisit';
    const TYPE_CODE_TWITTER_VISIT   = 'twitterVisit';
    const TYPE_CODE_GOOGLE_VISIT    = 'googleVisit';
    const TYPE_CODE_YOUTUBE_VISIT   = 'youtubeVisit';

    const TYPE_CODE_VIDEO_WATCHED  = 'videoWatched';
    const TYPE_CODE_REVIEW_CLICK   = 'reviewClick';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(name="date", type="date")
     */
    protected $date;

    /**
     * @var int
     * @ORM\Column(name="views", type="integer")
     */
    protected $views = 0;

    /**
     * @var int
     * @ORM\Column(name="impressions", type="integer")
     */
    protected $impressions = 0;

    /**
     * @var int
     * @ORM\Column(name="direction_button", type="integer", options={"default" : 0})
     */
    protected $directionButton = 0;

    /**
     * @var int
     * @ORM\Column(name="map_show_button", type="integer", options={"default" : 0})
     */
    protected $mapShowButton = 0;

    /**
     * @var int
     * @ORM\Column(name="map_marker_button", type="integer", options={"default" : 0})
     */
    protected $mapMarkerButton = 0;

    /**
     * @var int
     * @ORM\Column(name="web_button", type="integer", options={"default" : 0})
     */
    protected $webButton = 0;

    /**
     * @var int
     * @ORM\Column(name="call_mob_button", type="integer", options={"default" : 0})
     */
    protected $callMobButton = 0;

    /**
     * @var int
     * @ORM\Column(name="call_desk_button", type="integer", options={"default" : 0})
     */
    protected $callDeskButton = 0;

    /**
     * @var int
     * @ORM\Column(name="add_compare_button", type="integer", options={"default" : 0})
     */
    protected $addCompareButton = 0;

    /**
     * @var int
     * @ORM\Column(name="remove_compare_button", type="integer", options={"default" : 0})
     */
    protected $removeCompareButton = 0;

    /**
     * @var int
     * @ORM\Column(name="facebook_share", type="integer", options={"default" : 0})
     */
    protected $facebookShare = 0;

    /**
     * @var int
     * @ORM\Column(name="twitter_share", type="integer", options={"default" : 0})
     */
    protected $twitterShare = 0;

    /**
     * @var int
     * @ORM\Column(name="facebook_visit", type="integer", options={"default" : 0})
     */
    protected $facebookVisit = 0;

    /**
     * @var int
     * @ORM\Column(name="twitter_visit", type="integer", options={"default" : 0})
     */
    protected $twitterVisit = 0;

    /**
     * @var int
     * @ORM\Column(name="google_visit", type="integer", options={"default" : 0})
     */
    protected $googleVisit = 0;

    /**
     * @var int
     * @ORM\Column(name="youtube_visit", type="integer", options={"default" : 0})
     */
    protected $youtubeVisit = 0;

    /**
     * @var int
     * @ORM\Column(name="video_watched", type="integer", options={"default" : 0})
     */
    protected $videoWatched = 0;

    /**
     * @var int
     * @ORM\Column(name="review_click", type="integer", options={"default" : 0})
     */
    protected $reviewClick = 0;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="Domain\BusinessBundle\Entity\BusinessProfile",
     *     inversedBy="businessOverviewReports",
     *     cascade={"persist"}
     * )
     * @ORM\JoinColumn(name="business_profile_id", referencedColumnName="id")
     */
    protected $businessProfile;

    public static function getExportFormats()
    {
        return [
            self::CODE_PDF_BUSINESS_OVERVIEW_REPORT   => self::FORMAT_PDF,
            self::CODE_EXCEL_BUSINESS_OVERVIEW_REPORT => self::FORMAT_EXCEL,
        ];
    }

    public static function getTypes()
    {
        return [
            self::TYPE_CODE_IMPRESSION,
            self::TYPE_CODE_VIEW,
            self::TYPE_CODE_DIRECTION_BUTTON,
            self::TYPE_CODE_MAP_SHOW_BUTTON,
            self::TYPE_CODE_MAP_MARKER_BUTTON,
            self::TYPE_CODE_WEB_BUTTON,
            self::TYPE_CODE_CALL_MOB_BUTTON,
            self::TYPE_CODE_CALL_DESK_BUTTON,
            self::TYPE_CODE_ADD_COMPARE_BUTTON,
            self::TYPE_CODE_REMOVE_COMPARE_BUTTON,
            self::TYPE_CODE_FACEBOOK_SHARE,
            self::TYPE_CODE_TWITTER_SHARE,
            self::TYPE_CODE_FACEBOOK_VISIT,
            self::TYPE_CODE_TWITTER_VISIT,
            self::TYPE_CODE_GOOGLE_VISIT,
            self::TYPE_CODE_YOUTUBE_VISIT,
            self::TYPE_CODE_VIDEO_WATCHED,
            self::TYPE_CODE_REVIEW_CLICK,
        ];
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return BusinessOverviewReport
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set views
     *
     * @param integer $views
     *
     * @return BusinessOverviewReport
     */
    public function setViews($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * Get views
     *
     * @return integer
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set impressions
     *
     * @param integer $impressions
     *
     * @return BusinessOverviewReport
     */
    public function setImpressions($impressions)
    {
        $this->impressions = $impressions;

        return $this;
    }

    /**
     * Get impressions
     *
     * @return integer
     */
    public function getImpressions()
    {
        return $this->impressions;
    }

    /**
     * @return int
     */
    public function getDirectionButton()
    {
        return $this->directionButton;
    }

    /**
     * @param int $directionButton
     *
     * @return BusinessOverviewReport
     */
    public function setDirectionButton($directionButton)
    {
        $this->directionButton = $directionButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getMapShowButton()
    {
        return $this->mapShowButton;
    }

    /**
     * @param int $mapShowButton
     *
     * @return BusinessOverviewReport
     */
    public function setMapShowButton($mapShowButton)
    {
        $this->mapShowButton = $mapShowButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getMapMarkerButton()
    {
        return $this->mapMarkerButton;
    }

    /**
     * @param int $mapMarkerButton
     *
     * @return BusinessOverviewReport
     */
    public function setMapMarkerButton($mapMarkerButton)
    {
        $this->mapMarkerButton = $mapMarkerButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getWebButton()
    {
        return $this->webButton;
    }

    /**
     * @param int $webButton
     *
     * @return BusinessOverviewReport
     */
    public function setWebButton($webButton)
    {
        $this->webButton = $webButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getCallMobButton()
    {
        return $this->callMobButton;
    }

    /**
     * @param int $callMobButton
     *
     * @return BusinessOverviewReport
     */
    public function setCallMobButton($callMobButton)
    {
        $this->callMobButton = $callMobButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getCallDeskButton()
    {
        return $this->callDeskButton;
    }

    /**
     * @param int $callDeskButton
     *
     * @return BusinessOverviewReport
     */
    public function setCallDeskButton($callDeskButton)
    {
        $this->callDeskButton = $callDeskButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getAddCompareButton()
    {
        return $this->addCompareButton;
    }

    /**
     * @param int $addCompareButton
     *
     * @return BusinessOverviewReport
     */
    public function setAddCompareButton($addCompareButton)
    {
        $this->addCompareButton = $addCompareButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getRemoveCompareButton()
    {
        return $this->removeCompareButton;
    }

    /**
     * @param int $removeCompareButton
     *
     * @return BusinessOverviewReport
     */
    public function setRemoveCompareButton($removeCompareButton)
    {
        $this->removeCompareButton = $removeCompareButton;

        return $this;
    }

    /**
     * @return int
     */
    public function getFacebookShare()
    {
        return $this->facebookShare;
    }

    /**
     * @param int $facebookShare
     *
     * @return BusinessOverviewReport
     */
    public function setFacebookShare($facebookShare)
    {
        $this->facebookShare = $facebookShare;

        return $this;
    }

    /**
     * @return int
     */
    public function getTwitterShare()
    {
        return $this->twitterShare;
    }

    /**
     * @param int $twitterShare
     *
     * @return BusinessOverviewReport
     */
    public function setTwitterShare($twitterShare)
    {
        $this->twitterShare = $twitterShare;

        return $this;
    }

    /**
     * @return int
     */
    public function getFacebookVisit()
    {
        return $this->facebookVisit;
    }

    /**
     * @param int $facebookVisit
     *
     * @return BusinessOverviewReport
     */
    public function setFacebookVisit($facebookVisit)
    {
        $this->facebookVisit = $facebookVisit;

        return $this;
    }

    /**
     * @return int
     */
    public function getTwitterVisit()
    {
        return $this->twitterVisit;
    }

    /**
     * @param int $twitterVisit
     *
     * @return BusinessOverviewReport
     */
    public function setTwitterVisit($twitterVisit)
    {
        $this->twitterVisit = $twitterVisit;

        return $this;
    }

    /**
     * @return int
     */
    public function getGoogleVisit()
    {
        return $this->googleVisit;
    }

    /**
     * @param int $googleVisit
     *
     * @return BusinessOverviewReport
     */
    public function setGoogleVisit($googleVisit)
    {
        $this->googleVisit = $googleVisit;

        return $this;
    }

    /**
     * @return int
     */
    public function getYoutubeVisit()
    {
        return $this->youtubeVisit;
    }

    /**
     * @param int $youtubeVisit
     *
     * @return BusinessOverviewReport
     */
    public function setYoutubeVisit($youtubeVisit)
    {
        $this->youtubeVisit = $youtubeVisit;

        return $this;
    }

    /**
     * @return int
     */
    public function getVideoWatched()
    {
        return $this->videoWatched;
    }

    /**
     * @param int $videoWatched
     *
     * @return BusinessOverviewReport
     */
    public function setVideoWatched($videoWatched)
    {
        $this->videoWatched = $videoWatched;

        return $this;
    }

    /**
     * @return int
     */
    public function getReviewClick()
    {
        return $this->reviewClick;
    }

    /**
     * @param int $reviewClick
     *
     * @return BusinessOverviewReport
     */
    public function setReviewClick($reviewClick)
    {
        $this->reviewClick = $reviewClick;

        return $this;
    }



    /**
     * Set businessProfile
     *
     * @param \Domain\BusinessBundle\Entity\BusinessProfile $businessProfile
     *
     * @return BusinessOverviewReport
     */
    public function setBusinessProfile(\Domain\BusinessBundle\Entity\BusinessProfile $businessProfile = null)
    {
        $this->businessProfile = $businessProfile;

        return $this;
    }

    /**
     * Get businessProfile
     *
     * @return \Domain\BusinessBundle\Entity\BusinessProfile
     */
    public function getBusinessProfile()
    {
        return $this->businessProfile;
    }

    public function incrementBusinessCounter($type)
    {
        $this->{$type}++;
    }
}

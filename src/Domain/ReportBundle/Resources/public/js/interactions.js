//stats collector
//use GA as stats server
//@see 3.2.3.4	Interactions to Profile Report in SRS
$(document).ready(function() {
    //track sections list
    var IMAGE_SECTION_CLASS = '.businessProfileImage';
    var VIDEO_SECTION_CLASS = '.businessProfileVideo';
    var MAP_LINK_CLASS = '.businessProfileMapLink';
    var WEBSITE_LINK_CLASS = '.businessProfileWebsiteLink';
    var BANNER_CLASS = '.businessProfileBanner';
    var SOCIAL_NETWORK_LINK_CLASS = '.businessProfileSocialNetworkLink';

    //event types list
    var IMAGE_CLICK_EVENT_TYPE = 'bp_image_click';
    var VIDEO_CLICK_EVENT_TYPE = 'bp_video_click';
    var MAP_CLICK_EVENT_TYPE = 'bp_map_click';
    var WEBSITE_LINK_CLICK_EVENT_TYPE = 'bp_website_click';
    var BANNER_CLICK_EVENT_TYPE = 'bp_banner_click';
    var SOCIAL_NETWORK_LINK_CLICK_EVENT_TYPE = 'bp_social_network_click';

    //track click on business profile image
    $(document).on('click', IMAGE_SECTION_CLASS, function() {
        var businessProfileId = getBusinessProfileId($(this));
        track(IMAGE_CLICK_EVENT_TYPE, businessProfileId);
    });

    //track click on business profile video
    $(document).on('click', VIDEO_SECTION_CLASS, function() {
        var businessProfileId = getBusinessProfileId($(this));
        track(VIDEO_CLICK_EVENT_TYPE, businessProfileId);
    });

    //track click on business profile map
    $(document).on('click', MAP_LINK_CLASS, function() {
        var businessProfileId = getBusinessProfileId($(this));
        track(MAP_CLICK_EVENT_TYPE, businessProfileId);
    });

    //track click on business profile website
    $(document).on('click', WEBSITE_LINK_CLASS, function() {
        var businessProfileId = getBusinessProfileId($(this));
        track(WEBSITE_LINK_CLICK_EVENT_TYPE, businessProfileId);
    });

    //track click on business profile banner
    $(document).on('click', BANNER_CLASS, function() {
        var businessProfileId = getBusinessProfileId($(this));
        track(BANNER_CLICK_EVENT_TYPE, businessProfileId);
    });

    //track click on business profile social network link
    $(document).on('click', SOCIAL_NETWORK_LINK_CLASS, function() {
        var businessProfileId = getBusinessProfileId($(this));
        track(SOCIAL_NETWORK_LINK_CLICK_EVENT_TYPE, businessProfileId);
    });

    //get business profile id from link [data] attribute
    function getBusinessProfileId($element)
    {
        return $element.data('businessProfileId');
    }

    //send data to Google Analytics (use it as stats server)
    function track(interactionType, businessProfileId)
    {
        ga("send", "event", "interaction", interactionType, businessProfileId);
    }
});
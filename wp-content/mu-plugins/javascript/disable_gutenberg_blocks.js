(function () {
    wp.domReady(function () {
        wp.blocks.unregisterBlockType('bp/login-form');
        wp.blocks.unregisterBlockType('bp/member');
        wp.blocks.unregisterBlockType('bp/members');
        wp.blocks.unregisterBlockType('bp/dynamic-members');
        wp.blocks.unregisterBlockType('bp/online-members');
        wp.blocks.unregisterBlockType('bp/active-members');
        wp.blocks.unregisterBlockType('bp/latest-activities');
        wp.blocks.unregisterBlockType('bp/embed-activity');
        wp.blocks.unregisterBlockType('bp/friends');
        wp.blocks.unregisterBlockType('bp/group');
        wp.blocks.unregisterBlockType('bp/groups');
        wp.blocks.unregisterBlockType('bp/dynamic-groups');
        wp.blocks.unregisterBlockType('bp/sitewide-notices');
        wp.blocks.unregisterBlockType('getwid/mailchimp');
        wp.blocks.unregisterBlockType('getwid/mailchimp-field-email');
        wp.blocks.unregisterBlockType('getwid/mailchimp-field-first-name');
        wp.blocks.unregisterBlockType('getwid/mailchimp-field-last-name');
        wp.blocks.unregisterBlockType('getwid/map');
        wp.blocks.unregisterBlockVariation('core/embed', 'cloudup');
        wp.blocks.unregisterBlockVariation('core/embed', 'crowdsignal');
        wp.blocks.unregisterBlockVariation('core/embed', 'imgur');
        wp.blocks.unregisterBlockVariation('core/embed', 'kickstarter');
        wp.blocks.unregisterBlockVariation('core/embed', 'meetup-com');
        wp.blocks.unregisterBlockVariation('core/embed', 'mixcloud');
        wp.blocks.unregisterBlockVariation('core/embed', 'reddit');
        wp.blocks.unregisterBlockVariation('core/embed', 'reverbnation');
        wp.blocks.unregisterBlockVariation('core/embed', 'screencast');
        wp.blocks.unregisterBlockVariation('core/embed', 'smugmug');
        wp.blocks.unregisterBlockVariation('core/embed', 'speaker-deck');
        wp.blocks.unregisterBlockVariation('core/embed', 'tiktok');
        wp.blocks.unregisterBlockVariation('core/embed', 'TED');
        wp.blocks.unregisterBlockVariation('core/embed', 'tumblr');
        wp.blocks.unregisterBlockVariation('core/embed', 'videopress');
        wp.blocks.unregisterBlockVariation('core/embed', 'wordpress-tv');
        wp.blocks.unregisterBlockVariation('core/embed', 'amazon-kindle');
    });
})();
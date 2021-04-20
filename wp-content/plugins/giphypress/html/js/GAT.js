'use strict';

/**
 * Config Object
 * {
 *      account: 'UA-XXXXXXXX-X',
 *      loadGA: true / false , // this loads the lib async
 * }
 */

var GAT = function GAT (config) {
    var _config = {
        name: 'Test',
        account: 'UA-XXXXXXXX-X',
        loadGA: false,
        clickListeners: ['.gaTrack'],
        api_key: 'xxx'
    };

    // merge the incoming app
    for (var attrname in config) { _config[attrname] = config[attrname]; }

    if (_config.account === 'UA-XXXXXXXX-X') throw {name: '', message: ''}

    var 
    // A variable to store the user's client_id
    client_id,
    
    // A variable to store the session data
    session_data = {
      "sessions": [{
        "user": {
          "user_id": "xxx"
        },
        "events": []
      }]
    },

    // Reset the session data (used on load and when a session is closed)
    reset_session_data = function reset_session_data(id){
        session_data = {
            "sessions": [{
                "user": {
                    "user_id": id
                },
                "events": []
            }]
        }
    },

    send_pingback = function send_pingback (){

        // Send session data to GIPHY.
        
        var api_key = _config.api_key;
        var url = "http://pingback.giphy.com/pingback?api_key=" + api_key;
 
        jQuery.ajax({
            url : url,
            datatype:'json',
            type: "post", 
            cors: true,
            contentType: "application/json",
            data: JSON.stringify(session_data),
            success:function(data){console.log(data)}
        })
    },

    // Create a new GIF_SEARCH event with an empty actions array and add it to the session data.
    create_new_event = function create_new_event(resp_id){
        //console.log("new event")
        var new_event = 
            {
                "event_type": "GIF_SEARCH",
                "response_id": resp_id,
                "actions" : []
            }
            
        session_data["sessions"][0]["events"].push(new_event);
    },

    log_action = function log_action(action_type, gif_id, timestamp){
        var new_action =
        {
            "action_type" : action_type,
            "gif_id" : gif_id,
            "ts" : timestamp
        }

        // Add the new action to the current event.
        // Find the last (most recent) event in the events array and add the new action.
        var lastEventIndex = session_data["sessions"][0]["events"].length - 1;
        session_data["sessions"][0]["events"][lastEventIndex]["actions"].push(new_action);
    },

    track = function track (category, action, label) {
        var clickAttrs = {
            'eventCategory': category,
            'eventAction': action,
            'eventLabel': label
        }
        ga('send', 'event', clickAttrs);
    };

    /**
     * GA INSERT DEPENDENCY
     * This allows the GA object to be available
     *
     */
    if (_config.loadGA) {
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){

        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),

        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)

        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    }

    ga('create', _config.account, 'auto');
    ga('set', 'checkProtocolTask', function(){}); // Removes failing protocol check. @see: http://stackoverflow.com/a/22152353/1958200
    ga('require', 'displayfeatures');
    ga('send', 'pageview', _config.name + ' loaded');
    //console.log("setting up ga")
    ga(function(tracker) {
        client_id = tracker.get('clientId');
        session_data["sessions"][0]["user"].user_id = client_id;
    });
   // $(document).on('click', catchtracker);

    return {
        track : track,
        create_new_event : create_new_event,
        log_action : log_action,
        send_pingback : send_pingback
    }
};

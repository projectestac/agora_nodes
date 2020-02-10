(function() {

    var GiphyPressWizard = {
        init : function(ed, url) {
            ed.addButton('giphypresswizard', {
                title: 'GIPHY GIF Search',
                text: 'GIF',
                onclick : function(ev) {
                  //var modalw = Math.round(jQuery(window).width() *.6);
                  //var modalh = Math.round(jQuery(window).height() *.8);
                  var modalw = 480;
                  var modalh = 548;

                  ed.windowManager.open({
                      //title : "Giphypress Embed Wizard",
                      file: url + '/../html/giphy.html',
                      width : modalw,
                      height : modalh,
                      inline : true,
                      resizable: true,
                      scrollbars: true
                  }, {
                      plugin_url : url, // Plugin absolute URL
                      api_key : 'G46lZIryTGCUU', // the API key
                      api_host : 'http://api.giphy.com' // the API host
                  });
                }
            });

            ed.on('show init', function(event) {
                GiphyPressWizard.revertIframes(content);
            });
            ed.on('ExecCommand', function(event) {
                if ( (event.command === "mceInsertRawHTML") && (event.value.indexOf('//giphy.com/embed') > -1)) {
                    GiphyPressWizard.revertIframes(content);
                }
            });

        },

        revertIframes : function() {
            // TINYMCE MEDIA PLUGIN REPLACES IFRAMES WTF
            // GOTTA BRING EM BACK
            var rawContent = tinymce.editors[0].getContent({format : 'raw'});
            var updatedContent = rawContent;
            $content = jQuery(rawContent);
            $content.find("img.mce-object-iframe").each(function(idx, element) {
                var img = jQuery(element);
                var iframe = jQuery("<iframe>", {
                    "src": img.attr('data-mce-p-src'),
                });
                iframe.attr('width',img.attr('width'));
                iframe.attr('height', img.attr('height'));
                iframe.attr('frameBorder','0');
                iframe.attr('webkitAllowFullScreen');
                iframe.attr('mozallowfullscreen');
                iframe.attr('allowFullScreen');
                updatedContent = updatedContent.replace(img.prop('outerHTML'), iframe.prop('outerHTML'));
            });
            // clear up any old iframes
            if (updatedContent.indexOf("[iframe") > -1) {
                console.log('got old iframes');
                var re = /\[iframe(.*?)\]/ig;
                var re2 = '[/iframe]';
                while(updatedContent.search(re) !== -1)  {
                    updatedContent = updatedContent.replace(re, '<iframe $1pt"></iframe>');
                }
                while (updatedContent.indexOf(re2) !== -1) {
                    updatedContent = updatedContent.replace(re2, '');
                }
            }
            tinymce.editors[0].setContent(updatedContent,{format : 'raw'});
        },

        getInfo : function() {
          //console.log("giphypress tinymce get info");
            return {
                longname : "GiphyPress Shortcode Wizard",
                author : 'Team Giphy',
                authorurl : 'http://labs.giphy.com',
                infourl : 'http://labs.giphy.com',
                version : "1.5"
            };
        }
    }

    tinymce.create('tinymce.plugins.giphypresswizard', GiphyPressWizard);
    tinymce.PluginManager.add('giphypresswizard', tinymce.plugins.giphypresswizard);
})();

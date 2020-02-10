var GiphyCMSExt = {
  init: function() {
   // jQuery('#gif-inject-cms').html('<button class="button button-hero" onclick="GiphyCMSExt.doTinyMCEEmbed()" style="background: #6157FF;color: white;border: none;text-shadow: none;text-transform: uppercase;font-size:20px;font-weight:bold">Embed into post</button>');
  },
  doTinyMCEEmbed: function() {
    var embedId = jQuery('img#gif-detail-gif').attr('data-id');
    var width = jQuery('img#gif-detail-gif').attr('data-embed-width');
    var height = jQuery('img#gif-detail-gif').attr('data-embed-height');
    var username = jQuery('img#gif-detail-gif').attr('data-username');
    var profile_url = jQuery('img#gif-detail-gif').attr('data-profile-url');
    var shortlink = jQuery('img#gif-detail-gif').attr('data-shortlink');
    var attribution_string;

    // if (username != null && username != "null"){
    //   attribution_string = "via <a href = '" + profile_url + "'>" + username + "</a> on <a href = '"+ shortlink + "'>GIPHY</a>";
    // } else {
    //   attribution_string = "via <a href = '"+ shortlink + "'>GIPHY</a>";
    // }

    var uri = '<iframe src="//giphy.com/embed/' + embedId + '" width="' + width + '" height="' + height + '" frameBorder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>'

    parent.tinyMCE.activeEditor.execCommand("mceInsertRawHTML", false, uri);
    //parent.tinyMCE.activeEditor.execCommand("mceInsertContent", false, "<img src='" + gifToEmbed + "' width='" + width + "' alt='giphy gif'>");
    parent.tinyMCE.activeEditor.selection.select(parent.tinyMCE.activeEditor.getBody(), true); // ed is the editor instance
    parent.tinyMCE.activeEditor.selection.collapse(false);
    parent.tinyMCE.activeEditor.windowManager.close(window);
  }
};

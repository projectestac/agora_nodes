(function($){
$(function() {

    $( document ).ready(function() {
        var $form = $("#whats-new-form");
        var $textContainer = $form.find('#whats-new-textarea');
        var html = '<div class="bpfb_actions_container bpfb-theme-new bpfb-alignment-left">' +
                '<div class="bpfb_toolbar_container">' +
                    l10nBpfb.quota_exceeded
                '</div>' +
            '</div>';
        $textContainer.after(html);
    });
});
})(jQuery);
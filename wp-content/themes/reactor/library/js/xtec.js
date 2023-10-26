function equalize_cards() {
    //Targeta mes alta determina l'alçada de les targetes de la fila

    jQuery(".articles > .row").each(function() {
        var rowcards = jQuery(this).children();
        var maxHeight = Math.max.apply(null, rowcards.map(function() {
            return jQuery(this).height();
        }).get());

        rowcards.height(maxHeight + 20);

        jQuery(this).find("footer").css ({position: 'absolute', top: maxHeight - 60});
    });
}

//http://www.feedthebot.com/pagespeed/defer-loading-javascript.html
function addFunction_onload(callback) {
    if (window.addEventListener)
        window.addEventListener("load", callback, false);
    else if (window.attachEvent)
        window.attachEvent("onload", callback);
    else window.onload = callback;
}
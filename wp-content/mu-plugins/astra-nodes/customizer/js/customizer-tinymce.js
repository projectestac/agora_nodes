(function ($) {
    wp.customize.bind('ready', function () {
        tinymce.init({
            selector: '.customize-control-tinymce',
            setup: function (editor) {
                editor.on('init', function () {
                    console.log('TinyMCE Editor on customizer: initialized');
                    editor.on('change', function () {
                        editor.save();
                        $(editor.getElement()).trigger('change');
                    });
                });
            },
            height: 200,
            menubar: false,
            plugins: 'lists',
            toolbar: 'undo redo | alignleft aligncenter alignright alignjustify | bold italic underline | bullist numlist',
            branding: false
        });
    });
})(jQuery);

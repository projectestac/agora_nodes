(function ($) {
    wp.customize.bind('ready', function () {

        $('.customize-control-tinymce').each(function () {
            const $textarea = $(this);
            const editorId = $textarea.attr('id');

            wp.editor.initialize(editorId, {
                tinymce: {
                    selector: 'textarea',

                    convert_urls: false,
                    relative_urls: false,
                    remove_script_host: false,
                    link_assume_external_targets: true,

                    target_list: [
                        {title: 'Finestra nova (_blank)', value: '_blank'},
                        {title: 'La mateixa finestra', value: ''}
                    ],

                    height: 300,
                    menubar: false,
                    plugins: 'lists link',
                    toolbar1: 'undo redo | alignleft aligncenter alignright alignjustify | bold italic underline | bullist numlist | link unlink',

                    setup: function (editor) {
                        editor.on('change keyup paste', function () {
                            editor.save();
                            $textarea.trigger('change');
                        });
                    }
                },
                // Activate tab for HTML code.
                quicktags: true
            });
        });

    });
})(jQuery);

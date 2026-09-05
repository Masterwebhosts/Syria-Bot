jQuery(document).ready(function ($) {

    $('#syria-bot-start-import').on('click', function (e) {

        e.preventDefault();

        let button = $(this);

        button.prop('disabled', true);

        $('#syria-bot-progress').html(
            'جاري بدء تحديث قاعدة المعرفة...'
        );

        function runImport() {

            $.ajax({
                url: syriaBotAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'syria_bot_import_batch',
                    nonce: syriaBotAdmin.nonce
                },
                success: function (response) {

                    if (response.success) {

                        if (response.data.finished) {

                            $('#syria-bot-progress').html(
                                response.data.message
                            );

                            button.prop('disabled', false);

                        } else {

                            $('#syria-bot-progress').html(
                                'تم استيراد: ' +
                                response.data.offset +
                                ' مقال'
                            );

                            runImport();
                        }

                    } else {

                        $('#syria-bot-progress').html(
                            response.data && response.data.message
                                ? response.data.message
                                : 'حدث خطأ أثناء التحديث'
                        );

                        button.prop('disabled', false);
                    }
                },
                error: function (xhr, status, error) {

                    console.error('Syria Bot AJAX Error:', {
                        status: status,
                        error: error,
                        http_status: xhr.status,
                        response: xhr.responseText
                    });

                    $('#syria-bot-progress').html(
                        'تعذر الاتصال بالخادم (' + xhr.status + ')'
                    );

                    button.prop('disabled', false);
                }
            });
        }

        runImport();
    });
});

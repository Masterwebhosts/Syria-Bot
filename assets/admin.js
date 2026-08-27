jQuery(document).ready(function ($) {


    $('#syria-bot-start-import').on(
        'click',
        function (e) {


            e.preventDefault();


            let button = $(this);


            button.prop(
                'disabled',
                true
            );


            $('#syria-bot-progress').html(
                'جاري بدء تحديث قاعدة المعرفة...'
            );



            function runImport() {


                $.ajax({

                    url: ajaxurl,

                    type: 'POST',

                    data: {


                        action: 'syria_bot_import_batch',


                        nonce: syriaBotAdmin.nonce


                    },


                    success: function (response) {



                        if (
                            response.success
                        ) {



                            if (
                                response.data.finished
                            ) {


                                $('#syria-bot-progress').html(

                                    response.data.message

                                );


                                button.prop(
                                    'disabled',
                                    false
                                );


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
                                'حدث خطأ أثناء التحديث'
                            );


                            button.prop(
                                'disabled',
                                false
                            );


                        }


                    },


                    error: function () {


                        $('#syria-bot-progress').html(
                            'تعذر الاتصال بالخادم'
                        );


                        button.prop(
                            'disabled',
                            false
                        );


                    }


                });


            }



            runImport();



        }

    );


});
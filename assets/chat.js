jQuery(document).ready(function ($) {


    console.log('Syria Bot JS Loaded');


    let isSending = false;


    $(document).on(
        'click',
        '#syria-bot-toggle',
        function () {


            console.log('Button Clicked');


            $('#syria-bot-floating').toggleClass('open');


            console.log(
                $('#syria-bot-floating').attr('class')
            );


        }
    );

    /**
     * التأكد من AJAX قبل الإرسال فقط
     */
    function syriaBotSend() {


        if (typeof syriaBot === 'undefined') {


            $('#syria-bot-answer').text(
                'خطأ في تحميل النظام'
            );


            return;

        }

        let question = $.trim(
            $('#syria-bot-question').val()
        );



        if (!question) {


            $('#syria-bot-answer').text(
                'يرجى كتابة السؤال'
            );


            return;

        }




        isSending = true;



        $('#syria-bot-answer').text(
            'جاري البحث...'
        );




        $.ajax({


            type: 'POST',

            url: syriaBot.ajax_url,


            data: {


                action: 'syria_bot_chat',

                nonce: syriaBot.nonce,

                question: question


            },



            success: function (response) {



                if (
                    response.success
                    &&
                    response.data
                ) {



                    let answer = response.data.answer || '';



                    $('#syria-bot-answer').text(
                        answer
                    );




                    if (response.data.url) {



                        let link = $('<a>', {


                            href: response.data.url,

                            target: '_blank',

                            rel: 'noopener noreferrer',

                            text: 'اقرأ المزيد'


                        });



                        $('#syria-bot-answer').append(
                            '<br><br>'
                        );



                        $('#syria-bot-answer').append(
                            link
                        );


                    }



                } else {



                    $('#syria-bot-answer').text(

                        response.data.message ||
                        'حدث خطأ أثناء البحث'

                    );


                }



            },



            error: function () {


                $('#syria-bot-answer').text(

                    'تعذر الاتصال بالخادم'

                );


            },



            complete: function () {


                isSending = false;


            }



        });


    }





    /**
     * زر الإرسال
     */
    $(document).on(
        'click',
        '#syria-bot-send',
        function () {


            syriaBotSend();


        }
    );





    /**
     * الإرسال بالضغط على Enter
     */
    $(document).on(
        'keypress',
        '#syria-bot-question',
        function (e) {



            if (
                e.which === 13
            ) {


                e.preventDefault();


                syriaBotSend();


            }


        }
    );



});
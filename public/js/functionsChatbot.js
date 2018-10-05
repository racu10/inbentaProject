/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var jq = jQuery.noConflict();

jq(document).ready(function () {
    jq('#btnSendMessage').show();
    jq('#btnResetMessage').hide();

    jq.ajax({
        url: 'getChatbotStatus',
        type: 'POST',
        success: function (response) {
            if (response.chatbotStatus == 99) {
                jq('#btnSendMessage').hide();
                jq('#btnResetMessage').show();
            }
        }
    });


    jq("#btnSendMessage").click(function () {
        var message = jq('#txtMessage').val();
        var fullConver = jq('#containConver').text();
        if (fullConver == null) {
            fullConver = '';
        }
        var parametros = {
            "message": message,
            "fullConver": fullConver,
        };
        jq.ajax({
            data: parametros,
            url: 'chatbot-ajax',
            type: 'POST',
            success: function (response) {
                jq('#txtMessage').val('');
                jq('#containConver').html(response.fullConver);
                jq('#errorMessage').html(' ');

                if (response.errNum != -1 && response.errNum != 1) {
                    jq('#txtMessage').attr('name', response.type);
                    jq('#txtMessage').attr('type', response.type);
                    jq('#txtMessage').attr('placeholder', response.valMsg);
                    jq('#txtMessage').attr('title', response.valMsg);
                    jq('#txtMessage').attr('data-error-msg', response.errMsg);
                    jq('#txtMessage').attr('required', 'required');
                }

                if (response.errNum == 1) {
                    jq('#errorMessage').html(response.errMsg);
                }
                if (response.errNum == 99) {
                    jq('#btnSendMessage').hide();
                    jq('#btnResetMessage').show();
                } else {
                    jq('#btnSendMessage').show();
                    jq('#btnResetMessage').hide();

                }

            }
        });
    });

    jq("#btnResetMessage").click(function () {
        var parametros = {
            "message": '',
            "fullConver": '',
        };
        jq.ajax({
            data: parametros,
            url: 'resetChatbot',
            type: 'POST',
            success: function (response) {
                jq('#containConver').html(response.fullConver);
                jq('#btnSendMessage').show();
                jq('#btnResetMessage').hide();
            }
        });
    });


});


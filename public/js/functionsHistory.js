/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var jq = jQuery.noConflict();

jq(document).ready(function () {
   
    jq.ajax({
        url: 'getAllConversations',
        type: 'POST',
        success: function (response) {
            jq('#dvAllConversations').html(response);
        }
    });
});


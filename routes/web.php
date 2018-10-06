<?php

use App\Http\Controllers\LogicController;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | All routes controlled by the Controller -> chatbotController
  |
 */

Route::get('/', 'chatbotController@index');

Route::get('index', 'chatbotController@index');

Route::get('history', 'chatbotController@history');

Route::post('chatbot-ajax', 'chatbotController@chatbotLogic');

Route::post('resetChatbot', 'chatbotController@resetChatbot');

Route::post('getChatbotStatus', 'chatbotController@getChatbotStatus');

Route::post('getAllConversations', function () {
    $logicController = new LogicController();
    return $logicController->getParsedConversationFileToHTML();
});


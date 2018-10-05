<?php

use App\Http\Controllers\LogicController;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('/', function () {
    return view('main');
});

Route::get('index', function () {
    return view('main');
});

Route::get('history', function () {
    return view('history');
});

Route::post('chatbot-ajax', function () {
    $logicController = new LogicController();
    return $logicController->mainLogic();
});


Route::post('resetChatbot', function () {
    Session::flush();
    $fullConver = '';
    Session::put('fullConver', $fullConver);
    Session::put('errNum', 0);
    return Response::json(array(
                'fullConver' => $fullConver,
    ));
});

Route::post('getChatbotStatus', function () {
    $idStatus = Session::get('chatbotStatus');
    return Response::json(array(
                'chatbotStatus' => $idStatus,
    ));
});

Route::post('getAllConversations', function () {
    $logicController = new LogicController();
    return $logicController->getParsedConversationFileToHTML();
});


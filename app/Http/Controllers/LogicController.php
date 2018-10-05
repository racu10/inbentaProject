<?php

namespace App\Http\Controllers;

use Request;
use Session;
use Response;
use File;

class LogicController extends Controller {

    private $file = '../storage/app/contactInfoHist.json';

    public function mainLogic() {
        /*
         * $isAllOk {
         *  -1 :: NO_CHANGES_NOTHING
         *  0 :: OK
         *  1 :: ERROR
         *  99 :: END
         * }
         */
        $toFlush = 0;
        $isAllOk = -1;
        $message = Request::input('message');

        $validationMessage = '';
        $errorMessage = '';
        $nextAttribute = 'text';

        if (!Session::has('fullConver')) {
            Session::put('fullConver', '');
        }
        $fullConver = Session::get('fullConver');

        $nextMessage = '';
        if (trim($message) !== '' && $message !== NULL) {
            $isAllOk = 0;

            if (!Session::has('startConver')) {
                Session::put('startConver', $message);
                $nextMessage = 'Hello! I will ask you some questions ok? <br/> What is your name?';
                Session::put('typeTxt', 'text');
                Session::put('chatStatus', 'STARTED');
            } elseif (!Session::has('name')) {
                Session::put('name', $message);
                $nextMessage = 'What is your email?';

                $nextAttribute = 'email';
                $validationMessage = 'Email must be a valid address';
                Session::put('typeTxt', 'email');
            } elseif (!Session::has('email')) {
                if (filter_var($message, FILTER_VALIDATE_EMAIL)) {
                    Session::put('email', $message);
                    $nextMessage = 'What is your age?';
                    Session::put('typeTxt', 'numeric');

                    $nextAttribute = 'numeric';
                    $validationMessage = 'Age must be a number';
                } else {
                    $isAllOk = 1;
                    $errorMessage = 'Sorry, I could not understand your email address';
                }
            } elseif (!Session::has('age')) {
                if (is_numeric($message)) {
                    Session::put('age', $message);
                    Session::put('typeTxt', 'text');
                    $nextMessage = 'Thanks! Now I know you better. Your name is ' . Session::get('name') . ',
you are ' . Session::get('age') . ' old and I can contact you on ' . Session::get('email');
                    $isAllOk = 99;
                    Session::put('chatStatus', 'DONE');
                } else {
                    $isAllOk = 1;
                    $errorMessage = 'Sorry, I could not understand your age';
                }
            } else {
                $toFlush = 1;
                Session::flush();
                $nextMessage = '';
                Session::flush('fullConver');
                $fullConver = '';
                Session::put('typeTxt', 'text');
            }
            if ($toFlush == 0 && ($isAllOk == 0 || $isAllOk == 99)) {
                $fullConver = $fullConver . '' . $message . '<br/>' . $nextMessage . '<br/>';
            }
        }
        Session::put('fullConver', $fullConver);
        Session::put('chatbotStatus', $isAllOk);

        if ($isAllOk == 99) {
            $arrConversation = array(
                'conversation' => $fullConver,
                'name' => Session::get('name'),
                'email' => Session::get('email'),
                'age' => Session::get('age')
            );
            $this->store($arrConversation);
        }

        return Response::json(array(
                    'fullConver' => $fullConver,
                    'type' => $nextAttribute,
                    'valMsg' => $validationMessage,
                    'errMsg' => $errorMessage,
                    'errNum' => $isAllOk,
        ));
    }

    private function store(array $arr) {
        $this->file = '../storage/app/contactInfoHist.json';
        $arr['date'] = date('Y-m-d H:i:s');

        if (!File::exists($this->file)) {
            File::put($this->file, '[]');
        }

        $arrJson = json_decode(File::get($this->file));

        array_push($arrJson, json_encode($arr));
        File::put($this->file, json_encode($arrJson));
    }

    public function getParsedConversationFileToHTML() {
        $txtHtml = '';

        if (File::exists($this->file)) {
            $arrJson = json_decode(File::get($this->file));
            foreach ($arrJson as $key => $value) {
                $value = json_decode($value);
                $txtHtml = $txtHtml . '' . $value->{'conversation'} . '<br/><hr><br/>';
            }
        }
        return $txtHtml;
    }

}

<?php

namespace App\Http\Controllers;

use App\Chat;
use Request;
use Session;
use Response;
use Config;

class ChatbotController extends Controller {
    /*
     * Routing logic distribution
     */

    public function index() {
        return view('main');
    }

    public function history() {
        return view('history');
    }

    public function chatbotLogic() {
        /*
         * This function makes the logic of the chat.
         */
        $message = Request::input('message');
        $nextMessage = '';
        $validationMessage = '';
        $errorMessage = '';

        $actualStatus = Config::get('constants.statusChatbot.NO_CHANGES_NOTHING');

        if (!Session::has('fullConver')) {
            Session::put('fullConver', '');
        }
        $fullConver = Session::get('fullConver');

        if (trim($message) !== '' && $message !== NULL) {
            if (!Session::has('infoAskUser')) {
                // The user sends the first message, 
                // the bot initializes all the attributes that must be collected from the chat.
                // Then bot greets user.
                Session::put('infoAskUser', $this->getAllAttributes());
                Session::put('greet', $message);
                Session::put('lastAsk', '');
                Session::put('chatbotStatus', $actualStatus);
                $nextMessage = $nextMessage . Config::get('constants.botMessages.greet') . '<br/>';
            }

            $allMessages = $this->logicValidationsStatus($message, $actualStatus);
            $nextMessage .= $allMessages['nextMessage'];
            $validationMessage = $allMessages['validationMessage'];
            $errorMessage = $allMessages['errorMessage'];
            $actualStatus = $allMessages['actualStatus'];

            if ($actualStatus == Config::get('constants.statusChatbot.OK') || $actualStatus == Config::get('constants.statusChatbot.END')) {
                $fullConver = $fullConver . '' . $message . '<br/>';
                if ($nextMessage != '') {
                    $fullConver .= $nextMessage . '<br/>';
                }
                Session::put('fullConver', $fullConver);
            }

            if ($actualStatus == Config::get('constants.statusChatbot.END')) {
                // Saves the conversation and add the last info
                $fullConver = $this->saveAllConversation($actualStatus, $fullConver);
                Session::put('fullConver', $fullConver);
                Session::put('chatbotStatus', $actualStatus);
            }
        }

        $type = $this->getCorrectHTMLType(Session::get('lastAsk'));

        return Response::json(array(
                    'fullConver' => $fullConver,
                    'type' => $type,
                    'valMsg' => $validationMessage,
                    'errMsg' => $errorMessage,
                    'errNum' => $actualStatus,
        ));
    }

    public function resetChatbot() {
        Session::flush();
        $fullConver = '';
        Session::put('fullConver', $fullConver);
        Session::put('errNum', 0);
        return Response::json(array(
                    'fullConver' => $fullConver,
        ));
    }

    public function getChatbotStatus() {
        $idStatus = Session::get('chatbotStatus');
        return Response::json(array(
                    'chatbotStatus' => $idStatus,
        ));
    }

    public function getAllConversations() {
        return $this->getHistory();
    }

    /*     * *****
     * 
     * Private functions for views logic
     * 
     */

    private function logicValidationsStatus($message, $actualStatus) {
        /*
         * All the logic validation to update status and remove from the Stack
         */
        $nextMessage = '';
        $validationMessage = '';
        $errorMessage = '';

        if ($this->validationMessage($message, Session::get('lastAsk'))) {
            $actualStatus = Config::get('constants.statusChatbot.OK');
            if (Session::get('lastAsk') !== '') {
                // Some question is asked and its correctly responsed
                $arrAtt = Session::get('infoAskUser');
                $actAtt = array_shift($arrAtt);
                Session::put($actAtt, $message);
                Session::put('infoAskUser', $arrAtt);
            }

            if (!empty(Session::get('infoAskUser'))) {
                //While infoAskUser has attributes we made new questions to the user.
                Session::put('lastAsk', Session::get('infoAskUser')[0]);
                $attribute = $this->getAttribute(Session::get('lastAsk'));
                // Ask message
                $nextMessage = $attribute->ask;
                // Ask validation
                if (array_key_exists('validation', $attribute)) {
                    $validationMessage = $attribute->validation;
                }
            } else {
                // End status, there is no more attributes to ask. 
                $actualStatus = Config::get('constants.statusChatbot.END');
            }
        } else {
            $attribute = $this->getAttribute(Session::get('lastAsk'));
            $actualStatus = Config::get('constants.statusChatbot.ERROR');
            // Ask ERROR
            if (array_key_exists('error', $attribute)) {
                $errorMessage = $attribute->error;
            }
        }
        return array('nextMessage' => $nextMessage, 'validationMessage' => $validationMessage, 'errorMessage' => $errorMessage, 'actualStatus' => $actualStatus);
    }

    private function validationMessage(string $message, string $validationType) {
        /*
         * message -> input message from the user
         * validationType -> [ //It need to get the attribute that its analyzing
         *  - name :: Only validates if is not empty or null
         *  - age :: Validates if the input is an age between min and max from constants
         *  - email :: Validates if the input is a correct email
         * ]
         * 
         */
        $comp = false;
        if ($message !== '' && $message !== null) {
            switch (strtolower($validationType)) {
                case 'age': //Validates if is an number and the age is between a real range
                    $min = Config::get('constants.ranges.age.min');
                    $max = Config::get('constants.ranges.age.max');
                    if (filter_var($message, FILTER_VALIDATE_INT, array("options" => array("min_range" => $min, "max_range" => $max)))) {
                        $comp = true;
                    }
                    break;
                case 'email': // Validates if its a correct email
                    if (filter_var($message, FILTER_VALIDATE_EMAIL)) {
                        $comp = true;
                    }
                    break;
                default :
                    $comp = true;
                    break;
            }
        }

        return $comp;
    }

    private function getCorrectHTMLType($attribute) {
        /*
         * attribute --> Gets an attribute and map the type of the textbox must be
         */
        switch ($attribute) {
            case 'name':
                return 'text';
            case 'age':
                return 'numeric';
            case 'email':
                return 'email';
            default :
                return 'text';
        }
    }

    private function getAllAttributes() {
        // Get all atributes in the order that will appear in the chatbot
        $att = Config::get('constants.botMessages.attributes');

        $att = json_decode(json_encode($att[0]));

        usort($att, function($a, $b) {
            return $a->order < $b->order ? -1 : 1;
        });

        $arrAtt = array();
        foreach ($att as $obj) {
            array_push($arrAtt, $obj->attribute);
        }
        return $arrAtt;
    }

    private function getAttribute($attributeName) {
        /*
         * Recovers an attribute that its stored in Constants by name
         */
        $arrAtt = Config::get('constants.botMessages.attributes');

        $arrAtt = json_decode(json_encode($arrAtt[0]));

        $att = null;
        foreach ($arrAtt as $obj) {
            if ($attributeName === $obj->attribute) {
                $att = $obj;
            }
        }
        return $att;
    }

    private function saveAllConversation($actualStatus, $fullConver) {
        // Saves all the conversation and forgets infoAskUser for prevent multiple saves.
        Session::forget('infoAskUser');
        $arrAtt = $this->getAllAttributes();
        $chat = array();
        $chat['greet'] = Session::get('greet');
        foreach ($arrAtt as $att) {
            $chat[$att] = Session::get($att);
        }
        $chat['date'] = date('Y-m-d H:i:s');


        $endMessage = Config::get('constants.botMessages.byebye');

        if (array_key_exists('name', $chat)) {
            $endMessage .= 'Your name is ' . $chat['name'];
        }
        if (array_key_exists('age', $chat)) {
            $endMessage .= ', you are ' . $chat['age'] . ' old ';
        }
        if (array_key_exists('email', $chat)) {
            $endMessage .= 'and I can contact you on ' . $chat['email'];
        }
        $fullConver .= $endMessage;

        $chat['conversation'] = $fullConver;

        if (!Session::has('test')) {
            Chat::saveChat($chat);
        }

        return $fullConver;
    }

    private function getParsedConversationFileToHTML($arrJSON) {
        /**
         * Parse an array that contains conversation for show as html
         */
        $txtHtml = '';
        foreach ($arrJSON as $key => $value) {
            $value = json_decode($value);
            $txtHtml = $txtHtml . '' . $value->{'conversation'} . '<br/><hr><br/>';
        }
        return $txtHtml;
    }

    private function getHistory() {
        /*
         * Recover all saved conversations and transform to a historical show.
         */
        $html = '';
        $arrConv = Chat::getAllConversations();
        if ($arrConv !== null) {
            $html = $this->getParsedConversationFileToHTML($arrConv);
        }
        return $html;
    }

}

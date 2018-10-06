<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use File;
use Config;

class Chat extends Model {

    function __contruct() {
        
    }

    public static function saveChat($arrConversation) {

        $file = Config::get('constants.files.allConversations');

        if (!File::exists($file)) {
            File::put($file, '[]');
        }

        $arrJson = json_decode(File::get($file));

        array_push($arrJson, json_encode($arrConversation));
        File::put($file, json_encode($arrJson));
    }

    public static function getAllConversations(){
        $file = Config::get('constants.files.allConversations');
        $arrJson = null;
        if (File::exists($file)) {
            $arrJson = json_decode(File::get($file));            
        }        
        return $arrJson;
    }
    
}

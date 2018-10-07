<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Config;

class ChatBotControllerTest extends TestCase {

    private $name = 'RealName';
    private $email = 'aaaa@aa.com';
    private $age = '44';
    private $attName = 'name';
    private $attEmail = 'email';
    private $attAge = 'age';

    /**
     * Test pages are Up
     */
    public function testGetChatbotPageInPublicPath() {
        $response = $this->call('get', '/');
        $response->assertStatus(200);
    }

    public function testGetChatbotIndexPage() {
        $response = $this->call('get', 'index');
        $response->assertStatus(200);
    }

    public function testGetHistoryPage() {
        $response = $this->call('get', 'history');
        $response->assertStatus(200);
    }

    /*
     * Test json response are ok.
     */

    public function testGetChatbotStatus() {
        /*
         * Check if status of chatbot is working
         */

        $this->session(['test' => 1, 'chatbotStatus' => Config::get('constants.statusChatbot.OK')]);
        $response = $this->json('post', 'getChatbotStatus');

        $this->flushSession();

        $response
                ->assertStatus(200)
                ->assertJson([
                    'chatbotStatus' => Config::get('constants.statusChatbot.OK'),
        ]);
    }

    /*     * ********************** LOGIC **************************** 
     * Using name, email and age as attributes -> All this attributes must be in the well structured constants file
     * The order implemented for attributes are stored in Session variable 'infoAskUser' as array like [name,email,age].
     * If the order in Constants swap it will still work only using another order for questions. 
     * Tests will made with the logic mentioned before as order name -> email -> age.
     */

    public function testChatbotLogicSetName() {
        /*
         * Check if name is correct it return OK
         */

        $this->session(['test' => 1, 'lastAsk' => $this->attName, 'infoAskUser' => ['name', 'email', 'age'], 'fullConver' => '']);

        $response = $this->json('post', 'chatbot-ajax', ['message' => $this->name]);
        $this->flushSession();

        $response
                ->assertStatus(200)
                ->assertJson([
                    'errNum' => Config::get('constants.statusChatbot.OK'),
        ]);
    }

    public function testChatbotLogicSetEmail() {
        /*
         * Check if Email is correct it returns OK
         */

        $this->session(['test' => 1, 'lastAsk' => $this->attEmail, 'infoAskUser' => ['email', 'age'], 'fullConver' => '']);

        $response = $this->json('post', 'chatbot-ajax', ['message' => $this->email]);
        $this->flushSession();

        $response
                ->assertStatus(200)
                ->assertJson([
                    'errNum' => Config::get('constants.statusChatbot.OK'),
        ]);
    }

    public function testChatbotLogicSetEmailFail() {
        /*
         * Check if email is wrong it returns ERROR
         */

        $this->session(['test' => 1, 'lastAsk' => $this->attEmail, 'infoAskUser' => ['email', 'age'], 'fullConver' => '']);

        $response = $this->json('post', 'chatbot-ajax', ['message' => $this->name]);
        $this->flushSession();

        $response
                ->assertStatus(200)
                ->assertJson([
                    'errNum' => Config::get('constants.statusChatbot.ERROR'),
        ]);
    }

    public function testChatbotLogicSetAge() {
        /*
         * Check if age is correct and its the last attribute it returns END
         */

        $this->session(['test' => 1, 'lastAsk' => $this->attAge, 'infoAskUser' => ['age'], 'fullConver' => '']);

        $response = $this->json('post', 'chatbot-ajax', ['message' => $this->age]);
        $this->flushSession();

        $response
                ->assertStatus(200)
                ->assertJson([
                    'errNum' => Config::get('constants.statusChatbot.END'),
        ]);
    }

    public function testChatbotLogicSetAgeFail() {
        /*
         * Check if age is wrong and its the last attribute it returns ERROR
         */

        $this->session(['test' => 1, 'lastAsk' => $this->attAge, 'infoAskUser' => ['age'], 'fullConver' => '']);

        $response = $this->json('post', 'chatbot-ajax', ['message' => $this->name]);
        $this->flushSession();

        $response
                ->assertStatus(200)
                ->assertJson([
                    'errNum' => Config::get('constants.statusChatbot.ERROR'),
        ]);
    }

}

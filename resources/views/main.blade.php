<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
        <link href="{{ asset('css/mainSyle.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/bootstrap-4.1.3/css/bootstrap.min.css') }}" rel="stylesheet" />

        <!-- Scripts -->
        <script src="{{ asset('js/jquery-3.3.1.min.js') }}" ></script>
        <script src="{{ asset('js/functionsChatbot.js') }}" ></script>
        <script src="{{ asset('css/bootstrap-4.1.3/js/bootstrap.min.js') }}"></script>

    </head>
    <body>
        <div id="main">
            <div id="head">
                <button type="button" class="btn-primary" id="btnGoChatbot" onclick="location.href = '{{ url('index') }}'">Chatbot</button>
                <button type="button" class="btn-primary" id="btnGoHistory"  onclick="location.href = '{{ url('history') }}'">History</button>
            </div>
            <h1> Chat Bot </h1>
            <div id="containConver">
                @if(Session::has('fullConver')) 
                {!! Session::get('fullConver')!!}
                @endif
            </div>
            <div id="dvButtons">
                <input type="text" class="text-primary" id="txtMessage">
                <button type="button" class="btn-primary" id="btnSendMessage">Send</button>
                <button type="button" class="btn-primary" id="btnResetMessage">Reset</button>
            </div>
            <div id="errorMessage">

            </div>

        </div>
    </body>
</html>

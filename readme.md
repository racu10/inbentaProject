# Project Inbenta

- This project has been made with the Laravel framework.
- To start the project, you must place it in the htdocs folder in XAMMP / LAMMP. Or using the command "php artisan serve" inside the root project. 
- In case of using XAMMP / LAMMP we will place ourselves in the public folder for the visualization of the project.


## main structure 
- /app/ #There are the models 
- /app/Http/Controllers #There are the controllers
- /resources/views #There are the views
- /routes/web.php #There are the routing system. This comunicates directly with the Controllers.
- /public/js/ #There are the main JS files.
- /public/css/ #There are the main CSS files and Bootstrap.
- /storage/app/ # The .json file with all saved data of each conversation.
- /constants # There are the config files and constants

## Unit testing

- For execute unit tests with command line -> inside the root project, execute vendor/bin/phpunit.
- Tests are located in /tests/unit/ as ChatBotControllerTest.php



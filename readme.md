# HUST Online Judge Neo Version

HUST Online Judge is a web application.

This application still under construction, if you like improve it, please contact freefcw at qq.com.

[![StyleCI](https://github.styleci.io/repos/26354947/shield?branch=master)](https://github.styleci.io/repos/26354947)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/hustoj/hustoj-neo/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/hustoj/hustoj-neo/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/hustoj/hustoj-neo/badges/build.png?b=master)](https://scrutinizer-ci.com/g/hustoj/hustoj-neo/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/hustoj/hustoj-neo/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/hustoj/hustoj-neo/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/hustoj/hustoj-neo/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)

## ALERT

This version is not compatible with old version! The Judged is not upgrade! 

## Install Tutorial

1. Clone this repository

2. Install vendor package via composer

    ```bash
    cd hustoj-neo
    composer install --no-dev
    ```
    
    if you want have some develop, you can run `composer install` to install dev packages

3. config you application.

    ```bash
    php artisan key:generate # generate private key
    copy .env.example .env
    ```

4. database

    1. config you database in .env 

        ```
        # new database
        DB_CONNECTION=mysql
        DB_PORT=3306
        DB_HOST=127.0.0.1
        DB_DATABASE=neo
        DB_USERNAME=root
        DB_PASSWORD=root
        ```

    2. create new database structure

        ```
        php artisan migrate
        ```

    3. if you fresh to hustoj, can skip this step.
    
       migrate from old version hustoj, you can add old database configure:

        ```env
        # old database
        OLD_DB_PORT=3306
        OLD_DB_HOST=127.0.0.1
        OLD_DB_DATABASE=judge
        OLD_DB_USERNAME=root
        OLD_DB_PASSWORD=root
        ```


       If you migrate from the initial version [hustoj](https://github.com/zhblue/hustoj), you need migrate exist database structure follow [this wiki](https://github.com/freefcw/hustoj/wiki/database-changed), migrate database data via command:

        ```bash
    	php artisan database:migrate
    	```


6. Git code come with packed frontweb, if you don't need develop, can skip this step. build front web

        ```bash
        npm install
        npm run prod # build front web
        npm run admin-prod # build admin front web
        ```

7. setup other configure

        1. APP_NAME is your project name, will show on browser title and head brand.
        2. APP_DEBUG is debug switch
        3. APP_URL is your website url
        4. RABBITMQ_* is rabbitmq host, used to maintain solution queue to judger server. detail can see config/rabbitmq.php
        5. MAIL_* is config to setup email notify, detail can visit config/mail.php
        6. other relate config can visit config/hustoj.php
        7. Relate Project:
        	1. [judger](https://github.com/hustoj/judger) is judger for handle solution compile and runner.
        	2. [runner](https://github.com/hustoj/runner) is executor for compile solution source and execute program.

8. register a user from web, make you account has administrator privilege.

        ```bash
        php artisan assign:admin
        ```

9. use you administrator account login front web, you will see admin link in your top right.


## Contributing

Thank you for considering contributing to the HUST Online Judge! You can add issue or make pull request!

## Security Vulnerabilities

If you discover a security vulnerability within HUST Online Judge, please send an e-mail to freefcw at qq.com. All security vulnerabilities will be promptly addressed.

## License

The HUST Online Judge Neo Version Under MIT license.

## Thanks

* Thanks to [Jetbrains](https://www.jetbrains.com/?from=HUSTOJ)

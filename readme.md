# HUST Online Judge Neo Version

HUST Online Judge is a web application.

This application still under construction, if you like improve it, please contact freefcw at qq.com.

## ALERT

This version is not compatible with old version! The Judged is not upgrade!  

## Install Tutorial

1. clone this repository

2. install vendor package via composer

    ```
    cd hustoj-neo
    composer install
    ```

3. config you application.

    still under construction... 

4. database

    1. config you database in .env such like
    
        ```
        # new database
        DB_CONNECTION=mysql
        DB_PORT=3306
        DB_HOST=127.0.0.1
        DB_DATABASE=neo
        DB_USERNAME=root
        DB_PASSWORD=root
        
        # old database
        OLD_DB_PORT=3306
        OLD_DB_HOST=127.0.0.1
        OLD_DB_DATABASE=judge
        OLD_DB_USERNAME=root
        OLD_DB_PASSWORD=root
        ```
    
    2. If you migrate from the initial version [hustoj](https://github.com/zhblue/hustoj), you should migrate database structure follow [this wiki](https://github.com/freefcw/hustoj/wiki/database-changed)
    
    3. migrate database structure via command:
    
        ```
        php artisan databse:migrate
        ```
        
    4. make you account has administrator privilege.
    
        ```
        php artisan assign:admin
        ```

5. frontend, you should run this command for administrator ui 
    
    ```
    npm install
    npm run production
    ``` 

6. continue...

## Contributing

Thank you for considering contributing to the HUST Online Judge! You can add issue or make pull request!

## Security Vulnerabilities

If you discover a security vulnerability within HUST Online Judge, please send an e-mail to freefcw at freefcw at qq.com. All security vulnerabilities will be promptly addressed.

## License

The HUST Online Judge Neo Version Under MIT license.

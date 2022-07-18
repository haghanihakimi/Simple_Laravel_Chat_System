# Simple_Laravel_Chat_System
### Author: Haghani Hakimi

#### Overview:

This is my first Laravel app that I created recently. As I decided to learn Laravel, I had this idea to create a simple chat system using Laravel and Vue 2.<br/>
In this app, users can create and manage their accounts. They can make their account private or public!
##### Features of this app:
1. Creating and managing accounts
2. Managing account privacy<br/>
   - Users can set their account to private or LOCKED to prevent receiving unwanted friend requests!<br/>
   - Users can mark any friend requests as SPAM so targeted users (who's marked as spam) can't send any more requests.
3. Sending and receving friend requests
3. Users can block or unblock other users.
4. Users can add extra security to their account<br/>
    - Users can enable 2-Factor Authentication feature to verify security code before logging to their account.

And there are some more features in this simple application.<br/>

##### Technologies used in this app:
1. HTML & CSS
2. PHP Laravel & Vue JS 2
3. MySQL & Redis
4. Laravel Pusher

#### Default user account password:
The default password for user account is: **123456789**

#### Basic requirements to run and test application:
1. NPM/NodeJS
2. PHP Composer
3. **[Pusher](https://pusher.com/)** account to have required keys
4. **[MailTrap](https://mailtrap.io/signin)** account to have reuqired keys
5. **REDIS application** OR **[REDIS Cloud Account](https://app.redislabs.com/#/login)**
6. A local server on your system, such as **[WAMPServer](https://www.wampserver.com/en/)** or **[XAMPP](https://www.apachefriends.org/download.html)**


#### Note: Laravel DebugBar is installed. Keep APP_DEBUG to TRUE to be able to use this tool during Development Stage.

#### PS:
There can be bugs and some problems in every project and this one also may have some issues too!
There will be regular changes/updates to this project to inject my new knowledges and improvements to this project time-to-time in the future!

## Configure & run the project!
If you already have worked with Laravel and you have good experience with this Framework, then you may not have any problem with running this project.<br/>
In any way, I will explain how to run this project step by step!<br/><br/>

#### Step 1: XAMPP, Composer, NodeJS and Redis
1. First: please make sure you already installed **[XAMPP](https://www.apachefriends.org/download.html)**, **[Composer](https://getcomposer.org/download/)** and **[NodeJS](https://nodejs.org/en/)** on your system.<br/>
2. Second: make sure you installed Redis on your system. Redis DB is used in this project and without **[Redis](https://github.com/microsoftarchive/redis/releases/tag/win-3.0.504)** DB connection, you may face many errors and problems!
    * If you don't want to install any application on your system, you can create a **[Redis Cloud Account](https://app.redislabs.com/#/login)** and follow stage 2 and stage 4!<br/>

#### Step 2: REDIS Cloud Account & database.php file
If you decided not to install Redis application (given link in previous step), then the only way will be connecting entire project to your **[Redis Cloud Account](https://app.redislabs.com/#/login)**. Please create an account on Redislab. It is **free** and you don't have to install any application on your system! <br/>

#### Step 3: Add ``` .env ``` file to project
You may see a file named **.env.example**! Please copy & paste it then rename it to ```.env```. <br/>

#### Step 4: Redis DB configuration
1. Open ```.env``` file and make sure **DB_DATABASE** is equal to **tuba**. You can also change this name to whatever you want and then go to next step!<br/>
2. In ``` .env ``` file, scroll down to Redis DB configuration section.
3. Set ``` REDIS_HOST ``` equal to **Public endpoint** of Redis database you have on your Redis Cloud account.
4. Set ``` REDIS_PORT ``` equal to **Port Number** you see at the end of **Public Endpoint** address
5. Set ``` REDIS_USERNAME ``` equal to **Default User - Username** you can find under **Security** section of your Redis Cloud database.
6. Set ``` REDIS_PASSWORD ``` equal to **Default user password** you can find under **Security** section of your Redis Cloud database.
7. I attached an example of Redis DB configuration inside ``` .env ``` file down below. These information are just example and not the real one!
```
REDIS_HOST="redis-12345.c666.asia-northeast6-6.gce.cloud.redislabs.com"
REDIS_PORT="23470"
REDIS_USERNAME="default"
REDIS_PASSWORD="svEs9vncba63IWp6o4PmEyOwZlPA0T0"
```

#### Step 5: Create SQL Database on PHPMyadmin
Go go to **localhost/phpmyadmin** and create a database. Name the database **tuba** or whatever name you used for **DB_DATABASE** in previous step. <br/>

#### Step 6: Dependencies & Packages installation:
1. If you use **[VSCODE](https://code.visualstudio.com/download)** press **CTRL + J** to open terminal section.
2. In terminal section, please run ``` php artisan migrate:fresh --seed ``` to create tables and populate data samples into tables.
3. In terminal section, type & run ``` composer install ``` to install **Laravel COMPOSER** dependencies/packages
4. In terminal section, type & run ``` npm install ``` to install **node_modules**. This installs all libraries/plugins/packages used in VUE JS (front-end).<br/>
#### Step 7: Run the project!
If you done all steps mentioned above, it's time to run this project.<br/>
1. Type and run ``` php artisan serve ``` OR if you want to run project over **HTTPS** type and run ```php artisan serve --port=443  ```
2. Type and run ``` npm run watch ``` to compile front-end assets such as **styles** written in SCSS and Vue JS components & files.
RUN IN TERMINAL
1. composer install
2. copy .env.example .env
3. php artisan key:generate

!--Make sure to have a ready database (smart_accounting_v3)--!

4. run xampp and open apache and mysql

!-- Run this in terminal again. --!
5. php artisan migrate
6. php artisan db:seed (or php artisan migrate:fresh --seed)
7. php artisan storage:link

-- FOR LOCALHOST --
8. now go to localhost(browser). Go to Smart-Accounting-v3/public
    --If error appears double check steps 1 to 4.--
    --Accounts Passwords: password123--

-- FOR CLOUDFLARE --
8. edit .env file APP_URL, change into this: APP_URL=http://localhost
9. open cmd or powershell and paste this: winget install --id Cloudflare.cloudflared
10. open httpd-vhosts.conf file located at xampp\apache\conf\extra
11. replace the <Virtual Host> with this then save: 

<VirtualHost *:80>
    ##ServerAdmin webmaster@dummy-host.example.com
    DocumentRoot "C:/xampp/htdocs/Smart-Accounting-v3/public"
    ##ServerName smart-accounting.local
    <Directory "C:/xampp/htdocs/Smart-Accounting-v3/public">
    	AllowOverride All 
    	Require all granted 
    </Directory>
</VirtualHost>

12. go back to cmd run this: cloudflared tunnel --url http://localhost:80

---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

* All CSS, JS, SCSS can be found under 'public' folder.

* HTML files are in resources/views/convert/. All html files under 'Convert' needs to be converted to .blade.php

* index.blade.php has already been converted.

* All link, ref, script, img statements needs syntax changes in order to work in Laravel, For example:

    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">    Change it to =    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <img src="assets/images/logo-full.png" alt="" class="logo logo-lg">    Change it to =    <img src="{{ asset('images/logo-full.png') }}" alt="" class="logo logo-lg">

    <script src="assets/vendors/js/vendors.min.js"></script>    Change it to =    <script src="{{ asset('js/vendors.min.js') }}"></script>


* Remove 'assets' from the reference statements too, For example:

    <img src="{{ asset('assets/images/logo-full.png') }}">    Change it to =    <img src="{{ asset('images/logo-full.png') }}">

    -Because there is no asset folder. All images, css, scss, etc is under public. SO 'assets/' is REDUNDANT!


--IF YOU HAVE FURTHER INSTALLATION SOLUTIONS OR PATCHES ADD OR EDIT THIS FILE--
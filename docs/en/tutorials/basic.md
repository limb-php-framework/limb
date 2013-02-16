# CRUD — basic example of Limb3 based Create-Read-Update-Delete application
## The main goals of this tutorial

* Show the basics of Limb3 framework
* Demonstrate how to create an application with Limb3 from the scratch using WEB_APP package
* Make a short glance at WACT template engine: main tags, expressions etc.

You can find the source code of the example at Limb3 projects section (see CRUD).

## System requirements

* OS: *nix, Windows 98/ME/2000/XP
* Web-server: Apache 1.3+ (with mod_rewrite)
* PHP: PHP 5.1.4+
* DB Server: MySQL 4.1+ or SQLite

You can place your project files somewhere in your Apache DocumentRoot or you can also setup your web server with a Virtual Host:

    <VirtualHost *>
      DocumentRoot /var/dev/crud/www/
      ServerName crud
      ErrorLog logs/crud-error_log
      CustomLog logs/crud-access_log common
    </VirtualHost>

Also make sure what Apache reads .htaccess files from your application directory. There should be something like this in Apache httpd.conf file:

    <Directory "/var/dev/">  
        Options Indexes FollowSymLinks  
        AllowOverride All  
    </Directory>  

## Limb3 packages
Limb3, as framework, distributed via several packages, like, CORE, ACTIVE_RECORD, WEB_APP, DBAL etc. You can read a short tutorial about [Limb3 packages](../packages_architecture.md).

For our example application we need WEB_APP package and all other packages WEB_APP depends on.

The simplest way is to get WEB_APP via Limb3 PEAR channel:

    #PEAR-1.5 is in alpha
    $ pear install PEAR-alpha 
    # discovering Limb3 PEAR channel
    $ pear channel-discover pear.limb-project.com
    #web_app is in alpha too
    $ pear install limb/web_app-alpha 

There are [many different ways to get Limb3 source code](../how_to_download.md). For example you can download the release archive of Limb3 from SourceForge.net or checkout the code from SVN - just use method you feel comfortable with.

## Steps

1. [Step 1. Creating a skeleton of your application and make it run](./basic/step1.md)
2. [Step 2. Displaying newsline using WACT-template](./basic/step2.md)
3. [Step 3. Adding forms to allow creating and editing news. Data validation. News removal](./basic/step3.md)
4. [Step 4. Templates optimization. Adding news pagination](./basic/step4.md)
5. [Step 5. Adding more functionality: single news in detail, 5 latest news on the main page, sorting etc.](./basic/step5.md)
6. [Step 6. Creating newsline RSS-feed](./basic/step6.md)
7. [Step 7. Further readings](./basic/step7.md)

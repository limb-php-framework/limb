# Step 1. Creating a skeleton of your application and making it run
## Project file structure

If you installed Limb3 via PEAR channel go to ~pear/limb/web_app and copy the contents of ~pear/limb/web_app/skel folder into your project directory (say /var/dev/crud/). You can also follow the instructions on [How to setup Limb3 based project](../../installation.md) and download Limb3 sandbox application from SourceForge.net

The initial file structure of your project should be as follows:

    crud
    |
    +-/src - here we'll put our source code files
    |
    +-/template - here all our templates will be stored
    |
    +-/settings - configuration and settings files go here
    |
    +-/var - temporaty folder for cache and compiled templates files
    |
    +-/www - this is DocumentRoot of our application
    |   |
    |   |-index.php
    |   |-.htaccess
    |   `-favicon.ico
    |
    |-setup.php
    `-setup.override.php

Make sure that Apache has write&read access for ~crud/var/ folder

## Setting up application
### setup.php file
setup.php file is a common place for many configuration stuff:

* changing include_path
* including necessary classes and modules
* defining constants
* etc.

Our setup.php file is very simple:

    <?php
    // Adding a directory where Limb3 is located to include_path
    // You can remove this lines if you installed Limb3 via PEAR channel
    set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR . 
                     '/path/to/limb/parent/dir/' . PATH_SEPARATOR .
                     get_include_path());
 
    // LIMB_VAR_DIR point at folder with temporary files like compiled templates, cache files etc.
    @define('LIMB_VAR_DIR', dirname(__FILE__) . '/var/');
 
    // including Limb3 core module
    require_once('limb/core/common.inc.php');
    // including WEB_APP package base module
    require_once('limb/web_app/common.inc.php');
    ?>

### settings/db.conf.php file
db.conf.php file stores database connection parameters. For MySQL db.conf.php looks like as follows (you need to create a new MySQL database for your application yourself):

    <?php
    $conf = array('dsn' => 'mysql://root:secret@localhost/limb_crud?charset=utf8');
    ?>

For SQLite:

    <?php
    $conf = array('dsn' => 'sqlite://localhost/path/to/sqlite.db?charset=utf8');
    ?>

### settings/wact.conf.php file
WACT template engine settings can be found in settings/wact.conf.php file.

    <?php
    $conf = array(
    'forcescan' => 0,
    'forcecompile' => 1
    );
    ?>

**forcescan** tells WACT if it needs to scan certain folders in order to find WACT tags or filters that can be used in templates and that WACT compiler understands. This option is usefull only while creating new tags or running unit tests for WACT package. In our case we give **forcescan** 0 value.

Next parameter — is **forcecompile**. It tells WACT to compile templates anew every time. WACT compiles templates to php-code like Smarty does. If **forcecompile** is set to 0 WACT compiles template only ones and next time it just executes them. Compilation is rather slow and complex procedure and requires a lot of CPU time specially for complex templates thats why we set **forcecompile** to 1 only on developer boxes and always have it as 0 on production servers.

## Running the project
Open your browser and type project address. It can be something like http://localhost/crud/www or just http://crud depending on how you configured your web server. If you copied source code from ~limb/web_app/skel/ you should see just «Default main page» on your screen and if you installed Limb3 sandbox you should see a pretty invitation page.

If so let's go further.

## How our application works
~crud/www — is a DocumentRoot of your project.

### .htaccess file
.htaccess file just redirects all requests to **index.php** file if request doesn't point to any static content (file, directory or symlink):

    RewriteEngine On

    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-s
    RewriteCond %{REQUEST_FILENAME} !-l
    RewriteRule ^(.+)$ index.php?%{QUERY_STRING} [L]

### index.php file
**~crud/www/index.php** is an starting point of your application:

    <?php
    require_once(dirname(__FILE__) . '/../setup.php');
    require_once('limb/web_app/src/lmbWebApplication.class.php');
 
    $app = new lmbWebApplication();
    $app->process();
    ?>

As a matter of fact Limb3 based application is a single class, like lmbWebApplicaion. Don't worry about lmbWebApplication structure — we go into architectural details on Limb3 in the next tutorial (SHOP example). Our goal for this tutorial is just to show the very basics of Limb3 usage.

## What's next?
[Step 2. Displaying newsline using WACT-template](./step2.md)

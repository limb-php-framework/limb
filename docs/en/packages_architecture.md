# Intro to Limb3 packages concept
Limb3 — is a framework, separated into packages. What is package? Package — is a bunch of classes and(or) functions that solve some highly tailored tasks, those files are places into directory with particular structure (see below). For example, there are CORE package, WACT template engine package, DBAL package with database abstraction layer, I18N package to perform internationalization in your Limb3 applications, TREE package to deal with hierarchical data and many more.

Thus an application build with Limb3 uses several packages and moreover is considered as a package itself.

[Complete list of Limb3 packages](./packages_architecture.md)

## Howto include Limb3 package into your project

1. You must place a directory with Limb3 packages into your PHP include_path.
2. we prefer use special lmb_require instead of standard require_once/include_once since lmb_require supports lazy including with __autoload function.

For example:

    <?php
 
    // setting up include_path
    set_include_path('/path/to/limb/packages/parent/dir/' . PATH_SEPARATOR .
                     get_include_path()); 
 
    // including lmbUri class from NET package using standard require_once
    require_once('limb/net/http/lmbUri.class.php');
 
    // including lmbUri class from NET package using optimized version of require_once 
    // that supports lazy including. That means what lmbUri is not included immediately, 
    // real including is performed only with first lmbUri initialization.
    lmb_require('limb/net/http/lmbUri.class.php');
 
    $uri = new lmbUri('http://test.com');
 
    ?>

## Common directory structure of Limb3 package

Dir | Description
----|------------
build/ | Some build scripts and utilities
bin/ | CLI scripts
examples/	| Examples of usage
lib/ | External libraries that package depends on
init/	| Data files that can be used for initialization, e.g. sql-files
settings/	| Settings files (ini or conf files)
shared/	| None-php files that used in many projects and have to available via HTTP (css-styles, images, javascrips)
src/ | Source code: classes, modules, etc.
template/ | Template files
tests/ | Units tests
www/ | Web root of Limb3 based project (projects are considered to be packages too)

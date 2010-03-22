Kodoc

Documentation viewer and API browser.

Kodoc allows you to create documentation for your Kohana Modules.

To make your module show up you need three things: the config file, the menu file, and the docs themselves.

For example, for Kohana 301, the config file looks like:

**Config file:**  `modules/kohana301/config/userguide.php`
    <?php defined('SYSPATH') or die('No direct script access.');

    return array(
        'userguide' => array(
            
            // This should be the path to your userguide pages
            'kohana301' => array(
                
                // The name that should show up on the userguide index page
                'name'  => 'Kohana 301',
                
                // A short description of this module
                'desc'  => 'A beginners guide to Kohana 3.',
                
                // Where is the menu page for this page? WITHOUT 'guide/' at the beggining or '.md' at the end
                'menu'  => 'kohana301/menu',
            )	
        )
    );
    
**Menu file:** `modules/kohanut/guide/kohana301/menu.md`
    ### [Kohana 301](kohana301)
    1. [Pre-requisites](kohana301/prereqs)
        - [PHP5 and OOP](kohana301/prereqs/php5oop)
        - [Model View Controller](kohana301/prereqs/mvc)
        - [Working with Git](kohana301/prereqs/git)
        - [Differences from Kohana 2](kohana301/prereqs/differences)
    2. [Basics](kohana301/basics)
        - [Cascading Filesystem](kohana301/basics/cascade)
        - [The Bootstrap](kohana301/basics/bootstrap)
        - [Routing](kohana301/basics/routing)
    3. [Tutorials](kohana301/tutorials)
        - [Installing](kohana301/tutorials/installing)
        - [Hello World](kohana301/tutorials/hello)
        - [Using Routes](kohana301/tutorials/routes)
        - [Using a Template](kohana301/tutorials/template)
        - [Using a Database](kohana301/tutorials/database)
        - [Using Config files](kohana301/tutorials/config)
        - [Using Cookies](kohana301/tutorials/cookies)
        - [Using I18n](kohana301/tutorials/i18n)
    4. [Security](kohana301/security)
        - [XSS](kohana301/security/xss)
        - [Validation](kohana301/security/validation)
        - [Deployment](kohana301/security/deployment)

You can use either numbered lists or unordered lists, they show up the same.  Every item MUST be a link, and the heirarchy must be preserved in order for breadcrumbs to work correctly. So if there is a page with the url `kohana301/security/validation` there must be a page with the url `kohana301/security` (the file would be `guide/kohana301/security/index.md`) and a page with the url `kohana301` (the file would be `guide/kohana301/index.md`).

**The docs**

There should be a folder with the name of your module inside the guide folder (for example `guide/kohana301`) and all docs should be inside that folder.  There should be an `index.md` file directly inside that folder (for example `guide/kohana301/index.md`) and every subfolder should have an `index.md` file as well.

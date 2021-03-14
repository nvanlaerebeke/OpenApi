OpenApi-Samples: Versioning
===========================

Sample content
===============
See the [OpenApi-Samples Basic] [1] example first if you have not done so, this is a sequel.

This sample demonstrates how versioning in OpenApi works, it shows how to create multiple versions and how to version multiple parts of your application

Installation
=============
To use versioning, you must use a different dispatcher, this means a small change in your index.php file:

    App::uses('Dispatcher', 'Routing');
    $Dispatcher = new Dispatcher();

Must become:

    App::uses('ApiDispatcher', 'OpenApi.Routing');
    $Dispatcher = new ApiDispatcher();

If this isn't done, the routing will not work and unexpected results will be given.


Configuration
=============
In OpenApi can specify 0 or more versions and is configured in the core.php: 

    /**
     * Available API versions
     * 
     * Example, controller versions are located in APP_DIR/Controllers/<version>/<ControllerClass>
     *
     * Priority between the versions is from top to bottom 
     */
    Configure::write('OpenApi.Versions', array('2.0', '1.1', '1.0'));
    
When no versions are given, versioning is turned off.

If multiple versions are given, the version in the url is used to determine what API version you want to serve.

When the requested version can't be found, the OpenApi will search for the first version it finds top to bottom starting from the version given in the url, this to make sure of backwards compatibility.

Example requests:
1.  /2.0/Post/
2.  /1.1/Post/
3.  /Post/1

In the first case, the OpenApi will search for version 2 of the Post controller,
when it can't be found, it'll start searching in 1.1, then in 1.0 and when that fails it'll search in the default location

In the second case, OpenApi will search for the 1.1 version of the Post controller,
when that can't be found it'll search for the 1.0 version and when no versioned Controller can't be found, OpenApi will search in the default cake location.
This request will NOT try to use the 2.0 version, as it's 'newer' than what's being requested and would break backwards compatibly.

In the third and last case, OpenApi will start searching for the PostController starting with version 2.0 and finishing in the default cake location.


OpenApi can version just about anything, going from your controllers, components, models to your libs.
For a complete list, run the following code in a controller:

    print_r(array_keys(app::paths())); 
    
Depending on what type of api you're making, you'll want to version different parts within the application.
Commonly, the controllers will be the ones getting versioned, because the output(response array) you set there will determine the output format.

The data layer isn't normally versioned, but it's possible.

An example config:

    /**
     * List the type of components that need the ability to version
     * For a full list of components do:
    ;*     print_r(array_keys(app::paths()));
     *
     * The versioned files are always located under the base path and then a directory with the version as name
     * Examples: 
     *  - /path/to/cakephp/app/Controller/0.1/MyController.php
     *  - /path/to/cakephp/app/Controller/Component/0.1/MyComponent.php
    */
    Configure::write('OpenApi.VersionTypes', array('Controller', 'Controller/Component/Auth'));

As you can see in the above description, the cakephp search paths are manipulated to make the framework search.

If we take the versions we talked about previously, valid files/directories are:
 - Controller/2.0/PostController.php
 - Controller/1.1/PostController.php
 - Controller/1.0/PostController.php
 - Controller/PostController.php
 - Controller/Component/Auth/2.0/PostIndex/
 - Controller/Component/Auth/1.1/PostIndex/
 - Controller/Component/Auth/1.0/PostIndex/
 - Controller/Component/Auth/PostIndex/



Usage
=====

Point your browser to the following url's:
 - http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/&lt;version&gt;/post/
 - http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/&lt;version&gt;/post/delete/1
 
    

  [1]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Basic        "OpenApi-Samples Basic"
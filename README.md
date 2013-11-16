OpenApi
=======

OpenApi is a CakePHP plugin to enable rapid development for REST Api's.

It comes with everything you need to develop a fully featured REST Api.

Key Features
==============

* Easy plug and play authentication and authorization methods 
* Supports statefull and stateless requests
* Build in flexable versioning system, version what you want
* Automatically handles output (json/xml)
* Easy error handling with meaning and automatic output
* Automated parameter validation
* Configurable REST routing (GET/POST/PUT/DELETE)
* Build for the fat model slim controller principle
* Build with scalibility in mind
* Flexible licensing(MIT License)

Examples/Tutorials and documentation
=====================================

Examples and tutorials can be found in the [OpenApi-Samples] [1] project.

If needed aditional documentation will be created later.



Installation
=============

To install the plugin, download the source and put it in a your plugin directory under OpenApi.

Example:

/path/to/cakephp/app/plugins/OpenApi

Now load the plugin in your app/Config/bootstrap.php file

    CakePlugin::loadAll(
        array(
            'OpenApi' => array(
                'bootstrap' => true,
                'routes'    => true
            )
        )    
    );

It's imporant to note that you set bootstrap and routes to 'true'

Configuration
==============

OpenApi as a few configuration settings you can tweak to customize it's behaviour.

The options are:
 - Error Handler configuration
 - Exception Handler configuration
 - Configuration setting for splitting authorization classes by authorization type
 - Versioning

All of these settings are dicussed and used in the [OpenApi-Samples Basic] [2] project.

General
========

See the OpenApi-Samples basic example
All configuration options must be put in the core.php of your application


Error Handling
===============

For more details on Error handling, see the [OpenApi-Samples ErrorHandling] [3] project.


OpenApi comes with it's own error handler.

This is to make sure that the correct output is given

If you have your own, it's best to inherit from this one, 

    /**
     * 
     * ErrorHandler::handleError() is used. It will display errors using Debugger, when debug > 0
     * and log errors with CakeLog when debug = 0.
     *
     * Options:
     *
     * - `handler` - callback - The callback to handle errors. You can set this to any callable type,
     *   including anonymous functions.
     *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
     * - `level` - int - The level of errors you are interested in capturing.
     * - `trace` - boolean - Include stack traces for errors in log files.
     *
     * @see ErrorHandler for more information on error handling and configuration.
     */
    App::uses('ApiErrorHandler', 'OpenApi.Lib/Error');
	Configure::write('Error', array(
		'handler' => 'ApiErrorHandler::handleError',
		'level' => E_ALL & ~E_DEPRECATED,
		'trace' => true
	));

Like the error handler, OpenApi has it's own ExceptionHandler

    /**
     * Configure the Exception handler used for uncaught exceptions. By default,
     * ErrorHandler::handleException() is used. It will display a HTML page for the exception, and
     * while debug > 0, framework errors like Missing Controller will be displayed. When debug = 0,
     * framework errors will be coerced into generic HTTP errors.
     *
     * Options:
     *
     * - `handler` - callback - The callback to handle exceptions. You can set this to any callback type,
     *   including anonymous functions.
     *   Make sure you add App::uses('MyHandler', 'Error'); when using a custom handler class
     * - `renderer` - string - The class responsible for rendering uncaught exceptions. If you choose a custom class you
      *   should place the file for that class in app/Lib/Error. This class needs to implement a render method.
     * - `log` - boolean - Should Exceptions be logged?
     *
     * @see ErrorHandler for more information on exception handling and configuration.
     */
    App::uses('ApiErrorHandler', 'OpenApi.Lib/Error');
	Configure::write('Exception', array(
		'handler' => 'ApiErrorHandler::handleException',
		'renderer' => 'AppExceptionRenderer',
		'log' => true
	));
 
Versioning
===========

For more details and samples on Versioning, see the [OpenApi-Samples Versioning] [4] project.

OpenApi can version any kind of file in your app.
It starts by listing your versions in the configuration:
    
    /**
     * Available API versions
     * 
     * Example, controller versions are located in APP_DIR/Controllers/<version>/<ControllerClass>
     *
     * Priority between the versions is from top to bottom 
     */
    Configure::write('OpenApi.Versions', array(
        '0.3'
        '0.2'
        '0.1'
    );


Once the versions are configured, make sure to configure what you want to version.

Do you want to version just the Controllers?, or also the components.

    /**
     * List the type of components that need the ability to version
     * For a full list of components do:
    ;*     print_r(array_keys(app::paths()));'
     *
     * The versioned files are always located under the base path and then a directory with the version as name
     * Examples: 
     *  - /path/to/cakephp/app/Controller/0.1/MyController.php
     *  - /path/to/cakephp/app/Controller/Component/0.1/MyComponent.php
    */
    Configure::write('OpenApi.VersionTypes', array(
        'Controller'
     );

 
 
Auth
====

This is only a small overview, for more details on the Auth process, see the [OpenApi-Samples Auth] [5] project.


Auth has only 1 config setting, one that allows you to have the authorization be done by a certain authorization context.

    Configure::write('OpenApi.SeparateAuthorization', true);

This comes in handy when we have an API call that must be accessable with multiple authentication methods.

An example of this would be a 'PostController' delete method that must be accessable using basic authentication, using a certificate and at the same time by a certain server IP Address.

This would mean you have 3 auth classes, one for Basic Authentication, one for the Username and Password and another one for checking IP Addresses.


You can devide these in 2 groups:
 - User access: Basic Authentication and Certificate will return a User
 - Server access: IP Address, this one doesn't have a 'User' context, we just know the IP Address

The trick to accomplish this lies in the Authentication process.
It's known in what 'context' the request is done when authenticating.

Internally this is called the 'authorizetype', that authtype is than used to call the specific Authorize class.
The authorization class that will be called will be '<Controller><Action><AuthorizeType>Authorize'.
The classes must be located in app/Controller/Component/Auth/Authorize/<Controller><Action>/

Example authorize classes: 
  - PostEditUserAuthorize in: app/Controller/Component/Auth/Authorize/PostEdit/
  - PostDeleteServerAuthorize in: app/Controller/Component/Auth/Authorize/PostDelete/
 
When setting this to false, the authorize classes arn't devided in sub directories and are located in the the app/Controller/Component/Auth/Authorize directory directly. 
 Going from the examples above:
  - PostEditAuthorize in: app/Controller/Component/Auth/Authorize/
  - PostDeleteAuthorize in: app/Controller/Component/Auth/Authorize/


For more information on this topic, see the OpenApi-Samples



Getting started
=================

Start off with a simple API call is easy, just create the controller class and you're good to go.
This example has no authentication/authorization, it's a controller 'PostController' that has 2 function, a get and delete.

    
    /**
     * Import base controller class
     */
    App::uses('OpenApiAppController', 'OpenApi.Controller');

    /**
     * Serie Controller, provides Api logic for Series
     *
     * @package       OpenApi.Controller
     * @link          http://book.cakephp.org/2.0/en/controllers.html
     */
    class PostController extends OpenApiAppController {
        /**
         * Models to include 
         * @var array
         */
        public $uses = array('Post');
        
        /**
         * Authentication configuration
         */
        public $AuthConfig = array(
            'get' => array(
                'NoAuth' => array()
            ),
            'delete' => array(
                'NoAuth' => array()
            )
        );
        
        /**
         * Gets a post by id
         * @param string $pID Post ID
         */
        public function get($pID) {
            $this->Post->findByID($pID);
        }
        
        /**
         * Delete a post
         * @param string $pID Post ID
         */
        public function delete($pID) {
            $this->Post->delete($pID);
        }
    }


  [1]: https://github.com/nvanlaerebeke/OpenApi-Samples        "OpenApi-Samples"
  [2]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Basic        "OpenApi-Samples Basic"
  [3]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Error%20Handling        "OpenApi-Samples ErrorHandling"
  [4]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Versioning        "OpenApi-Samples Versioning"
  [5]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Auth        "OpenApi-Samples Auth"
OpenApi-Samples: Basic Usage
=============================

Sample content
===============
This sample contains a very basic overview of the steps required to get a simple REST call response.<br />
It does not contain versioning or any kind of Auth process.

Usage
=====

Point your browser to the following url's:
 - http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/index or when using http method get http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/ 
 - http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/delete/1 or when using http method delete http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/1
 
For the get the output will be:

    <Post>
        <author>SomeGuy</author>
        <messsage>MyMessage</messsage>
        <date>2013-11-16 08:17</date>
    </Post>
    
For the delete the output will be:

        <result>success</result>
    
How it's done
==============

Creating a basic response like this doesn't require much work.

Each OpenApi request contains a few simple steps:
 1. Dispatching
 2. Authentication
 3. Authorization
 4. Execution
 5. Response

For step 1 and 5 you don't need to do anything, those are done automatically for you.<br />
The 2nd and 3rd step, Authentication and Authorization, you must specify what kind of authentication you want to use, and create a class that will do Authoriation for that action.<br />
For now, don't think to much about the Auth process, we'll get back to that later.<br />
The Execution, step 4, is the logic you'll put in your controller(s)/model(s)/lib(s) etc and are specific for you application<br />
The final step, step 5, you can use automatically generated response in xml or json format, there is an option to create your own custom views, but don't mind that for now.


Step 1: Load the plugin
=======================

Load the plugin, this is done in the bootstrap.php file in your Config directory:

    CakePlugin::loadAll(
        array(
            'OpenApi' => array(
                'bootstrap' => true,
                'routes'    => true
            )
        )    
    );

Step 2: Configure the plugin
==============================

There are a few configuration settings you must set before you can start using the plugin.

Add the following at the bottom of your core.php file:

    /** 
     * OpenApi configuration 
     */
    
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
    
    
    
    /**
     * Available API versions
     * 
     * Example, controller versions are located in APP_DIR/Controllers/<version>/<ControllerClass>
     *
     * Priority between the versions is from top to bottom 
     */
    Configure::write('OpenApi.Versions', array());
    
    
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
    Configure::write('OpenApi.VersionTypes', array());
    
    /**
     * Clear paths that don't need to be cached because of versioning
     * Have to do it here, before App::init is called
     * Plugins aren't loaded yet at this stage
     * Only run when using OpenApi's Versioning
     */
    /*$result = Cache::read('file_map', '_cake_core_');
    if(!empty($result)) {
        foreach(Configure::read('OpenApi.VersionTypes') as $type) {
            foreach(Configure::read('OpenApi.Versions') as $version) {
                foreach($result as $key => $value) {
                    if(strpos($value, ROOT.DS.APP_DIR.DS.$type.DS.$version.DS) === 0) {
                        $result[$key] = null;
                    }
                }
            }
        }
        Cache::write('file_map', array_filter($result), '_cake_core_');
    } */
    
    Configure::write('OpenApi.SeparateAuthorization', true);
    
    /**
     * OpenApi default output format
     * Can be xml or json
     */
    Configure::write('OpenApi.DefaultOutputFormat', 'xml');
    
    
    /**
     * Enable stateless auth process
     * Be warned that some auth classes use sessions to store user information for future requests
     * Setting this to true might enable unwanted extra auth actions when requests are expected to be statefull
     * 
     * The stateless requests are pefect for when you authenticate by ip, or use basic auth 
     */
    Configure::write('OpenApi.Auth.Stateless', true);
    

Create the Controller(s)
=========================

The controller needs the OpenApiAppController as a base class.<br />
In most cases, if the application is an Api only, all Controller will need to have the OpenApiAppController as a base class.<br />
So the easiest is making your AppController extend the OpenApiAppController instead of the normal Controller class and put your own custom logic in the AppController.<br />
Make sure to call the parent beforeFilter and beforeRender functions!, else things won't work as they should.

An example AppController:

    App::uses('OpenApiAppController', 'OpenApi.Controller');
    class AppController extends OpenApiAppController { }
    
The PostController:

    class PostController extends AppController {
        /**
         * Auth configuration
         */
        public $AuthConfig = array(
            'index' => array(
                'NoAuth' => array()
            ),
            'delete' => array(
                'NoAuth' => array()
            )
        );
    
        public function index() {
            $this->set('response', array(
                'Post' => array(
                    'author' => 'SomeGuy',
                    'messsage' => 'MyMessage',
                    'date' => date('Y-m-d h:i')
                )
            ));
            $this->set('_serialize', 'response');
        }

        public function delete($pID) {
            $this->set('response', array('result' => 'success'));
            $this->set('_serialize', 'response');
        }
    }

As you can see, the controller has 2 functions and an array that holds some configuration.<br />
In the AuthConfig we're saying that the 2 functions don't need any authentication, for more information about the auth process, see the Auth sample.<br />
Note that both functions set a return variable 'response' and '_serialize', these are needed.<br />

See here for more info: "http://book.cakephp.org/2.0/en/views/json-and-xml-views.html"

Create the Authorizer
=====================

As a security precaution you always need a class that does the authorization for your action.<br />
Even if you want to skip it!, this is to prevent any security issues in larger api's - where a missing or deleted file can open big holes.

Authorizers are located in the Component/Authorization directory.<br />
Each action must have a directory <ControllerName><Action> and must contain a class <ControllerName><Action><AuthenticationType>Authorize.

As example, the PostController class from above, the 2 Authorize classes are:
 - PostIndexNoAuthAuthorize: located in "Controller/Component/Auth/Authorization/PostIndex/PostIndexNoAuthAuthorize.php"
 - PostDeleteNoAuthAuthorize: located in "Controller/Component/Auth/Authorization/PostDelete/PostDeleteNoAuthAuthorize.php"

As we'll go over the Auth process in a different sample, for now just return true:

    class PostIndexNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }
    
    class PostDeleteNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }
    
The result
==========

Point your browser to the following url's:
 - http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/
 - http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/delete/1
 
For the get the output will be:

    <Post>
        <author>SomeGuy</author>
        <messsage>MyMessage</messsage>
        <date>2013-11-16 08:17</date>
    </Post>
    
For the delete the output will be:

        <result>success</result>
OpenApi-Samples: Error Handling
================================

Sample content
===============
See the [OpenApi-Samples Basic] [1] example first if you have not done so, this assumes you've read and understood that.

This sample demonstrates how you can easily handle exceptions and errors in your application and use your AppController to leverage the features OpenApi gives you. 


Introduction
=============
In every application you must have error handling.

The idea behind the error handling in OpenApi was that all you need to know is an error code, the rest should will be done for you.<br />
That way it's easy to quickly build something without having to worry about the details.


Basic Configuration 
===================

To start using the error handling in OpenApi, configure the custom ErrorHandler and Controller.

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
    

Your Own ApiError class
=========================

When you develop an application with its own errors, you'll want to create your own ApiError class.<br />
This class contains a list of all error (exit) codes and a custom message to go with it.

An example would be an exception with code '666' was thrown, automatically the message 'This is Evil!' can be set so the output to the end user is more usefull.<br />
By default the exit code will be '500', with message "Internal Server Error".

The HTTP status code for errors will always be 500 unless otherwise configured.

Your custom error class, lets call it 'OpenApiCustomApiError' must implement IApiError so that the custom error controller and renderer can use this class.<br />
A different option is to extend the BaseApiError class, a sample for that can be found in the Validation sample, here we will be creating a new class.

Configuration:
==============

We'll also include a custom ApiError class, first configure the 'OpenApiCustomApiError' to be used as error class:

    Configure::write('OpenApi.ErrorClass', 'OpenApiCustomApiError');

Example:
========

Based on the Basic sample, create a controller with a function that throws an ApiException:

    class PostController extends AppController {
        /**
         * Auth configuration
         */
        public $AuthConfig = array(
            'get' => array(
                'NoAuth' => array()
            ),
            'delete' => array(
                'NoAuth' => array()
            )
        );
    
        public function get($pID) {
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
            throw new ApiException(666);
        }
    }


Now create the 'OpenApiCustomApiError' file & class in <yourapp>/Lib/Error/OpenApiCustomApiError.php

    class OpenApiCustomApiError extends BaseApiError {
        /**
         * List of error codes with their message
         * @var array 
         */      
        protected static $__codes = array(
            500 => 'Internal Error',
            666 => 'Deleting is Evil!'
        );
    }

Now point your browser to [GET] http://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/delete/123 or [DELETE] http(s)://&lt;yourhost&gt;/&lt;pathtoyourapp&gt;/post/123

The result
==========

The result is a 500 HTTP status code with as output:

    <response>
        <code>666</code>
        <message>Deleting is Evil!</message>
    </response>

Customizing output
===================

When you need more then just the code/message output, you can create your own controller that handles the errors.<br />
This is done by setting the following configuration setting:

     Configure::write('OpenApi.ErrorController', 'OpenApiCustomErrorController');
     
The sample error controller we'll show here is based on the cakephp error controller:

    App::uses('AppController', 'Controller');

    class OpenApiCustomErrorController extends AppController {
        public $uses = array();
    
        public function __construct($request = null, $response = null) {
            parent::__construct($request, $response);
            $this->constructClasses();
            if (count(Router::extensions()) && !$this->Components->attached('RequestHandler')) {
                $this->RequestHandler = $this->Components->load('RequestHandler');
            }
            if ($this->Components->enabled('Auth')) {
                $this->Components->disable('Auth');
            }
            if ($this->Components->enabled('Security')) {
                $this->Components->disable('Security');
            }
            $this->_set(array('cacheAction' => false, 'viewPath' => 'Errors'));
        }
            
        public function beforeRender() {
            parent::beforeRender();
    
            $this->viewVars = array(
                'ExitCode' => $this->ExitCode,
                'ExitMessage' => $this->ExitMessage,
                'MyCustomFields' => array(
                    'field1' => 'value1',
                    'field2' => 'value2'
                )
            );
            $this->viewVars['_serialize'] = array ('ExitCode', 'ExitMessage', 'MyCustomFields');
        }
    }
 
Make sure this Controller extends the OpenApiAppController somehow.<br />
In this sample we're assuming your AppController extends OpenApiController.
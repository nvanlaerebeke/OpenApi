# WARNING: ARCHIVED AND NOT UPDATED PROJECT

# OpenApi

OpenApi is a CakePHP plugin to enable rapid development for REST Api's.

Note that the CakePHP 3 version does not yet have all the features the CakePHP 2 version did

## Key Features

* Easy plug and play authentication and authorization methods 
* Build in flexable versioning system, version what you want
* Automatically handles output (json/xml)
* Easy error handling with meaning and automatic output
* Build for the fat model slim controller principle
* Build with scalibility in mind
* Flexible licensing(MIT License)

In Progress:
 * Configurable REST routing (GET/POST/PUT/DELETE)
 * Automated parameter validation
 
Was fixed in CakePHP3:
* Supports statefull and stateless requests

## Examples/Tutorials and documentation


Samples directory


## Installation


To install the plugin, download the source and put it in a your plugin directory under OpenApi.

Example:
```
/path/to/your/app/plugins/OpenApi
```

Now load the plugin in your app/Config/bootstrap.php file
```
Plugin::load('OpenApi', [ 'routes' => true]);
```

Make sure routes is set to 'true'
Unlike the older version bootstrap is not needed anymore

## Configuration


OpenApi as a few configuration settings you can tweak to customize it's behaviour.

The options are:

- Error Handler configuration
- Exception Handler configuration
- Configuration setting for splitting authorization classes by authorization type
- Versioning (ToDo)

## General

in progress

## Exception Renderer


For more details on error handling, see the [OpenApi-Samples ErrorHandling] [3] project.


OpenApi comes with it's own error renderer.

This is to make sure that the correct output is given

If you have your own, it's best to inherit from this one, 
```
    /**
     * Configure the Error and Exception handlers used by your application.
     *
     * By default errors are displayed using Debugger, when debug is true and logged
     * by Cake\Log\Log when debug is false.
     *
     * In CLI environments exceptions will be printed to stderr with a backtrace.
     * In web environments an HTML page will be displayed for the exception.
     * With debug true, framework errors like Missing Controller will be displayed.
     * When debug is false, framework errors will be coerced into generic HTTP errors.
     *
     * Options:
     *
     * - `errorLevel` - int - The level of errors you are interested in capturing.
     * - `trace` - boolean - Whether or not backtraces should be included in
     *   logged errors/exceptions.
     * - `log` - boolean - Whether or not you want exceptions logged.
     * - `exceptionRenderer` - string - The class responsible for rendering
     *   uncaught exceptions.  If you choose a custom class you should place
     *   the file for that class in src/Error. This class needs to implement a
     *   render method.
     * - `skipLog` - array - List of exceptions to skip for logging. Exceptions that
     *   extend one of the listed exceptions will also be skipped for logging.
     *   E.g.:
     *   `'skipLog' => ['Cake\Network\Exception\NotFoundException', 'Cake\Network\Exception\UnauthorizedException']`
     */
    'Error' => [
        'errorLevel' => E_ALL & ~E_DEPRECATED,
        'exceptionRenderer' => '\OpenApi\Error\ExceptionRenderer',
        'skipLog' => [],
        'log' => true,
        'trace' => true,
    ],
```
 
## Versioning

ToDo
 
## Auth

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



## Getting started

To get started, see the [OpenApi-Samples Basic] [2] sample.

Sample & tutorial list:
- [OpenApi-Samples] [1]
- [OpenApi-Samples Basic] [2]
- [OpenApi-Samples Versioning] [4]
- [OpenApi-Samples Auth] [5]
- [OpenApi-Samples Error Handling] [3]
- [OpenApi-Samples REST Routing] [6]

  [1]: https://github.com/nvanlaerebeke/OpenApi-Samples        "OpenApi-Samples"
  [2]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Basic        "OpenApi-Samples Basic"
  [3]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Error%20Handling        "OpenApi-Samples ErrorHandling"
  [4]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Versioning        "OpenApi-Samples Versioning"
  [5]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Auth        "OpenApi-Samples Auth"
  [6]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/REST%20Routing        "OpenApi-Samples REST Routing"

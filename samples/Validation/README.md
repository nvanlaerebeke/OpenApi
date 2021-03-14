OpenApi-Samples: Validation
===========================

Sample content
===============
See the [OpenApi-Samples Basic] [1] example first if you have not done so, this sample assumes you have seen and understood the basics.

This sample demonstrates how automatic parameter validation is done in OpenApi when passing parameters as a key/value pair in the request. 


Introduction
=============
Configuring the parameter validtion is done in the controller.

As you've already seen in previous samples, there is an AuthConfig variable in the controller that contains what Authentication methods are allowed per action.

Each of those Authentication keys can have an array as parameter and in that array you can specify what parameters must be passed to the REST call and how they must validate.

For more on Authentication see the [OpenApi-Samples Auth] [2] Sample, in this sample 'NoAuth' will be used.

Configuration Array
====================
An example configuration array:

    /**
     * Auth configuration
     */
    public $AuthConfig = array(
        'add' => array(
            'NoAuth' => array(
                'FirstName' => 'alphaNumeric',
                'Email' => 'email',
                'Age' => array(
                    'comparison' => array(
                        'rule'    => array('comparison', '>=', 18),
                        'message' => 'Must be at least 18 years old to qualify.'
                    )
                )
            )
        )
    );
    
In the config above, we're configuring the 'create' action for the Post controller.

Specified in that config is:
 - Use the NoAuth Authenticator(so no authentication is done) 
 - The FirstName parameter must be 'alphaNumeric'
 - The 'Email' must ben an 'email' 
 - The Age parameter must be larger than 18.

The parameters are configured specifically for the 'NoAuth' authentication method, when we have multiple authentication methods, you can have different validation rules for each.

For more information about what rules you can use and how they're configured, see the [CakePHP Data Validation] [3] chapter in the cookbook and the [Validation] [4] class in the Cakephp Api documentation
Also use full can be the api documentation on the cakephp website.

Example:
========

Create the Controller
=========================

The PostController:

    class PostController extends AppController {
        /**
         * Auth configuration
         */
        public $AuthConfig = array(
            'add' => array(
                'NoAuth' => array(
                    'FirstName' => 'alphaNumeric',
                    'Email' => 'email',
                    'Age' => array(
                        'comparison' => array(
                            'rule'    => array('comparison', '>=', 18),
                            'message' => 'Must be at least 18 years old to qualify.'
                        )
                    )
                )
            )
        );

        public function add() {
            $this->set('response', array('created' => 'true'));
            $this->set('_serialize', 'response');        
        }
    }


Create the Authorizers
======================

Create the "PostAddNoAuthAuthorize" located in "Controller/Component/Auth/Authorization/PostAdd/PostAddNoAuthAuthorize.php"

    class PostAddNoAuthAuthorize extends BaseAuthorize {
        public function authorize($user, CakeRequest $request) {
            return true;
        }
    }
    
The result
==========

Depending on the validation rule that fails, you'll see output like this:

    <?xml version="1.0" encoding="UTF-8"?>
    <response>
      <code>400</code>
      <url>/validation/post/create/?email=blaatex%40ample.com&amp;amp;age=19&amp;amp;firstname=</url>
      <name>FirstName: This field must be an alpha-numeric value</name>
    </response>

Customizing output
===================

The message can be manipulated by providing your own, this can be done just as with the 'Age' in the example above.
When wanting to customize the code (HTTP status), one can extend the BaseApiError class or implement the IApiError interface and provide custom messages there.

Once you know the class you'll be using, make sure to configure it in your core.php:

    Configure::write('OpenApi.ErrorClass', 'OpenApiCustomApiError');

An example when extending the BaseApiError class:
Put the OpenApiCustomApiError class in <yourapp>/Lib/Error/OpenApiCustomApiError.php


    App::uses('BaseApiError', 'OpenApi.Lib/Error');
    
    
    class OpenApiCustomApiError extends BaseApiError {
       
        /**
         * List of params with their error code
         * Used for when parameters are not filled in
         *  
         * @var array 
         */      
        protected static $__params = array();
    
        /**
         * List of validation types with their error code
         * @var array 
         */      
        public static $__validationcodes = array(
            'alphaNumeric' => 999,
            'between' =>  999,
            'blank' =>  999,
            'boolean' =>  999,
            'cc' =>  999,
            'comparison' => 999, 
            'custom' =>  999,
            'date' =>  999,
            'datetime' =>  999,
            'decimal' =>  999,
            'email' =>  999,
            'equalTo' =>  999,
            'extension' =>  999,
            'fileSize' =>  999,
            'inList' =>  999,
            'ip' =>  999,
            'isUnique' =>  999,
            'luhn' =>  999,
            'maxLength' =>  999,
            'mimeType' =>  999,
            'minLength' =>  999,
            'money' =>  999,
            'multiple' =>  999,
            'notEmpty' =>  999,
            'numeric' =>  999,
            'naturalNumber' =>  999,
            'phone' =>  999,
            'postal' =>  999,
            'range' =>  999,
            'ssn' =>  999,
            'time' =>  999,
            'uploadError' =>  999,
            'url' =>  999,
            'userDefined' =>  999,
            'uuid' =>  999
        );
    
        /**
         * List of messages for validation types
         *  
         * @var array
         */      
        protected static $__validationmessages = array(
            'alphaNumeric' => "This field must be an alpha-numeric value",
            'between' => "This field must fall between the required values",
            'blank' => "This field must be blank",
            'boolean' => "This field must be a boolean",
            'cc' => "This field must be a credit card number",
            'comparison' => "This field does not validate for the required comparison",
            'custom' => "Validation was not successful for this field",
            'date' => "This field must be a date",
            'datetime' => "This field must be a datetime",
            'decimal' => "This field must be a decimal",
            'email' => "This field must be an email",
            'equalTo' => "This field mucst be equal to the provided value",
            'extension' => "This field must have the correct extension",
            'fileSize' => "This field must be a filesize",
            'inList' => "This field must be in the provided list",
            'ip' => "This field must be an IP address",
            'isUnique' => "This field must be unique",
            'luhn' => "",
            'maxLength' => "This field must not be longer then the provided value",
            'mimeType' => "This field must be a mime type",
            'minLength' => "This field must not be shorter then the provided value",
            'money' => "This field must be a currency",
            'multiple' => "",
            'notEmpty' => "This field cannot be left blank",
            'numeric' => "This field must be a number",
            'naturalNumber' => "",
            'phone' => "This field must be a phone number",
            'postal' => "This field must be a postal code",
            'range' => "This field must fall in the provided range",
            'ssn' => "",
            'time' => "This field must be a time",
            'uploadError' => "This field must be an upload error",
            'url' => "This field must be a URL",
            'userDefined' => "",
            'uuid' => "This field must be a universal unique identifier"
        );
    }
    
The difference here is I replaced the 400 with 999 error codes, those will now be shown instead of the previous 400.


  [1]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Basic         "OpenApi-Samples Basic"
  [2]: https://github.com/nvanlaerebeke/OpenApi-Samples/tree/master/Auth          "OpenApi-Samples Auth"
  [3]: http://book.cakephp.org/2.0/en/models/data-validation.html                 "CakePHP Data Validation"
  [4]: http://book.cakephp.org/2.0/en/models/data-validation.html                 "CakePHP Validation Class"

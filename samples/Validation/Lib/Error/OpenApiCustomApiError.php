<?php
/**
 * Api Error class containing functionallity to manage error codes and messages
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 
 * @link          http://blog.crazytje.com
 * @package       OpenApi.Error
 * @since         OpenApi v0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Include the BaseApiError
 */
App::uses('BaseApiError', 'OpenApi.Lib/Error');

/**
 * Api Error class containing functionallity to manage error codes and messages
 *  
 * @package       OpenApi.Error
 */
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
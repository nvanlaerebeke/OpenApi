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
 * @package       OpenApi.Lib.Error
 * @since         OpenApi v0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Include the IApiError Interface
 */
App::uses('IApiError', 'OpenApi.Lib/Error');

/**
 * Api Error class containing functionallity to manage error codes and messages
 *  
 * @package       OpenApi.Lib.Error
 */
class BaseApiError implements IApiError {
    /**
     * Returns the message for a given code
     *  
     * @var int $pCode Code to get the message for
     * @return string messsage for the given code
     */ 
    public static function GetMessageForCode($pCode) {
        if(isset(static::$__codes[$pCode])) {
            return static::$__codes[$pCode];
        }
        return static::$__codes[500];
    }

    /**
     * Returns the code for a given missing parameter
     *  
     * @var string $pParam 
     * @return int error code
     */     
    public static function GetCodeForParam($pParam) {
        if(isset(static::$__params[$pParam])) {
            return static::$__params[$pParam];
        }
        return 400;
    }
    
    /**
     * Retuns the message for a certain type of validation error 
     *  
     * @var string $pType type of validation error
     * @return string 
     */ 
    public static function GetMessageForValidationError($pType) {
        if(isset(static::$__validationmessages[$pType])) {
            return static::$__validationmessages[$pType];
        }
        return "";
    }

    /**
     * Returns the code for a validation error
     *  
     * @var string $pType 
     * @return int
     */     
    public static function GetCodeForValidationError($pType) {
        if(isset(static::$__validationcodes[$pType])) {
            return static::$__validationcodes[$pType];
        }
        return 400;
    }
    
    /**
     * List of error codes with their message
     * @var array 
     */      
    protected static $__codes = array(
        500 => 'Internal Error'
    );
    
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
    protected static $__validationcodes = array(
        'alphaNumeric' => 400,
        'between' =>  400,
        'blank' =>  400,
        'boolean' =>  400,
        'cc' =>  400,
        'comparison' => 400, 
        'custom' =>  400,
        'date' =>  400,
        'datetime' =>  400,
        'decimal' =>  400,
        'email' =>  400,
        'equalTo' =>  400,
        'extension' =>  400,
        'fileSize' =>  400,
        'inList' =>  400,
        'ip' =>  400,
        'isUnique' =>  400,
        'luhn' =>  400,
        'maxLength' =>  400,
        'mimeType' =>  400,
        'minLength' =>  400,
        'money' =>  400,
        'multiple' =>  400,
        'notEmpty' =>  400,
        'numeric' =>  400,
        'naturalNumber' =>  400,
        'phone' =>  400,
        'postal' =>  400,
        'range' =>  400,
        'ssn' =>  400,
        'time' =>  400,
        'uploadError' =>  400,
        'url' =>  400,
        'userDefined' =>  400,
        'uuid' =>  400,
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
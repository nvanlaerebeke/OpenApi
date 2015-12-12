<?php
/**
 * ApiError interface file
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
 * Api Error interface containing functions needed to be implemented for the Api.ErrorClass 
 *  
 * @package       OpenApi.Lib.Error
 */
interface IApiError {
    /**
     * Returns the message for a given code
     *  
     * @var int $pCode Code to get the message for
     * @return string messsage for the given code
     */      
    static function GetMessageForCode($pCode);
    
    /**
     * Returns the code for a given missing parameter
     *  
     * @var string $pParam 
     * @return int error code
     */     
    static function GetCodeForParam($pParam);
    
    /**
     * Retuns the message for a certain type of validation error 
     *  
     * @var string $pType type of validation error
     * @return string 
     */     
    static function GetMessageForValidationError($pType);
    
    /**
     * Returns the code for a validation error
     *  
     * @var string $pType 
     * @return int
     */       
    static function GetCodeForValidationError($pType);
}
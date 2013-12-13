<?php
/**
 * Api Exception class
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
 * Exception class for OpenApi based on CakeException
 *  
 * @package       OpenApi.Error
 */
class ApiException extends CakeException {
    /**
     * ApiException class constructor
     * 
     * Message text is automatically filled in based on the configured ApiError class  
     * 
     * @var int $pCode
     * @var string $pMessage
     * @var Exception $pPrevious 
     */
    public function __construct($pCode = 500, $pMessage = null, Exception $pPrevious = null) {
        // -- Auto fills in the message when $pMessage isn't passed
        if(empty($pMessage)) {
            $class = $this->GetErrorClass();
            $pMessage = $class::GetMessageForCode($pCode);
        }
        // -- Call base class constructor
        parent::__construct($pMessage, $pCode, $pPrevious);
    }
    
    /**
     * The error class can be configured in Configure::read('Api.ErrorClass')
     * 
     * Configured class must extend BaseApiError or implement IApiError
     * When not configured, will fall back to the BaseApiClass
     * 
     * @return class Class that contans errors codes and messages
     */
    protected function GetErrorClass() {
        $errorclass = Configure::read('OpenApi.ErrorClass');
        if(!empty($errorclass)) {
            App::uses($errorclass, 'Lib/Error');
            if(!class_exists($errorclass)) {
                $errorclass = null;
            }
        }
        if(empty($errorclass)) {
            CakeLog::write('apilog', 'ApiError class not found!, no message auto filled in');
            $errorclass = 'BaseApiError';
            App::uses($errorclass, 'OpenApi.Lib/Error');
        }
        return $errorclass;
    }
}
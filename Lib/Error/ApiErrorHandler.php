<?php
/**
 * Api Error Handler class
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
 * Include the ApiException class
 */
App::uses('ApiException', 'OpenApi.Lib/Error');

/**
 * Api error handler class providing extra handing for errors
 *  
 * @package       OpenApi.Error
 * @link          http://book.cakephp.org/2.0/en/controllers.html
 */
class ApiErrorHandler extends ErrorHandler {
    /**
     * Function called when cakephp wants to handle an exception
     * Turn of logging for ApiExceptions
     * 
     * @var Exception $exception
     */
    public static function handleException(Exception $pException) {
        /* Turn off logging for apiexceptions */
        $log = Configure::read('Exception.log');
        if($log && $pException instanceof ApiException) {
            Configure::write('Exception.log', false);
            CakeLog::write('apilog', 'Api Call Stopped with error code "'.$pException->getCode().'" and message "'.$pException->getMessage().'"');
        }
        parent::handleException($pException);
        if($log) { Configure::write('Exception.log', $log); }
    }   
}
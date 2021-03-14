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
     * List of error codes with their message
     * @var array 
     */      
    protected static $__codes = array(
        //500 => 'Internal Error',
        666 => 'Deleting is Evil!'
    );
}
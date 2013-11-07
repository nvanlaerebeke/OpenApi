<?php
/**
 * IPAddress Authenticate class file for OpenApi
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @package       OpenApi.Controller.Authentication
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
App::uses('ApiBaseAuthenticate', 'Api.Lib'); 

/**
 * IPAddress Authenticate class for OpenApi
 * 
 * Allowed IP's must be set in the Configuration.
 * Example:
 * 
 *  Config::write(
 *      'Api.Authentication.AllowedIPs',
 *      array(
 *          '127.0.0.1',
 *          '192.168.1.1'
 *      )
 *  )
 *
 * When running from CLI, 127.0.0.1 is  used as IP automatically
 * 
 * @package       OpenApi.Controller.Authentication
 */
class IPAddressAuthenticate extends ApiBaseAuthenticate {
    /**
     * authentication function based on requests IPAddress  
     *
     * @var CakeRequest $pRequest
     * @var CakeResponse $pResponse
     * @return mixed true/false or an array with parameters 
     */
    public function authenticate(CakeRequest $pRequest, CakeResponse $pResponse) {
        //Read allowed IPAddress list
        $allowedips = Configure::read('Api.Authentication.AllowedIPs');
        
        //When we're running from CLI and the remote address isn't set, use 127.0.0.1(localhost) 
        if(!isset($_SERVER['REMOTE_ADDR']) && php_sapi_name() == 'cli') {
            $clientip = "127.0.0.1";
        } else {
            $clientip = $_SERVER['REMOTE_ADDR'];
        }
        if(empty($clientip) || empty($allowedips) || !in_array($clientip, $allowedips)) {
            return false;
        }
        return true;
    }
}

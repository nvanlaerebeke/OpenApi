<?php
/**
 * class file for OpenApi IP Authentication
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
**/
 
namespace OpenApi\Auth\Authenticate;
 
use Cake\Network\Request;
use Cake\Network\Response;
 
/**
 * IPAddress Authenticate class for OpenApi
 *
 * When running from CLI, 127.0.0.1 is used as IP automatically
 *  
 * Allowed IP's must be set in your configuration (app.php) .
 * Example:
 * 
 * OpenApi' => [
 *     'Authentication' => [
 *         AllowedIPs => [
 *             '127.0.0.1',
 *             '192.168.1.1' 
 *         ]
 *      ]
 *  ],
 *
 * 
 */
class IPAddressAuthenticate extends \OpenApi\Auth\Authenticate\BaseAuthenticate {
    
    
    /**
     * authentication function based on requests IPAddress  
     *
     * @var Request $pRequest
     * @var Response $pResponse
     * @return mixed false or an array with authorization type 'Machine'
     */
    public function authenticate(\Cake\Network\Request $request, \Cake\Network\Response $response) {
        //Read allowed IPAddress list
        $allowedips = Configure::read('OpenApi.Authentication.AllowedIPs');
        
        //When we're running from CLI and the remote address isn't set, use 127.0.0.1(localhost) 
        if(!isset($_SERVER['REMOTE_ADDR']) && php_sapi_name() == 'cli') {
            $clientip = "127.0.0.1";
        } else {
            $clientip = $_SERVER['REMOTE_ADDR'];
        }
        if(empty($clientip) || empty($allowedips) || !in_array($clientip, $allowedips)) {
            return false;
        }
        return array(
            'authorizetype' => 'Machine'
        );
    }
}
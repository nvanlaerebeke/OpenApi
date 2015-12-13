<?php
/**
 * class file for the "NoAuthenticate" method
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
 * NoAuth Authenticate class for OpenApi
 * 
 * Authenticate call used when no real authentication is needed
 * Is used to normalize the Authenticate classes output
 * 
 */
class NoAuthAuthenticate extends \OpenApi\Auth\Authenticate\BaseAuthenticate {
    /**
     * authentication function   
     *
     * @var Request $pRequest
     * @var Response $pResponse
     * @return mixed true or an array authorization type 'NoAuth' 
     */
    public function authenticate(\Cake\Network\Request $request, \Cake\Network\Response $response) {
        return array(
            'authorizetype' => 'NoAuth'
        );
    }
    
    /**
     * @return array the user info
     */
    public function getUser(\Cake\Network\Request $request){ return array(); }
}
<?php
/**
 * class file for the custom "ApiAuth" component
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

namespace OpenApi\Controller\Component;

use Cake\Network\Request;

/**
 * ApiAuth component
 */
class ApiAuthComponent extends \Cake\Controller\Component\AuthComponent {
    
    /**
     * Check if the provided user is authorized for the request.
     *
     * Uses the configured Authorization adapters to check whether or not a user is authorized.
     * Each adapter will be checked in sequence, if any of them return true, then the user will
     * be authorized for the request.
     *
	 * Difference with the original version is added support to return more then just true/false
	 * 
     * @param array|null $user The user to check the authorization of.
     *   If empty the user fetched from storage will be used.
     * @param \Cake\Network\Request|null $request The request to authenticate for.
     *   If empty, the current request will be used.
     * @return mixed True/Array if $user is authorized, otherwise false
     */
    public function isAuthorized($user = null, Request $request = null) {
        if (empty($user) && !$this->user()) { return false; }
        if (empty($user)) { $user = $this->user(); }
        if (empty($request)) { $request = $this->request; }
        if (empty($this->_authorizeObjects)) { $this->constructAuthorize(); }


        foreach ($this->_authorizeObjects as $authorizer) {
        	$response = $authorizer->authorize($user, $request);

            if ($response  === true) {
                $this->_authorizationProvider = $authorizer;
                return true;
            } elseif(is_array($response)) {
            	return $response;
            }
        }
        return false;
    }	
	
    protected function _unauthenticated(\Cake\Controller\Controller $controller) {
        return false;
    }
}

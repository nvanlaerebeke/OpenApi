<?php
/**
 * NoAuth Authenticate class file for OpenApi
 *
 * Copyright (c)  *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @package       OpenApi.Controller.Auth.Authentication
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
App::uses('ApiBaseAuthenticate', 'Controller/Component/Auth');

/**
 * NoAuth Authenticate class for OpenApi
 * 
 * Authenticate call used when no real authentication is needed
 * Is used to normalize the Authenticate classes output
 * 
 * @package       OpenApi.Controller.Auth.Authentication
 */
class NoAuthAuthenticate extends ApiBaseAuthenticate {
    /**
     * authentication function   
     *
     * @var CakeRequest $pRequest
     * @var CakeResponse $pResponse
     * @return mixed true or an array authorization type 'NoAuth' 
     */
    public function authenticate(CakeRequest $request, CakeResponse $response) {
        return array(
            'authorizetype' => 'NoAuth'
        );
    }
    
    /**
     * @return array the user info
     */
    public function getUser(CakeRequest $request){ return array(); }
}

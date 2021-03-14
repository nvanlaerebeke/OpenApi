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
 * @package       OpenApi.Controller.Authentication
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('ApiBaseAuthenticate', 'Controller/Component/Auth');

/**
 * EmailPassword Authenticate class for OpenApi
 * 
 * Authenticate call used when no real authentication is needed
 * Is used to normalize the Authenticate classes output
 * 
 * @package       OpenApi.Controller.Authentication
 */
class EmailPasswordAuthenticate extends ApiBaseAuthenticate {
    private $user = null;
    
    /**
     * authentication function   
     *
     * @var CakeRequest $pRequest
     * @var CakeResponse $pResponse
     * @return mixed true or an array with parameters 
     */
    public function authenticate(CakeRequest $request, CakeResponse $response) {
        //these are passed using GET, in reallity better use post over SSL or implement a layer before the CakeRequest is made so that all input is normalized
        $email = (!empty($request->query['emailaddress'])) ? $request->query['emailaddress'] : "";
        $pw = (!empty($request->query['password'])) ? $request->query['password'] : "";
        
        //in reality this will most likely be a DB query
        $this->user = array(
            'User' => array(
                'id' => 1,
                'emailaddress' => 'email@example.com',
                'password' => 'mypassword'
            )
        );
        
        //simple basic compare
        if($email != $this->user['User']['emailaddress'] || $this->user['User']['password'] != $pw) {
            return false;
        }

        //Say we're using a 'User' authorize type when using EmailPassword and pass the         
        return array(
            'User' => $this->user['User'],
            'authorizetype' => 'User'
        );
    }
    
    /**
     * @return array the user info
     */
    public function getUser(CakeRequest $request){ return $this->user; }
}

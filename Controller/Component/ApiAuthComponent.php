<?php
/**
 * Custom AuthComponent
 *
 * Copyright (c)  *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @package       OpenApi.Controller.Auth
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 

/**
 * Include the core AuthComponent that the ApiAuthComponent is based on
 */
App::uses('AuthComponent', 'Controller/Component');

/**
 * Custom AuthComponent
 * 
 * Differences with the CakePHP AuthComponent: 
 *  - Supports Stateless Authentication, the default AuthComponent starts a session slowing down the request
 *  - Successfull authentication method is stored in Configure::read('Auth.Method')
 *  - Authorization can return more then just true/false
 * 
 * @package       OpenApi.Controller.Auth
 */
class ApiAuthComponent extends AuthComponent {
    /**
     * Login method
     * Difference with the base class is that we do not want to refresh the session id every time we authenticate
     * 
     * @var array $user 
     * @return bool
     */
    public function login($user = null) {
        if(!empty(self::$sessionKey)) {
            if(Configure::read('debug') > 0) {
                $this->Session->delete(self::$sessionKey);
            }
            $value = parent::login($user);
            $response = $this->Session->read(self::$sessionKey);
        } else {
            $this->_setDefaults();
            $response = $this->identify($this->request, $this->response);
        }

        if ($response) {
            $classname = $this->get_calling_class();
            $method = substr($classname, 0, strpos($classname, 'Authenticate'));
            
            Configure::write('Auth.Info', $response);
            return true;
        }
        return false;
    }
            
    /**
     * Identify a user
     * Difference with the original function is that we want to know the method that we're authenticating with further down the line 
     * 
     * @var CakeRequest $request 
     * @var CakeResponse $response
     * @return mixed bool or return value from Authentication class
     */
    public function identify(CakeRequest $request, CakeResponse $response) {
        if (empty($this->_authenticateObjects)) {
            $this->constructAuthenticate();
        }
        foreach ($this->_authenticateObjects as $auth) {
            $result = $auth->authenticate($request, $response);
            if (!empty($result) && is_array($result)) {
                $classname = get_class($auth);
                Configure::write('Auth.Method', substr($classname, 0, strpos($classname, 'Authenticate')));
                return $result;
            }
        }
        return false;
    }
    
    
    /**
     * Checks if a user is authrozed for an action
     * Difference with the original version is added support to return more then just true/false
     * 
     * @var array User Information Array
     * @var CakeRequest $request 
     * @return mixed bool or return value from Authorize class
     */
    public function isAuthorized($user = null, CakeRequest $request = null) {
        if (empty($user) && !$this->user()) { return false; }
        if (empty($user)) { $user = $this->user(); }
        if (empty($request)) { $request = $this->request; }
        if (empty($this->_authorizeObjects)) { $this->constructAuthorize(); }
        
        foreach ($this->_authorizeObjects as $authorizer) {
            $response = $authorizer->authorize($user, $request);
            if ($response  === true) {
                return true;
            } elseif(is_array($response)) {
                return $response;
            }
        }
        return false;
    }
    
    /**
     * Looks up the user
     * Difference with the original function is that we don't trigger a session start if it's not needed
     * 
     * @var string $key
     * @return mixed User array or null
     */
    public static function user($key = null) {
        if(!empty(self::$_user) && $key == null) {
            return self::$_user;
        }
        if(empty(self::$_user) && empty(self::$sessionKey)) {
            return null;
        }
        return parent::user($key);
    }
    
    
    /**
     * Returns the name of the class name from the function calling this function
     * 
     * @return string Calling classname
     */
    private function get_calling_class() {
        //get the trace
        $trace = debug_backtrace();

        // Get the class that is asking for who awoke it
        $class = $trace[1]['class'];

        // +1 to i cos we have to account for calling this function
        for ( $i=1; $i<count( $trace ); $i++ ) {
            if ( isset( $trace[$i] ) ) { // is it set?
                if ( $class != $trace[$i]['class'] ) { // is it a different class
                    return $trace[$i]['class'];
                }
            }
        }
    }
    
    /**
     * Auth component shutdown function
     * Difference is that the session isn't started if the session key isn't set
     * 
     * @var Controller $controller
     */
    public function shutdown(Controller $controller) {
        if ($this->loggedIn() && !empty(self::$sessionKey)) {
            $this->Session->delete('Auth.redirect');
        }
    }
}

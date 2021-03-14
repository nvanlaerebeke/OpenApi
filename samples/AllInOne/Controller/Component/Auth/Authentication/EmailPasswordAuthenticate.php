<?php
App::uses('ApiBaseAuthenticate', 'Controller/Component/Auth');

class EmailPasswordAuthenticate extends ApiBaseAuthenticate {
    /**
     * Holds the user object/array 
     */
    private $user = null;

    /**
     * Authenticate function that uses basic auth to get the user credentials
     */
    public function authenticate(CakeRequest $pRequest, CakeResponse $pResponse) {
        //Use basic authentication to get the username/pw
        if(!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }
        
        //store for easy use
        $username = $_SERVER['PHP_AUTH_USER'];
        $pw = $_SERVER['PHP_AUTH_PW'];
        
        //Init the User model
        $this->User = ClassRegistry::init('User');
        
        //query for the user
        $this->user = $this->User->find('first', array('conditions' => array('username' => $username, 'password' => $pw), 'recursive' => -1));
        if(empty($this->user)) { return false; }

        //Say we're using a 'User' authorize type when using EmailPassword and pass the         
        return array(
            'User' => $this->user['User'],
            'authorizetype' => 'User'
        );
    }

    public function getUser(CakeRequest $request){ return $this->user; }
}
<?php
/**
 * OpenApi
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 
 * @link          http://blog.crazytje.com
 * @package       OpenApi.Controller
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
App::uses('AppController', 'Controller');

/**
 * Base controller class
 * Handles process logic for all API requests
 * 
 * Contains the following things:
 * 
 * -   OpenApi initialization
 * -   Auhtentication
 * -   Authorization
 * -   Parameter checking and validation
 * -   Handling the output format 
 *
 * Resources:
 * -   http://martinfowler.com/articles/richardsonMaturityModel.html
 * -   http://www.ics.uci.edu/~fielding/pubs/dissertation/top.htm
 * -   http://munich2012.drupal.org/program/sessions/designing-http-interfaces-and-restful-web-services.html
 * 
 * @ToDo document how stateless auth works + write test to prove that the api supports stateless auth
 * @ToDo document that the authenticator will use session data if config < 0 for increased performace if not a stateless request
 * @ToDo document the steps to set up the Api Plugin(things like the dispatcher etc)
 * @ToDo document the usage of APIError, auto filling in error messages
 * @ToDo document the ip authenticator
 * @ToDo document that error codes will be set in the controller $this->ExitCode and $this->ExitMessage
 * @ToDo: Unit tests
 *
 * @package       OpenApi.Controller
 */
class OpenApiAppController extends Controller {
    /**
     * The exit code used for this Api request
     * @var int
     */
    public $ExitCode = 0;
    
    /**
     * Exit message for the Api Request
     * @var string
     */
    public $ExitMessage = "";    

    /**
     * Components used by the OpenApiAppController
     * @var array component configuration
     */    
    public $components = array(
        'OpenApi.ApiAuth',
        'Session',
        'RequestHandler' => array(
            "loginRedirect" => false,
            "logoutRedirect" => false,
            "autoRedirect" => false,
            "unauthorizedRedirect" => false
        )
    );

    /**
     * First function called in the api
     * Responsible for the setup before the action is called
     */
    public function beforeFilter() {
        $this->log('');
        $this->log('');
        $this->log('-----------------------------------------------');
        $this->log('------------START RECEIVING API CALL-----------');
        $this->log('-----------------------------------------------');
        $this->log('Api Call Received - Start Handling');
        parent::beforeFilter();

        // -- Make all parameter keys lower case
        if(isset($this->params->query)) { $this->params->query = array_change_key_case($this->params->query); } 
        $this->Params = $this->params->query;
        
        if(Configure::read('debug') > 0) {
            $this->log('Request: '.$_SERVER['HTTP_HOST'].urldecode($_SERVER['REQUEST_URI']));
            $this->log('Recieved:');
            $this->log($this->params->query);
        }

        // -- Make sure the framework knows where everything is
        $this->_setupPaths();                    
                
        //do auth
        $this->_authenticate();
        $this->_authorize();
        $this->_checkParams();
    }

    /**
     * Function called before the output is generated
     * Responsible to give us nice xml/json
     */    
    public function beforeRender() {
        parent::beforeRender();

        /** Default to configured output type, if not configured, use xml */
        if(empty($this->RequestHandler->request->params['ext'])) {
            $type = Configure::read('OpenApi.DefaultOutputFormat');
            if(empty($type)) { $type = 'xml'; }
            $this->RequestHandler->renderAs($this, $type); 
        }
        
        //Log the end, for easier viewing log files
        $this->log('-----------------------------------------------');
        $this->log('-------------END HANDLING API CALL-------------');
        $this->log('-----------------------------------------------');
        $this->log('');
        $this->log('');
    }
    
    
    /**
     * Sets up the App::paths() so that cakephp can locate our OpenApi classes
     */
    private function _setupPaths() {
        //path to the OpenApi plugin
        $root_path = App::pluginPath('OpenApi');
        
        //Get the location for the Authorization classes with versioning support
        $authorizationdirectory = array();
        $authdirectories = array();
        if(in_array('Controller/Component/Auth', Configure::read('OpenApi.VersionTypes'))) {
            $versions = Configure::read('OpenApi.Versions');
            if(!empty($versions) && count($versions) > 0) {
                foreach($versions as $version) {
                    $authorizationdirectory[] = ROOT.DS.APP_DIR.DS.'Controller/Component/Auth'.DS.$version.DS.'Authorization'.DS;
                    $authdirectories[] = ROOT.DS.APP_DIR.DS.'Controller/Component/Auth'.DS.$version.DS.'Authentication'.DS;
                }
            }
        }

        //in case versioning wasn't configured use the base directories
        if(empty($authorizationdirectory) || empty($authdirectories)) {
            $authorizationdirectory[] = ROOT.DS.APP_DIR.DS.'Controller/Component/Auth'.DS.'Authorization'.DS;
            $authdirectories[] = ROOT.DS.APP_DIR.DS.'Controller/Component/Auth'.DS.'Authentication'.DS;
            // -- Base & helper classes for authentication and authorization
            $authdirectories[] = $root_path.'Controller'.DS.'Component'.DS.'Auth'.DS.'Authentication'.DS;
            $authdirectories[] = $root_path.'Controller'.DS.'Component'.DS.'Auth'.DS.'Authorization'.DS;
        }
        
        //When Authorization is separate per context, add the postfix
        if(Configure::read('OpenApi.SeparateAuthorization')) {
            for($i = 0; $i < count($authorizationdirectory); $i++) {
                $authorizationdirectory[$i] .= ucfirst($this->params->params['controller']).ucfirst($this->params->params['action']).DS;
            }
        }
        
        // -- Base & helper classes for authentication and authorization
        $authorizationdirectory[] = $root_path.'Controller'.DS.'Component'.DS.'Auth'.DS.'Authentication'.DS;
        $authorizationdirectory[] = $root_path.'Controller'.DS.'Component'.DS.'Auth'.DS.'Authorization'.DS;

        /**
         * Add paths to the App::paths()
         */
        App::build(
            array ('Controller/Component/Auth' =>  $authorizationdirectory)
        );
    }
    
    /**
     * Auth part of every api call
     * Runs the Authentication and Authorization for the api call
     */
    private function _authenticate() {
        // -- Authentication methods must be configured in the controller
        if(isset($this->AuthConfig[strtolower($this->params['action'])])) {
            $this->ApiAuth->authenticate = array_keys($this->AuthConfig[strtolower($this->params['action'])]);
        } else {
            throw new UnauthorizedException();
        }

        //Run the authentication
        $this->log('Starting Authentication...');
        /** 
         * For activating the stateless(no sessions + cookies) mode, set Api.Auth.Stateless to true
         */
        if(Configure::read('Api.Auth.Stateless')) {
            AuthComponent::$sessionKey = false;
        } 

        // -- Start the Authentication Process
        if($this->ApiAuth->login()) {
            $this->log('Authentication Successfull using method "'.Configure::read('Auth.Method').'"');
            $authresult = Configure::read('Auth.Info');
            // -- Authentication can store additional user information in 'Auth.Info'
            if(is_array($authresult)) {
                $this->Params = array_merge($this->Params, $authresult);
            }
        } else {
            $this->log('Authentication Failed');
            throw new UnauthorizedException(); // -- will set 401 http status code
        }
    }

    /**
     * Authorize part of the auth process
     */
    private function _authorize() {
        /**
         * To make authorization more scalable, a separate class is called called <Controller><Action><(optional)AuthorizeType> class
         */
        $authname = ucfirst($this->params->params['controller']).ucfirst($this->params->params['action']);
        if(Configure::read('OpenApi.SeparateAuthorization')) {
            $authtype = Configure::read('Auth.Info.authorizetype');
            if(empty($authtype)) {
                $this->log('Warning, SeparateAuthorization setting set to true, but authenticte process didn\'t set authorizetype');
            } else {
                $authname.= $authtype;
            }
            
        }
        $this->ApiAuth->authorize = array($authname);

        $this->log('Starting Authorization using "'.$authname.'"');
        $authresult = $this->ApiAuth->isAuthorized(Configure::read('Auth.Info'));
        
        if($authresult != false) {
            // -- The authorize classes can return additional information that will be added to the $this->Params array
            if(is_array($authresult)) {
                $this->Params = array_merge($this->Params, $authresult);
            }
            $this->log('Authorization successful');
        } else {
            $this->log('Authorization failed');
            throw new ForbiddenException(); // -- will set 403 http status code
        }
    }
    
    /**
     * Checks if all the required parameters are set and valid in the request
     */
    private function _checkParams() {
        if(!empty($this->AuthConfig[$this->params['action']][Configure::read('Auth.Method')])) {
            App::uses('ApiParameterValidator', 'OpenApi.Lib');
            ApiParameterValidator::Validate($this->params->query, $this->AuthConfig[$this->params['action']][Configure::read('Auth.Method')]);
        }
    }
}
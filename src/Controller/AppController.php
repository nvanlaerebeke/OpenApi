<?php
/**
 * class file for the OpenApi "AppController"
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
namespace OpenApi\Controller;

use App\Controller\AppController as BaseController;
use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Core\Configure;

use OpenApi\Lib\ApiParameterValidator;

/**
 * AppController class
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
 * @ToDo: Seperate authorization setting: Controller/Action not yet implemented
 * @ToDo: Authconfig in controller should contain $array['action']['authorize'] = array(false/array with authorizers etc)
 * 	      Not yet correct format
 * @ToDo: Parameter validation
 * @ToDo: Versioning: no idea yet how we can do this in cakephp 3, in v2 we could use app::paths
 * @ToDo: Do we need the custom logging?
 * @ToDo: custom appmodel for better listing
 * @ToDo: ApiExceptions - easy code to msg conversion etc
 * @ToDo: config(& docs) for custom rest routing so that we can use delete/post/put etc from the browser for example
 * @ToDo: convert samples from cakephp2 in cakephp3, will also help me verify if I've implemented everything
 * @ToDo: add dispatcher filter that sets request to not be cached
 * @ToDo: Test paging - shouldn't be part of the plugin, but good idea to test if it works together with OpenApi
 * @ToDo: Add config setting for picking the default output method xml/json
 *
 *
 * @ToDo document: convert the cakephp2 docs in cakephp3 - config etc is different + some feature have not yet been implemented
 * @ToDo document: in the ErrorHandling sample add a sample for extending the ApiError class(currently it's pointing to the validation sample for that)
 * @ToDo document: in the ErrorHandling sample add an example on how to manipulate the http status codes for errors
 * @ToDo document: in the REST Routing sample, show an example on how to modify the default routings and regex that only uses numbers by default
 * @ToDo document: that error codes will be set in the controller $this->ExitCode and $this->ExitMessage
 * @ToDo: Unit tests
 */
class AppController extends BaseController {
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
	 * All params related to this api request
	 */
	 protected $Params = array();

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize() {
        parent::initialize();

		$this->loadComponent('OpenApi.ApiAuth');
		$this->loadComponent('RequestHandler');
    }

	/**
     * First function called in the api
     * Responsible for the setup before the action is called
     */
    public function beforeFilter(Event $event) {
        $this->debug('');
        $this->debug('');
        $this->debug('-----------------------------------------------');
        $this->debug('------------START RECEIVING API CALL-----------');
        $this->debug('-----------------------------------------------');
        $this->debug('Api Call Received - Start Handling');
        parent::beforeFilter($event);

        // -- Make all parameter keys lower case & merge the request data(post) with the query(get)
        if(isset($this->request->query)) { $this->request->query = array_change_key_case($this->request->query); }
        if(isset($this->request->data)) { $this->request->data = array_change_key_case($this->request->data); }
        $this->request->query = array_merge($this->request->query, $this->request->data);

        $this->Params = $this->request->query;

        $this->debug('Request: '.$_SERVER['HTTP_HOST'].urldecode($_SERVER['REQUEST_URI']));
        $this->debug('Recieved:');
        $this->debug($this->request->query);

        //do auth
        $this->_authenticate();
        $this->_authorize();
        $this->_checkParams();
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return void
     */
    function beforeRender(Event $event) {
        parent::beforeRender($event);

        //this way we have always 'something' to return, usefull for requests that don't need to return anything
        //for example a delete, http status code 200 is enough
        if(empty($this->viewVars)) {
            $this->set(['status' => $this->ExitCode, 'message' => $this->ExitMessage ]);
        }

        /** Default to configured output type, if not configured, use json */
        if(empty($this->request->params['ext'])) {
            $type = Configure::read('OpenApi.DefaultOutputFormat');
            if(empty($type)) { $type = 'json'; }
            $this->request->params['ext'] = $type;
            $this->RequestHandler->renderAs($this, $type);
        }

        if($this->request->params['ext'] == 'xml') {
            $this->viewVars = ['response' => $this->viewVars];
        }
        $this->set('_serialize', true);

        //Log the end, for easier viewing log files
        $this->debug('-----------------------------------------------');
        $this->debug('-------------END HANDLING API CALL-------------');
        $this->debug('-----------------------------------------------');
        $this->debug('');
        $this->debug('');
    }

	/**
     * Auth part of every api call
     * Runs the Authentication and Authorization for the api call
     */
    private function _authenticate() {
        // -- Authentication methods must be configured in the controller
        if(
            isset($this->AuthConfig[strtolower($this->request->params['action'])]) &&
            isset($this->AuthConfig[strtolower($this->request->params['action'])]['authorize'])
        ) {
			$this->ApiAuth->config('authenticate', $this->AuthConfig[strtolower($this->request->params['action'])]['authorize']);
        } else {
            $this->debug('UnAuthozied Access: no configuration found for "' . strtolower($this->request->params['action'] . '"'));
            throw new \Cake\Network\Exception\UnauthorizedException();
        }
        //Run the authentication
        $this->log('Starting Authentication...');

        /**
         * For activating the stateless(no sessions + cookies) mode, set Api.Auth.Stateless to true
         */
        if(Configure::read('OpenApi.Auth.Stateless')) {
            AuthComponent::$sessionKey = false;
        }
        // -- Start the Authentication Process
        if($result = $this->ApiAuth->identify()) {
            $this->debug('Authentication Successfull using method "'.$result['authorizetype'].'"');

			// -- Authentication can store additional user information in '$result['params']'
            if(isset($result) && is_array($result)) {
            	$this->Params = array_merge($this->Params, $result);
			}
        } else {
            $this->debug('Authentication Failed');
            throw new \Cake\Network\Exception\UnauthorizedException(); // -- will set 401 http status code
        }
    }

	/**
     * Authorize part of the auth process
     */
    private function _authorize() {
        /**
         * To make authorization more scalable, a separate class is called called <Controller><Action><(optional)AuthorizeType> class
         * Here we will 'create' the class name so that the framework can locate the authorization class
         */
        if(Configure::read('OpenApi.SeparateAuthorization')) {
            if(empty($this->Params['authorizetype'])) {
                $this->debug('Warning, SeparateAuthorization setting set to true, but authenticte process didn\'t set authorizetype');
            }
        } else {
            $className = $this->Params['authorizetype'];
            $fullClassName = "\\".Configure::read('App.namespace')."\Auth\Authorize\\".ucfirst($this->request->params['controller'])."\\".ucfirst($this->request->params['action'])."\\".$className.'Authorize';
        }

		$this->ApiAuth->config('authorize', [ $className => [
            'className' => $fullClassName,
		]]);

        $this->debug('Starting Authorization using "'.$this->Params['authorizetype'].'"');
        $authresult = $this->ApiAuth->isAuthorized($this->Params, $this->request);

        if($authresult != false) {
            // -- The authorize classes can return additional information that will be added to the $this->Params array
            if(is_array($authresult)) {
                $this->Params = array_merge($this->Params, $authresult);
            }
            $this->debug('Authorization successful');
        } else {
            $this->debug('Authorization failed');
            throw new \Cake\Network\Exception\ForbiddenException(); // -- will set 403 http status code
        }
    }

    /**
     * Checks if all the required parameters are set and valid in the request
     */
    private function _checkParams() {
        $params = [];
        if(!empty($this->AuthConfig[$this->request->params['action']]['params'])) {
            $params = array_merge($params, $this->AuthConfig[$this->request->params['action']]['params']);
        }
        if(!empty($this->AuthConfig[$this->request->params['action']][$this->Params['authorizetype']]['params'])) {
            $params = array_merge($params, $this->AuthConfig[$this->request->params['action']][$this->Params['authorizetype']]['params']);
        }
        if(!empty($params)) {
            ApiParameterValidator::Validate($this->request->query, $params);
        }
    }

    protected function debug($pValue) {
        if(Configure::read('debug')) {
            $this->log($pValue);
        }
    }
}
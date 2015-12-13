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
 
/**
 * Include OpenApi app controller
 */ 
App::uses('OpenApiAppController', 'OpenApi.Controller');


/**
 * Class that handles all Error Output
 * We needed to extend the OpenApiController so that our output is generated automatically & in the correct format
 * This is a copy of the Core CakeErrorController, for more information, see the Core class docs 
 *
 * @package       OpenApi.Controller
 */
class ApiErrorController extends OpenApiAppController {
    /**
     * __construct
     *
     * @param CakeRequest $request
     * @param CakeResponse $response
     */
    public function __construct($request = null, $response = null) {
        parent::__construct($request, $response);
        $this->constructClasses();
        if (count(Router::extensions()) && !$this->Components->attached('RequestHandler')) {
            $this->RequestHandler = $this->Components->load('RequestHandler');
        }
        if ($this->Components->enabled('Auth')) {
            $this->Components->disable('Auth');
        }
        if ($this->Components->enabled('Security')) {
            $this->Components->disable('Security');
        }
        $this->_set(array('cacheAction' => false, 'viewPath' => 'Errors'));
    }


    /**
     * Called after the controller action is run, but before the view is rendered. You can use this method
     * to perform logic or set view variables that are required on every request.
     *
     * @return void
     * @link http://book.cakephp.org/2.0/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeRender() {
        parent::beforeRender();
        
        //modify the output to code/message format
        $this->viewVars['code'] = $this->ExitCode;
        $this->viewVars['message'] = $this->ExitMessage;
        $this->viewVars['_serialize'] = array ('code', 'message');
    }
}
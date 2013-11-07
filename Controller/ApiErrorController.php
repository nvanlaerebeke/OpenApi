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
    public $name = 'CakeError';
    public $uses = array();

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
    
    public function beforeFilter() {}
}
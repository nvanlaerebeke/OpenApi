<?php
/**
 * Base Authorizer class file for OpenApi
 *
 * Copyright (c)  *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @package       OpenApi.Controller.Auth.Authorization
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Base Authorizer class for OpenApi
 * 
 * @package       OpenApi.Controller.Auth.Authorization
 */
abstract class BaseAuthorizer extends Object {

    /**
     * Class constructor
     * Adds model support to Authroize classes
     */
    public function __construct() {
        parent::__construct();
        if(isset($this->uses)) {
            for($i = 0; $i < count($this->uses); $i++) {
                App::Import('Model', $this->uses[$i]);
                $name = $this->uses[$i]; 
                $this->$name = new $name();
            }
        } 
    }
    
    /**
     * Authorize the request 
     * @var array $pContext 
     * @var array $pParams
     * @return mixed
     */
    abstract protected function Authorize($pContext, $pParams);
}

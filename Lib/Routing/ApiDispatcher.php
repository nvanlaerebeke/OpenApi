<?php
/**
 * Api Dispatcher class file
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 
 * @link          http://blog.crazytje.com
 * @package       OpenApi.Routing
 * @since         OpenApi v0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Include the core dispatcher 
 */
App::uses('Dispatcher', 'Routing');

/**
 * Custom Dispatcher for dispatching to the correct controller
 *
 * @ToDo: Cache the  __getLineage result for increased performace
 * 
 * @package       OpenApi.Routing
 */
class ApiDispatcher extends Dispatcher {
    /**
     * Gets the correct versioned controller the application needs
     * Priority will be given to the version entered in the URL
     * 
     * Uses the following config settings:
     *  - Api.Versions
     *  - Api.VersionTypes
     * 
     * @param CakeRequest $pRequest
     * @param CakeResponse $pResponse
     * @return Controller 
     */
    protected function _getController($pRequest, $pResponse) {
        $apiversions = Configure::read('Api.Versions');
        $versiontypes = Configure::read('Api.VersionTypes');

        if(!empty($apiversions) && !empty($versiontypes)) {
            foreach($versiontypes as $type) {
                $paths = array();
                if(isset($pRequest->params['version']) && in_array($pRequest->params['version'], $apiversions)) {
                    $paths[] = ROOT.DS.APP_DIR.DS.$type.DS.$pRequest->params['version'].DS;
                }
                foreach($apiversions as $version) {
                    $path = ROOT.DS.APP_DIR.DS.$type.DS.$version.DS;
                    if(!in_array($path, $paths)) {
                        $paths[] = $path;
                    }
                }
                App::build(array($type => $paths), App::PREPEND);
            }
        }
        $ctrller = parent::_getController($pRequest, $pResponse);
        if($ctrller) {
            //If the base class is ApiController, then map the REST methods
            if(in_array("ApiController", $this->__getLineage($ctrller))) {
                if(!empty($_SERVER['REQUEST_METHOD'])) {
                    $update = false;
                    switch($_SERVER['REQUEST_METHOD']) {
                        case 'GET':
                        case 'POST':
                            break;
                        case 'PUT':
                            $pRequest->params['pass'][] = $pRequest->params['action'];
                            $pRequest->params['action'] = 'edit';
                            $update = true;
                            break;
                        case 'DELETE':
                            $pRequest->params['pass'][] = $pRequest->params['action'];
                            $pRequest->params['action'] = 'delete';
                            $update = true;
                            break;
                         
                    }
                    if($update) {
                        $ctrller = parent::_getController($pRequest, $pResponse);
                    }
                }
            }
            return $ctrller; 
        } else {
            App::uses('ApiException', 'OpenApi.Lib');
            throw new NotFoundException();
        }
    }

    /**
     * Gets the inheritance for an object
     * @param object $pObject
     * @return array List of ancestors for $pObject  
     */
    private function __getLineage($pObject) {
        $class = new ReflectionClass($pObject);
        $lineage = array();

        while ($class = $class->getParentClass()) {
            $lineage[] = $class->getName();
        }
        return $lineage;
    }
}
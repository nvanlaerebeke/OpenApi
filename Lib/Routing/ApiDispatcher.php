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
 * @package       OpenApi.Routing
 */
class ApiDispatcher extends Dispatcher {
    /**
     * Gets the correct versioned controller the application needs
     * Priority will be given to the version entered in the URL
     * 
     * Uses the following config settings:
     *  - OpenApi.Versions
     *  - OpenApi.VersionTypes
     * 
     * @param CakeRequest $pRequest
     * @param CakeResponse $pResponse
     * @return Controller 
     */
    protected function _getController($pRequest, $pResponse) {
        //Setup the paths to support Versioning
        $this->__setupPaths(&$pRequest);
        $this->__clearVersionedPaths();
        
        $ctrller = parent::_getController($pRequest, $pResponse);
        if($ctrller) {
            //If the base class is ApiController, then map the REST methods if needed
            if(in_array("OpenApiAppController", $this->__getLineage($ctrller))) {
                $RESTMappings = Configure::read('OpenApi.REST.methods');
                //When the request methods must be mapped, and the current action is not equal to the mapped action, change the request to the correct action
                if(!empty($_SERVER['REQUEST_METHOD']) && is_array($RESTMappings) && in_array($_SERVER['REQUEST_METHOD'], array_keys($RESTMappings))) {
                    if($pRequest->params['action'] != $RESTMappings[$_SERVER['REQUEST_METHOD']]) {
                        $pRequest->params['pass'][] = $pRequest->params['action'];
                        $pRequest->params['action'] = $RESTMappings[$_SERVER['REQUEST_METHOD']];
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

    private function __setupPaths(&$pRequest) {

        $apiversions = Configure::read('OpenApi.Versions');
        $versiontypes = Configure::read('OpenApi.VersionTypes');

        //no valid version was given in the URL, assume it was the ctrller
        if(!empty($apiversions) && isset($pRequest->params['version']) && !in_array($pRequest->params['version'], $apiversions)) {
            $pRequest->params['pass'][] = $pRequest->params['action'];
            $pRequest->params['action'] = $pRequest->params['controller'];
            $pRequest->params['controller'] = $pRequest->params['version'];
            $pRequest->params['version'] = $apiversions[0];
        }

        /**
         * Filter out newer versions then what's requested
         * We never want to use newer versions when we requested an older version
         */
        $newversions = array();
        if(!empty($apiversions)) {
            $found = false;
            foreach($apiversions as $version) {
                if(!empty($pRequest->params['version']) && $version == $pRequest->params['version']) {
                    $found = true;
                }
                if($found) {
                    $newversions[] = $version;
                }
            }
            $apiversions = $newversions;
            Configure::write('OpenApi.Versions', $apiversions);
        }

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
    
    private function __clearVersionedPaths() {
        
        /*foreach(Configure::read('OpenApi.VersionTypes') as $type) {
            foreach(Configure::read('OpenApi.Versions') as $version) {
                echo ROOT.DS.APP_DIR.DS.$type.DS.$version.DS."\n";
            }
        }*/
    }
}
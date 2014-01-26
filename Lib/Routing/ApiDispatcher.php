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
     * See Dispatcher dispatch function for more info
     * 
     * Addition is that here we detect the version we're trying to use
     * Creates a new CakeRequest object with the version as a parameter 
     */
    public function dispatch(CakeRequest $pRequest, CakeResponse $pResponse, $pAdditionalParams = array()) {
        /**
         * We could use routes for getting the correct result, but doing it like this is much easier. 
         */
        $parts = explode('/', $pRequest->url);
        $version = (isset($parts[0])) ? $parts[0] : '';

        if(!empty($version) && in_array($version, Configure::read('OpenApi.Versions'))) {
            $pRequest = new CakeRequest(str_replace($version, '', $pRequest->url));
            $pRequest->params['version'] = $version; 
        }
        parent::dispatch($pRequest, $pResponse, $pAdditionalParams);
    }    
    
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
        
        return parent::_getController($pRequest, $pResponse);
    }

    private function __setupPaths(&$pRequest) {
        $apiversions = Configure::read('OpenApi.Versions');
        $versiontypes = Configure::read('OpenApi.VersionTypes');

        /**
         * - Filter out newer versions then what's requested
         *   We never want to use newer versions when we requested an older version
         * 
         * - Add paths to the App::paths that we want versioned
         * 
         */
        $newversions = array();
        if(!empty($apiversions) && !empty($versiontypes)) {
            if(!empty($pRequest->params['version'])) {
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
}
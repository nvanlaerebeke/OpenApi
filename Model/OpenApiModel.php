<?php
/**
 * Custom ApiModel class file
 *
 * Copyright (c)  *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @package       OpenApi.Controller.Authorize
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Import the base model class for OpenApi
 */ 
App::uses('OpenApiAppModel', 'OpenApi.Model');

/**
 * Custom ApiModel class
 * 
 * @package       OpenApi.Model
 */
class OpenApiModel extends OpenApiAppModel {
    /**
     * List of extra find methods
     * @var array 
     */
    public $findMethods = array('apilist' =>  true); 

    /**
     * Extra find method that returns a listing that can be used to directly output xml/json
     * @var string $state
     * @var array $query
     * $var array $results
     */
    protected function _findApilist($state, $query, $results = array()) {
        if ($state === 'after') {
            if(!empty($results) && count($results) > 0) {
                $key = key($results[0]);
                $count = count($results); $modified = array();
                for($i = 0; $i < $count; $i++) {
                    $modified[$key][] = $results[$i][$key];
                }
                return $modified;
            }
            return $results;
        } else {
            return $query;
        }
    }
}
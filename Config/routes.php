<?php
/**
 * Routes file for OpenApi
 * Brings some custom routing to OpenApi 
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Crazy <info@crazytje.com>
 * @link          http://blog.crazytje.com 
 * @package       OpenApi.Config
 * @since         OpenApi v 0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
*/

    /*Router::resourceMap(array(
        array('action' => 'index', 'method' => 'GET', 'id' => false),
        array('action' => 'view', 'method' => 'GET', 'id' => true),
        array('action' => 'add', 'method' => 'POST', 'id' => false),
        array('action' => 'edit', 'method' => 'PUT', 'id' => true),
        array('action' => 'delete', 'method' => 'DELETE', 'id' => true),
        array('action' => 'update', 'method' => 'POST', 'id' => true)
    ));*/
    Router::mapResources(array(':controller'));
    Router::parseExtensions(); 

/** 
 * Adds versioning support to the api plugin
 * This is used in the ApiDispatcher, without it versioning will not work
 */
    $versions = Configure::read('OpenApi.Versions');
    if(!empty($versions) &&  count($versions) > 0) {
        Router::connect('/:controller/:action/*', array());
    }
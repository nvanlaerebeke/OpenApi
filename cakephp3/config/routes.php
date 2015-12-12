<?php
/**
 * Routes file for OpenApi
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
 */
 
use Cake\Routing\Router;

Router::plugin('OpenApi', function ($routes) {
    $routes->fallbacks('DashedRoute');
	
    $routes->extensions(['json', 'xml' ]);
    $routes->resources('*');
});

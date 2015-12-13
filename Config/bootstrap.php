<?php
/**
 * Bootstrap file for OpenApi
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
/**
 * Load some classes we always need available in case of exceptions
 */
App::uses('ApiException', 'OpenApi.Lib/Error');

/**
 * Include The Exception Renderer
 * For some reason it doesn't want to be included using App::uses('ApiExceptionRenderer', 'Api.lib/Error');
 */
include_once(App::pluginPath('OpenApi').DS.'Lib'.DS.'Error'.DS.'AppExceptionRenderer.php'); 
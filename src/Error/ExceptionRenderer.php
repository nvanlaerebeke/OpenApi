<?php
/**
 * class file for the custom "ExceptionRenderer"
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
**/

namespace OpenApi\Error;

use Cake\Core\App;
use Cake\Routing\Router;
use Cake\Network\Response;

/**
 * Api exception renderer class
 */
class ExceptionRenderer extends \Cake\Error\ExceptionRenderer {
    /**
     * Get the controller instance to handle the exception.
     * Override this method in subclasses to customize the controller used.
     * This method returns the built in `ErrorController` normally, or if an error is repeated
     * a bare controller will be used.
     *
     * @return \Cake\Controller\Controller
     * @triggers Controller.startup $controller
     */
    protected function _getController() {
        if (!$request = Router::getRequest(true)) {
            $request = Request::createFromGlobals();
        }
        $response = new Response();

        try {
            $class = '\OpenApi\Controller\ErrorController';
            $controller = new $class($request, $response);
            $controller->startupProcess();
            $startup = true;
        } catch (Exception $e) {
            $startup = false;
        }

        // Retry RequestHandler, as another aspect of startupProcess()
        // could have failed. Ignore any exceptions out of startup, as
        // there could be userland input data parsers.
        if ($startup === false && !empty($controller) && isset($controller->RequestHandler)) {
            try {
                $event = new Event('Controller.startup', $controller);
                $controller->RequestHandler->startup($event);
            } catch (Exception $e) {
            }
        }
        if (empty($controller)) {
            $controller = new \OpenApi\Controller\ErrorController($request, $response);
        }
        $controller->ExitCode = $this->error->getCode();
        $controller->ExitMessage = $this->error->getMessage();
        return $controller;
    }
}

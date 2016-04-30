<?php
/**
 * Api missing param exception class file
 *
 * Copyright (c)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c)
 * @link          http://blog.crazytje.com
 * @package       OpenApi.Lib.Error
 * @since         OpenApi v0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace OpenApi\Error\Exception;

/**
 * Exception class used for missing params, based on ApiException
 *
 * @package       OpenApi.Lib.Error
 */
class MissingParameterException extends OpenApiException {
    /**
     * @var string $pParam
     * @var string $pMessage
     * @var Exception $pPrevious
     */
    public function __construct($pParam = null, $pMessage = null, Exception $pPrevious = null) {
        $errorclass = $this->GetErrorClass();
        $code = $errorclass::GetCodeForParam($pParam);
        $pMessage = $pParam.': '.$errorclass::GetMessageForValidationError('notEmpty');
        if($code == 400) {
            $code = $errorclass::GetCodeForValidationError('notEmpty');
        }
        parent::__construct($code, $pMessage, $pPrevious);
    }
}
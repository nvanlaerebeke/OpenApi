<?php
/**
 * Api Log class file
 *
 * Copyright (c) 
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) 
 * @link          http://blog.crazytje.com
 * @package       OpenApi.Log.Engine
 * @since         OpenApi v0.0.1
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

 
/**
 * Import the cakephp FileLog engine
 */
App::uses('FileLog', 'Log/Engine');


/**
 * File Storage stream for Logging. Writes logs to different files
 * based on the type of log it is.
 *
 * @package       OpenApi.Log.Engine
 */
class ApiLog extends FileLog {
    /**
     * Constructs a new File Logger.
     *
     * Config
     *
     * - `types` string or array, levels the engine is interested in
     * - `scopes` string or array, scopes the engine is interested in
     * - `file` log file name
     * - `path` the path to save logs on.
     *
     * @param array $options Options for the FileLog, see above.
     */
    public function __construct($config = array()) {
        parent::__construct($config);
        
        $configured = CakeLog::configured();
        foreach($configured as $stream) {
            CakeLog::drop($stream);
        }
    }

    /**
     * Implements writing to log files.
     *
     * @param string $type The type of log you are making.
     * @param string $message The message you want to log.
     * @return boolean success of write.
     */
    public function write($type, $message) {
        $filename = $this->_path . $this->_file;
        $output = date('Y-m-d H:i:s') . ': ' . '['.getmypid().'] ' . $message . "\n";
        return file_put_contents($filename, $output, FILE_APPEND);
    }
}
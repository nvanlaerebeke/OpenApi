<?php

/**
 * ApiGen 3.0dev - API documentation generator for PHP 5.3+
 *
 * Copyright (c) 2010-2011 David Grudl (http://davidgrudl.com)
 * Copyright (c) 2011-2012 Jaroslav Hanslík (https://github.com/kukulich)
 * Copyright (c) 2011-2012 Ondřej Nešpor (https://github.com/Andrewsville)
 *
 * For the full copyright and license information, please view
 * the file LICENSE.md that was distributed with this source code.
 */

namespace ApiGen;

/**
 * pear.ApiGen.org update checker.
 */
class UpdateChecker extends Object implements IUpdateChecker
{
	/**
	 * Creates a checker instance.
	 *
	 * Tries to set the default socket timeout.
	 */
	public function __construct()
	{
		@ini_set('default_socket_timeout', 5);
	}

   /**
	 * Returns the newest version.
	 *
	 * @return string
	 */
	public function getLatestVersion()
	{
		$latestVersion = @file_get_contents('http://pear.apigen.org/rest/r/apigen/latest.txt');
		return false === $latestVersion ? null : trim($latestVersion);
	}

	/**
	 * Checks if there is newer version. If so, triggers the "updateAvailable" event.
	 *
	 * @return boolean
	 */
	public function checkUpdate()
	{
		$latestVersion = $this->getLatestVersion();
		if (!empty($latestVersion) && version_compare(Environment::getApplicationVersion(), $latestVersion, '<')) {
			$this->fireEvent('updateAvailable', $latestVersion);
			return true;
		}

		return false;
	}
}

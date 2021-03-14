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

namespace ApiGen\Checkstyle;

use ApiGen\Reflection\ReflectionBase;
use ApiGen\Reflection\ReflectionElement;

class DescriptionCheck implements ICheck
{
	public function isDoable(ReflectionBase $element)
	{
		return $element instanceof ReflectionElement;
	}

	public function check(ReflectionBase $element)
	{
		$messages = array();

		if (empty($element->shortDescription)) {
			$annotations = $element->getAnnotations();

			if (empty($annotations)) {
				$messages[] = new Message(sprintf('Missing documentation of %s', Report::getElementLabel($element)), $element->getStartLine());
			} else {
				$messages[] = new Message(sprintf('Missing description of %s', Report::getElementLabel($element)), $element->getStartLine());
			}
		}

		return $messages;
	}
}

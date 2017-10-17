<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFContrastingColor
{
	/**
	 * @var string|null
	 */
	private $sourceColor;

	public function __construct($sourceColor)
	{
		$this->sourceColor = str_replace('#', '', $sourceColor);
	}

	public function getHex(){
		// http://24ways.org/2010/calculating-color-contrast/
		$r = hexdec(substr($this->sourceColor,0,2));
		$g = hexdec(substr($this->sourceColor,2,2));
		$b = hexdec(substr($this->sourceColor,4,2));
		$yiq = (($r*299)+($g*587)+($b*114))/1000;
		return ($yiq >= 128) ? '#000' : '#fff';
	}

	public function __toString()
	{
		return $this->getHex();
	}
}
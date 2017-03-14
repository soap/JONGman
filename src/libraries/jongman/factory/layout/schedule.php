<?php
jimport('jongman.base.ilayoutfactory');
class RFFactoryLayoutSchedule implements ILayoutFactory
{
	private $_targetTimezone;

	/**
	 * @param string $targetTimezone target timezone of layout
	 */
	public function __construct($targetTimezone)
	{
		$this->_targetTimezone = $targetTimezone;
	}

	/**
	 * @see ILayoutFactory::CreateLayout()
	 */
	public function createLayout()
	{
		return new RFLayoutSchedule($this->_targetTimezone);
	}
}
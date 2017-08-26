<?php
interface ILayoutFactory
{
	/**
	 * @return IScheduleLayout
	 */
	public function createLayout();
}
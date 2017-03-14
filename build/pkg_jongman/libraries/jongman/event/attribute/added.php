<?php
defined('_JEXEC') or die;
class RFEventAttributeAdded extends RFSeriesEvent
{
	/**
	 * @return int
	 */
	public function attributeId()
	{
		return $this->attribute->attributeId;
	}

	/**
	 * @return mixed
	 */
	public function value()
	{
		return $this->attribute->value;
	}

	/**
	 * @var \RFAttributeValue
	 */
	private $attribute;

	public function __construct(RFAttributeValue $attribute, RFReservationExistingSeries $series)
	{
		$this->attribute = $attribute;

		parent::__construct($series, RFEventPriority::Low);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->attribute->__toString());
	}
}
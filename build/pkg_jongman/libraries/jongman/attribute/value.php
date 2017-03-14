<?php
defined('_JEXEC') or die;

class RFAttributeValue
{
	/**
	 * @var int
	 */
	public $attributeId;

	/**
	 * @var mixed
	 */
	public $value;

	/**
	 * @var string
	 */
	public $attributeLabel;

	/**
	 * @param $attributeId int
	 * @param $value mixed
	 * @param $attributeLabel string|null
	 */
	public function __construct($attributeId, $value, $attributeLabel = null)
	{
		$this->attributeId = $attributeId;
		$this->value = trim($value);
		$this->attributeLabel = $attributeLabel;
	}

	public function __toString()
	{
		return sprintf("AttributeValue id:%s value:%s", $this->attributeId, $this->value);
	}
}

class RFAttributeNullValue extends RFAttributeValue
{
	public function __construct()
	{
		parent::__construct(null, null);
	}
}
<?php

interface IAttributeRepository
{
	/**
	 * @abstract
	 * @param CustomAttribute $attribute
	 * @return int
	 */
	public function add(CustomAttribute $attribute);

	/**
	 * @abstract
	 * @param $attributeId int
	 * @return CustomAttribute
	*/
	public function loadById($attributeId);

	/**
	 * @abstract
	 * @param CustomAttribute $attribute
	*/
	public function update(CustomAttribute $attribute);

	/**
	 * @abstract
	 * @param $attributeId int
	 * @return void
	*/
	public function deleteById($attributeId);

	/**
	 * @abstract
	 * @param int|CustomAttributeCategory $category
	 * @return array|CustomAttribute[]
	*/
	public function getByCategory($category);

	/**
	 * @abstract
	 * @param int|CustomAttributeCategory $category
	 * @param array|int[] $entityIds if null is passed, get all entity values
	 * @return array|AttributeEntityValue[]
	*/
	public function getEntityValues($category, $entityIds = null);

}

class RFAttributeRepository implements IAttributeRepository
{
	/**
	 * @var DomainCache
	 */
	private $cache;

	public function __construct()
	{
		//$this->cache = new DomainCache();
	}

	public function add(CustomAttribute $attribute)
	{
		return ServiceLocator::GetDatabase()
		->ExecuteInsert(
				new AddAttributeCommand($attribute->Label(), $attribute->Type(), $attribute->Category(), $attribute->Regex(),
						$attribute->Required(), $attribute->PossibleValues(), $attribute->SortOrder(), $attribute->EntityId()));
	}

	/**
	 * @param int|CustomAttributeCategory $category
	 * @return array|CustomAttribute[]
	 */
	public function getByCategory($category)
	{
		if (!$this->cache->exists($category))
		{
			$reader = ServiceLocator::GetDatabase()->Query(new GetAttributesByCategoryCommand($category));

			$attributes = array();
			while ($row = $reader->GetRow())
			{
				$attributes[] = CustomAttribute::FromRow($row);
			}

			$this->cache->add($category, $attributes);

		}

		return $this->cache->Get($category);
	}

	/**
	 * @param $attributeId int
	 * @return CustomAttribute
	 */
	public function loadById($attributeId)
	{
		$reader = ServiceLocator::GetDatabase()
		->Query(new GetAttributeByIdCommand($attributeId));

		$attribute = null;
		if ($row = $reader->GetRow())
		{
			$attribute = CustomAttribute::FromRow($row);
		}

		return $attribute;
	}

	/**
	 * @param CustomAttribute $attribute
	 */
	public function update(CustomAttribute $attribute)
	{
		ServiceLocator::GetDatabase()
		->Execute(
		new UpdateAttributeCommand($attribute->Id(), $attribute->Label(), $attribute->Type(), $attribute->Category(),
		$attribute->Regex(), $attribute->Required(), $attribute->PossibleValues(), $attribute->SortOrder(),
		$attribute->EntityId()));
	}

	/**
	 * @param int|CustomAttributeCategory $category
	 * @param array|int[] $entityIds
	 * @return array|AttributeEntityValue[]
	 */
	public function getEntityValues($category, $entityIds = null)
	{
		$values = array();

		if (!is_array($entityIds) && !empty($entityIds))
		{
			$entityIds = array($entityIds);
		}

		if (empty($entityIds))
		{
			$reader = ServiceLocator::GetDatabase()
			->Query(new GetAttributeAllValuesCommand($category));
		}
		else{
			$reader = ServiceLocator::GetDatabase()
			->Query(new GetAttributeMultipleValuesCommand($category, $entityIds));
		}
		$attribute = null;
		while ($row = $reader->GetRow())
		{
			$values[] = new AttributeEntityValue(
					$row[ColumnNames::ATTRIBUTE_ID],
					$row[ColumnNames::ATTRIBUTE_ENTITY_ID],
					$row[ColumnNames::ATTRIBUTE_VALUE]);
		}

		return $values;
	}

	/**
	 * @param $attributeId int
	 * @return void
	 */
	public function deleteById($attributeId)
	{
		ServiceLocator::GetDatabase()
		->Execute(new DeleteAttributeCommand($attributeId));
		ServiceLocator::GetDatabase()
		->Execute(new DeleteAttributeValuesCommand($attributeId));
	}
}
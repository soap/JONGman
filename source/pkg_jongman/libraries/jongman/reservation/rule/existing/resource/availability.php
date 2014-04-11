<?php
defined('_JEXEC') or die;

class RFReservationRuleExistingResourceAvailability extends RFReservationRuleResourceAvailability //implements IUpdateReservationValidationRule
{
	public function validate($series)
	{
		//just want to change isInconflict method
		return parent::validate($series);
	}
	
	/**
	 * @param RFReservation $instance
	 * @param RFReservationSeries|RFReservationExistingSeries $series
	 * @param IReservedItem $existingItem
	 * @return bool
	 */
	protected function isInConflict(RFReservation $instance, RFReservationSeries $series, IReservedItem $existingItem)
	{
		if ($existingItem->getId() == $instance->reservationId() ||
			$series->isMarkedForDelete($existingItem->getId()) ||
			$series->isMarkedForUpdate($existingItem->getId())
		)
		{
			return false;
		}
		
		return ($existingItem->getResourceId() == $series->resourceId()) ||
			(false !== array_search($existingItem->getResourceId(), $series->allResourceIds()));
	}	
}
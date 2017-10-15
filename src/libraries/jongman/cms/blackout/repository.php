<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IBlackoutRepository
{
	/**
	 * @param RFBlackoutSeries $blackoutSeries
	 * @return int
	 */
	public function add(RFBlackoutSeries $blackoutSeries);

	/**
	 * @param RFBlackoutSeries $blackoutSeries
	*/
	public function update(RFBlackoutSeries $blackoutSeries);

	/**
	 * @param int $blackoutId
	*/
	public function delete($blackoutId);

	/**
	 * @param int $blackoutId
	*/
	public function deleteSeries($blackoutId);

	/**
	 * @param int $blackoutId
	 * @return RFBlackoutSeries
	*/
	public function loadByInstanceId($instanceId);
}

class RFBlackoutRepository implements IBlackoutRepository
{
	/**
	 * @param BlackoutSeries $blackoutSeries
	 * @return int
	 */
	public function add(RFBlackoutSeries $blackoutSeries)
	{
		$seriesId = $this->addSeries($blackoutSeries);
		$table = JTable::getInstance('BlackoutInstance', 'JongmanTable');
		foreach ($blackoutSeries->allBlackouts() as $blackout)
		{
			$table->reset();
			$table->save(array(
							'blackout_id'=>$seriesId, 
							'start_date'=>$blackout->startDate()->toDatabase(), 
							'end_date'=>$blackout->endDate()->toDatabase()
					));			
		}

		return $seriesId;
	}

	/**
	 * 
	 * @param RFBlackoutSeries $blackoutSeries
	 * @return unknown
	 * @todo improve this method based on Joomla Model save method (add event support by plugin etc)
	 */
	private function addSeries (RFBlackoutSeries $blackoutSeries)
	{
		$dbo = JFactory::getDbo();
		
		$table = JTable::getInstance('Blackout', 'JongmanTable');
		$data = array('owner_id' => $blackoutSeries->ownerId(), 
					'title' => $blackoutSeries->title(), 
					'repeat_type' => $blackoutSeries->repeatType(), 
					'repeat_options'=> $blackoutSeries->repeatConfigurationString(),
					'alias' => JFilterOutput::stringURLSafe(JUserHelper::genRandomPassword()),
					'state' => $blackoutSeries->getState()
			);
		$table->bind($data);
		$table->check();
		$table->store();
		
		$seriesId = $table->id;

		foreach ($blackoutSeries->resourceIds() as $resourceId)
		{
			$blackoutResource = new StdClass();
			$blackoutResource->blackout_id = $seriesId;
			$blackoutResource->resource_id = $resourceId;
			$dbo->insertObject('#__jongman_blackout_resources', $blackoutResource);
		}

		return $seriesId;
	}

	/**
	 * @param int $blackoutId
	 */
	public function delete($blackoutId)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->delete('#__jongman_blackout_instances')->where('id='.$blackoutId);
		
		$dbo->setQuery($query);
		$dbo->query();
	}

	/**
	 * @param int $blackoutId
	 */
	public function deleteSeries($blackoutId)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('blackout_id')->from('#__jongman_blackout_instances')->where('id='.$blackoutId);
		
		$dbo->setQuery($query);
		$seriesId = $dbo->loadResult();
		$table = JTable::getInstance('Blackout', 'JongmanTable');
		
		$table->delete($seriesId);
	}

	/**
	 * @param int $blackoutId
	 * @return RFBlackoutSeries
	 */
	public function loadByInstanceId($instanceId)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->select('bs.*, bi.id as instance_id, bi.start_date, bi.end_date')
			->from('#__jongman_blackouts AS bs')
			->join('inner','#__jongman_blackout_instances AS bi ON bi.blackout_id=bs.id')
			->where('bi.id='.$instanceId);
		
		$dbo->setQuery($query);

		$blackoutObj = $dbo->loadObject();
		if ($blackoutObj)
		{
			$series = RFBlackoutSeries::fromRow($blackoutObj);

			$query->clear();
			$query->select('*')
				->from('#__jongman_blackout_instances AS bi')
				->where('blackout_id='.$series->getId());
			$dbo->setQuery($query);
			$instanceObjects= $dbo->loadObjectList();

			foreach ($instanceObjects as $instanceObj)
			{
				$instance = new RFBlackout(
									new RFDateRange(RFDate::fromDatabase($instanceObj->start_date), RFDate::fromDatabase($instanceObj->end_date) )
									);
				$instance->withId($instanceObj->id);
				$series->addBlackout($instance);
			}

			$query->clear();
			$query->select('r.*, s.id as schedule_id')
				->from('#__jongman_blackout_resources AS rr')
				->join('INNER', '#__jongman_resources AS r ON rr.resource_id=r.id')
				->join('INNER', '#__jongman_schedules AS s ON r.schedule_id=s.id')
				->where('rr.blackout_id='.$series->id())
				->order('r.title');
			
			$dbo->setQuery($query);
			
			$resourceObjects = $dbo->loadObjectList();

			foreach ($resourceObjects as $resourceObj)
			{
				$series->addResource(new RFBlackoutResource(
						$resourceObj->id,
						$resourceObj->title,
						$resourceObj->schedule_id,
						'',//$resourceObj->admin_group_id,
						'',//$resourceObj->admin_group_alias,
						$resourceObj->state));
			}

			return $series;
		}
		else
		{
			return null;
		}
	}

	/**
	 * @param RFBlackoutSeries $blackoutSeries
	 */
	public function update(RFBlackoutSeries $blackoutSeries)
	{
		if ($blackoutSeries->isNew())
		{
			$seriesId = $this->addSeries($blackoutSeries);
			
			$start = $blackoutSeries->currentBlackout()->startDate();
			$end = $blackoutSeries->currentBlackout()->endDate();
			$instance = JTable::getInstance('BlackoutInstance', 'JongmanTable');
			$instance->load($blackoutSeries->currentBlackoutInstanceId());
			
			$instance->blackout_id = $seriesId; 
			$instance->start_date = $start->toDatabase();
			$instance->end_date = $end->toDatabase(); 
			$table->store();
			
		}
		else
		{
			$this->deleteSeries($blackoutSeries->currentBlackoutInstanceId());
			$this->add($blackoutSeries);
		}
	}
}
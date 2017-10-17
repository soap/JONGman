<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

abstract class RFReservationNotificationService implements IReservationNotificationService
{
	/**
	 * @var IReservationNotification[]
	 */
	protected $notifications;

	/**
	 * @param IReservationNotification[] $notifications
	 */
	public function __construct($notifications)
	{
		$this->notifications = $notifications;
	}

	/**
	 * @param $reservationSeries ReservationSeries|ExistingReservationSeries
	 * @return void
	 */
	public function notify($reservationSeries)
	{
		$referenceNumber = $reservationSeries->currentInstance()->referenceNumber();

		foreach ($this->notifications as $notification)
		{
			try
			{
				JLog::add("Calling notify on %s for reservation %s", get_class($notification), $referenceNumber);

				$notification->Notify($reservationSeries);
			}
			catch(Exception $ex)
			{
				JLog::add("Error sending notification of type %s for reservation %s. Exception: %s", get_class($notification), $referenceNumber, $ex);
			}
		}
	}
}
?>
<?php
defined('_JEXEC') or die;

class SlotLabelFactory
{
	/**
	 * @static
	 * @param ReservationItemView $reservation
	 * @return string
	 */
	public static function getInstance(RFReservationItem $reservation)
	{
		$f = new SlotLabelFactory();
		return $f->format($reservation);
	}

	/**
	 * @param ReservationItemView $reservation
	 * @return string
	 */
	public function format(RFReservationItem $reservation)
	{
		$property = JComponentHelper::getParams('com_jongman')->get('reservation_label_format');
		$name = $this->getFullName($reservation);

		if ($property == 'titleORuser')
		{
			if (strlen($reservation->title))
			{
				return $reservation->title;
			}
			else
			{
				return $name;
			}
		}
		if ($property == 'title')
		{
			return $reservation->title;
		}
		if ($property == 'none' || empty($property))
		{
			return '';
		}
		if ($property == 'name' || $property == 'user')
		{
			return $name;
		}

		$label = $property;
		$label = str_replace('{name}', $name, $label);
		$label = str_replace('{title}', $reservation->Title, $label);
		$label = str_replace('{description}', $reservation->Description, $label);

		return $label;
	}

	protected function getFullName(RFReservationItem $reservation)
	{
		$shouldHide = JComponentHelper::getParams('com_jongman')->get('reservation_hide_user');
		if ($shouldHide)
		{
			return JText::_('COM_JONGMAN_PRIVATE');
		}
		return $reservation->fullname;

	}
}
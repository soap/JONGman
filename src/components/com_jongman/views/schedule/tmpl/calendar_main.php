<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

//return page url after reservation complete/cance
$return = base64_encode(JFactory::getURI()->toString());

$today = RFDate::now(); 
$displayDates = $this->scheduledates->dates();
$baseRef = 'index.php?option=com_jongman&task=reservation.add&Itemid='.JRequest::getInt('Itemid');

$dailyDateFormat = $this->state->get('params')->get('daily_date_format', 'Y-m-d');
?>
<div class="clear-both"></div>
<div id="reservations">
<!-- start one day reservation table -->
<?php foreach($displayDates as $date) : ?>
	<table class="reservations ui-widget" border="1" cellpadding="0" width="100%">
	<?php if ($today->dateEquals($date)) : ?>
		<tr class="today">
	<?php else: ?>
		<tr>
	<?php endif;?>
			<td class="resdate"><?php echo $date->format($dailyDateFormat)?></td>
		<?php foreach ($this->layout->getPeriods($date, true) as $period) :?>
			<td class="reslabel" colspan="<?php echo $period->span()?>"><?php echo $period->label($date)?></td>
		<?php endforeach?>
		</tr>
	<?php foreach ($this->resources as $resource) :?>
	<?php $slots = $this->layout->getLayout($date, $resource->id);?>
		<tr class="slots">
			<td class="resourcename">
				<?php if ($this->layout->isDateReservable($date)) :?>
				<a class="resourceNameSelector" href="#" resourceId="<?php echo $resource->id?>"><?php echo $resource->title?></a>
				<?php else: ?>
				<?php echo $resource->title?>
				<?php endif; ?>
			</td>
		<?php 
			foreach ($slots as $slot) :
				$slotRef = $slot->beginDate()->format('YmdHis').$resource->id;
				$href = "{$baseRef}&rid={$resource->id}&sid={$this->schedule->id}&rd={$date->format('Y-m-d')}";
				/* @todo valida action permission of current user on this slot */
				echo RFDisplaySlotFactory::display($slot, $slotRef, $href, true, $this);
			endforeach;
		?>
		</tr>	
	<?php endforeach?>
	</table>
<!-- end of one day reservation table -->
<?php endforeach; ?>
</div>
<div style="height: 10px">&nbsp;</div>

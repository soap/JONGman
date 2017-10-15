<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
$filters = $this->filters;
$groupName = '';
$displayDate = $this->displayDate;
$filterUrl = 'index.php?option=com_jongman&view=calendar&caltype='.$this->calendar->getType();
if (!empty($displayDate))
	$filterUrl .= '&dd='.$displayDate->day().'&mm='.$displayDate->month().'&yy='.$displayDate->year();
?>
<div id="filter" class="row-fluid row-centered">
	<div class="col-centered">
	<?php if ($groupName) : ?>
		<span class="groupName">{$GroupName}</span>
	<?php else: ?>
		<label for="calendarFilter"><?php echo JText::_('COM_JONGMAN_CHANGE_RESOURCE')?></label>
		<select id="calendarFilter" class="textbox" ref="<?php echo JRoute::_($filterUrl);?>">
		<?php foreach ($filters->getFilters() as $filter ) :?>
			<option value="<?php echo $filter->getId()?>" class="schedule" <?php if ($filter->selected()) : ?>selected="selected"<?php endif?>><?php echo $filter->getName()?></option>
			<?php foreach ($filter->getFilters() as $subfilter) :?>
				<option value="<?php echo $subfilter->getId()?>" class="resource" <?php if ($subfilter->selected()) : ?>selected="selected"<?php endif?>><?php echo ' - '.$subfilter->getName()?></option>
			<?php endforeach?>
		<?php endforeach?>
		</select>
	<?php endif?>
	</div>
</div>
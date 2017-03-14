<?php
$filters = $this->filters;
$groupName = '';
?>
<div id="filter" class="row-fluid">
	<?php if ($groupName) : ?>
	<span class="groupName">{$GroupName}</span>
	<?php else: ?>
	<!--  label for="calendarFilter"--><?php echo JText::_('COM_JONGMAN_CHANGE_RESOURCE')?><!--  /label-->
	<select id="calendarFilter" class="inputbox">
		<?php foreach ($filters->getFilters() as $filter ) :?>
			<option value="<?php echo $filter->getId()?>" class="schedule" <?php if ($filter->selected()) : ?>selected="selected"<?php endif?>><?php echo $filter->getName()?></option>
			<?php foreach ($filter->getFilters() as $subfilter) :?>
				<option value="<?php echo $subfilter->getId()?>" class="resource" <?php if ($subfilter->selected()) : ?>selected="selected"<?php endif?>><?php echo ' - '.$subfilter->getName()?></option>
			<?php endforeach?>
		<?php endforeach?>
	<?php endif?>
	</select>
	<!--  a href="#" id="showResourceGroups"><?php echo JText::_('COM_JONGMAN_RESOURCE_GROUPS')?></a--!>
</div>
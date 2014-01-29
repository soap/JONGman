<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=reservations');?>" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search">
				<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>:</label>
			<input type="text" name="filter_search" id="filter_search"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				title="<?php echo JText::_('COM_JONGMAN_RESERVATIONS_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

		</div>
		<div class="filter-select fltrt">
			<select name="filter_schedule_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JONGMAN_OPTION_SELECT_SCHEDULE');?></option>
				<?php echo JHtml::_('select.options', JongmanHelper::getScheduleOptions(),
					'value', 'text', $this->state->get('filter.schedule_id'));?>
			</select>
			
			<select name="filter_resource_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JONGMAN_OPTION_SELECT_RESOURCE');?></option>
				<?php echo JHtml::_('select.options', JongmanHelper::getResourceOptions($this->state->get('filter.schedule_id')),
					'value', 'text', $this->state->get('filter.resource_id'));?>
			</select>
			
			<select name="filter_reservation_category" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JONGMAN_OPTION_SELECT_RESERVATION_CATEGORY');?></option>
				<?php echo JHtml::_('select.options', 
					JongmanHelper::getReservationCategoryOptions(), 	
					'value', 'text', $this->state->get('filter.reservation_category'));?>
			</select>
			
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'),
					'value', 'text', $this->state->get('filter.published'), true);?>
			</select>
			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'),
					'value', 'text', $this->state->get('filter.access'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_SCHEDULE', 'schedule_title', $listDirn, $listOrder); ?>				
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_RESOURCE', 'schedule_title', $listDirn, $listOrder); ?>				
				</th>	
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_START_DATE', 'start_date', $listDirn, $listOrder); ?>
				</th>	
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_END_DATE', 'end_date', $listDirn, $listOrder); ?>
				</th>							
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_OWNER', 'owner', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_STATE', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 're.author_name', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_CREATED_DATE', 're.created_time', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 're.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
	<?php if (empty($this->items)) : ?>
			<tr>
				<td colspan="12"><?php echo JText::_("COM_JONGMAN_NO_RECORD")?></td>
			</tr>
	<?php else: ?>
		<?php foreach ($this->items as $i => $item) :
			$item->max_ordering = 0; //??
			$ordering	= ($listOrder == 'a.ordering');
			$canCreate	= $user->authorise('core.create',		'com_jongman');
			$canEdit	= $user->authorise('core.edit',			'com_jongman.reservation.'.$item->id);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_jongman.reservation.'.$item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'reservations.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canCreate || $canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=reservation.edit&id='.$item->id);?>">
						<?php echo $this->escape(JString::substr($item->title, 0, 50)); ?></a>
					<?php else : ?>
						<?php echo $this->escape(JString::substr($item->title, 0, 50)); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
				<td>
					<?php echo $this->escape($item->schedule_title)?>
				</td>
				<td>
					<?php //echo $this->escape($item->resource_title)?>
				</td>
				<td>
					<?php //echo JDate::getInstance($item->start_date)->format("Y-m-d");?>
				</td>
				<td>
					<?php //echo JDate::getInstance($item->end_date)->format("Y-m-d");?>
				</td>				
				<td>
					<?php echo $this->escape($item->owner); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->state, $i, 'reservations.', $canChange); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->author); ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
	<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>


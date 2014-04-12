<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=reservations');?>" method="post" name="adminForm" id="adminForm">
<?php
if (!$this->is_j25) :
	if (!empty($this->sidebar)) :
?>
	<div id="j-sidebar-container" class="span2">
    	<?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span10">
    <?php else : ?>
    	<div id="j-main-container">
    <?php
    endif;
    //Search Toolbar
    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
else:
	 echo $this->loadTemplate('filter_j25');
endif;
?>	
	<div class="clr"></div>
<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>	
	<table class="adminlist table table-stripped">
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
		<tbody>
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
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->reference_number));?></p>
				</td>
				<td>
					<?php echo $this->escape($item->schedule_title)?>
				</td>
				<td>
					<?php echo $this->escape($item->resources)?>
				</td>
				<td>
					<?php echo JHtml::date($item->start_date, 'Y-m-d H:i', true) ;?>
				</td>
				<td>
					<?php echo JHtml::date($item->end_date, 'Y-m-d H:i', true) ;?>
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
		</tbody>
		<?php if ($this->is_j25) : ?>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<?php endif; ?>
	</table>
<?php endif; ?>
	<?php if (!$this->is_j25) : echo $this->pagination->getListFooter(); endif; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
<?php if (!$this->is_j25) : ?>
	</div>
<?php endif; ?>		
</form>


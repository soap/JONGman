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
$dbo 		= JFactory::getDbo();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=reservations');?>" method="post" name="adminForm" id="adminForm">
<?php
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
?>	
	<div class="clr"></div>
<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('COM_JONGMAN_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>	
	<table class="adminlist table table-stripped">
		<thead>
			<tr>
				<th width="1%" class="hidden-phone">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_STATE', 'r.state', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_SCHEDULE', 'schedule_title', $listDirn, $listOrder); ?>				
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_RESOURCE', 'resource_title', $listDirn, $listOrder); ?>				
				</th>	
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_START_DATE', 'a.start_date', $listDirn, $listOrder); ?>
				</th>	
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_END_DATE', 'a.end_date', $listDirn, $listOrder); ?>
				</th>							
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_OWNER', 'owner', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'author', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_CREATED_DATE', 'r.created', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
			<tr class="row<?php echo $i % 2; ?> sortable-group-id="<?php echo $item->schedule_id?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
					<?php if ($this->workflow) :?>
							<?php  $date = ($item->workflow_state->modified==$dbo->getNullDate() ? $item->workflow_state->created : $item->workflow_state->modified);?>
							<div class="btn-group" id="reserv_<?php echo $item->reservation_id?>">
								<button data-toggle="dropdown" class="dropdown-toggle btn btn-micro">
									<span class="caret"></span>
									<span class="element-invisible">JACTIONS</span>
								</button>
								<span class="pull-right"><?php echo JHtml::_('rfhtml.label.state', $item->workflow_state->title, $date)?></span>	
								<ul class="dropdown-menu"></ul>	
								<script type="text/javascript">
									WFWorkflow.loadWorkflowState('<?php echo JURi::root()?>index.php', 'com_jongman.reservation', jQuery('#reserv_<?php echo $item->reservation_id?>'), '<?php echo $item->reservation_id?>');
								</script>
							</div>	
					<?php else:?>
					<div class="btn-group">
						<?php 
							$states = array(1 => array('unapprove', 'COM_JONGMAN_APPROVED', 'COM_JONGMAN_RESERVATION_UNAPPROVE_ITEM', 'COM_JONGMAN_APPROVED', true, 'publish', 'publish'),
								0 => array('approve', 'COM_JONGMAN_UNAPPROVED', 'COM_JONGMAN_RESERVATION_APPROVE_ITEM', 'COM_JONGMAN_UNAPPROVED', true, 'unpublish', 'unpublish'),
								-1 => array('approve', 'COM_JONGMAN_PENDING', 'COM_JONGMAN_RESERVATION_APPROVE_ITEM', 'COM_JONGMAN_APPROVED', true, 'pending', 'pending'));
						
							echo JHtml::_('jgrid.state',$states, $item->state, $i, 'reservations.', $canChange); 
							// Create dropdown items
							if ($item->state == -1) {
								JHtml::_('actionsdropdown.addCustomItem', JText::_('COM_JONGMAN_ACTION_RESERVATION_APPROVE'), 'published', 'cb' . $i, 'reservations.approve');
								JHtml::_('actionsdropdown.addCustomItem', JText::_('COM_JONGMAN_ACTION_RESERVATION_UNAPPROVE'),'unpublished', 'cb' . $i, 'reservations.unapprove');
							}else if ($item->state == 1) {
								JHtml::_('actionsdropdown.addCustomItem', JText::_('COM_JONGMAN_ACTION_RESERVATION_UNAPPROVE'),'unpublished', 'cb' . $i, 'reservations.unapprove');
							}
							
							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
						?>
					</div>
					<?php endif;?>
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
				<td class="center">
					<?php echo $this->escape($item->owner); ?>
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
	</table>
<?php endif; ?>
	<?php if (!$this->is_j25) : echo $this->pagination->getListFooter(); endif; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
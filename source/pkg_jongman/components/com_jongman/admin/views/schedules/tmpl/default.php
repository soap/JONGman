<?php
/**
 * @version     $Id: default.php 506 2013-01-01 05:22:08Z mrs.siam $
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

$user		= JFactory::getUser();
$userId 	= $user->get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn   = $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder=='ordering';
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=schedules'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty($this->sidebar)) : ?>
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

	<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('COM_JONGMAN_NO_MATCHING_RESULTS'); ?>
	</div>
	<?php else : ?>
	<table class="adminlist table table-striped">
		<thead>
			<tr>
				<th width="1%" class="nowrap center hidden-phone">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="1%" class="hidden-phone">
					<?php echo JHtml::_('grid.checkall'); ?>
				</th>
				<th width="1%" style="min-width:55px" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'JSTATUS', 's.published', $listDirn, $listOrder); ?>
				</th>
				<th class="center">
					<?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_NAME', 'title', $listDirn, $listOrder); ?>
				</th>
                <th width="40% nowrap">
                    <?php echo JText::_('COM_JONGMAN_HEADING_RESERVANLE_SLOTS')?>
                </th>
                <th width="15%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_BLOCKED_SLOTS')?>
                </th>
                <th width="5%">
                	<?php echo JHtml::_('grid.sort','COM_JONGMAN_HEADING_FIRST_DAY', 's.weekday_start', $listDirn, $listOrder)?>
                </th>
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_VIEW_DAYS', 's.view_days', $listDirn, $listOrder)?>
                </th>
				<th width="10%" class="hidden-phone">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>         
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'ordering');
			$canCreate	= $user->authorise('core.create', 'com_jongman');
			$canEdit	= $user->authorise('core.edit', 'com_jongman');
			$canCheckin	= $user->authorise('core.manage',	'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state', 'com_jongman') && $canCheckin;            
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="">
					<td class="order nowrap center hidden-phone">
					<?php
						$iconClass = '';
						if (!$canChange)
						{
							$iconClass = ' inactive';
						}
						elseif (!$saveOrder)
						{
							$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
						}
					?>
						<span class="sortable-handler<?php echo $iconClass ?>">
							<i class="icon-menu"></i>
						</span>
						<?php if ($canChange && $saveOrder) : ?>
						<input type="text" style="display:none" name="order[]" size="5"
								value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
						<?php endif; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'schedules.', $canChange, 'cb'); ?>
							<?php echo JHtml::_('jgrid.isdefault', $item->default, $i, 'schedules.', $canChange)?> 
							<?php
							// Create dropdown items
							$action = $archived ? 'unarchive' : 'archive';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'schedules');

							$action = $trashed ? 'untrash' : 'trash';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'schedules');

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->name));
							?>
						</div>
					</td>
					<td class="nowrap has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'schedules.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=schedule.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->name); ?></a>
							<?php else : ?>
									<?php echo $this->escape($item->name); ?>
							<?php endif; ?>
							<div class="small">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							</div>
						</div>
					</td> 
				<?php 
					$slots = $item->layout->getSlots();
					$reservableSlots = array();
					$blockedSlots = array();
					foreach($slots as $slot) {
                		if ($slot->periodType == RFSchedulePeriodTypes::RESERVABLE) {
                			$reservableSlots[] = $slot->start->format('H:i').'-'.$slot->end->format('H:i');		
                		}elseif( $slot->periodType == RFSchedulePeriodTypes::NONRESERVABLE) {
                			$blockedSlots[] = $slot->start->format('H:i').'-'.$slot->end->format('H:i');	
                		} 
					}	
				?>
                <td class="center">
                	<?php // firefox has problem with long td content ?>
                	<div style="word-break:break-all; word-wrap: break-word;">
                	<?php echo implode(',', $reservableSlots)?>
                	</div>
                </td>
                <td class="center">
                	<div style="word-break:break-all; word-wrap: break-word;">
                	<?php echo implode(',', $blockedSlots)?>                  
                	</div>  
                </td>
                <td class="center">
                	<?php echo JText::plural('COM_JONGMAN_DAY_OF_WEEK',$item->weekday_start)?>
                </td>
                <td class="center">
                    <?php echo $item->view_days?>
                </td>
                <td class="center">
                    <?php echo $item->access_level?>
                </td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php  echo $this->pagination->getListFooter(); ?>
<?php endif?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
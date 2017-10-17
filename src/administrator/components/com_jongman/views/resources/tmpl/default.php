<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
$user		= JFactory::getUser();
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$saveOrder	= $listOrder == 'ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_jongman&task=resources.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'adminForm', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=resources'); ?>" method="post" name="adminForm" id="adminForm">
<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php else : ?>
	<div id="j-main-container">
<?php endif;?>
   <?php 
    //Search Toolbar
    	echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
	?>		
		<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('COM_JONGMAN_NO_MATCHING_RESULTS'); ?>
		</div>
		<?php else : ?>	
		<table class="adminlist table table-stripped">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '', 'ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'r.published', $listDirn, $listOrder); ?>
					</th>
					<th class="center">
						<?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_NAME', 'title', $listDirn, $listOrder); ?>
					</th>
                	<th class="nowrap center">
                    	<?php echo JText::_('COM_JONGMAN_HEADING_MIN_RESERVATION') ?>
                	</th>
                	<th class="nowrap center">
                    	<?php echo JText::_('COM_JONGMAN_HEADING_MAX_RESERVATION') ?>
                	</th>
                	<th class="nowrap center">
                    	<?php echo JText::_('COM_JONGMAN_HEADING_MIN_NOTICE_TIME'); ?>
                	</th>
                	<th class="nowrap center">
                    	<?php echo JText::_('COM_JONGMAN_HEADING_MAX_NOTICE_TIME'); ?>
                	</th>                
                	<th width="5%">
                    	<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_NEED_APPROVAL', 'r.requires_approval', $listDirn, $listOrder); ?>
                	</th>
                	<th width="10%">
                		<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'r.access', $listDirn, $listOrder); ?>
                	</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'r.ordering');
			$canCreate	= $user->authorise('core.create', 'com_jongman');
			$canEdit	= $user->authorise('core.edit', 'com_jongman');
			$canCheckin	= $user->authorise('core.manage',	'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state', 'com_jongman') && $canCheckin;            
			?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->schedule_id?>">
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
							<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
							<?php endif; ?>
					</td>
					<td class="center hidden-phone">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'resources.', $canChange, 'cb'); ?>
							<?php
							// Create dropdown items
							$action = $archived ? 'unarchive' : 'archive';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'resources');

							$action = $trashed ? 'untrash' : 'trash';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'resources');

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
							?>
						</div>
					</td>
					<td class="nowrap has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'contacts.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=resource.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->title); ?></a>
							<?php else : ?>
									<?php echo $this->escape($item->title); ?>
							<?php endif; ?>
							<span class="small">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							</span>
							<div class="small">
								<?php echo $item->schedule_name; ?>
							</div>
						</div>
					</td>              
                	<td class="center">
                		<?php 
                			$title = JText::_('COM_JONGMAN_TIP_RESOURCE_MIN_DURATION')."<br />".(!$item->hasMinDuration? JText::_("COM_JONGMAN_NO_MIN_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MIN_DURATION_DESC", $item->minDuration->interval()));
                    	?>
                		<span class="tip-top hasTooltip" title="<?php echo $title?>">
                    		<?php echo ($item->hasMinDuration ? $item->minDuration->interval() : "NA") ?>
                    	</span>
                	</td>
                	<td class="center">
                		<?php 
                			$title = JText::_('COM_JONGMAN_TIP_RESOURCE_MAX_DURATION')."<br />".(!$item->hasMaxDuration? JText::_("COM_JONGMAN_NO_MAX_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MAX_DURATION_DESC", $item->maxDuration->interval()));
                    	?>
                		<span class="tip-top hasTooltip" title="<?php echo $title?>">
                    		<?php echo ($item->hasMaxDuration ? $item->maxDuration->interval() : "NA") ?>
                    	</span>
                	</td>
                	<td class="center">
                		<?php
                			$title = JText::_('COM_JONGMAN_TIP_RESOURCE_MIN_NOTICE_DURATON')."<br />".(!$item->hasMinNoticeTime ? JText::_("COM_JONGMAN_NO_MIN_NOTICE_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MIN_NOTICE_DURATION_DESC", $item->minNoticeTime->interval()));
                		?>
                		<span class="tip-top hasTooltip" title="<?php echo $title?>">
                    		<?php echo ($item->hasMinNoticeTime ? $item->minNoticeTime->interval() : "NA")?>
                    	</span>
                	</td>
                	<td class="center">
                		<?php 
               				$title = JText::_('COM_JONGMAN_TIP_RESOURCE_MAX_NOTICE_DURATON')."<br />".(!$item->hasMaxNoticeTime ? JText::_("COM_JONGMAN_NO_MAX_NOTICE_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MAX_NOTICE_DURATION_DESC", $item->maxNoticeTime->interval()));
                    	?>	
                		<span class="tip-top hasTooltip" title="<?php echo $title?>">
                    		<?php echo ($item->hasMaxNoticeTime ? $item->maxNoticeTime->interval() : "NA") ?>
                    	</span>
                	</td>                
                	<td class="center">           
                    	<?php echo JHtml::_('jgrid.state', 
                        	array(
                                	//task, text, active title, inactive title, tip, active class, inactive class
                            	1=> array('resetapproval',	'JPUBLISHED',	'COM_JONGMAN_RESOURCE_RESET_APPROVAL',	'JPUBLISHED',	false,	'publish',		'publish'),
                            	0=> array('setapproval',	'JUNPUBLISHED',	'COM_JONGMAN_RESOURCE_SET_APPROVAL',	'JUNPUBLISHED',	false,	'unpublish',	'unpublish')      
                            ),
                        	$item->requires_approval, $i, 
                        	'resources.',         
                        	$canChange); ?>
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
		<?php echo $this->pagination->getListFooter(); ?>
	<?php endif ?>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>			
</form>

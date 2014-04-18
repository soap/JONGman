<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
$user		= JFactory::getUser();
JHtml::_('behavior.tooltip');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder=='ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=resources'); ?>" method="post" name="adminForm" id="adminForm">
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
	<div class="clr"> </div>
<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('COM_JONGMAN_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>	
	<table class="adminlist table table-stripped">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_NAME', 'title', $listDirn, $listOrder); ?>
				</th>
                <th width="20%">
                    <?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_SCHEDULE', 'schedule_name', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MIN_RESERVATION') ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MAX_RESERVATION') ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MIN_NOTICE_TIME'); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MAX_NOTICE_TIME'); ?>
                </th>                
                <th width="5%">
                    <?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_NEED_APPROVAL', 'need_approval', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                	<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
                </th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'published', $listDirn, $listOrder); ?>
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
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'resources.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=resource.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
                <td class="center">
                    <?php echo $item->schedule_name?>
                </td>
                <td class="center">
                	<?php 
                	$title = "Minimum Duration::".(!$item->hasMinDuration? JText::_("COM_JONGMAN_NO_MIN_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MIN_DURATION_DESC", $item->minDuration->interval()));
                    ?>
                	<span class="hasTip" title="<?php echo $title?>">
                    	<?php echo ($item->hasMinDuration ? $item->minDuration->interval() : "NA") ?>
                    </span>
                </td>
                <td class="center">
                	<?php 
                	$title = "Maximum Duration::".(!$item->hasMaxDuration? JText::_("COM_JONGMAN_NO_MAX_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MAX_DURATION_DESC", $item->maxDuration->interval()));
                    ?>
                	<span class="hasTip" title="<?php echo $title?>">
                    	<?php echo ($item->hasMaxDuration ? $item->maxDuration->interval() : "NA") ?>
                    </span>
                </td>
                <td class="center">
                	<?php
                	$title = "Start Time::".(!$item->hasMinNoticeTime ? JText::_("COM_JONGMAN_NO_MIN_NOTICE_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MIN_NOTICE_DURATION_DESC", $item->minNoticeTime->interval()));
                	?>
                	<span class="hasTip" title="<?php echo $title?>">
                    	<?php echo ($item->hasMinNoticeTime ? $item->minNoticeTime->interval() : "NA")?>
                    </span>
                </td>
                <td class="center">
                	<?php 
               		$title = "End Time::".(!$item->hasMaxNoticeTime ? JText::_("COM_JONGMAN_NO_MAX_NOTICE_DURATION_DESC") : JText::sprintf("COM_JONGMAN_ON_MAX_NOTICE_DURATION_DESC", $item->maxNoticeTime->interval()));
                    ?>	
                	<span class="hasTip" title="<?php echo $title?>">
                    	<?php echo ($item->hasMaxNoticeTime ? $item->maxNoticeTime->interval() : "NA") ?>
                    </span>
                </td>                
                <td class="center">           
                    <?php echo JHtml::_('jgrid.state', 
                        array(
                                //task, text, active title, inactive title, tip, active class, inactive class
                            1=> array('resetapproval',	'JPUBLISHED',	'COM_JONGMAN_RESOURCE_RESET_APPROVAL',	'JPUBLISHED',	false,	'publish',		'unpublish'),
                            0=> array('setapproval',	'JUNPUBLISHED',	'COM_JONGMAN_RESOURCE_SET_APPROVAL',	'JUNPUBLISHED',	false,	'unpublish',	'publish')      
                            ),
                        $item->requires_approval, $i, 
                        'resources.',         
                        $canChange); ?>
                </td>
                <td class="center">
                	<?php echo $item->access_level?>
                </td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'resources.', $canChange); ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
		<?php if ($this->is_j25) : ?>
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>		
		<?php endif; ?>
	</table>
	<?php if (!$this->is_j25) : echo $this->pagination->getListFooter(); endif; ?>
<?php endif ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
<?php if (!$this->is_j25) : ?>
	</div>
<?php endif; ?>			
</form>

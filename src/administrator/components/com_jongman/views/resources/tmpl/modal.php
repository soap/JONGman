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
$field		= JRequest::getCmd('field');
$schedule_id = JRequest::getCmd('filter_schedule_id');

$function	= 'jSelectResource_'.$field;
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder=='ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=resources&layout=modal&tmpl=component'); ?>" method="post" name="adminForm" id="adminForm">
	<div id="j-main-container">
<?php echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this)); ?>
<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('COM_JONGMAN_NO_MATCHING_RESULTS'); ?>
		</div>
<?php else : ?>	
	<table class="adminlist table table-stripped">
		<thead>
			<tr>
				<th width="30%">
					<?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_NAME', 'title', $listDirn, $listOrder); ?>
				</th>
                <th width="20%">
                    <?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_SCHEDULE', 'schedule_name', $listDirn, $listOrder); ?>
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
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>		
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'ordering');      
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>');">
						<?php echo $item->title; ?></a>
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
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>        
	</table>
<?php endif;?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="field" value="<?php echo $this->escape($field);?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
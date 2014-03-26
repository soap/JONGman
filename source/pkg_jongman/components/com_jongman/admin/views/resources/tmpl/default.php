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

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder=='ordering';
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=resources'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search">
				<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>:</label>
			<input type="text" name="filter_search" id="filter_search"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				title="<?php echo JText::_('COM_JONGMAN_RESOURCES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn">
				<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
				<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>

		</div>
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'),
					'value', 'text', $this->state->get('filter.state'), true);?>
			</select>

			<select name="filter_schedule_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_JONGMAN_OPTION_SELECT_SCHEDULE');?></option>
				<?php echo JHtml::_('select.options',JongmanHelper::getScheduleOptions(),
					'value', 'text', $this->state->get('filter.schedule_id'));?>
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
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_NAME', 'title', $listDirn, $listOrder); ?>
				</th>
                <th width="20%">
                    <?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_SCHEDULE', 'schedule_name', $listDirn, $listOrder); ?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MIN_RES').' ('
                        .JText::_('COM_JONGMAN_MINUTES').')'?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MAX_RES').' ('
                        .JText::_('COM_JONGMAN_MINUTES').')'?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MIN_NOTICE_TIME').' ('
                        .JText::_('COM_JONGMAN_HOURS').')'?>
                </th>
                <th width="5%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MAX_NOTICE_TIME').' ('
                        .JText::_('COM_JONGMAN_HOURS').')'?>
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
		<tfoot>
			<tr>
				<td colspan="11">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
        <?php if (count($this->items)==0) : ?>
            <tr>
                <td colspan="11" class="center"><?php echo JText::_("COM_JONGMAN_NO_RECORD")?></td>
            </tr>
        <?php else: ?>
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
                    <?php echo $item->params->get('min_reservation_duration')?>
                </td>
                <td class="center">
                    <?php echo $item->params->get('max_reservation_duration')?>
                </td>
                <td class="center">
                    <?php echo $item->params->get('min_notice_duration')?>
                </td>
                <td class="center">
                    <?php echo $item->params->get('max_notice_duration')?>
                </td>                
                <td class="center">           
                    <?php echo JHtml::_('jgrid.state', 
                        array(
                                //task, text, active title, inactive title, tip, active class, inactive class
                            1=> array('resetapproval',	'JPUBLISHED',	'COM_JONGMAN_RESOURCE_RESET_APPROVAL',	'JPUBLISHED',	false,	'publish',		'unpublish'),
                            0=> array('setapproval',	'JUNPUBLISHED',	'COM_JONGMAN_RESOURCE_SET_APPROVAL',	'JUNPUBLISHED',	false,	'unpublish',	'publish')      
                            ),
                        $item->params->get('need_approval'), $i, 
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
			<?php endforeach; 
            endif;
            ?>
		</tbody>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

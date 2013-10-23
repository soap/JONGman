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

			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_ACCESS');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('access.assetgroups'),
					'value', 'text', $this->state->get('filter.access'));?>
			</select>
		</div>
	</fieldset>
	<table class="adminlist">
		<thead>
			<tr>
				<th width="30%">
					<?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_NAME', 'title', $listDirn, $listOrder); ?>
				</th>
                <th width="20%">
                    <?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_SCHEDULE', 'schedule_name', $listDirn, $listOrder); ?>
                </th>
                <th width="10%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MIN_RES').'('
                        .JText::_('COM_JONGMAN_MINUTES').')'?>
                </th>
                <th width="10%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MAX_RES').'('
                        .JText::_('COM_JONGMAN_MINUTES').')'?>
                </th>
                <th width="10%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MIN_NOTICE_TIME').'('
                        .JText::_('COM_JONGMAN_HOURS').')'?>
                </th>
                <th width="10%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_MAX_NOTICE_TIME').'('
                        .JText::_('COM_JONGMAN_HOURS').')'?>
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
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<?php if (count($this->items)==0) : ?>
            <tr>
                <td colspan="10" class="center"><?php echo JText::_("COM_JONGMAN_NO_RECORD")?></td>
            </tr>
        <?php else: ?>
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
                    <?php echo $item->min_res?>
                </td>
                <td class="center">
                    <?php echo $item->max_res?>
                </td>
                <td class="center">
                    <?php echo $item->min_notice_time?>
                </td>
                <td class="center">
                    <?php echo $item->max_notice_time?>
                </td>                
                <td class="center">
                	<?php echo $item->access_level?>
                </td>
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'resources.', false); ?>
				</td>
				<td class="center">
					<?php echo $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>        
        <?php endif;?>
	</table>
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
		<input type="hidden" name="filter_schedule_id" value="<?php echo $schedule_id?>"
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
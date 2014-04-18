<?php
// No direct access
defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=quotas');?>" method="post" name="adminForm" id="adminForm">
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
	<div class="clr" />
<?php if (empty($this->items)) : ?>
	<div class="alert alert-no-items">
		<?php echo JText::_('COM_JONGMAN_NO_MATCHING_RESULTS'); ?>
	</div>
<?php else : ?>
	<table class="adminlist table table-stripped">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(this)" />
				</th>
				<th width="30%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_QUOTA_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_QUOTA_SCHEDULE_TITLE', 'schedule_title', $listDirn, $listOrder);?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_QUOTA_RESOURCE_TITLE', 'resource_title', $listDirn, $listOrder);?>
				</th>				
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_QUOTA_GROUP_TITLE', 'group', $listDirn, $listOrder);?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_QUOTA_LIMIT', 'q.quota_limit', $listDirn, $listOrder);?>
				</th>		
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_QUOTA_UNIT', 'q.unit', $listDirn, $listOrder);?>
				</th>	
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_QUOTA_PERIOD', 'q.period', $listDirn, $listOrder);?>
				</th>	
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'q.published', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'q.created_user_id', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'q.created_time', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'q.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$canCreate	= $user->authorise('core.create',		'com_jongman');
			$canEdit	= $user->authorise('core.edit',			'com_jongman.quota.'.$item->id);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_jongman.quota.'.$item->id) && $canCheckin;
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
					<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=quota.edit&id='.$item->id);?>">
						<?php echo $this->escape(JString::substr($item->title, 0, 50)); ?></a>
					<?php else : ?>
						<?php echo $this->escape(JString::substr($item->title, 0, 50)); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>				
				<td class="center">
					<?php echo $item->schedule_title?>
				</td>
				<td class="center">
					<?php echo $item->resource_title?>
				</td>
				<td class="center">
					<?php echo $item->group_title?>
				</td>
				<td class="center">
					<?php echo $item->quota_limit; ?>	
				</td>
				<td class="center">
					<?php echo $item->unit; ?>	
				</td>				
				<td class="center">
					<?php echo $item->duration; ?>	
				</td>				
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'quotas.', $canChange); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->author_name); ?>
				</td>
				<td class="center">
					<?php echo JHTML::_('date',$item->created_time, 'Y-m-d'); ?>
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
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<?php endif ?>
	</table>
	<?php if (!$this->is_j25) : echo $this->pagination->getListFooter(); endif; ?>
<?php endif?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
<?php if (!$this->is_j25) : ?>
	</div>
<?php endif; ?>		
</form>

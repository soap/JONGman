<?php
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=layouts');?>" method="post" name="adminForm" id="adminForm">
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
					<?php echo JHtml::_('grid.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('COM_JONGMAN_DEFAULT_LAYOUT')?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_TIMEZONE', 'a.timezone', $listDirn, $listOrder); ?>
				</th>		
                <th width="25%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_RESERVANLE_SLOTS')?>
                </th>
                <th width="15%">
                    <?php echo JText::_('COM_JONGMAN_HEADING_BLOCKED_SLOTS')?>
                </th>
                <th width="2%">
                	<?php echo JText::_('COM_JONGMAN_HEADING_USED_COUNT')?>
                </th>						
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JPUBLISHED', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th width="10%">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
				</th>
				<th width="5%">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<th width="1%" class="nowrap">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$item->max_ordering = 0; //??

			$canCreate	= $user->authorise('core.create',		'com_jongman');
			$canEdit	= $user->authorise('core.edit',			'com_jongman.layout.'.$item->id);
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state',	'com_jongman.layout.'.$item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center" class="hidden-phone">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'layouts.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canCreate || $canEdit) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=layout.edit&id='.$item->id);?>">
						<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php if (empty($item->note)) : ?>
							<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
						<?php else : ?>
							<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
						<?php endif; ?></p>
				</td>
				<td class="center">
					<?php echo JHtml::_('jgrid.isdefault', $item->default, $i, 'layouts.', $canChange)?> 
				</td>
				<td class="center">
					<?php echo $this->escape($item->timezone); ?>
				</td>
				<td>
				
				</td>
				<td>
				
				</td>		
				<td class="center">
					<?php echo $this->escape($item->used_count)?>
				</td>		
				<td class="center">
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'layouts.', $canChange); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center">
					<?php echo $this->escape($item->author_name); ?>
				</td>
				<td class="center">
					<?php echo JHTML::_('date',$item->created, 'Y-m-d'); ?>
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

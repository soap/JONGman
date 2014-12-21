<?php
defined('_JEXEC') or die;

$user		= JFactory::getUser();
JHtml::_('behavior.tooltip');
$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$saveOrder	= $listOrder=='ordering';
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
?>
<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=layouts'); ?>" method="post" name="adminForm" id="adminForm">
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
		<table class="table table-stripped">
			<thead>
				<tr>
					<th width="1%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
					</th>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="1%" style="min-width:55px" class="nowrap center">
						<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
					</th>
					<th width="10%">
						<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_DEFAULT_LAYOUT', 'a.default', $listDirn, $listOrder); ?>
					</th>
					<th class="center">
						<?php echo JHtml::_('grid.sort',  'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
					</th>
					<th class="center">
						<?php echo JHtml::_('grid.sort',  'COM_JONGMAN_HEADING_USED_COUNT', 'a.used_count', $listDirn, $listOrder); ?>
					</th>
                	<th width="10%" class="center">
                		<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
                	</th>
					<th width="1%" class="nowrap center">
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
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->timezone?>">
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
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'layouts.', $canChange, 'cb'); ?>
							<?php
							// Create dropdown items
							$action = $archived ? 'unarchive' : 'archive';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'layouts');

							$action = $trashed ? 'untrash' : 'trash';
							JHtml::_('actionsdropdown.' . $action, 'cb' . $i, 'layouts');

							// Render dropdown list
							echo JHtml::_('actionsdropdown.render', $this->escape($item->title));
							?>
						</div>
					</td>
					<td class="center">
						<?php echo JHtml::_('jgrid.isdefault', $item->default, $i, 'layouts.', $canChange)?> 
					</td>
					<td class="nowrap has-context">
						<div class="pull-left">
							<?php if ($item->checked_out) : ?>
								<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'layouts.', $canCheckin); ?>
							<?php endif; ?>
							<?php if ($canEdit || $canEditOwn) : ?>
								<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=layout.edit&id='.(int) $item->id); ?>">
									<?php echo $this->escape($item->title); ?></a>
							<?php else : ?>
									<?php echo $this->escape($item->title); ?>
							<?php endif; ?>
							<span class="small">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							</span>
							<div class="small">
								<?php echo $item->timezone; ?>
							</div>
						</div>
					</td>          
					<td class="center">
						<?php echo $item->used_count?>
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
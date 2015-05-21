<?php
/**
 * @version: $Id$
 * @copyright 2011 Prasit Gebsaap
 */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$dbo		= JFactory::getDbo();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$toolbar 	= JToolBar::getInstance('toolbar')->render('toolbar');
$archived	= $this->state->get('filter.published') == 2 ? true : false;
$trashed	= $this->state->get('filter.published') == -2 ? true : false;
$filter_in  = ($this->state->get('filter.isset') ? 'in ' : '');

$datetimeFormat = $this->params->get('datetimeFormat');

$print_url = JongmanHelperRoute::getReservationsRoute()
. '&tmpl=component&layout=print';
$print_opt = 'width=1024,height=600,resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,menubar=no';
?>
<div id="jongman" class="category-list<?php echo $this->pageclass_sfx;?> view-tasks PrintArea all">
	<div class="clearfix"></div>
	<div class="cat-items">
		<form action="<?php echo htmlspecialchars(JFactory::getURI()->toString()); ?>" method="post" name="adminForm" id="adminForm">
			<div class="grid">
				<div class="btn-toolbar btn-toolbar-top">
                	<?php echo $this->toolbar;?>
					<a class="btn button" id="print_btn" href="javascript:void(0);" onclick="window.open('<?php echo JRoute::_($print_url);?>', 'print', '<?php echo $print_opt; ?>')">
                	    <?php echo JText::_('COM_JONGMAN_PRINT'); ?>
                	</a>
				</div>
				<div class="clearfix"></div>
				<div class="<?php echo $filter_in;?>collapse" id="filters">
                	<div class="btn-toolbar">
                    	<div class="filter-search btn-group pull-left">
                        	<input type="text" name="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
                    	</div>
                    	<div class="filter-search-buttons btn-group pull-left">
                        	<button type="submit" class="btn" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>">
                            	<span aria-hidden="true" class="icon-search"></span>
                        	</button>
                        	<button type="button" class="btn" rel="tooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.id('filter_search').value='';this.form.submit();">
                           	<span aria-hidden="true" class="icon-remove"></span>
                        	</button>
                    	</div>

                    	<div class="clearfix"> </div>
                    	<hr />

                    	<div class="filter-author btn-group">
                        	<select id="filter_owner_id" name="filter_owner_id" class="inputbox input-medium" onchange="this.form.submit()">
                            	<option value=""><?php echo JText::_('JOPTION_SELECT_AUTHOR');?></option>
                            	<?php echo JHtml::_('select.options', $this->owners, 'value', 'text', $this->state->get('filter.owner_id'));?>
                        	</select>
                    	</div>
                    	<div class="clearfix"> </div>
					</div>
            	</div>
           	<?php if (empty($this->items)) : ?>
				<div class="alert alert-no-items">
					<?php echo JText::_('COM_JONGMAN_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php else : ?>	
				<table class="adminlist" id="box-table-a">
					<thead>
					<tr>
						<th width="1%">
							<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>"
                                onclick="Joomla.checkAll(this);" />
						</th>
						<th width="15%">
							<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_STATE', 'r.state', $listDirn, $listOrder); ?>
						</th>
						<th>
							<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_TITLE', 'reservation_title', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_RESOURCE_NAME', 'resource_name', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_RESERVED_FOR', 'reserved_for', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_START_DATETIME', 'start_date', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_END_DATETIME', 'end_date', $listDirn, $listOrder); ?>
						</th>
						<th width="10%">
							<?php echo JHtml::_('grid.sort', 'COM_JONGMAN_HEADING_RESERVATION_RESERVED_BY', 'reserved_by', $listDirn, $listOrder); ?>
						</th>
						<th width="1%" class="nowrap">
							<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'r.id', $listDirn, $listOrder); ?>
						</th>
					</tr>
					</thead>
					<tfoot>
					<tr>
						<td colspan="10">
						<?php if ($this->pagination->get('pages.total') > 1) : ?>
        	    			<div class="pagination center">
        	        			<?php echo $this->pagination->getPagesLinks(); ?>
        	    			</div>
        	    			<p class="counter center"><?php echo $this->pagination->getPagesCounter(); ?></p>
        				<?php endif; ?>
            				<div class="filters center">
            					<span class="display-limit">
            	    				<?php echo $this->pagination->getLimitBox(); ?>
            					</span>
            				</div>
						</td>
					</tr>
				</tfoot>
				<tbody>
			<?php foreach ($this->items as $i => $item) :
				$ordering	= true; //($listOrder == 'a.ordering');
				$canCreate	= $user->authorise('core.create',		'com_jongman');
				$canEdit	= $user->authorise('core.edit',			'com_jongman.reservation.'.$item->reservation_id);
				$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $user->get('id') || $item->checked_out == 0;
				$canChange	= $user->authorise('core.edit.state',	'com_jongman.reservation.'.$item->reservation_id) && $canCheckin;
				?>
					<tr class="row<?php echo $i % 2; ?>">
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->instance_id); ?>
						</td>
						<td>
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
							<a href="<?php echo JRoute::_('index.php?option=com_jongman&task=instance.view&id='.$item->instance_id);?>">
								<?php echo $this->escape($item->reservation_title); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->reservation_title); ?>
							<?php endif; ?>
							<div class="btn-group">
							<?php
								// Create dropdown items
								JHtml::_('actionsdropdown.addCustomItem', JText::_('COM_JONGMAN_VIEW'), '', 'cb'.$i, 'instance.view');
								// Render dropdown list
								echo JHtml::_('actionsdropdown.render', $this->escape($item->reservation_title));
							?>
							</div>
							<p class="smallsub">
								<?php echo JText::sprintf('COM_JONGMAN_RESERVATION_REFERENCE_NUMBER', $this->escape($item->reference_number));?>
							</p>
						</td>

						<td class="center">
							<?php echo $this->escape($item->resource_title); ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('rfhtml.label.author', $item->owner_name, $item->created) ?>
						</td>
						<td>
							<?php echo JHtml::date($item->start_date, $datetimeFormat, true)?>
						</td>
						<td>
							<?php echo JHtml::date($item->end_date, $datetimeFormat, true)?>
						</td>
						<td class="center">
							<?php echo JHtml::_('rfhtml.label.author', $item->author_name, $item->created) ?>
						</td>
						<td class="center">
							<?php echo (int) $item->reservation_id; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif;?>
		</div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" /> 
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo JHtml::_('form.token'); ?>
		</form>
	</div>
</div>
<script type="text/javascript">
	PNotify.prototype.options.styling = "bootstrap2"; //jqueryui
	var stack_bar_top = {"dir1": "down", "dir2": "right", "push": "top", "spacing1": 0, "spacing2": 0};
</script>
<div id="transition-dialog" style="display:none; cursor: default">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">			
				<button type="button" class="close" aria-hidden="true" onclick="jQuery.unblockUI();">×</button>
				<h4 class="modal-title">You are about to make a transition</h4>
			</div>
			<div class="modal-body">
				<textarea name="comment" id="transition-comment" rows="2" cols="15" placeholder="Provide your comment here.."></textarea>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-primary" id="transition-yes">Continue</button>
				<button type="button" class="btn btn-default" onclick="jQuery.unblockUI();">Cancel</button>
			</div>
		</div>
	</div>
</div>


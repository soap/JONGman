/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
	<div class="btn-toolbar btn-toolbar-top">
		<?php echo $this->workflowToolbar; ?>
	</div>
	<div id="ajax-loading-container" style="display:none">
		<img src="<?php echo JURI::root().'/media/com_workflow/workflow/images/ajax-loading.gif'?>" class="ajax-loader"/>
	</div>
	<div id="workflow-message-container"></div>
	<span class="item-title editor-title">
		<?php echo JText::_('COM_WORKFLOW_WRITE_COMMENT'); ?>
	</span>

	<div class="comment-editor-input">
		<textarea id="comment" class="input-xxlarge" name="comment"></textarea>
	</div>
	<hr />	
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-hover table-condensed">
			<caption><strong><?php echo JText::_('COM_WORKFLOW_HEADING_TRANSITION_LOGS')?></strong></caption>
        	<thead>
        		<tr>
        			<th class="center">
        				<?php echo JText::_('COM_WORKFLOW_HEADING_LOG_DATETIME');?>
        			</th>
           			<th class="center">
      					<?php echo JText::_('COM_WORKFLOW_HEADING_LOG_AUTHOR_NAME');?>
           			</th>
           			<th class="center">
           				<?php echo JText::_('COM_WORKFLOW_HEADING_LOG_TITLE');?>
           			</th>
           			<th class="center">
           				<?php echo JText::_('COM_WORKFLOW_HEADING_LOG_TRANSITION_NAME');?>
           			</th>
           			<th class="center">
           				<?php echo JText::_('COM_WORKFLOW_HEADING_LOG_FROM_STATE');?>
           			</th>
           			<th class="center">
           				<?php echo JText::_('COM_WORKFLOW_HEADING_LOG_TO_STATE');?>
           			</th>
           			<th class="center">
           				<?php echo JText::_('COM_WORKFLOW_HEADING_LOG_COMMENT');?>
           			</th>
           		</tr>
           	</thead>
           	<tbody>
           	<?php foreach ($this->logs as $log) :?>
           		<tr>
           			<td><?php echo JHtml::_('date', $log->created, 'Y-m-d H:i:s');?></td>
           			<td><?php echo $log->author_name;?></td>
           			<td><?php echo $this->escape($log->title);?></td>
           			<td><?php echo $this->escape($log->transition_name);?></td>
           			<td><?php echo $this->escape($log->from_state);?></td>
           			<td><?php echo $this->escape($log->to_state);?></td>
           			<td><?php echo $this->escape($log->comment);?></td>           			           			           			           			           			
           		</tr>
           	<?php endforeach;?>
           	</tbody>
		</table>
	</div>
	<input type="hidden" name="transition_id" value="" />	
	<!--  input type="hidden" name="return" value="<?php //echo base64_encode($this->return_page);?>" /-->		 

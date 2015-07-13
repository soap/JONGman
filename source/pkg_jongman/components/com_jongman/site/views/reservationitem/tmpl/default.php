<?php
defined('_JEXEC') or die();

// Create shortcuts to some parameters.
$item    = &$this->item;
$params	 = $item->params;
$canEdit = $item->params->get('access-edit');
$user	 = JFactory::getUser();
$uid	 = $user->get('id');

$nulldate = JFactory::getDBO()->getNullDate();

$asset_name = 'com_jongman.reservation.'.$this->item->id;
$canEdit	= ($user->authorise('core.edit', $asset_name));
$canEditOwn	= ($user->authorise('core.edit.own', $asset_name) && $this->item->created_by == $uid);
$params = new JRegistry();
$params->set('show_icons', 1);
?>
<div id="jongman" class="item-page view-task">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <div class="btn-toolbar btn-toolbar-top">
        <?php echo $this->toolbar;?> 
    </div>
	<?php echo JHtml::_('icons.print_popup', $this->item, $params); ?>
    <div class="page-header">
	    <h2><?php echo $this->escape($item->title); ?></h2>
	</div>

    <?php //echo $item->event->beforeDisplayContent;?>

	<div class="item-description">
		<dl>
	        <dt class="start-title resourceHover" resourceId="<?php echo $item->resources[0]->resource_id?>">
        		<?php echo JText::_('COM_JONGMAN_GRID_HEADING_RESOURCE');?>:
        	</dt>
        	<dd class="start-data">
        		 <?php echo $item->resources[0]->resource_title; ?>
        	</dd>
  		<?php if($item->start_date != $nulldate): ?>
        	<dt class="start-title">
        		<?php echo JText::_('COM_JONGMAN_GRID_HEADING_START_DATE');?>:
        	</dt>
        	<dd class="start-data">
        		<?php echo JHtml::_('rfhtml.label.datetime', $item->start_date); ?>
        	</dd>
        <?php endif; ?>
        <?php if($item->end_date != $nulldate): ?>
        	<dt class="due-title">
        		<?php echo JText::_('COM_JONGMAN_GRID_HEADING_END_DATE');?>:
        	</dt>
        	<dd class="due-data">
        		<?php echo JHtml::_('rfhtml.label.datetime', $item->end_date); ?>
        	</dd>
        <?php endif; ?>
    		<dt class="owner-title">
    			<?php echo JText::_('COM_JONGMAN_GRID_HEADING_CREATED_BY');?>:
    		</dt>
    		<dd class="owner-data">
    			 <?php echo JHtml::_('rfhtml.label.author', $item->author, $item->created); ?>
    		</dd>
		<?php if ($item->users) : ?>
            <dt class="assigned-title">
    			<?php echo JText::_('COM_JONGMAN_FIELDSET_ASSIGNED_USERS');?>:
    		</dt>			
    		<dd class="assigned-data">
    			 <?php //echo JHtml::_('pftasks.assignedLabel', $item->id, $item->id, $item->users); ?>
    		</dd>
			<?php endif; ?>
    	</dl>
    	<legend><?php echo JText::_('COM_JONGMAN_RESERVATION_DESCRIPTION')?></legend>
        <?php echo $item->description; ?>
        <div class="clearfix"></div>
	</div>
	<div class="row-fluid">

		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'workflow')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'workflow', JText::_('COM_JONGMAN_RESERVARION_WORKLOW', true)); ?>
				<div class="span12">
				<?php echo $this->loadTemplate('workflow')?>
				</div>
			<?php  echo JHtml::_('bootstrap.endTab'); ?>
			<?php if ($this->customFields) : ?>
			<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'customfields', JText::_('COM_JONGMAN_RESERVATION_CUSTOMFIELD_FIELDSET', true)); ?>
				<div class="span12">
				<?php echo $this->loadTemplate('customfields')?>
				</div>
			<?php  echo JHtml::_('bootstrap.endTab'); ?>
			<?php endif?>
		<?php echo JHtml::_('bootstrap.endTabSet');?>
	</div>
	<hr />

    <?php //echo $item->event->afterDisplayContent;?>

</div>
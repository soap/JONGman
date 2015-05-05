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
?>
<div id="jongman" class="item-page view-task">
	<?php if ($this->params->get('show_page_heading', 1)) : ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php endif; ?>

    <div class="btn-toolbar btn-toolbar-top">
        <?php echo $this->toolbar;?>
    </div>

    <div class="page-header">
	    <h2><?php echo $this->escape($item->title); ?></h2>
	</div>

    <?php //echo $item->event->beforeDisplayContent;?>

	<div class="item-description">
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
        <?php echo $item->description; ?>
        <div class="clearfix"></div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<?php echo $this->loadTemplate('workflow')?>
		</div>
	</div>
	<hr />

    <?php //echo $item->event->afterDisplayContent;?>

</div>
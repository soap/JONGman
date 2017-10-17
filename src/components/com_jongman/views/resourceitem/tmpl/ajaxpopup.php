<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

$resource = $this->item;
if (isset($resource->image) && !empty($resource->image)) {
	$imageUrl = JURI::root().'media/com_jongman/images/'.$resource->image;
	$imageName = $this->escape($resource->title);
}else{
	$imageUrl = JURI::root().'media/com_jongman/jongman/images/no-image.jpg';
	$imageName = 'blank';	
}

if (isset($resource->params) && is_array($resource->params)) {
	$resource->params = JArrayHelper::toObject($resource->params, 'JObject');
}
?>
<div id="resourceDetailsPopup">
    <h4><?php echo $resource->title?></h4>
    <div style="clear"></div>
<?php if ($imageUrl != '') : ?>
    <div class="resourceImage img-rounded">
        <img style="max-height:200px; max-width:200px;" src="<?php echo $imageUrl?>" alt="<?php echo $imageName?>>"/>
    </div>
<?php endif ?>
    <div class="description">
        <span class="bold"><?php echo JText::_('COM_JONGMAN_DESCRIPTION_LABEL')?></span>
	<?php if (!empty($resource->description)) : ?>
		<?php echo $this->escape($resource->description)?>
	<?php else: ?>
		<?php echo JText::_('COM_JONGMAN_RESOURCE_NO_DESC_LABEL')?>
	<?php endif?>
        <br/>
        <span class="bold"><?php echo JText::_('COM_JONGMAN_NOTE_LABEL');?></span>
	<?php if (!empty($resource->note)) : ?>
		<?php echo $this->escape($resource->note)?>
	<?php else:?>
		<?php echo JText::_('COM_JONGMAN_RESOURCE_NO_NOTE')?>
	<?php endif; ?>
        <br/>
        <span class="bold"><?php echo JText::_('COM_JONGMAN_CONTACT_LABEL')?></span>
	<?php if (!empty($resource->contact_info)) : ?>
		<?php echo $this->escape($resource->contact_info)?>
	<?php else:?>
		<?php echo JText::_('COM_JONGMAN_RESOURCE_NO_CONTACT')?>
	<?php endif; ?>		
        <br/>
        <span class="bold"><?php echo JText::_('COM_JONGMAN_LOCATION_LABEL')?></span>
	<?php if (!empty($resource->location)) : ?>
		<?php echo $this->escape($resource->location)?>
	<?php else:?>
		<?php echo JText::_('COM_JONGMAN_RESOURCE_NO_LOCATION')?>
	<?php endif; ?>		
    </div>
    <div class="attributes">
        <ul>
            <li>
				<?php echo JText::plural('COM_JONGMAN_MIN_RESERVATION_DURATION', $resource->params->get('min_reservation_duration'))?>
            </li>
            <li>
				<?php echo JText::plural('COM_JONGMAN_MAX_RESERVATION_DURATION', $resource->params->get('max_reservation_duration'))?>
            </li>
            <li>
				<?php echo JText::sprintf('COM_JONGMAN_APPROVAL_REQUIRED', $resource->params->get('need_approval')? JText::_('JYES') : JText::_('JNO'))?>
            </li>
            <li>
				<?php echo JText::plural('COM_JONGMAN_MIN_NOTICE_DURATION', $resource->params->get('min_notice_duration'))?>
            </li>
            <li>
				<?php echo JText::plural('COM_JONGMAN_MAX_NOTICE_DURATION', $resource->params->get('max_notice_duration'))?>

            </li>
            <li>
				<?php echo JText::sprintf('COM_JONGMAN_OVERLAPPED_DAY_RESERVATION', $resource->params->get('overlapped_day_reservation') ? JText::_('JYES') : JText::_('JNO'))?>

            </li>
            <li>
				<?php echo JText::plural('COM_JONGMAN_MAX_PARTICIPANTS', $resource->params->get('max_participants'))?>

            </li>
        </ul>
    </div>
    <div style="clear"></div>
</div>
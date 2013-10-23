<?php $params = $this->state->get('params');?>
<div class="jm-containner">
	<div class="jm-legend-inner">
	<table id="jm-colour-keys">
		<tr style="font-size: 10px; font-weight: bold; text-align: center; vertical-align: center;">
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('my_res_colour')?>; border: 2px #000000 solid;"><?php echo JText::_('COM_JONGMAN_MY_RESERVATIONS')?></td>
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('my_past_res_colour')?>; border: 2px #000000 solid;"><?php echo JText::_('COM_JONGMAN_MY_PAST_RESERVATIONS')?></td>
<?php if (1==0): ?>
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('participant_res_colour')?>; border: 2px #000000 solid;"><?php echo JText::_('COM_JONGMAN_MY_PARTICIPATION')?></td>
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('participant_past_res_colour')?>; border: 2px #000000 solid; color: #CCCCCC;"><?php echo JText::_('COM_JONGMAN_MY_PAST_PARTICIPATION')?></td>
<?php endif ?>
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('other_res_colour')?>; border: 2px #000000 solid;"><?php echo JText::_('COM_JONGMAN_OTHER_RESERVATIONS')?></td>
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('other_past_res_colour')?>; border: 2px #000000 solid;"><?php echo JText::_('COM_JONGMAN_OTHER_PAST_RESERVATIONS')?></td>
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('pending_colour')?>; border: 2px #000000 solid;"><?php echo JText::_('COM_JONGMAN_PENDING_APPROVAL')?></td>
			<td style="width: 75px; height: 38px; background-color: <?php echo $params->get('blackout_colour')?>; border: 2px #000000 solid; color: #CCCCCC;"><?php echo JText::_('COM_JONGMAN_BLACKED_OUT_TIME')?></td>
		</tr>
	</table>
	</div>
</div>

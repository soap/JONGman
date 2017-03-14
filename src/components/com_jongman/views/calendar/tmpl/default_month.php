<?php
$monthName = JText::_($this->months[$this->displayDate->month()-1]);
$today = RFDate::now()->toTimezone(RFApplicationHelper::getUserTimezone());
?>
<?php echo $this->loadTemplate('filter')?>

<div class="calendarHeading">
	<div style="float:left;">
		<a href="<?php echo $this->prevLink?>"><?php echo JHtml::image('com_jongman/arrow_large_left.png', 'Back', array(), true);?></a>
		<?php echo $monthName?> <?php echo $this->displayDate->year()?>
		<a href="<?php echo $this->nextLink?>"><?php echo JHtml::image('com_jongman/arrow_large_right.png', 'Forward', array(), true);?></a>
	</div>
	<div class="pull-right fc-right">
		<div class="btn-group">
			<button type="button" id="fc-month-btn" class="fc-month-button fc-button fc-state-default fc-corner-left fc-state-active"><?php echo JText::_('COM_JONGMAN_MONTH')?></button>
			<button type="button" id="fc-week-btn" class="fc-agendaWeek-button fc-button fc-state-default"><?php echo JText::_('COM_JONGMAN_WEEK')?></button>
			<button type="button" id="fc-day-btn" class="fc-agendDay-button fc-button fc-state-default fc-corner-right"><?php echo JText::_('COM_JONGMAN_DAY')?></button>
		</div>
	</div>
	<div class="clear">&nbsp;</div>
</div>
<?php $this->viewMode = 'month'?>
<?php echo $this->loadTemplate('common')?>

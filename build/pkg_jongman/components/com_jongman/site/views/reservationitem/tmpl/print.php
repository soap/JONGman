<?php
$params = new JRegistry();
$params->set('show_icons', '1');
$timezone = JongmanHelper::getUserTimezone();
$startDate = RFDate::fromDatabase($this->item->start_date);
$endDate = RFDate::fromDatabase($this->item->end_date);

if ($startDate->dateEquals($endDate)) {
	$date = JDate::getInstance($this->item->start_date, $timezone);
}else{
	
}
?>
<div class="item-page container">
	<?php if ($this->print) : ?>
	<div id="pop-print" class="btn hidden-print">
		<?php echo JHtml::_('icons.print_screen', $params); ?>
	</div>
	<div class="clearfix"></div>
	<?php endif; ?>
	<div id="print-this">
		<div class="row">
			<div class="span12">
				<table class="table table-bordered">
				<tbody>
					<tr>
						<td colspan="2" class="center"><?php echo $this->item->reservation_custom_fields['customer_address']?></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $date->format('D d/m/Y', true)?></td>
						<td class="span6 center"><strong>FB & Food (Kitchen)</strong></td>
					</tr>
					<tr>
						<td class="span6">
							<table class="table table-bordered">
							<tr>
								<td><?php echo JText::_('COM_JONGMAN_DAY')?></td>
								<td><?php echo JText::_('MON')?></td>
								<td><?php echo JText::_('TUE')?></td>
								<td><?php echo JText::_('WED')?></td>
								<td><?php echo JText::_('THU')?></td>
								<td><?php echo JText::_('FRI')?></td>
								<td><?php echo JText::_('SAT')?></td>
								<td><?php echo JText::_('SUN')?></td>
							</tr>
							<tr>
								<td><?php echo JText::_('COM_JONGMAN_DATE')?></td>
								<td><?php echo ($startDate->weekday()==1) ? $startDate->day() : '&nbsp;'?></td>
								<td><?php echo ($startDate->weekday()==2) ? $startDate->day() : '&nbsp;'?></td>
								<td><?php echo ($startDate->weekday()==3) ? $startDate->day() : '&nbsp;'?></td>
								<td><?php echo ($startDate->weekday()==4) ? $startDate->day() : '&nbsp;'?></td>
								<td><?php echo ($startDate->weekday()==5) ? $startDate->day() : '&nbsp;'?></td>
								<td><?php echo ($startDate->weekday()==6) ? $startDate->day() : '&nbsp;'?></td>
								<td><?php echo ($startDate->weekday()==7) ? $startDate->day() : '&nbsp;'?></td>
							</tr>
						`	</table>
						</td>
						<td class="span6" rowspan="10"><?php echo $this->item->reservation_custom_fields['fbfood_task']?></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->description?></td>
					</tr>
					<tr>
						<td class="span6 center"><strong>Front Hotel</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['fronthotel_task']?></td>
					</tr>
					<tr>
						<td class="span6 center"><strong>Golf Operation</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['golfoperation_task']?></td>
					</tr>	
					<tr>
						<td class="span6 center"><strong>Front Golf</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['frontgolf_task']?></td>
					</tr>
					<tr>
						<td class="span6 center"><strong>HRM</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['hrm_task']?></td>
					</tr>
					<tr>
						<td class="span6 center"><strong>Front Hotel</strong></td>
						<td class="span6 center"><strong>F&B Beverage (Bar)</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['fronthotel_task']?></td>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['fbbeverage_task']?></td>
					</tr>	
					
					<tr>
						<td class="span6 center"><strong>Artist</strong></td>
						<td class="span6 center"><strong>Banquet (F&B Service)</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['artisk_task']?></td>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['banquet_task']?></td>
					</tr>
					<tr>
						<td class="span6 center"><strong>IT</strong></td>
						<td class="span6 center"><strong>Engineering</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['it_task']?></td>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['engineering_task']?></td>
					</tr>
					<tr>
						<td class="span6 center"><strong>Finance & Accounting</strong></td>
						<td class="span6 center"><strong>Housekeeping & Decoration</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['accountandfinance_task']?></td>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['housekeeping_task']?></td>
					</tr>
				</tbody>																									
				</table>
			</div>
		</div>
		<div class="row">
			<div class="span12">
				<table>
					<tr>
						<td class="span3 center">Prepared / Person in charge</td>
						<td class="span3 center">Acknowledged</td>
						<td class="span6 center">Checked</td>
					</tr>
						<td class="center signature">_____________________</td>
						<td class="center signature">_____________________</td>
						<td class="center signature">_____________________</td>
					<tr>
						<td class="center" >Sales Executive</td>
						<td class="center">Director of Sales & Marketing</td>
						<td class="center">F&B Director</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div><!-- item-page container -->
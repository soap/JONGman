<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

if ($this->format== 'pdf') {
	jimport('tcpdf.tcpdf');
	$pdf = new TCPDF();
}

$doc = JFactory::getDocument();
$params = new JRegistry();
$params->set('show_icons', '1');
$timezone = RFApplicationHelper::getUserTimezone();
$startDate = RFDate::fromDatabase($this->item->start_date);
$endDate = RFDate::fromDatabase($this->item->end_date);

if ($startDate->dateEquals($endDate)) {
	$date = JDate::getInstance($this->item->start_date, $timezone);
}else{
	
}
$align		='';
$bordered	= '';
$height		='';
if ($this->format == 'pdf'){
	$align = ' align="center"';
	$bordered='cellspacing="0" cellpadding="1" border="1"';
	$height = ' height="30px"';
}
$css = array();
if ($this->ownerSignature) {
	if ($this->print) {
		$css[] = 'td.owner-signature { background-image: URL("./'.$this->ownerSignature.'"); }';
		$ownerParams = '';
	}else if ($this->format=='pdf'){
		$ownerParams = $pdf->serializeTCPDFtagParameters(array('./'.$this->ownerSignature));
	}
}

if ($this->approverSignature) {
	if ($this->print) {
		$css[] = 'td.approver-signature {background-image: URL("./'.$this->approverSignature.'"); }';
	}else if ($this->format=='pdf'){
		
	}
}

if ($this->ackbySignature) {
	if ($this->print) {
		$css[] = 'td.ackby-signature {background-image: URL("./'.$this->ackbySignature.'"); }';
	}else if ($this->format=='pdf'){
		
	}
}
//if ($this->print && !empty($css)) $doc->addStyleDeclaration(implode("\n", $css), 'text/css');
?>
<div class="item-page container" <?php if ($this->format="pdf") echo 'style="height:auto"'?>>
	<?php if ($this->print) : ?>
	<div id="pop-print" class="btn hidden-print">
		<?php echo JHtml::_('icons.print_screen', $params); ?>
	</div>
	<div class="clearfix"></div>
	<?php endif; ?>
	<div id="print-this">
		<div class="row">
			<div class="span12">
				<table class="table table-bordered" <?php echo $bordered?>>
				<tbody>
					<tr>
						<td colspan="2" class="center" <?php echo $align?>><?php echo JLayoutHelper::render('customer.address', $this->item->customer, JPATH_COMPONENT.'/layouts')?></td>
					</tr>
					<tr>
						<td class="span6 center" <?php echo $align?>><?php echo $date->format('D d/m/Y', true)?></td>
						<td class="span6 center" <?php echo $align?>><strong>FB &amp; Food (Kitchen)</strong></td>
					</tr>
					<tr>
						<td class="span6">
							<table class="table table-bordered" <?php echo $bordered?>>
							<tr>
								<td class="center" <?php echo $align?>><?php echo JText::_('COM_JONGMAN_DAY')?></td>
								<td class="center" <?php echo $align?>><?php echo JText::_('MON')?></td>
								<td class="center" <?php echo $align?>><?php echo JText::_('TUE')?></td>
								<td class="center" <?php echo $align?>><?php echo JText::_('WED')?></td>
								<td class="center" <?php echo $align?>><?php echo JText::_('THU')?></td>
								<td class="center" <?php echo $align?>><?php echo JText::_('FRI')?></td>
								<td class="center" <?php echo $align?>><?php echo JText::_('SAT')?></td>
								<td class="center" <?php echo $align?>><?php echo JText::_('SUN')?></td>
							</tr>
							<tr>
								<td class="center" <?php echo $align?>><?php echo JText::_('COM_JONGMAN_DATE')?></td>
								<td class="center" <?php echo $align?>><?php echo ($startDate->weekday()==1) ? $startDate->day() : '&nbsp;'?></td>
								<td class="center" <?php echo $align?>><?php echo ($startDate->weekday()==2) ? $startDate->day() : '&nbsp;'?></td>
								<td class="center" <?php echo $align?>><?php echo ($startDate->weekday()==3) ? $startDate->day() : '&nbsp;'?></td>
								<td class="center" <?php echo $align?>><?php echo ($startDate->weekday()==4) ? $startDate->day() : '&nbsp;'?></td>
								<td class="center" <?php echo $align?>><?php echo ($startDate->weekday()==5) ? $startDate->day() : '&nbsp;'?></td>
								<td class="center" <?php echo $align?>><?php echo ($startDate->weekday()==6) ? $startDate->day() : '&nbsp;'?></td>
								<td class="center" <?php echo $align?>><?php echo ($startDate->weekday()==7) ? $startDate->day() : '&nbsp;'?></td>
							</tr>
							</table>
						</td>
						<td class="span6" rowspan="10"><?php echo $this->item->reservation_custom_fields['fbfood_task']?></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->description?></td>
					</tr>
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>Front Hotel</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['fronthotel_task']?></td>
					</tr>
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>Golf Operation</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['golfoperation_task']?></td>
					</tr>	
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>Front Golf</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['frontgolf_task']?></td>
					</tr>
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>HRM</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['hrm_task']?></td>
					</tr>
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>Front Hotel</strong></td>
						<td class="span6 center" <?php echo $align?>><strong>F&amp;B Beverage (Bar)</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['fronthotel_task']?></td>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['fbbeverage_task']?></td>
					</tr>	
					
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>Artist</strong></td>
						<td class="span6 center" <?php echo $align?>><strong>Banquet (F&amp;B Service)</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['artist_task']?></td>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['banquet_task']?></td>
					</tr>
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>IT</strong></td>
						<td class="span6 center" <?php echo $align?>><strong>Engineering</strong></td>
					</tr>
					<tr>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['it_task']?></td>
						<td class="span6"><?php echo $this->item->reservation_custom_fields['engineering_task']?></td>
					</tr>
					<tr>
						<td class="span6 center" <?php echo $align?>><strong>Finance &amp; Accounting</strong></td>
						<td class="span6 center" <?php echo $align?>><strong>Housekeeping &amp; Decoration</strong></td>
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
			<div class="span12" <?php echo $height?>>
				<table>
					<tr>
						<td class="span3 col-sm-3 center" <?php echo $align?>>Prepared / Person in charge</td>
						<td class="span3 col-sm-3 center" <?php echo $align?>>Acknowledged</td>
						<td class="span6 col-sm-3 center" <?php echo $align?>>Checked</td>
					</tr>
					<tr>
						<td class="center owner-signature signature" <?php echo $align.$height?>><?php if ($this->ownerSignature) :?><img src="./<?php echo $this->ownerSignature?>" /><?php endif?></td>
						<td class="center approver-signature signature" <?php echo $align.$height?>><?php if ($this->approverSignature) :?><img src="./<?php echo $this->approverSignature?>" /><?php endif?></td>
						<td class="center ack-signature signature" <?php echo $align.$height?>><?php if ($this->ackbySignature) :?><img src="./<?php echo $this->ackbySignature?>" /><?php endif?></td>
					</tr>
					<tr>
						<td class="center" <?php echo $align?>><?php echo JFactory::getUser($this->item->owner_id)->name;?></td>
						<td class="center" <?php echo $align?>><?php if ($this->item->approver_id) : echo JFactory::getUser($this->item->approver_id)->name; endif?></td>
						<td class="center" <?php echo $align?>><?php if ($this->item->ackby_id) : echo JFactory::getUser($this->item->ackby_id)->name; endif?></td>
					</tr>
					<tr>
						<td class="center" <?php echo $align?>><strong>Sales Executive</strong></td>
						<td class="center" <?php echo $align?>><strong>Director of Sales &amp; Marketing</strong></td>
						<td class="center" <?php echo $align?>><strong>F&amp;B Director</strong></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div><!-- item-page container -->
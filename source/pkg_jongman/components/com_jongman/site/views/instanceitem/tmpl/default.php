<?php
defined('_JEXEC') or die;
?>
<div class="reservationbox">
	<div id="reservationDetails">
		<ul class="unstyle">
			<div class="users"><?php echo $this->item->owner_name?></div>
			<div class="dates"></div>
			<div class="referncenumber"><?php echo $this->item->reference_number?></div>
			<div class="title"><?php echo $this->item->title?></div>
			<div class="resources"></div>
		</ul>
	</div>
</div>
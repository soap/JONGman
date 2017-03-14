<?php
defined('_JEXEC') or die;
?>
<div class="row">
	<h4><?php echo $displayData->name?></h4>
	<span><?php echo $displayData->address?> A.<?php echo $displayData->suburb?> <?php echo $displayData->state?> <?php echo $displayData->postcode?> <?php echo $displayData->country?></span>
	<div class="clearfix"></div>
	<?php if ($displayData->telephone) : ?>
	<span>Tel: <?php echo $displayData->telephone?></span>
	<?php endif?>
</div>
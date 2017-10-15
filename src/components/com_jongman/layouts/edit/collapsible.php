<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
?>
<?php if ($displayData['bootstrap3']) : 
		//$displayData['field']->class .= ' collapse'; 
?>
	<div class="form-group form-horizontal">
		<div class="col-sm-3">
		<?php echo $displayData['field']->label?>
			<button class="btn btn-default btn-xs" style="border:none" type="button" data-toggle="collapse" data-target="#cl-<?php echo $displayData['field']->id?>" aria-expanded="false" aria-controls="cl-<?php echo $displayData['field']->id?>">
				<i class="caret"></i>
			</button>
		</div>
		<div class="col-sm-9 collapse" id="cl-<?php echo $displayData['field']->id?>">
			<?php echo $displayData['field']->input?>	
		</div>
	</div>
<?php else : ?>
	<div class="control-group">
		<?php echo $displayData['field']->label; ?>
   		<div class="controls">
			<?php echo $displayData['field']->input; ?>
        </div>
	</div>
<?php endif?>  
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
<?php if ($this->postaction == 'close') : ?>
<script type="text/javascript">
	if (window.parent) {
		window.parent.jModalClose();
	}	
</script>
<?php endif;?>

<?php if ($this->postaction == 'update') : 
$title = $this->item->name. ', '.$this->item->suburb.', '.$this->item->state.', '.$this->item->postcode.' '.$this->item->country;
?>
<script type="text/javascript">
	if (window.parent) {
		window.parent.jSelectCustomer_jform_customer_id('<?php echo $this->item->id?>', '<?php echo $this->escape(addslashes($title))?>');
		
	}	
</script>
<?php endif;?>

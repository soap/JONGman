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
<form name="adminForm" id="adminForm" action="index.php?option=com_jongman&view=postinstall" method="post">
	<div id="postinstall-status"></div>
	<input type="button" id="installbutton" class="button btn btn-small btn-primary" value="<?php echo JText::_('COM_JONGMAN_INSATLL_SAMPLEDATA');?>" />
	<input type="hidden" name="task" value="" />
</form>
<script type="text/javascript">
window.addEvent('domready',  function() {

	function install(){
		var req = new Request.JSON({
		    url: '<?php echo JRoute::_("index.php?option=com_jongman&task=postinstall.install", false)?>',
			noCache : 'true',
			onRequest: function () {
				document.id('installbutton').set('disabled', 'true');
				document.id('postinstall-status').addClass('ajax-loading');	
			},
			onSuccess : function (data, text) {
				if (data.success==true) {
					document.id('postinstall-status').setProperty('html', '<?php echo JText::_("COM_JONGMAN_SUCCESS_INSTALL_SAMPLE_DATA")?>');
					document.id('postinstall-status').addClass('alert alert-error');
				}else{
					document.id('postinstall-status').setProperty('html', data.message);
					document.id('postinstall-status').addClass('alert alert-info');
				}
				document.id('postinstall-status').removeClass('ajax-loading');	
				
			},
			 
			onError: function (text, error) {
				document.id('postinstall-status').set('html','<?php echo JText::_("COM_JONGMAN_ERROR_AJAX_SAMPLEDATA_REQUEST")?>');
				document.id('postinstall-status').removeClass('ajax-loading').addClass('alert alert-error');	
			},
			
			onFailure: function (xhr) {
				var error = "Error " + this.status;
		        switch (this.status) {
		            case 404:
		                error = "Document not found (404)";
		            break;
		            case 301:
		                error = "Object moved permanently (301 redirect)";
		            break;
		            case 302:
		                error = "Object moved temporarliy (302 redirect)";
		            break;
		        }
		        alert(error);
				document.id('postinstall-status').removeClass('ajax-loading');		
			}
			
		}).send();
							
	};

	document.id('installbutton').addEvent('click', install);	
});
</script>
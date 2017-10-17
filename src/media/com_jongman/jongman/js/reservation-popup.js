/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
jQuery.fn.attachReservationPopup = function (refNum, detailsUrl)
{
	var me = jQuery(this);
	if (detailsUrl == null)
	{
		detailsUrl = "index.php?option=com_jongman&view=instanceitem&mode=popup&tmpl=component&format=html";
	}

	me.qtip({
		position:{
			my:'bottom left',
			at:'top left',
			target:false,
			viewport: jQuery(window),
			effect:false
		},

		content:{
			text:'Loading...',
			ajax:{
				url:detailsUrl,
				type:'GET',
				data:{ id:refNum },
				dataType:'html'
			}
		},

		show:{
			delay:700,
			effect:false
		},
		
		style: {
	        classes: 'qtip-bootstrap qtip-tipped'
	    }
	});
}
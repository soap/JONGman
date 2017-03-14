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
<<<<<<< HEAD
		},
		
		style: {
	        classes: 'qtip-bootstrap qtip-tipped'
	    }
=======
		}
>>>>>>> f260c473c4627674d709964076fdcb5b4545f5fb
	});
}
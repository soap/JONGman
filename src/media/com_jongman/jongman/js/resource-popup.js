/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
jQuery.fn.bindResourceDetails = function (resourceId, options)
{
	var opts = jQuery.extend({preventClick:false}, options);

	bindResourceDetails(jQuery(this));

	function getDiv()
	{
		if (jQuery('#resourceDetailsDiv').length <= 0)
		{
			return jQuery('<div id="resourceDetailsDiv"/>').appendTo('body');
		}
		else
		{
			return jQuery('#resourceDetailsDiv');
		}
	}

	function hideDiv()
	{
		var tag = getDiv();
		var timeoutId = setTimeout(function ()
		{
			tag.hide();
		}, 500);
		tag.data('timeoutId', timeoutId);
	}

	function bindResourceDetails(resourceNameElement)
	{
		if (resourceNameElement.attr('resource-details-bound') === '1')
		{
			return;
		}

		if (opts.preventClick)
		{
			resourceNameElement.click(function (e)
			{
				e.preventDefault();
			});
		}

		var tag = getDiv();

		tag.mouseenter(function ()
		{
			clearTimeout(tag.data('timeoutId'));
		}).mouseleave(function ()
		{
			hideDiv();
		});

		resourceNameElement.mouseenter(function ()
		{
			var tag = getDiv();
			clearTimeout(tag.data('timeoutId'));

			var data = tag.data('resourcePopup' + resourceId);
			if (data != null)
			{
				showData(data);
			}
			else
			{
				jQuery.ajax({
					url:'index.php?option=com_jongman&view=resourceitem&tmpl=component&layout=ajaxpopup&id=' + resourceId,
					type:'GET',
					cache:true,
					beforeSend:function ()
					{
						tag.html('Loading...').show();
						tag.position({my:'left bottom', at:'right top', of:resourceNameElement});
					},
					error:tag.html('Error loading resource data!').show(),
					success:function (data, textStatus, jqXHR)
					{
						tag.data('resourcePopup' + resourceId, data);
						showData(data);
					}
				});
			}

			function showData(data)
			{
				tag.html(data).show();
				tag.position({my:'left bottom', at:'right top', of:resourceNameElement});
			}
		}).mouseleave(function ()
			{
				hideDiv();
			});

		resourceNameElement.attr('resource-details-bound', '1');
	}

};
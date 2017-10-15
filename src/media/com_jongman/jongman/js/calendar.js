/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
function Calendar(opts, reservations)
{
	var _options = opts;
	var _reservations = reservations;

	var dayDialog = jQuery('#dayDialog');

	Calendar.prototype.init = function()
	{
		jQuery('#calendar').fullCalendar({
			header: '',
			editable: false,
			defaultView: _options.view,
			year: _options.year,
			month: _options.month-1,
			date: _options.date,
			events: _reservations,
			eventRender: function(event, element) { element.attachReservationPopup(event.id); },
			dayClick: dayClick,
			dayNames: _options.dayNames,
			dayNamesShort: _options.dayNamesShort,
			monthNames: _options.monthNames,
			monthNamesShort: _options.monthNamesShort,
			weekMode: 'variable',
			timeFormat: _options.timeFormat,
			columnFormat:  {
				month: 'dddd',
			    week: 'dddd ' + _options.dayMonth,
			    day: 'dddd ' + _options.dayMonth
			},
			axisFormat: _options.timeFormat,
			firstDay: _options.firstDay
		});

		jQuery('.fc-widget-content').hover(
			function() {
				jQuery(this).addClass('hover');
			},
				
			function() {
				jQuery(this).removeClass('hover');
			}
		);

		jQuery(".reservation").each(function(index, value) {
			var refNum = jQuery(this).attr('refNum');
			value.attachReservationPopup(refNum);
		});

		jQuery('#calendarFilter').change(function() {
			var queryString = jQuery(this).attr('ref').split('?')[1];
            var queries = [];
            jQuery.each(queryString.split('&'),function(c,q){
                var i = q.split('=');
                queries[i[0].toString()] = i[1].toString();
            });
            console.log(queries);

			var scheduleId = '';
			var resourceId = '';
			if ((!('Itemid' in queries)) && ('view' in queries))
			{
                delete queries['view'];
            }
			if (jQuery(this).find(':selected').hasClass('schedule'))
			{
				queries['sid'] = jQuery(this).val();
			}
			else
			{
				queries['sid']= jQuery(this).find(':selected').prevAll('.schedule').val();
				queries['rid'] = jQuery(this).val();
			}

			var url = [location.protocol, '//', location.host, location.pathname].join('');

            var esc = encodeURIComponent;
            var query = Object.keys(queries).map(k => esc(k) + '=' + esc(queries[k])).join('&');

			url = url + '?' + query;
			//console.log(url);
			window.location = url;
		});

        jQuery('#turnOffSubscription').click(function(e){
            e.preventDefault();
            PerformAsyncAction(jQuery(this), function(){return opts.subscriptionDisableUrl;});
        });

        jQuery('#turnOnSubscription').click(function(e){
            e.preventDefault();
            PerformAsyncAction(jQuery(this), function(){return opts.subscriptionEnableUrl;});
        });

		dayDialog.find('a').click(function(e){
			e.preventDefault();
		});

		jQuery('#dayDialogCancel').click(function(e){
			dayDialog.dialog('close');
		});

		jQuery('#dayDialogView').click(function(e){
			drillDownClick();
		});

		jQuery('#dayDialogCreate').click(function(e){
			openNewReservation();
		});

		jQuery('#showResourceGroups').click(function(e){
			e.preventDefault();

			var resourceGroupsContainer = jQuery('#resourceGroupsContainer');

			if (resourceGroupsContainer.is(':visible'))
			{
				resourceGroupsContainer.hide();
			}
			else
			{
				if (!resourceGroupsContainer.data('positionSet'))
				{
					resourceGroupsContainer.position({my:'left top',at: 'right bottom',of:'#showResourceGroups'})
				}
				resourceGroupsContainer.data('positionSet', true);
				resourceGroupsContainer.show();
			}
		})
	};

	Calendar.prototype.bindResourceGroups = function(resourceGroups, selectedNode)
	{
		if (!resourceGroups || resourceGroups.length == 0)
		{
			jQuery('#showResourceGroups').hide();
			return;
		}
		// this is copied out of schedule.js, so this needs to be fixed

		function ChangeGroup(groupId)
		{
			RedirectToSelf('gid', /gid=\d+/i, "gid=" + groupId, RemoveResourceId);
		}

		function ChangeResource(resourceId)
		{
			RedirectToSelf('rid', /rid=\d+/i, "rid=" + resourceId, RemoveGroupId);
		}

		function RemoveResourceId(url)
		{
			if (!url)
			{
				url = window.location.href;
			}
			return url.replace(/&*rid=\d+/i, "");
		}

		function RemoveGroupId(url)
		{
			return url.replace(/&*gid=\d+/i, "");
		}

		function RedirectToSelf(queryStringParam, regexMatch, substitution, preProcess)
		{
			var url = window.location.href;
			var newUrl = window.location.href;

			if (preProcess)
			{
				newUrl = preProcess(url);
				newUrl = newUrl.replace(/&{2,}/i, "");
			}

			if (newUrl.indexOf(queryStringParam + "=") != -1)
			{
				newUrl = newUrl.replace(regexMatch, substitution);
			}
			else if (newUrl.indexOf("?") != -1)
			{
				newUrl = newUrl + "&" + substitution;
			}
			else
			{
				newUrl = newUrl + "?" + substitution;
			}

			newUrl = newUrl.replace("#", "");

			window.location = newUrl;
		}

		var groupDiv = jQuery("#resourceGroups");
		groupDiv.tree({
					data: resourceGroups,
					saveState: 'resourceCalendar',

					onCreateLi: function (node, $li)
					{
						if (node.type == 'resource')
						{
							$li.addClass('group-resource')
						}
					}
				});

				groupDiv.bind(
						'tree.select',
						function (event)
						{
							if (event.node)
							{
								var node = event.node;
								if (node.type == 'resource')
								{
									ChangeResource(node.resource_id);
								}
								else
								{
									ChangeGroup(node.id);
								}
							}
						});

		if (selectedNode)
		{
			groupDiv.tree('openNode', groupDiv.tree('getNodeById', selectedNode));
		}
	};

	var dateVar = null;

	var dayClick = function(date, allDay, jsEvent, view)
	{
		dateVar = date;

		if (!opts.reservable)
		{
			drillDownClick();
			return;
		}

		if (view.name.indexOf("Day") > 0)
		{
			handleTimeClick();
		}
		else
		{
			// showCloseButton  and showTitleBar come from dialog-options.js
			dayDialog.dialog({modal: true, height: 100, minheight: 'auto', width: 'auto', responsive: true, showCloseButton: false, showTitleBar: false});
			dayDialog.dialog("widget").position({
						       my: 'left top',
						       at: 'left bottom',
						       of: jsEvent
						    });
		}
	};

	var handleTimeClick = function()
	{
		openNewReservation();
	};

	var drillDownClick = function()
	{
		var month =  dateVar.getMonth()+1;
		var url =  _options.dayClickUrl;
		url = url + '&yy=' + dateVar.getFullYear() + '&mm=' + month + '&dd=' + dateVar.getDate();

		window.location = url;
	};

	var openNewReservation = function(){
		var end = new Date(dateVar);
		end.setMinutes(dateVar.getMinutes()+30);

		var url = _options.reservationUrl + "&sd=" + getUrlFormattedDate(dateVar) + "&ed=" + getUrlFormattedDate(end);

		window.location = url;
	};

	var getUrlFormattedDate = function(d)
	{
		var month =  d.getMonth()+1;
		return encodeURI(d.getFullYear() + "-" + month + "-" + d.getDate() + " " + d.getHours() + ":" + d.getMinutes());
	}

}
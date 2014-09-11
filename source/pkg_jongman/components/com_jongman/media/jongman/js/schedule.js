var JSchedule = function JSchedule(opts)
{
	this.options = opts;
	/** Dynimic method (public) **/
	this.setup = function()
	{
		this.initRotateSchedule();
		this.initReservations();

		var reservations = jQuery('#reservations');
		reservations.delegate('.clickres:not(.reserved)', 'hover', function ()
		{
			jQuery(this).siblings('.resourcename').toggleClass('hilite');
			var ref = jQuery(this).attr('ref');
			reservations.find('td[ref="' + ref + '"]').toggleClass('hilite');
		});

		reservations.delegate('td.clickres', 'mousedown', function ()
		{
			jQuery(this).addClass('clicked');
		});

		reservations.delegate('td.clickres', 'mouseup', function ()
		{
			jQuery(this).removeClass('clicked');
		});

		reservations.delegate('.reservable', 'click', function ()
		{
			var start = jQuery('.start', this).val();
			var end = jQuery('.end', this).val();
			var link = jQuery('.href', this).val();
			window.location = link + "&sd=" + start + "&ed=" + end;
		});

		this.initResources();
		this.initNavigation();
	};
	
	this.initResources = function ()
	{
		jQuery('.resourceNameSelector').each(function ()
		{
			jQuery(this).bindResourceDetails(jQuery(this).attr('resourceId'));
		});
	};

	this.initNavigation = function ()
	{
		jQuery("#calendar_toggle").click(function (event)
		{
			event.preventDefault();
			
			var datePicker = jQuery("#datepicker");
			datePicker.toggle();

			if (datePicker.css("display") == "none")
			{
				jQuery(this).find("img").first().attr("src", "media/com_jongman/jongman/images/calendar.png");
			}
			else
			{
				jQuery(this).find("img").first().attr("src", "media/com_jongman/jongman/images/calendar-minus.png");
			}
		});
	};

	this.initRotateSchedule = function ()
	{
		jQuery('#rotate_schedule').click(function (e)
		{
			e.preventDefault();
			createCookie(opts.cookieName, opts.cookieValue, 30);
			window.location.reload();
		});
	};

	this.initReservations = function ()
	{
		var reservations = jQuery('#reservations');

		this.makeSlotsSelectable(reservations);

		jQuery('td.reserved', reservations).each(function ()
		{
			var resid = jQuery(this).attr('resid');
			var pattern = 'td[resid="' + resid + '"]';

			jQuery(this).hover(
					function ()
					{
						jQuery(pattern, reservations).addClass('hilite');
					},
					function ()
					{
						jQuery(pattern, reservations).removeClass('hilite');
					}
			);

			jQuery(this).click(function ()
			{
				var form = document.getElementById('reservation-form');
				for(var i=0; i<form.elements.length; i++) {
					if (form.elements[i].name = 'cid[]') {
						form.elements[i].value = resid;
						break;
					}
				}
				
				Joomla.submitform('instance.edit', form); 
			});

			jQuery(this).qtip({
				position: {
					my: 'bottom left',
					at: 'top left',
					viewport: jQuery(window),
					effect: false
				},
				content: {
					text: 'Loading...',
					ajax: {
						url: options.summaryPopupUrl,
						type: 'GET',
						data: { id: resid },
						dataType: 'html'
					}
				},
				show: {
					delay: 700,
					event: 'mouseenter'
				},
				style: {
				},
				hide: {
					fixed: true
				},
				overwrite: false
			});
		});
	};

	this.makeSlotsSelectable = function (reservationsElement)
	{
		var startHref = '';
		var startDate = '';
		var endDate = '';
		var href = '';
		var select = function (element)
		{
			href = element.find('.href').val();
			if (startHref == '')
			{
				startDate = element.find('.start').val();
				startHref = href;
			}
			console.log('Selecting ' + href);
			if (href != startHref)
			{
				element.removeClass('ui-selecting');
			}
			else
			{
				endDate = element.find('.end').val();
			}
		};
		

		reservationsElement.selectable({
			filter: 'td.reservable',
			distance: 20,
			start: function (event, ui)
			{
				startHref = '';
			},
			selecting: function (event, ui)
			{
				select(jQuery(ui.selecting));
			},
			unselecting: function (event, ui)
			{
				select(jQuery(ui.unselecting));
			},
			stop: function (event, ui)
			{
				if (href != '' && startDate != '' && endDate != '')
				{
					window.location = href + "&sd=" + startDate + "&ed=" + endDate;
					console.log('Start:' + startDate + ' end:' + endDate);
				}
			}
		});
	};
	
};

function dpDateChanged(dateText, inst)
{
	ChangeDate(inst.selectedYear, inst.selectedMonth + 1, inst.selectedDay);
}

function ChangeDate(year, month, day)
{
	RedirectToSelf("sd", /sd=\d{4}-\d{1,2}-\d{1,2}/i, "sd=" + year + "-" + month + "-" + day);
}

function RedirectToSelf(queryStringParam, regexMatch, substitution)
{
	var url = window.location.href;
	var newUrl = window.location.href;

	if (url.indexOf(queryStringParam + "=") != -1)
	{
		newUrl = url.replace(regexMatch, substitution);
	}
	else if (url.indexOf("?") != -1)
	{
		newUrl = url + "&" + substitution;
	}
	else
	{
		newUrl = url + "?" + substitution;
	}
	
	newUrl = newUrl.replace("#sc-top", "");
	newUrl = newUrl.replace("sc-top", "");
	window.location = newUrl;
}


function Reservation(opts) {
	var options = opts;

	var elements = {
		beginDate: jQuery('#jform_start_date'),
		endDate: jQuery('#jform_end_date'),
        endDateTextbox: jQuery('#jform_end_date'),

		beginTime: jQuery('#jform_start_time'),
		endTime: jQuery('#jform_end_time'),
		durationDays: jQuery('#durationDays'),
		durationHours: jQuery('#durationHours')
	};
	
	var oneDay = 86400000;
	var scheduleId;
	
	Reservation.prototype.init = function() {
		scheduleId = jQuery('#jform_schedule_id').val();
		InitializeDateElements();
		DisplayDuration();
	}
	
	function InitializeDateElements() {
		var periodsCache = [];

        elements.beginDate.data['previousVal'] = elements.beginDate.val();
		elements.endDate.data['previousVal'] = elements.endDate.val();

		elements.beginDate.change(function() {
			PopulatePeriodDropDown(options.layoutUrl , elements.beginDate, elements.beginTime);
			AdjustEndDate();
			DisplayDuration();

			elements.beginDate.data['previousVal'] = elements.beginDate.val();
		});

		elements.endDate.change(function() {
			PopulatePeriodDropDown(options.layoutUrl, elements.endDate, elements.endTime);
			DisplayDuration();

			elements.endDate.data['previousVal'] = elements.endDate.val();
		});

		elements.beginTime.change(function() {
			DisplayDuration();
		});

		elements.endTime.change(function() {
			DisplayDuration();
		});

		var PopulatePeriodDropDown = function(layoutUrl, dateElement, periodElement)
			{
				var prevDate = new Date(dateElement.data['previousVal']);
				var currDate = new Date(dateElement.val());
				if (prevDate.getTime() == currDate.getTime())
				{
					return;
				}

				var selectedPeriod = periodElement.val();

				var weekday = currDate.getDay();

				if (periodsCache[weekday] != null)
				{
					periodElement.empty();
					periodElement.html(periodsCache[weekday])
					periodElement.val(selectedPeriod);
					return;
				}
				jQuery.ajax({
					url: layoutUrl,
					dataType:'json',
					data:{'id':scheduleId, 'date':dateElement.val()},
					success: function (data)
					{
						var items = [];
						periodElement.empty();
						jQuery.map(data.periods, function (item)
						{
							items.push('<option value="' + item.begin + '">'+ item.label + '</option>')
						});
						var html = items.join('');
						periodsCache[weekday] = html;
						periodElement.html(html);
						periodElement.val(selectedPeriod);
					},
					async: false
				});
			};
	}
	
	var DisplayDuration = function() {
		var rounded = dateHelper.GetDateDifference(elements.beginDate, elements.beginTime, elements.endDate, elements.endTime);
		var daySuffix = '_1';
		if (parseFloat(rounded.RoundedDays) > 1) {
			daySuffix = '_MORE';
		}
		var hourSuffix = '_1';
		if (parseFloat(rounded.RoundedHours) > 1) {
			hourSuffix = '_MORE';
		}
		elements.durationDays.text(rounded.RoundedDays + ' ' + Joomla.JText._('COM_JONGMAN_DAYS'+daySuffix));
		elements.durationHours.text(rounded.RoundedHours + ' ' + Joomla.JText._('COM_JONGMAN_HOURS'+hourSuffix));
	};
}
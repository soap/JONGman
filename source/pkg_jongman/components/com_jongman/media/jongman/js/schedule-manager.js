function ScheduleManagement()
{
	var elements = {
		reservableEdit:jQuery('#reservableEdit'),
		blockedEdit:jQuery('#blockedEdit'),
		
		layoutDialog:jQuery('#timeslot-form'),
		quickLayoutInterval:jQuery('#quickLayoutInterval'),
		quickLayoutStart:jQuery('#quickLayoutStart'),
		quickLayoutEnd:jQuery('#quickLayoutEnd'),
		createQuickLayout:jQuery('#createQuickLayout'),

		daysVisible:jQuery('#daysVisible'),
		dayOfWeek:jQuery('#dayOfWeek'),
		usesSingleLayout:jQuery('#usesSingleLayout')
	};

	ScheduleManagement.prototype.init = function ()
	{
		elements.quickLayoutInterval.change(function ()
		{
			createQuickLayout();
		});

		elements.quickLayoutStart.change(function ()
		{
			createQuickLayout();
		});

		elements.quickLayoutEnd.change(function ()
		{
			createQuickLayout();
		});

		elements.createQuickLayout.click(function (e)
		{
			e.preventDefault();
			createQuickLayout();
		});

		elements.usesSingleLayout.change(function ()
		{
			toggleLayoutChange(jQuery(this).is(':checked'));
		});
	};

	var createQuickLayout = function ()
	{
		var intervalMinutes = elements.quickLayoutInterval.val();
		var startTime = elements.quickLayoutStart.val();
		var endTime = elements.quickLayoutEnd.val();
		
		if (intervalMinutes != '' && startTime != '' && endTime != '')
		{
			var layout = '';
			var blocked = '';

			if (startTime != '00:00')
			{
				blocked += '00:00 - ' + startTime + "\n";
			}

			if (endTime != '00:00')
			{
				blocked += endTime + ' - 00:00';
			}

			var startTimes = startTime.split(":");
			var endTimes = endTime.split(":");

			var currentTime = new Date();
			currentTime.setHours(startTimes[0]);
			currentTime.setMinutes(startTimes[1]);

			var endDateTime = new Date();
			endDateTime.setHours(endTimes[0]);
			endDateTime.setMinutes(endTimes[1]);

			var nextTime = new Date(currentTime);

			var intervalMilliseconds = 60 * 1000 * intervalMinutes;
			while (currentTime.getTime() < endDateTime.getTime())
			{
				nextTime.setTime(nextTime.getTime() + intervalMilliseconds);

				layout += getFormattedTime(currentTime) + ' - ';
				layout += getFormattedTime(nextTime) + '\n';

				currentTime.setTime(currentTime.getTime() + intervalMilliseconds);
			}

			jQuery('.reservableEdit:visible', elements.layoutDialog).val(layout);
			jQuery('.blockedEdit:visible', elements.layoutDialog).val(blocked);
		}
	};

	var getFormattedTime = function (date)
	{
		var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
		var minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
		return hour + ":" + minute;
	};
}
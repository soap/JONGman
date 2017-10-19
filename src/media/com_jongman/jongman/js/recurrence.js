function Recurrence(recurOptions, recurElements) {
	var e = {
		repeatOptions:jQuery('#jform_repeatOptions'),
		repeatDiv:jQuery('#repeatDiv'),
		repeatInterval:jQuery('#jform_repeat_interval'),
		repeatIntervalUnit:jQuery('#jform_repeat_interval_unit'),
		repeatTermination: jQuery('#jform_repeat_terminated'),
		repeatTerminationTextbox:jQuery('#jform_repeat_terminated'),
		beginDate: jQuery('#jform_start_date'),
		endDate: jQuery('#jform_end_date'),
		beginTime: jQuery('#jform_start_time'),
		endTime: jQuery('#jform_end_time')
	};
	
	var options = recurOptions;

	var elements = jQuery.extend(e, recurElements);

	var repeatToggled = false;
	var terminationDateSetManually = false;

	this.init = function () {
		InitializeDateElements();
		InitializeRepeatElements();
		InitializeRepeatOptions();
	};	
	var ChangeRepeatOptions = function () {
		var repeatDropDown = elements.repeatOptions;
		if (repeatDropDown.val() != 'none') {
			jQuery('#repeatUntilDiv').show();
		}
		else {
			jQuery('div.control-group', elements.repeatDiv).hide();
			
		}
		
		if (repeatDropDown.val() == 'daily') {
			jQuery('.weeks', elements.repeatDiv).hide();
			jQuery('.months', elements.repeatDiv).hide();
			jQuery('.years', elements.repeatDiv).hide();

			jQuery('.days', elements.repeatDiv).show();
		}

		if (repeatDropDown.val() == 'weekly') {
			jQuery('.days', elements.repeatDiv).hide();
			jQuery('.months', elements.repeatDiv).hide();
			jQuery('.years', elements.repeatDiv).hide();

			jQuery('.weeks', elements.repeatDiv).show();
		}

		if (repeatDropDown.val() == 'monthly') {
			jQuery('.days', elements.repeatDiv).hide();
			jQuery('.weeks', elements.repeatDiv).hide();
			jQuery('.years', elements.repeatDiv).hide();

			jQuery('.months', elements.repeatDiv).show();
		}

		if (repeatDropDown.val() == 'yearly') {
			jQuery('.days', elements.repeatDiv).hide();
			jQuery('.weeks', elements.repeatDiv).hide();
			jQuery('.months', elements.repeatDiv).hide();

			jQuery('.years', elements.repeatDiv).show();
		}
	};

	function InitializeDateElements() {
		elements.beginDate.change(function () {
			ToggleRepeatOptions();
		});

		elements.endDate.change(function () {
			ToggleRepeatOptions();
		});

		elements.beginTime.change(function () {
			ToggleRepeatOptions();
		});

		elements.endTime.change(function () {
			ToggleRepeatOptions();
		});
	}

	function InitializeRepeatElements() {
		elements.repeatOptions.change(function () {
			ChangeRepeatOptions();
			AdjustRepeatIntervalUnit();
			AdjustTerminationDate();
		});

		elements.repeatInterval.change(function () {
			AdjustTerminationDate();
			AdjustRepeatIntervalUnit();
		});

		elements.beginDate.change(function () {
			AdjustTerminationDate();
		});

		elements.repeatTermination.change(function () {
			terminationDateSetManually = true;
		});
	}

	function InitializeRepeatOptions() {
		if (options.repeatType) {
			elements.repeatOptions.val(options.repeatType);
			elements.repeatInterval.val(options.repeatInterval);
			for (var i = 0; i < options.repeatWeekdays.length; i++) {
				var id = "#repeatDay" + options.repeatWeekdays[i];
				jQuery(id).attr('checked', true);
			}

			jQuery("#repeatOnMonthlyDiv :radio[value='" + options.repeatMonthlyType + "']").attr('checked', true);

			ChangeRepeatOptions();
		}
	}

	var ToggleRepeatOptions = function () {
		var SetValue = function (value, disabled) {
			elements.repeatOptions.val(value);
			elements.repeatOptions.trigger('change');
			if (disabled) {
				jQuery('select, input', elements.repeatDiv).attr("disabled", 'disabled');
			}
			else {
				jQuery('select, input', elements.repeatDiv).removeAttr("disabled");
			}
		};

		if (dateHelper.MoreThanOneDayBetweenBeginAndEnd(elements.beginDate, elements.beginTime, elements.endDate, elements.endTime)) {
			elements.repeatOptions.data["current"] = elements.repeatOptions.val();
			repeatToggled = true;
			SetValue('none', true);
		}
		else {
			if (repeatToggled) {
				SetValue(elements.repeatOptions.data["current"], false);
				repeatToggled = false;
			}
		}
	};

	var AdjustRepeatIntervalUnit = function () {
		var repeatOption = elements.repeatOptions.val();
		var repeatIntervalUnit = elements.repeatIntervalUnit;
		var repeatInterval = elements.repeatInterval.val();
		var suffix = '_1';
		if (repeatInterval != '1') {
			suffix = '_MORE';
		}
		if (repeatOption == 'daily') {
			repeatIntervalUnit.text(Joomla.JText._('COM_JONGMAN_DAYS'+suffix, 'day(s)'));
		}
		else if (repeatOption == 'weekly') {
			repeatIntervalUnit.text(Joomla.JText._('COM_JONGMAN_WEEKS'+suffix, 'week(s)'));
		}
		else if (repeatOption == 'monthly') {
			repeatIntervalUnit.text(Joomla.JText._('COM_JONGMAN_MONTHS'+suffix, 'month(s)'));
		}
		else if (repeatOption = 'yearly') {
			repeatIntervalUnit.text(Joomla.JText._('COM_JONGMAN_YEARS'+suffix, 'year(s)'));
		}
		else {
			repeatIntervalUnit.text('');
		}
		
		if (repeatInterval == null) elements.repeatInterval.val('1'); 
	}
	
	var AdjustTerminationDate = function () {
		if (terminationDateSetManually) {
			return;
		}

		var newEndDate = new Date(elements.beginDate.val());
		var interval = parseInt(elements.repeatInterval.val());
		var currentEnd = new Date(elements.repeatTermination.val());

		var repeatOption = elements.repeatOptions.val();

		if (repeatOption == 'daily') {
			newEndDate.setDate(newEndDate.getDate() + interval);
		}
		else if (repeatOption == 'weekly') {
			newEndDate.setDate(newEndDate.getDate() + (7 * interval));
		}
		else if (repeatOption == 'monthly') {
			newEndDate.setMonth(newEndDate.getMonth() + interval);
		}
		else if (repeatOption = 'yearly') {
			newEndDate.setFullYear(newEndDate.getFullYear() + interval);
		}
		else {
			newEndDate = currentEnd;
		}
		
		var d = newEndDate.getDate();
		var m = newEndDate.getMonth() + 1;
		var y = newEndDate.getFullYear();
		var dateString = y + '-' + (m<=9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
		elements.repeatTerminationTextbox.val(dateString);
	};	
}
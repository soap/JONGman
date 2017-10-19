/**

* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

var dateHelper = {
	oneDay: 86400000, //24*60*60*1000 => hours*minutes*seconds*milliseconds

	MoreThanOneDayBetweenBeginAndEnd:function (beginDateElement, beginTimeElement, endDateElement, endTimeElement) {

		var begin = this.GetDate(beginDateElement, beginTimeElement);
		var end = this.GetDate(endDateElement, endTimeElement);

		var timeBetweenDates = end.getTime() - begin.getTime();

		return timeBetweenDates > this.oneDay;
	},

	GetDate:function (dateElement, timeElement) {
		return new Date(dateElement.val() + 'T' + timeElement.val());
	},

	GetDateDifference:function (beginDateElement, beginTimeElement, endDateElement, endTimeElement) {
		var begin = this.GetDate(beginDateElement, beginTimeElement);
		var end = this.GetDate(endDateElement, endTimeElement);

		var difference = end - begin;
		var days = difference / this.oneDay;
		var hours = (days % 1) * 24;

		var roundedHours = (hours % 1) ? hours.toPrecision(2) : hours;
		var roundedDays = Math.floor(days);

		return {RoundedHours: roundedHours, RoundedDays: roundedDays};
	}

};
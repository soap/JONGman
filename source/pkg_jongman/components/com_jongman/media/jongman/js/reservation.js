var JMrepeatoptions =
{
	/**
	 * Function that dynamically reloads the specified form fields
	 * @param    string    fs    The form fields to reload (comma separated)
	 */
		
	toggle: function(src, fs) {
        // Get elements from string
        var els = fs.split(',');
        var option = jQuery('#'+src.id);
        switch(option.val()) {
	        case 'none' :
	        	jQuery('.repeatoption').hide();
	        	break;
	        case 'daily' :
	        		jQuery('.repeatoption').show();
	        		jQuery('.not-daily').hide();
	        		jQuery('#jform_repeat_interval_unit').text('Day(s)');
	        	break;
	        case 'weekly' :
        			jQuery('.repeatoption').show();
        			jQuery('.not-weekly').hide();
        			jQuery('#jform_repeat_interval_unit').text('Week(s)');
	        	break;
	        case 'monthly' :
	        		jQuery('.repeatoption').show();
	        		jQuery('.not-monthly').hide();
	        		jQuery('#jform_repeat_interval_unit').text('Month(s)');
	        	break;
	        case 'yearly':
	        		jQuery('.repeatoption').show();
	        		jQuery('.not-yearly').hide();
	        		jQuery('#jform_repeat_interval_unit').text('Year(s)');
	        	break;
        }
	},

}

function isIE() {
	return document.all;
}

// BUGFIX by Eric Maclot
function isIE7() {
    return (document.all && (typeof document.body.style.maxHeight != "undefined"));
} 

function changeInterval(opt) {
    elTerminateDate = document.id("jform_repeat_until");
    elRepeatType = document.id("jform_repeat_type");
    elIntervalUnit = document.id("jform_repeat_interval_unit");
    elRepeatDays = document.id('repeat_days_div');
    if (opt.options[0].selected == true) {
    	until_el.hide();
    	frequency_el.hide();
    	repeat_days_el.hide();
    }else{
    	until_el.show();
    	frequency_el.show();
    	switch (opt.selectedIndex) {
    		case 1 : frequency_unit_el.setProperty('value',' days');
    				repeat_days_el.hide();
    			break;
    		case 2 : frequency_unit_el.setProperty('value',' weeks');
    				repeat_days_el.show();
    			break;
    		case 3 : frequency_unit_el.setProperty('value',' months');
    				repeat_days_el.hide();
    			break;
    		case 4 : frequency_unit_el.setProperty('value',' years');
    				repeat_days_el.hide();
    			break;
    	} 
    	
    }
}
/* Show or hide recursive date selection
    opt 0->none, 1=day, 2=week, 3=month_date, 4=month_day
*/
function toggleDays(opt) {
    e = document.id("repeat_until_div");
    if (opt.options[0].selected == true) {
   		e.style.visibility = "hidden";
		e.style.display = "none";
    }else{
  		e.style.visibility = "visible";
		e.style.display = isIE() ? "inline" : "table";   
    }
    
	e = document.getElementById("days");
	if (opt.options[2].selected == true || opt.options[4].selected == true) {
		e.style.visibility = "visible";
		e.style.display = isIE() ? "inline" : "table";
	}
	else {
		e.style.visibility = "hidden";
		e.style.display = "none";
	}
	
	e = document.getElementById("week_num")
	if (opt.options[4].selected == true) {
		e.style.visibility = "visible";
		e.style.display = isIE() ? "inline" : "table";
	}
	else {
		e.style.visibility = "hidden";
		e.style.display = "none";
	}
}
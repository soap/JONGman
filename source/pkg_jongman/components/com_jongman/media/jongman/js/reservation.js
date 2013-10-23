/**
 * 
 */


function isIE() {
	return document.all;
}

// BUGFIX by Eric Maclot
function isIE7() {
    return (document.all && (typeof document.body.style.maxHeight != "undefined"));
}
 

function changeInterval(opt) {
    until_el = document.id("repeat_until_div");
    frequency_el = document.id("repeat_frequency_div");
    frequency_unit_el = document.id("jform_frequency_unit");
    repeat_days_el = document.id('repeat_days_div');
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
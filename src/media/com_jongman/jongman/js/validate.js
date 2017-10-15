/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
window.addEvent('domready', function(){
    document.formvalidator.setHandler('endtimeverify', function (value) {
        return (parseInt(document.id('jform_start_time').value) < parseInt(value)); 
    });
});
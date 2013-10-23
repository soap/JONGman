window.addEvent('domready', function(){
    document.formvalidator.setHandler('endtimeverify', function (value) {
        return (parseInt(document.id('jform_start_time').value) < parseInt(value)); 
    });
});
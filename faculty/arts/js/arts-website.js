/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function() {

    jQuery("#arts-main-colour").wpColorPicker({
        palettes: ['#5F62A9', '#98D5CA', '#D6DF30', '#FFDD00', '#E11553', '#EE523C']
    });

    jQuery("#arts-gradient-colour").wpColorPicker({
        palettes: ['#5F62A9', '#98D5CA', '#D6DF30', '#FFDD00', '#E11553', '#EE523C']
    });

    jQuery("#arts-hover-colour").wpColorPicker({
        palettes: ['#5F62A9', '#98D5CA', '#D6DF30', '#FFDD00', '#E11553', '#EE523C']
    });

    jQuery('#arts-gradient-colour').after('<a id="lighter-colour" href="#">20% lighter than (A)</a>');

    function hexToRgb(hex) {
        var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
        } : null;
    }

    jQuery('#lighter-colour').click(function(){
            var a = jQuery('#arts-main-colour').val();

            var r = hexToRgb(a).r;
            var g = hexToRgb(a).g;
            var b = hexToRgb(a).b;

            jQuery('#arts-gradient-colour').wpColorPicker( 'color', Lighthen(r, g, b));
        });

    function Lighthen(red, green, blue){

        multiplier = 120/100; // (100 + % lighter)/100

        var r = Math.round(red * multiplier);
        var g = Math.round(green * multiplier);
        var b = Math.round(blue * multiplier);

        if (r > 255) r = 255;
        if (g > 255) g = 255;
        if (b > 255) b = 255;


        return rgbToHex(r, g, b);
    }

    function componentToHex(c) {
        var hex = c.toString(16);
        return hex.length == 1 ? "0" + hex : hex;
    }

    function rgbToHex(r, g, b) {
        var hex =  "#" + componentToHex(r) + componentToHex(g) + componentToHex(b);
        return hex;
    }

    if(jQuery('#ubc-collab-theme-options-arts-enable-why-unit').attr('checked')){
        jQuery(".arts-why-inputs").show();
    } else{
        jQuery(".arts-why-inputs").hide();
    }
    if(jQuery('#ubc-collab-theme-options-arts-enable-apply-now').attr('checked')){
        jQuery(".arts-apply-inputs").show();
    } else{
        jQuery(".arts-apply-inputs").hide();
    }
    jQuery('#ubc-collab-theme-options-arts-enable-why-unit').change( function(){

		if( jQuery(this).prop("checked") ){
			jQuery(".arts-why-inputs").slideDown();
		} else {
			jQuery(".arts-why-inputs").slideUp();
		}
	});

        jQuery('#ubc-collab-theme-options-arts-enable-apply-now').change( function(){
		console.log('checked');
		if( jQuery(this).prop("checked") ){
			jQuery(".arts-apply-inputs").slideDown();
		} else {
			jQuery(".arts-apply-inputs").slideUp();
		}
	});
});
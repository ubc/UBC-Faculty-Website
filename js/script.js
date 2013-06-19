/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function() {

    jQuery('.faculty-select-input').click(function(){
        jQuery(document).ajaxStop(function(){
            window.location.reload();
        });
    });
});
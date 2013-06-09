// jQuery Featured Rrotator

jQuery(document).ready(function(){
		jQuery('#featured-rotator').tabs( { show: { effect: "slide", direction: "left", duration: 500 }});
	
    // PAUSE ON HOVER  
    jQuery('#featured-rotator').hover(  
        function() {  
            jQuery('#featured-rotator').tabs('rotate',0,true);  
        },  
        function() {  
            jQuery('#featured-rotator').tabs('rotate',5000,true);  
        }  
    );
	
	// TEXT SHADOW
	jQuery('.title').css('text-shadow','0 -1px 0 rgba(0, 0, 0, 0.3)');
	
		// TEXT COLOR
		jQuery('.title').css('color','#0055ff');
	
			// TEXT COLOR
			jQuery('.excerpt').css('color','#ffffff');
	
				// BACKGROUND COLOR
				jQuery('.title').css('background','0 0 rgba(0, 0, 0, 0.6)');
	
// FADE
    // SETS OPACITY TO FADE DOWN TO 0% WHEN PAGE LOADS
    jQuery('.title').css('opacity','0.0');

    // ON MOUSE OVER
    jQuery('.title').hover(function () {

    // SET OPACITY TO 90% ON HOVER
    jQuery(this).stop().animate({
        opacity: 0.9
        }, 'slow');
   },

    // ON MOUSE OUT
   function () {

   // SET OPACITY BACK TO 0% ON MOUSE OUT
   jQuery(this).stop().animate({
       opacity: 0.0
       }, 'slow');
   });
	
});
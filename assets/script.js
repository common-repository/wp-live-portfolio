jQuery(document).ready(function() {
jQuery('#menu-portfolio-menu a').on('click', function(e){
var url= document.location.href;	
window.history.pushState({}, "", url.split("?")[0]);	
e.preventDefault();
		
   jQuery(this).addClass('active').parent().siblings().find('a').removeClass('active');
   localStorage.setItem('id',jQuery(this).data("id"));
   selectedClass =jQuery(this).attr("data-rel"); 
   jQuery(".website-portfolio").fadeTo(100, 0.4);
   jQuery(".website-portfolio div").not("."+selectedClass).fadeOut().removeClass('scale-anm');
  			 setTimeout(function() 
			  {
				  jQuery("."+selectedClass).fadeIn().addClass('scale-anm');
				  jQuery(".website-portfolio").fadeTo(100, 0.4);
				}, 300); 
	jQuery.ajax({
		type: "POST",                 
		url: wp_portfolio_ajax_url,      
		data: {
			action     : 'wp_portfolio', 
			wp_portfolio : jQuery(this).text(),  		
		},
		success:function( data ) {
			jQuery( '#wp-portfolio-container' ).html( data );
		},
		error: function(){
			console.log(errorThrown); 
		}
	});
});
		
var id = localStorage.getItem('id');
id || jQuery('a[data-id="' + id + '"]').addClass('active').parent().siblings().find('a').removeClass('active');
var portfolio=jQuery('ul#menu-portfolio-menu li a.active').text();
jQuery.ajax({
		type: "POST",               
		url: wp_portfolio_ajax_url,     
		data: {
			action     : 'wp_portfolio', 
			wp_portfolio : portfolio,  		
		},
		success:function( data ) {
			jQuery( '#wp-portfolio-container' ).html( data );
		},
		error: function(){
			console.log(errorThrown); 
		}
	});
    
    jQuery(window).load(function() {
    jQuery('.preloader').fadeOut('slow');
    });
	
	
});

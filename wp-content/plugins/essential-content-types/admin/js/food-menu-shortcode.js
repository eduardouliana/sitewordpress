jQuery(function($){
	//$('.ect-post').parent().siblings('.entry-header').remove();
	
		/*Our Menu Script for mobile */
	$('.menu-content-wrapper .menu-nav-collapse .ui-tabs-anchor').on('click', function(event){

		 event.preventDefault();

		 if( ! $(this).parent().hasClass('ui-state-active') ){

		  $('.menu-tabs-panel').removeClass('active-tab').fadeOut(0);
		  $('.menu-nav-collapse').removeClass('ui-state-active');
		  $(this).parent().addClass('ui-state-active');

		  var  getID = $(this).attr('href');

		  $(getID).addClass('active-tab').fadeOut(0).fadeIn(500);

		  var anchoID = $(this).offset().top;

		  $("html, body").animate({ scrollTop: anchoID - 15}, "slow");
		}

	});


	/*Our Menu Script for pc */
	$('.menu-content-wrapper .menu-tabs-tab .ui-tabs-anchor').on('click', function(event){
		event.preventDefault();

		if( !$(this).parent().hasClass('ui-state-active') ){
			$('.menu-tabs-tab').removeClass('ui-state-active');
			$('.menu-tabs-panel').removeClass('active-tab').fadeOut(0);

			$(this).parent().addClass('ui-state-active');;


			var anchorAttr = $(this).attr('href');

			$(anchorAttr).addClass('active-tab').fadeOut(0).fadeIn(500);
		}

	});
});
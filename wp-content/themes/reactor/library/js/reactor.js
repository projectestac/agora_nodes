/* Reactor - Anthony Wilhelm - http://awtheme.com/ */
( function($) {
	$(document).ready( function() {
		/* adds .button class to submit button on comment form */
		$('#commentform input#submit').addClass('button').addClass('small');

		/* adjust site for fixed top-bar with wp admin bar */
		if($('body').hasClass('admin-bar')) {
			if($('.top-bar').parent().hasClass('fixed')) {
				if($('body').hasClass('has-top-bar')) {
					$('.top-bar').parent().css('margin-top', "+=28");
				}
				$('body').css('padding-top', "+=28");
			}
		}

	/* prevent default if menu links are # */
	$('nav a').each(function() {
		var nav = $(this);
		if(nav.attr('href') == '#') {
			$(this).on('click', function(e){
				e.preventDefault();
			});
		}
	});

	/**
	* // XTEC ************ AFEGIT
	* SideMenuWalker: Search all div class="section" when document is ready,
    * and check if the section have not childrens (div="content"). If not found any children,
    * class "noChildren" is added for no load the up arrow image
	* 2015.11.13 @nacho
    */
	var SideMenuWalker = {
		init: function() {
			$('div.section').each(function(){
				var self = jQuery(this);
				if (!self.find('div.content').length) {
					self.addClass("noChildren");
				}
			});
		}
    };
	SideMenuWalker.init();

	/**
     * Control the click event on dropDown class to add or remove a parent class
    */
	$('.dropDown').on('click', function(e) {
		e.preventDefault();
		var section = $(this).parent().closest('div');
		if (section.hasClass('section')){
			section.removeClass("section").addClass("customSection active");
		}else {
			section.removeClass("customSection active").addClass("section");
		}
	});

	/**
     * Control the click event on a section for load the content through the correct link
     */
	$('.section a').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		window.location.href = $(this).attr('href');
	});
	//************ FI

	/* MixItUp - http://mixitup.io/ */
    if($().mixitup) {
        $(function(){
            $('#Grid').mixitup();
        });
    }
	
  }); /* end $(document).ready */

	/* Off Canvas - http://www.zurb.com/playground/off-canvas-layouts */
	var events = 'click.fndtn';
	var $selector = $('#mobileMenuButton');
	if ($selector.length > 0) {
		$('#mobileMenuButton').on(events, function(e) {
			e.preventDefault();
			$('body').toggleClass('active');
		});
	}
	
	/* Initialize Foundation Scripts */
	$(document).foundation();
})( jQuery );
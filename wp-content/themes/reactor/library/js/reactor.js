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
     * 2015.11.13 @nacho
     * SideMenuWalker: Search all div class="section" when document is ready,
     * and check if the section have not childrens (div="content"). If not found any children,
     * class "noChildren" is added for no load the up arrow image
    */
	var SideMenuWalker = {
		init: function() {
			var sections = $('div.section');
			var content = sections.find('div.content');
			sections.each(function(index, value){
				var self = jQuery(this);
				var content = self.find('div.content');
				if (!content.length) {
					self.addClass("noChildren");
				}
			});
		}
    }
	SideMenuWalker.init();

	/**
     * 2015.11.13 @nacho
     * Control the click event on dropDown class to add or remove a parent class
    */
	$('.dropDown').on('click', function(e) {
		e.preventDefault();
		var section = $(this).parent().closest('div');

		if (section.hasClass('section')){
			section.removeClass("section");
			section.addClass("customSection active");
		}else {
			section.removeClass("customSection active");
			section.addClass("section");
		}
	});

	/**
     * 2015.11.13 @nacho
     * Control the click event on a section for load the content through the correct link
     */
	$('.section a').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		var href = $(this);
		window.location.href = href.attr('href');
	});

	/* MixItUp - http://mixitup.io/ */
    if($().mixitup) {
        $(function(){
            $('#Grid').mixitup();
        });
    }
	
  }); /* end $(document).ready */

	/* Off Canvas - http://www.zurb.com/playground/off-canvas-layouts */
	events = 'click.fndtn';
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

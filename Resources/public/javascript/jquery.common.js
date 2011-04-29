$(document).ready(function() {

	$('#filter_fields').hide();
	$('#filter_fields').slideDown(400);

	$(".panel_content").parent("#contacts, #secondarycontact, #gettingthere, #floorplans, #description, #suppliers, #notes, #healthandsafety, #licences, #facilities, #hiringandavailability").children(".panel_content").hide();

	//LIST: TR CLICKABLE
	$('td').not('.td_checkbox').not('.td_data_action').click(function () { //All TDs clickable but the one which has the checkbox.
		window.location = $(this).parent().find('a.edit').attr('href'); //Link goes to the same href as the "edit" button.
	});  
	
	//CHECK ALL function
	$('.checkall').click(function () {
		$(this).parents('fieldset:eq(0)').find(':checkbox').attr('checked', this.checked);
	});

	//FILTER SLIDE UP/DOWN (TOGGLE)	
	$('.filterresults').click(function() {

		if ( $(".filterresults .button_icon_link").hasClass("arrowdown")) {
			$(".filterresults .button_icon_link").removeClass("arrowdown");
			$(".filterresults .button_icon_link").addClass("arrowup");
		} 
		else {
			$(".filterresults .button_icon_link").removeClass("arrowup");
			$(".filterresults .button_icon_link").addClass("arrowdown");
		}
				
		$('#filter_fields').slideToggle('fast');

		$(".filterresults .button_icon_link").removeClass("arrowhover");
	});
	
	//FILTER HOVER
	$('.filterresults').mouseenter(function() {
		$(".filterresults .button_icon_link").addClass("arrowhover");
	});
	
	$('.filterresults').mouseout(function() {
		$(".filterresults .button_icon_link").removeClass("arrowhover");
	});
	
	$(".button_icon_link").mouseenter(function(){
		$(this).addClass("arrowhover");
	})
	
	$(".button_icon_link").mouseout(function(){
		$(this).removeClass("arrowhover");
	});

	//PANEL HEADER SLIDE UP/DOWN (TOGGLE)	
	$('.panel_header').click(function() {

		if ( $(this).children("span").hasClass("arrowdown")) {
			$(this).children("span").removeClass("arrowdown");
			$(this).children("span").addClass("arrowup");
		} 
		else {
			$(this).children("span").removeClass("arrowup");
			$(this).children("span").addClass("arrowdown");
		}
				
		$(this).parent().children(".panel_content").slideToggle('fast');
	})

/*	//SUBPANEL HEADER SLIDE UP/DOWN (TOGGLE)	
	$('.subpanel_header').click(function() {

		if ( $(this).children("span").hasClass("arrowdown")) {
			$(this).children("span").removeClass("arrowdown");
			$(this).children("span").addClass("arrowup");
		} 
		else {
			$(this).children("span").removeClass("arrowup");
			$(this).children("span").addClass("arrowdown");
		}
				
		$(this).parent().children(".subpanel_content").slideToggle(400);
	})
*/

})

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

  // Form submission buttons for main forms
  $("input.jsreplace[type=submit]").each( function() {

    // Replace these with nice buttons

    // Which class(es) are we using?
    var useClass = "button_slidingdoors button_yellow";
    if ($(this).attr("class") != "jsreplace")
    {
      // Extra classes - probably size, colour
      useClass = $(this).attr("class");
      useClass = useClass.replace(/jsreplace/, "");
    }
    var but  = "<a href='#' rel='" + $(this).attr("name") + "' ";
    but += "class='button_slidingdoors js-form-submit " + useClass + "'";
    if ($(this).attr("id").length)
    {
      but += " id='" + $(this).attr("id") + "'";
    }
    but += ">";
    but += "<span>" + $(this).attr("value") + "</span>";
    but += "</a>";

    // Anything for onclick?
    var oc = $(this).attr("onclick");

    $(this).replaceWith(but);
    but = $("a[rel='" + $(this).attr("name") + "']").last();
    if (oc)
    {
      but.bind("click", oc);
    }
  });
  $("td a.js-form-submit").each( function() {

    // Table cells with replaced buttons in need a width setting for IE
    var width = $(this).closest("td").width();
    $(this).closest("td").css("width", width + "px");
  });
  $(".js-form-submit").live('click', function() {

    // Add in a hidden input to signify the action
    // This is based on the rel
    $(this).closest("form").append("<input type='hidden' name='" + $(this).attr("rel") + "' />");

    $(this).closest("form").submit();
    return false;
  });
});

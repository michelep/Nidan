// ========================================================================================
function printNotice(text,type) {
    new Noty({
        text: text,
        type: type,
	layout: 'topRight',
	closeWith: ['click','button'],
	timeout: 3000,
	animation: {
    	    open: 'animated bounceInRight', // Animate.css class names
    	    close: 'animated bounceOutRight' // Animate.css class names
	}
    }).show();
}


// ========================================================================================= AJAXDIALOG
$(function (){
    $('.ajaxDialog').click(function(e) {
	e.preventDefault();
        var url = this.href;
        var title = this.title;
        // show a spinner or something via css
        var dialog = $("<div style='display:none' class='loading'><i class='fa fa-refresh fa-spin fa-3x fa-fw'></i><span class='sr-only'>Loading...</span></div>").appendTo("body");
        // open the dialog
        dialog.dialog({
    	    open: function(event, ui) {
    		$('#ajaxDialog').validationEngine();
    	    },
            // add a close listener to prevent adding multiple divs to the document
            close: function(event, ui) {
                // remove div with all data and events
                $('#ajaxDialog').validationEngine('hideAll');
                dialog.remove();
            },
    	    title: title,
            modal: true,
            height: 'auto',
            width: 500,
            buttons: {
        	    'OK': function() {
        		if(jQuery('#ajaxDialog').validationEngine('validate')) { 
        		    jQuery('#ajaxDialog').submit();
        		    $(this).dialog("close");
        		}
        	    },
        	    "Annulla": function() {
        		$(this).dialog("close");
        	    }
        	}
        });
        // load remote content
        dialog.load(
            url,
            {}, // omit this param object to issue a GET request instead a POST request, otherwise you may provide post parameters within the object
            function (responseText, textStatus, XMLHttpRequest) {
                // remove the loading class
                dialog.removeClass('loading');
            }
        );
        //prevent the browser to follow the link 
        return false;
    });
    $('.ajaxCall').click(function(e) {
	e.preventDefault();
        var url = this.href;
	$.ajax({
    	    url: url,
	    dataType: "html"
	}).done(function(data) {
	    printNotice(data,'success');
	});
    });
});

$(document).ready(function() {
    $( "input[type=submit], button" ).button();

    jQuery('form').validationEngine();
});

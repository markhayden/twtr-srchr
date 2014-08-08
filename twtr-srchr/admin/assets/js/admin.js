(function ( $ ) {
	"use strict";

	$(function () {
		function testQuery() {
			$.get( window.location.protocol + '//' + window.location.host + '/wp-content/plugins/twtr-srchr/public/includes/twitter-search-endpoint.php?q=tacos&t=true', function( data ) {
				$( "#twtrTestOutpt" ).html( data );
				$( "#twtrTestOutpt" ).fadeIn();
				$("#twtrClickTest").fadeIn();
			});
		}

		$('#twtrClickTest').click( function() {
			$("#twtrClickTest").fadeOut();
			$( "#twtrTestOutpt" ).fadeIn();
			testQuery();
		});

	});

}(jQuery));
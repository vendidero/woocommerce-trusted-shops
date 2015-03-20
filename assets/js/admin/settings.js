jQuery( function ( $ ) {
	$( document ).on( 'click', '#wc-trusted-shops-export', function() {
		var href_org = $( this ).data( "href-org" );
		$( this ).attr( "href", href_org + '&interval=' + $( '#woocommerce_trusted_shops_review_collector' ).val() ); 
	});
});
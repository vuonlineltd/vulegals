( function ( $ ) {
	function updatePreview() {
		var p = document.getElementById( 'vul-preview' );
		if ( ! p ) return;
		var map = { colour_bg: '--vul-bg', colour_fg: '--vul-fg', colour_muted: '--vul-muted', colour_accent: '--vul-accent', colour_accent_fg: '--vul-accent-fg', colour_border: '--vul-border', radius: '--vul-radius', font: '--vul-font' };
		Object.keys( map ).forEach( function ( id ) {
			var el = document.getElementById( id );
			if ( el && el.value ) p.style.setProperty( map[ id ], el.value );
		} );
	}
	function toggleCustom() {
		var mode = $( 'input[name$="[style_mode]"]:checked' ).val();
		$( '.vul-if-custom' ).toggle( mode !== 'theme' );
	}
	$( function () {
		$( '.vul-colour' ).wpColorPicker( { change: function () { setTimeout( updatePreview, 10 ); }, clear: updatePreview } );
		$( '#radius, #font' ).on( 'input', updatePreview );
		$( 'input[name$="[style_mode]"]' ).on( 'change', toggleCustom );
		toggleCustom();
		updatePreview();
	} );
} )( jQuery );

/* Documents: data repeater */
( function () {
	var table = document.getElementById( 'vul-data-rows' );
	if ( ! table ) return;
	var add = document.getElementById( 'vul-add-row' );
	function reindex() {
		table.querySelectorAll( 'tbody tr' ).forEach( function ( tr, i ) {
			tr.querySelectorAll( '[name]' ).forEach( function ( el ) {
				el.name = el.name.replace( /\[privacy_data\]\[\d+\]/, '[privacy_data][' + i + ']' );
			} );
		} );
	}
	add.addEventListener( 'click', function () {
		var rows = table.querySelectorAll( 'tbody tr' );
		var clone = rows[ rows.length - 1 ].cloneNode( true );
		clone.classList.remove( 'vul-repeater__blank' );
		clone.querySelectorAll( 'input' ).forEach( function ( el ) { el.value = ''; } );
		clone.querySelector( 'select' ).selectedIndex = 0;
		table.querySelector( 'tbody' ).appendChild( clone );
		reindex();
		clone.querySelector( 'input' ).focus();
	} );
	table.addEventListener( 'click', function ( e ) {
		if ( ! e.target.classList.contains( 'vul-repeater__remove' ) ) return;
		var tr = e.target.closest( 'tr' );
		if ( table.querySelectorAll( 'tbody tr' ).length > 1 ) tr.remove(); else tr.querySelectorAll( 'input' ).forEach( function ( el ) { el.value = ''; } );
		reindex();
	} );
} )();

( function ( blocks, element, serverSideRender, blockEditor ) {
	var el = element.createElement;
	blocks.registerBlockType( 'vu-legals/cookie-policy', {
		edit: function () {
			var props = blockEditor.useBlockProps();
			return el( 'div', props, el( serverSideRender, { block: 'vu-legals/cookie-policy' } ) );
		},
		save: function () { return null; }
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor );

( function ( blocks, element, serverSideRender, blockEditor, components ) {
	var el = element.createElement;
	var types = [
		{ value: 'terms', label: 'Website Terms of Use' },
		{ value: 'privacy', label: 'Privacy Policy' },
		{ value: 'accessibility', label: 'Accessibility Statement' },
		{ value: 'cookies', label: 'Cookie Policy' }
	];
	blocks.registerBlockType( 'vu-legals/document', {
		edit: function ( props ) {
			var bp = blockEditor.useBlockProps();
			return el( element.Fragment, {},
				el( blockEditor.InspectorControls, {},
					el( components.PanelBody, { title: 'Document' },
						el( components.SelectControl, { label: 'Type', value: props.attributes.type, options: types, onChange: function ( v ) { props.setAttributes( { type: v } ); } } )
					)
				),
				el( 'div', bp, el( serverSideRender, { block: 'vu-legals/document', attributes: props.attributes } ) )
			);
		},
		save: function () { return null; }
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.components );

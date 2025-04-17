const addDisplayType = wp.compose.createHigherOrderComponent(
	( BlockEdit ) => ( props ) => {
		if ( props.name !== 'core/gallery' ) {
			return wp.element.createElement( BlockEdit, props );
		}

		return wp.element.createElement(
			BlockEdit,
			{
				...props,
				className: `is-display-type-${
					props.attributes.displayType || 'default'
				}`,
			}
		);
	},
	'withDisplayType'
);

wp.hooks.addFilter(
	'editor.BlockEdit',
	'css-carousel-block/add-display-type',
	addDisplayType
);

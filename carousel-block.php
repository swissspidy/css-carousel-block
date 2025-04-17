<?php
/**
 * Plugin Name:       CSS Carousel Block
 * Plugin URI:        https://github.com/swissspidy/css-carousel-block
 * Description:       Carousel variation for the core gallery block.
 * Version:           0.1.0
 * Author:            Pascal Birchler
 * Author URI:        https://pascalbirchler.com
 * License:           Apache-2.0
 * License URI:       https://www.apache.org/licenses/LICENSE-2.0
 * Text Domain:       css-carousel-block
 * Requires at least: 6.7
 * Requires PHP:      8.0
 *
 * @package CssCarouselBlock
 */

/**
 * Filters gallery block metadata to add displayType attribute.
 *
 * @param array $metadata Block metadata.
 * @return array Filtered block metadata.
 */
function css_carousel_block_filter_gallery_block_attributes( array $metadata ): array {
	if ( 'core/gallery' === $metadata['name'] ) {
		$metadata['attributes']['displayType'] = [
			'type' => 'string',
		];
	}

	return $metadata;
}

add_filter( 'block_type_metadata', 'css_carousel_block_filter_gallery_block_attributes' );

/**
 * Adds carousel variation to gallery block.
 *
 * @param array    $variations List of all block variations.
 * @param WP_Block $block_type Block type instance.
 * @return array Filtered list of block variations.
 */
function css_carousel_block_add_carousel_variation( $variations, $block_type ) {
	if ( 'core/gallery' !== $block_type->name ) {
		return $variations;
	}

	$variations[] = array(
		'name'        => 'carousel',
		'title'       => __( 'Carousel', 'css-carousel-block' ),
		'description' => __( 'A carousel', 'css-carousel-block' ),
		'scope'       => array( 'block', 'inserter', 'transform' ),
		'isDefault'   => false,
		'attributes'  => array(
			'displayType' => 'carousel',
		),
	);

	return $variations;
}

add_filter( 'get_block_type_variations', 'css_carousel_block_add_carousel_variation', 10, 2 );

/**
 * Filters rendered gallery block markup for carousel support.
 *
 * @param string $block_content Rendered block markup.
 * @param array  $block Block metadata.
 * @return string Filtered block markup.
 */
function css_carousel_block_filter_gallery_block_html( $block_content, $block ) {
	if ( 'core/gallery' !== $block['blockName'] ) {
		return $block_content;
	}

	if ( ! isset( $block['attrs']['displayType'] ) || 'carousel' !== $block['attrs']['displayType'] ) {
		return $block_content;
	}

	$processor = new WP_HTML_Tag_Processor( $block_content );
	if ( $processor->next_tag( 'figure' ) ) {
		$class_name = $processor->get_attribute( 'class' );
		$class_name = is_string( $class_name ) ? $class_name : '';
		$class_name = trim( $class_name . ' is-display-type-carousel' );

		$processor->set_attribute( 'class', $class_name );
		$processor->set_attribute( 'data-previous', __( 'Previous', 'css-carousel-block' ) );
		$processor->set_attribute( 'data-next', __( 'Next', 'css-carousel-block' ) );
	}

	wp_enqueue_style( 'css-carousel-block-styles' );

	return $processor->get_updated_html();
}

add_filter( 'render_block', 'css_carousel_block_filter_gallery_block_html', 10, 2 );

/**
 * Enqueues scripts for the block editor.
 */
function css_carousel_block_enqueue_block_editor_assets(): void {
	wp_enqueue_script(
		'css-carousel-block-editor',
		plugins_url( 'assets/editor.js', __FILE__ ),
		array( 'wp-compose', 'wp-hooks', 'wp-element' )
	);
	wp_enqueue_style( 'css-carousel-block-styles' );
}

add_action( 'enqueue_block_editor_assets', 'css_carousel_block_enqueue_block_editor_assets' );

/**
 * Registers the shared CSS for the block.
 */
function css_carousel_block_register_assets() {
	wp_register_style(
		'css-carousel-block-styles',
		plugins_url( 'assets/styles.css', __FILE__ )
	);
}

add_action( 'init', 'css_carousel_block_register_assets' );

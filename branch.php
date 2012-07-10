<?php
/*
Plugin Name: Branch
Plugin URI: http://branch.com
Description: Embed Branch conversations in your WordPress site.
Author: Daniel Bachhuber
Version: 0.1.1
Author URI: http://danielbachhuber.com
*/

class Branch
{

	/**
	 * Initialize most of the plugin on init, so we can depend on other things being available too
	 */
	function __construct() {

		add_action( 'init', array( $this, 'action_init' ) );
		add_filter( 'oembed_result', array( $this, 'filter_oembed_result' ), 10, 3 );
	}

	/**
	 * Register our oEmbed provider and shortcode
	 */
	function action_init() {

		wp_oembed_add_provider( '#http://([a-z0-9]+\.)?branch\.com/*#i', 'http://api.branch.com/oembed', true );
		add_shortcode( 'branch', array( $this, 'shortcode' ) );
	}

	/**
	 * Do the shortcode, which is just a wrapper for oEmbed
	 */
	function shortcode( $atts, $content = '' ) {
		global $wp_embed;

		if ( empty( $atts[0] ) && empty( $content ) )
			return '<!-- Missing Branch URL -->';

		$url = ( ! empty( $content ) ) ? $content : $atts[0];

		// WP_Embed will take care of validation and everything else
		return $wp_embed->shortcode( $atts, $url );
	}

	/**
	 * Filter the oembed result to remove 'data-max-height="700"' from the
	 * rich HTML response as this sets an unnecessary height restriction on the embed.
	 */
	function filter_oembed_result( $html, $url, $args ) {

		// Only filter if it's a request to Branch's oEmbed endpoint
		if ( false === stripos( $url, 'branch.com' ) )
			return $html;

		$html = preg_filter( "/[\s]?data-max-height=(['\"])[\d]+(['\"])/", '', $html );
		return $html;
	}

}

global $branch;
$branch = new Branch();
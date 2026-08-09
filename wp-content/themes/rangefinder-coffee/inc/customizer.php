<?php
/**
 * Theme Customizer: hero tagline, cozy story text, contact details, footer links.
 */
defined( 'ABSPATH' ) || exit;

function rangefinder_customize_register( $wp_customize ) {

	$wp_customize->add_section( 'rangefinder_content', array(
		'title'    => 'Café Content',
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'hero_tagline', array(
		'default'           => 'Dialed-in Specialty Espresso & Community Hub in Fayetteville, West Virginia',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'hero_tagline', array(
		'label'   => 'Hero Tagline',
		'section' => 'rangefinder_content',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'hero_image', array(
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'hero_image', array(
		'label'   => 'Hero Background Image',
		'section' => 'rangefinder_content',
	) ) );

	$wp_customize->add_setting( 'cozy_summary', array(
		'default'           => "Tucked into the heart of Fayetteville, Range Finder Coffee began as a single espresso cart and grew into a community gathering place for climbers, rafters, and neighbors alike. Every cup is dialed in with the same care we'd want waiting for us after a long day on the New River Gorge trails.",
		'sanitize_callback' => 'sanitize_textarea_field',
	) );
	$wp_customize->add_control( 'cozy_summary', array(
		'label'   => 'Our Story (Cozy Summary)',
		'section' => 'rangefinder_content',
		'type'    => 'textarea',
	) );

	$wp_customize->add_section( 'rangefinder_contact', array(
		'title'    => 'Contact & Footer Links',
		'priority' => 31,
	) );

	$contact_fields = array(
		'contact_phone'   => array( 'label' => 'Phone Number', 'default' => '(304) 555-0199' ),
		'contact_email'   => array( 'label' => 'Email Address', 'default' => 'rangefindercoffee@gmail.com' ),
		'contact_address' => array( 'label' => 'Postal Address', 'default' => 'Fayetteville, WV, 25840' ),
		'career_link'     => array( 'label' => 'Barista Openings URL', 'default' => '#' ),
		'application_link' => array( 'label' => 'Application Portal URL', 'default' => '#' ),
		'privacy_link'    => array( 'label' => 'Privacy Practices URL', 'default' => '#' ),
		'terms_link'      => array( 'label' => 'Usage Standards URL', 'default' => '#' ),
	);

	foreach ( $contact_fields as $id => $field ) {
		$sanitize = ( false !== strpos( $id, 'link' ) ) ? 'esc_url_raw' : 'sanitize_text_field';
		$wp_customize->add_setting( $id, array(
			'default'           => $field['default'],
			'sanitize_callback' => $sanitize,
		) );
		$wp_customize->add_control( $id, array(
			'label'   => $field['label'],
			'section' => 'rangefinder_contact',
			'type'    => 'text',
		) );
	}
}
add_action( 'customize_register', 'rangefinder_customize_register' );

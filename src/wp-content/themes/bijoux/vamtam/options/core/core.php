<?php

/**
 * Controls attached to core sections
 *
 * @package vamtam/bijoux
 */


return array(
	array(
		'label'     => esc_html__( 'Header Logo Type', 'bijoux' ),
		'id'        => 'header-logo-type',
		'type'      => 'switch',
		'transport' => 'postMessage',
		'section'   => 'title_tagline',
		'choices'   => array(
			'image'      => esc_html__( 'Image', 'bijoux' ),
			'site-title' => esc_html__( 'Site Title', 'bijoux' ),
		),
		'priority' => 8,
	),
);



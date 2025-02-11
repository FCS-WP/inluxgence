<?php
namespace VamtamElementor\Widgets\ProductDataTabs;

// Extending the WC Product Data Tabs widget.

// Is WC Widget.
if ( ! vamtam_has_woocommerce() ) {
	return;
}

// Is Pro Widget.
if ( ! \VamtamElementorIntregration::is_elementor_pro_active() ) {
	return;
}

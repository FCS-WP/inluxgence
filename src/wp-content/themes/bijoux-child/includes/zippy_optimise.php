<?php


if (function_exists("register_sidebar")) {
  register_sidebar();
}

add_action( 'admin_init', function () {
  remove_menu_page( 'vamtam_theme_setup' );
});

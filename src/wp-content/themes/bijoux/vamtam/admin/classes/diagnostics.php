<?php

class VamtamDiagnostics {
	private static $test_results = [];

	public function __construct() {
		add_action( 'admin_notices', array( __CLASS__, 'notice' ), 5 );
	}

	public static function notice() {
		$tests = VamtamDiagnostics::tests();

		if ( empty( $tests ) ) {
			return;
		}

		?>
		<div class="vamtam-ts-notice">
			<div class="vamtam-diagnostics-notice warning notice">
				<div class="vamtam-notice-aside">
					<div class="vamtam-notice-icon-wrapper">
						<img id="vamtam-logo" src="<?php echo esc_attr( VAMTAM_ADMIN_ASSETS_URI . 'images/vamtam-logo.png' ); ?>"></img>
					</div>
				</div>
				<div class="vamtam-notice-content">
					<?php self::print_content() ?>
				</div>
			</div>
		<?php
	}

	public static function print() {
?>
		<div class="vamtam-box-wrap vamtam-diagnostics-box">
			<header>
				<h3><?php esc_html_e( 'Diagnostics', 'bijoux' ); ?></h3>
			</header>
			<div class="content">
				<?php VamtamDiagnostics::print_content() ?>
			</div>
		</div>
<?php
	}

	public static function print_content() {
		$tests = VamtamDiagnostics::tests();

		if ( ! empty( $tests ) ) :
			if ( doing_action( 'admin_notices' ) ) : ?>
				<h3><?php esc_html_e( "We detected some problems with your server. This may cause parts of the demo content to not work as expected:", 'bijoux' ) ?></h3>
			<?php else : ?>
				<div>
					<span class="dashicons dashicons-warning" style="color:#D03032"></span>
					<?php esc_html_e( "We detected some problems with your server. Please resolve them before importing the demo content:", 'bijoux' ) ?>
				</div>
			<?php endif ?>

			<table>
				<?php foreach( $tests as $id => $test ) : ?>
					<tr id="<?= esc_attr( $id ) ?>" data-pass="<?= esc_attr( $test['pass'] ) ?>">
						<td><strong><?= $test['title'] ?></strong></td>
						<td class="result"><?= $test['result'] ?></td>
						<td>
							<?php if ( ! $test['pass'] ) : ?>
								<?= $test['msg'] // xss ok ?>
							<?php endif ?>
						</td>
					</tr>
				<?php endforeach ?>
			</table>
		<?php else : ?>
			<div>
				<span class="dashicons dashicons-yes-alt" style="color:#039406"></span>
				<?php esc_html_e( "We haven't detected any problems with your server. You may proceed with the demo content import", 'bijoux' ); ?>
			</div>
		<?php endif;
	}

	private static function is_https() {
		if ( array_key_exists("HTTPS", $_SERVER) && 'on' === $_SERVER["HTTPS"] ) {
			return true;
		}

		if ( array_key_exists("SERVER_PORT", $_SERVER) && 443 === (int)$_SERVER["SERVER_PORT"] ) {
			return true;
		}

		if ( array_key_exists("HTTP_X_FORWARDED_SSL", $_SERVER) && 'on' === $_SERVER["HTTP_X_FORWARDED_SSL"] ) {
			return true;
		}

		if ( array_key_exists("HTTP_X_FORWARDED_PROTO", $_SERVER) && 'https' === $_SERVER["HTTP_X_FORWARDED_PROTO"] ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool true if mixed content detected
	 */
	private static function mixed_content_test() {
		// this page was loaded over https
		if ( self::is_https() ) {
			global $wpdb;

			// home or site url is not using https
			if ( ! wp_is_using_https() ) {
				return 'Settings/General';
			}

			$home_url_raw = '%' . str_replace( 'https', 'http', get_option( 'home' ) ) . '%';
			$site_url_raw = '%' . str_replace( 'https', 'http', get_option( 'siteurl' ) ) . '%';
			$home_url_raw_json = substr( json_encode( $home_url_raw ), 1, -1 );
			$site_url_raw_json = substr( json_encode( $site_url_raw ), 1, -1 );

			if ( (int) $wpdb->get_var( $wpdb->prepare( "
					select count(*) from $wpdb->options where
					option_value like %s or option_value like %s or
					option_value like %s or option_value like %s
				"),
					$home_url_raw, $home_url_raw_json,
					$site_url_raw, $site_url_raw_json
				)
			) {
				return "$wpdb->options table";
			}


			if ( (int) $wpdb->get_var( $wpdb->prepare( "
					select count(*) from $wpdb->postmeta where
					meta_value like %s or meta_value like %s or
					meta_value like %s or meta_value like %s
				"),
					$home_url_raw, $home_url_raw_json,
					$site_url_raw, $site_url_raw_json
				)
			) {
				return "$wpdb->postmeta table";
			}

			if ( (int) $wpdb->get_var( $wpdb->prepare( "
					select count(*) from $wpdb->posts where
					post_content like %s or post_content like %s or
					post_content like %s or post_content like %s
				"),
					$home_url_raw, $home_url_raw_json,
					$site_url_raw, $site_url_raw_json
				)
			) {
				return "post content";
			}
		}

		return false;
	}

	private static function memory_in_mbytes( $memory ) {
		return (int)preg_replace_callback( '/(\-?\d+)(.?)/', function ( $m ) {
			return $m[1] * pow( 1024, strpos( 'BKMG', $m[2] ) );
		}, strtoupper( $memory ) ) / 1024 / 1024;
	}

	public static function tests() {
		if ( ! empty( self::$test_results ) ) {
			return self::$test_results;
		}

		$phpversion         = phpversion();
		$phpversion_minimum = '8.0';

		$mixed_content = self::mixed_content_test();

		self::$test_results = [
			'phpversion' => [
				'title'  => esc_html__( 'PHP Version', 'bijoux' ),
				'result' => $phpversion,
				'pass'   => version_compare( $phpversion, $phpversion_minimum, '>=' ),
				'msg'    => sprintf( esc_html__( 'PHP version %s is below %s, which is the minimum recommended', 'bijoux' ), $phpversion, $phpversion_minimum ),
			],
			'basicauth' => [
				'title'  => esc_html__( 'Basic Auth', 'bijoux' ),
				'result' => $_SERVER['REMOTE_USER'] ?: 'none',
				'pass'   => empty( $_SERVER['REMOTE_USER'] ),
				'msg'    => wp_kses_post( sprintf( __( 'Basic access authentication detected. Please ensure that both <strong>%s</strong> and <strong>%s</strong> are accessible without a password.', 'bijoux' ), get_option( 'home' ), admin_url( 'admin-ajax.php' ) ) ),
			],
			'mixedcontent' => [
				'title'  => esc_html__( 'Mixed content', 'bijoux' ),
				'result' => $mixed_content ? "Detected in $mixed_content" : 'passed',
				'pass'   => ! $mixed_content,
				'msg'    => esc_html__( 'This page was loaded over HTTPS, however URLs using HTTP were found in the database. Please replace all HTTP links with their HTTPS equivalents.', 'bijoux' ),
			],
			'memory_frontend' => [
				'title'  => esc_html__( 'Memory limit (frontend)', 'bijoux' ),
				'result' => WP_Site_Health::get_instance()->php_memory_limit,
				'pass'   => self::memory_in_mbytes( WP_Site_Health::get_instance()->php_memory_limit ) >= 256,
				'msg'    => esc_html__( 'The memory limit for this site is too low, we recommend a minimum of 256MB. Please contact your hosting provider if you are unsure how to change this.', 'bijoux' ),
			]
		];

		if ( function_exists( 'ini_get' ) ) {
			$post_max_size       = ini_get( 'post_max_size' );
			$upload_max_filesize = ini_get( 'upload_max_filesize' );
			$memory_limit        = ini_get( 'memory_limit' );

			self::$test_results = array_merge( self::$test_results, [
				'memory' => [
					'title'  => esc_html__( 'Memory limit (admin)', 'bijoux' ),
					'result' => $memory_limit,
					'pass'   => self::memory_in_mbytes( $memory_limit ) >= 256,
					'msg'    => esc_html__( 'The memory limit for this site is too low, we recommend a minimum of 256MB. Please contact your hosting provider if you are unsure how to change this.', 'bijoux' ),
				],
				'post_max_size' => [
					'title'  => esc_html__( 'Post Max Size', 'bijoux' ),
					'result' => $post_max_size,
					'pass'   => self::memory_in_mbytes( $post_max_size ) > 32,
					'msg'    => esc_html__( 'post_max_size is too low, we recommend setting it to at least 32MB to avoid problems with large pages. Please contact your hosting provider if you are unsure how to change this.', 'bijoux' ),
				],
				'upload_max_filesize' => [
					'title'  => esc_html__( 'Upload Max File Size', 'bijoux' ),
					'result' => $upload_max_filesize,
					'pass'   => self::memory_in_mbytes( $upload_max_filesize ) > 32,
					'msg'    => esc_html__( 'upload_max_filesize is too low, we recommend setting it to at least 32MB so that you can upload large images and videos. Please contact your hosting provider if you are unsure how to change this.', 'bijoux' ),
				],
			] );
		}

		self::$test_results = array_filter( self::$test_results, function( $test ) {
			return ! $test['pass'];
		} );

		return self::$test_results;
	}
}
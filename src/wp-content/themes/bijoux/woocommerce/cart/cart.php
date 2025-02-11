<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'vamtam_render_cart_item' ) ) {
	function vamtam_render_cart_item( $cart_item_key, $cart_item ) {
		$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
		$is_product_visible = ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) );

		if ( ! $is_product_visible ) {
			return;
		}

		$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
		$product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
		$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
		/**
		 * Filter the product name.
		 *
		 * @since 2.1.0
		 * @param string $product_name Name of the product in the cart.
		 * @param array $cart_item The product in the cart.
		 * @param string $cart_item_key Key for the product in the cart.
		 */
		$product_name = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );

		?>
		<div class="vamtam-cart__product woocommerce-cart-form__cart-item <?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

			<div class="vamtam-cart__product-image product-thumbnail">
				<?php
				$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

				if ( ! $product_permalink ) {
					echo $thumbnail; // PHPCS: XSS ok.
				} else {
					printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
				}
				?>
			</div>

			<div class="vamtam-cart__product-name product-name" data-title="<?php esc_attr_e( 'Product', 'bijoux' ); ?>">
				<?php
				if ( ! $product_permalink ) {
					echo wp_kses_post( $product_name . '&nbsp;' );
				} else {
					/**
					 * This filter is documented above.
					 *
					 * @since 2.1.0
					 */
					echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_name() ), $cart_item, $cart_item_key ) );
				}

				do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

				// Meta data.
				echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

				// Backorder notification
				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
					echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'bijoux' ) . '</p>', $product_id ) );
				}
				?>
			</div>

			<div class="vamtam-cart__product-price product-price" data-title="<?php esc_attr_e( 'Price', 'bijoux' ); ?>">
				<?php
					if ( $_product->is_sold_individually() ) {
						$min_quantity = 1;
						$max_quantity = 1;
					} else {
						$min_quantity = 0;
						$max_quantity = $_product->get_max_purchase_quantity();
					}

					$product_quantity = woocommerce_quantity_input(
						array(
							'input_name'   => "cart[{$cart_item_key}][qty]",
							'input_value'  => $cart_item['quantity'],
							'max_value'    => $max_quantity,
							'min_value'    => $min_quantity,
							'product_name' => $product_name,
						),
						$_product,
						false
					);

					echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
				?>
			</div>

			<div class="vamtam-cart__product-remove product-remove">
				<?php
				echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
					'<a href="%s" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s"></a>',
					esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
					/* translators: %s is the product name */
					esc_attr( sprintf( __( 'Remove %s from cart', 'bijoux' ), wp_strip_all_tags( $product_name ) ) ),
					esc_attr( $product_id ),
					esc_attr( $cart_item_key ),
					esc_attr( $_product->get_sku() )
				), $cart_item_key );
				?>
			</div>
		</div>
		<?php
	}
}

do_action( 'woocommerce_before_cart' ); ?>
<div class="vamtam-woocommerce-cart-form-wrapper">
	<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
		<div class="vamtam-cart-main">
			<div class="vamtam-cart__header">
				<span class="font-h4 label"><?php esc_html_e( 'Your cart ', 'bijoux' ); ?></span>
				<span class="font-h4 item-count">(<?php echo esc_html( sprintf( _n( '%s item', '%s items', WC()->cart->get_cart_contents_count(), 'bijoux' ), WC()->cart->get_cart_contents_count() ) ); ?>)</span>
			</div>
			<?php do_action( 'woocommerce_before_cart_table' ); ?>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>
			<div class="woocommerce-cart-form__contents">
				<?php
					foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
						vamtam_render_cart_item( $cart_item_key, $cart_item );
					}
				?>
			</div>

			<div class="cart-contents">
				<?php do_action( 'woocommerce_cart_contents' ); ?>

				<input class="hidden" type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'bijoux' ); ?>">

				<?php if ( wc_coupons_enabled() ) { ?>
					<input class="hidden" type="submit" name="apply_coupon" value="<?php esc_attr_e( 'Redeem', 'bijoux' ); ?>">
				<?php } ?>

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>

				<?php do_action( 'woocommerce_after_cart_contents' ); ?>
			</div>

			<?php do_action( 'woocommerce_after_cart_table' ); ?>
		</div>
	</form>
	<div class="vamtam-cart-collaterals">
		<div class="vamtam-sticky-wrapper">
			<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

			<div class="cart-collaterals">
				<?php
					/**
					* woocommerce_cart_collaterals hook.
					*
					* @hooked woocommerce_cross_sell_display
					* @hooked woocommerce_cart_totals - 10
					*/
					do_action( 'woocommerce_cart_collaterals' );
				?>
			</div>

			<?php if ( wc_coupons_enabled() ) { ?>
				<div class="coupon">
					<details>
						<summary>
							<span class="label">
								<?php esc_html_e( 'Add a voucher', 'bijoux' ); ?>
								<span><?php esc_html_e( '(Optional)', 'bijoux' ); ?></span>
							</span>
							<svg width="36" height="36" viewBox="0 0 36 36" ><path d="M22.593 18.088l-7.532 7.473-1.057-1.065 6.466-6.418L14 11.556l1.065-1.056z" fill="currentColor" fill-rule="evenodd"></path></svg>
						</summary>
						<div class="content">
							<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Enter your code', 'bijoux' ); ?>" />
							<button type="submit" name="apply_coupon" value="<?php esc_attr_e( 'Redeem', 'bijoux' ); ?>">
								<!-- // Bijoux-only. -->
								<span class="vamtam-prefix"></span>
								<?php esc_html_e( 'Redeem', 'bijoux' ); ?>
							</button>
						</div>
					</details>

					<?php do_action( 'woocommerce_cart_coupon' ); ?>

				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php do_action( 'woocommerce_after_cart' ); ?>

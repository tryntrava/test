<?php
/*
Plugin Name: MQ Woocommerce Product's Price Bulk Edit
Plugin URL: https://github.com/mostafaqanbari/mq-woocommerce-products-price-bulk-edit
Description: This Plugins Simplifies process of changing woocommerce products in a woocommerce shop. it also provides a shortcode for displaying a Pricing Table and use it in a Post or Page.
Version: 1.0
Author: Mostafa Qanbari
Author URI: 
Text Domain: mq-woocommerce-products-price-bulk-edit
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/*  
Copyright 2014 Woocommerce Products Bulk Edit(http://www.mqanbari.ir)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    // Hook for adding admin menus
	add_action('admin_menu', 'mq_wpbe_add_pages');

    //action function for above hook
	function mq_wpbe_add_pages(){
		add_submenu_page( 'woocommerce', __('Product\'s Bulk Price Edit','mq-woocommerce-products-price-bulk-edit'), __('Product\'s Bulk Price Edit','mq-woocommerce-products-price-bulk-edit'), 'view_woocommerce_reports', 'Woocommerce_Products_Bulk_Edit', 'mq_wpbe_toplevel_page' );
	}

	// mq_wpbe_toplevel_page() displays the page content for the custom Test Toplevel menu
	function mq_wpbe_toplevel_page(){
	    echo '<div class="wrap">';
	   	echo '<div class="mq_wpbe_note_box_' . ((get_locale() == "fa_IR" || get_locale() == "ar_AR" || get_locale() == "he_IL") ? 'right' : 'left') . '">';
	    echo '<p class="mq_wpbe_message">' . __('Use <strong>[mq_wpbe]</strong> Shortcode in your pages or posts for Getting Pricing Table. Please Note that if you update your product form a Product Edit Page your Pricing Table Won\'t Update! Use This page to update Pricing Table in Front View.','mq-woocommerce-products-price-bulk-edit') . '</p>';
	    echo '</div>';
	    echo "<h2>" . __( 'MQ Woocommerce Product\'s Price Bulk Edit','mq-woocommerce-products-price-bulk-edit') . "</h2>";
		
		//get all products
		$args = array( 'post_type' => 'product', 'posts_per_page' => -1 );
	    $loop = new WP_Query( $args );
	    
	    //plus uncategories
	    if ( $loop->have_posts() ){

	    	echo '<div class="mq_wpbe_box">';
	    	echo '<p class="mq_wpbe_message">' . __('Select A Category To Filter: ','mq-woocommerce-products-price-bulk-edit') . '</p>';


	    	//all the categories
		    $args = array(
			    'orderby'    => 'name',
			    'hide_empty' => 1,
			    'orderby' => 'category'
			);
			$category_terms = get_terms( 'product_cat', $args );
			foreach($category_terms as $cat_term){
				echo '<div class="mq_wpbe_categories"><input class="category_checkbox" id="' . $cat_term->term_id . '" cat_name="' . $cat_term->name . '" type="checkbox"><label for="' . $cat_term->term_id . '">' . $cat_term->name . '</label><span class="mq_category_count_span">(' . $cat_term->count . ')</span></div>';
			}

			//findout how many uncategorized products we have
			$args_uncategorized_products = array( 'post_type' => 'product',
							'tax_query' => array(
								array(
									'taxonomy' => 'product_cat',
									'field' => 'id',
									'terms' => get_terms( 'product_cat', array( 'fields' => 'ids' ) ),
									'operator' => 'NOT IN'
								)
							)
						 );
		    $loop_uncategorized_products = new WP_Query( $args_uncategorized_products );
		    if($loop_uncategorized_products->post_count > 0){
		    	//add uncategories checkbox
				echo '<div class="mq_wpbe_categories"><input class="category_checkbox" id="Uncategorized" cat_name="Uncategorized" type="checkbox"><label for="Uncategorized">' . __('Uncategorized','mq-woocommerce-products-price-bulk-edit') . '</label><span class="mq_category_count_span">(' . $loop_uncategorized_products->post_count . ')</span></div>';
		    }
		    echo '</div>'; //end of mq_wpbe_box div

		    echo '<table class="mq_wpbe_admin_table_header_' . ((get_locale() == "fa_IR" || get_locale() == "ar_AR" || get_locale() == "he_IL") ? 'right' : 'left') . '"><tr><th style="width: 330px;">' . __('Product', 'mq-woocommerce-products-price-bulk-edit') . '</th><th style="width: 215px;">' . __('Regular Price', 'mq-woocommerce-products-price-bulk-edit') . '</th><th>' . __('Sales Price', 'mq-woocommerce-products-price-bulk-edit') . '</th></tr></table>';
		    echo '<ul class="mq_wpbe_products_ul">';

		    while ( $loop->have_posts() ){

		    	//get the product
		    	$loop->the_post();
		    	$product = get_product( $loop->post->ID );
		    	
		    	$categories = wp_get_post_terms( $product->id, 'product_cat' );
		    	$categories_id = "";
				$categories_names = "";
		    	foreach($categories as $cat){
		    		$categories_id .= $cat->term_id . " ";
					$categories_names .= $cat->name . " ";
		    	}
		    	$categories_id = !empty($categories_id) ? trim($categories_id) : "Uncategorized";
				$categories_names = !empty($categories_names) ? trim($categories_names) : "Uncategorized";
		 		echo '<li class="mq_wpbe_product_li mq_wpbe_li_' . ((get_locale() == "fa_IR" || get_locale() == "ar_AR" || get_locale() == "he_IL") ? 'right' : 'left') . '" cats="' . $categories_id . '">';
		 		echo '<a ';
		 		if($product->is_type('variable'))
		 			echo 'class="variable_product"';
		 		$out_of_stock_message = (!$product->is_in_stock()) ? '<span style="color: red; display: inline-block;">' . __('(Out Of Stock)','mq-woocommerce-products-price-bulk-edit') . '</span>' : "";
		 		echo ' href="' . get_permalink() . '">' . $out_of_stock_message . $product->get_title() . '</a>';

		 		if(!$product->is_type('variable')){
		 			echo '<input type="text" class="regular_price_input" id="' . $product->id . '" size="5" value="' . $product->get_regular_price() . '">';
		 			echo '<input type="text" class="sales_price_input" style="background-color: rgb(215, 255, 255);" id="' . $product->id . '" size="5" value="' . $product->get_sale_price() . '"><span class="ajax_loader mq_wpbe_ajax_loader_' . (get_locale() == "fa_IR" ? 'right' : 'left') . '" id="' . $product->id . '">&nbsp;</span>';
		 		}else{
		 			echo '<ul class="mq_wpbe_variable_products_ul">';
		    		foreach($product->get_children() as $child_id){
		    			$variation = $product->get_child( $child_id );

						//variation products
						$categories = wp_get_post_terms( $variation->variation_id, 'product_cat' );
				    	$categories_id = "";
				    	foreach($categories as $cat){
				    		$categories_id .= $cat->term_id . " ";
				    	}
				    	$categories_id = !empty($categories_id) ? trim($categories_id) : "uncat";
				 		echo '<li class="" cats="' . $categories_id . '">';
				 		if ( ! empty( $variation->variation_id ) ) {
				 			echo '<span class="attributes">';						
							echo implode(", ", $variation->get_variation_attributes());
							echo '</span>';
						}
				 		echo '<input type="text" class="regular_price_input" id="' . $variation->variation_id . '" size="5" value="' . $variation->get_regular_price() . '">';
				 		echo '<input type="text" class="sales_price_input" style="background-color: rgb(215, 255, 255);" id="' . $variation->variation_id . '" size="5" value="' . $variation->get_sale_price() . '"><span class="ajax_loader mq_wpbe_ajax_loader_' . (get_locale() == "fa_IR" ? 'right' : 'left') . '" id="' . $variation->variation_id . '">&nbsp;</span>';
				 		echo '</li>';	
					}
					echo '</ul>';
		 		}
		 		echo '</li>';
		    }//end of while loop
		    echo "</ul>";

		    echo '<table class="mq_wpbe_admin_table_footer_' . ((get_locale() == "fa_IR" || get_locale() == "ar_AR" || get_locale() == "he_IL") ? 'right' : 'left') . '"><tr><th style="width: 330px;padding-left: 5px;">' . __('Product', 'mq-woocommerce-products-price-bulk-edit') . '</th><th  style="width: 215px">' . __('Regular Price', 'mq-woocommerce-products-price-bulk-edit') . '</th><th>' . __('Sales Price', 'mq-woocommerce-products-price-bulk-edit') . '</th></tr></table>';
			
		}else{
			echo '<p class="mq_wpbe_warning_box">' . __('Sorry, no Products were found!', 'mq-woocommerce-products-price-bulk-edit' ) . '</p>';
		}
		
		echo '</div>'; //end of Wrap Div

	    wp_reset_query();

		//add Ajax
		add_action( 'admin_footer', 'mq_wpbe_js_action' );
	}//end of mq_wpbe_toplevel_page() function

	function mq_wpbe_js_action(){
	?>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			
			/**********************
			 *loading All Products
			 **********************/
			var all_products = [];
			$('li.mq_wpbe_product_li').each(function(){
				all_products.push($(this).attr("cats"));
			});
			
			//array of inputs
			var mq_wpbe_filter = function(){
				var all_checked_categories = [];
				var all_unchecked_categories = [];
				$('input[type=checkbox].category_checkbox').each(function(){
					if($(this).is(':checked')){
						all_checked_categories.push($(this).attr("id"));
					}else{
						all_unchecked_categories.push($(this).attr("id"));
					}
				});

				jQuery.each(all_unchecked_categories, function(index, current_category) {
					jQuery.each(all_products, function(pindex, current_product){
						mq_wpbe_check_product_for_unchecked_category(current_product, current_category);
					});
			   	});
				jQuery.each(all_checked_categories, function(index, current_category) {
					jQuery.each(all_products, function(pindex, current_product){
						mq_wpbe_check_product_for_checked_category(current_product, current_category);
					});
			   });
			};

			var mq_wpbe_check_product_for_unchecked_category = function(product, category){
				//see if this product contains the category
				var cats = product.split(" ");

				if(jQuery.inArray(category, cats) != -1){
					$('li.mq_wpbe_product_li[cats="' + product + '"]').addClass("mq_woo_price_bulk_edit_hide");
				}
			};
			var mq_wpbe_check_product_for_checked_category = function(product, category){
				//see if this product contains the category
				var cats = product.split(" ");

				if(jQuery.inArray(category, cats) != -1){
					$('li.mq_wpbe_product_li[cats="' + product + '"]').removeClass("mq_woo_price_bulk_edit_hide");
				}
			};

			$('input[type=checkbox]').on("click", function(){
				mq_wpbe_filter();
				if($('input[type=checkbox]:checked').length == 0){
					$('li.mq_wpbe_product_li').each(function(){
						$(this).fadeIn().removeClass("mq_woo_price_bulk_edit_hide");
					});
				}
			});

			/************************
			 *Save a Product's Price
			 ***********************/
			$('input').keypress(function(e) {
				if(e.which == 13) {
					var mq_wpbe_check_get_product_by_group = function(product_cats, category){
						//see if this product contains the category
						var cats = product_cats.split(" ");
						if(jQuery.inArray(category, cats) != -1){
							return true;
						}
					};

					var product_id = $(this).attr('id');
					var frontend_table = "";
					var title_record = '<tr class=title_record><td>Product</td><td>Regular Price</td><td>Sales Price</td></tr>';
					var title_attribute_record = '<tr class=title_record><td>&nbsp;</td><td>Regular Price</td><td>Sales Price</td></tr>';
					var checkbox_category = "";
					$('input.category_checkbox[type="checkbox"]').each(function(){
						checkbox_category = $(this).attr("id");
						frontend_table += '<table class=mq_wpbe_frontend_table id=' + $(this).attr("cat_name") + '>';
						frontend_table += '<tr><th colspan=3>' + $(this).attr("cat_name") + '</th></tr>';
						frontend_table += title_record;
						$('li.mq_wpbe_product_li').each(function(){
							if(mq_wpbe_check_get_product_by_group($(this).attr("cats"), checkbox_category) === true){
								frontend_table += '<tr>';
								frontend_table += '<td><a href=' + $(this).find("a").attr("href") + '>' + $(this).find("a").text() + '</td>';
								if($(this).find('input[type="text"]').length == 2){
									$(this).find('input[type="text"]').each(function(){
										frontend_table += "<td>";
										frontend_table += $(this).val();
										frontend_table += "</td>";
									});
								}else{
									frontend_table += '<td colspan=2><table>';
									frontend_table += title_attribute_record;
									$(this).find('span.attributes').each(function(){
										frontend_table += "<tr>";
										frontend_table += "<td>" + $(this).text() + "</td>";
										frontend_table += '<td>' + $(this).next('input').val() + '</td>';
										frontend_table += '<td>' + $(this).next('input').next('input').val() + '</td>';
										frontend_table += "</tr>";
									});
									frontend_table += "</table></td>";
								}
								frontend_table += '</tr>';
							}
						});
						frontend_table += '</table>';
					});

					var data = {
						'action': 'mq_wpbe_action',
						'product_id': $(this).attr('id'),
						'product_sale_price': $('input.sales_price_input[id="' + $(this).attr('id') + '"]').val(),
						'product_regular_price': $('input.regular_price_input[id="' + $(this).attr('id') + '"]').val(),
						'frontend_table': frontend_table
					};

					$.ajax({
					   beforeSend: function(){
					   		$('span#' + product_id + '.ajax_loader').fadeIn('slow').html('<img src="<?php echo plugins_url( 'gif-load.gif', __FILE__ ); ?>">');
					   },
					   complete: function(){
						   	setTimeout(function() {
								$('span#' + product_id + '.ajax_loader').html('&nbsp;');
							}, 2000);
							$('span#' + product_id + '.ajax_loader').fadeIn('slow').html('<img src="<?php echo plugins_url( 'tick.png', __FILE__ ); ?>">');
					   }
					});
					$.post(ajaxurl, data, function(response){});
			    }
			});
		});
		</script>
	<?php
	}

	add_action( 'wp_ajax_mq_wpbe_action', 'mq_wpbe_price_edit' );

	function mq_wpbe_price_edit(){
		global $wpdb;
		$product_id = $_POST['product_id'];
		if(!empty($_POST['product_sale_price'])){
			update_post_meta( $product_id, '_regular_price', $_POST['product_regular_price']);
			update_post_meta( $product_id, '_sale_price', $_POST['product_sale_price']);
			update_post_meta( $product_id, '_price', $_POST['product_sale_price']);
		}else{
			update_post_meta( $product_id, '_regular_price', $_POST['product_regular_price'] );
			update_post_meta( $product_id, '_sale_price', $_POST['product_sale_price'] );
			update_post_meta( $product_id, '_price', $_POST['product_regular_price']);
		}
		//update frontend table
		update_option( 'mq_woocommerce_bulk_edit_option', $_POST['frontend_table'] );
		die();
	}

	function mq_woocommerce_bulk_edit_option_style(){
    	wp_enqueue_style('my-admin-theme', plugins_url('style.css', __FILE__));
	}

	//styling
	add_action( 'admin_enqueue_scripts', 'mq_woocommerce_bulk_edit_option_style' );
	
	//add table option for frontview
	add_option( 'mq_woocommerce_bulk_edit_option', 'HTML Pricing Table Not yet Set', '', 'yes' );
	
	//add shortcode
	function mq_woocommerce_bulk_edit_shortcode( $atts ){
		//return get_option( 'mq_woocommerce_bulk_edit_option' );
		$string_output .= get_option( 'mq_woocommerce_bulk_edit_option' );
		return $string_output;
	}
	add_shortcode( 'mq_wpbe', 'mq_woocommerce_bulk_edit_shortcode' );

	//loading Text Domain For Localization
	function mq_wpbe_init() {
		load_plugin_textdomain( 'mq-woocommerce-products-price-bulk-edit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	add_action('admin_init', 'mq_wpbe_init');

}//end of if woocommerce is active

<?php
function pmwi_pmxi_after_xml_import($import_id){
	// If there is only one row in the file for a particular product, post it as a simple product.
	$import = new PMXI_Import_Record();
	$import->getBy('id', $import_id);
	if ( ! $import->isEmpty() and $import->options['make_simple_product'] and $import->options['custom_type'] == 'product' and $import->options['is_update_product_type']){
		$postList = new PMXI_Post_List();														
		$posts = $postList->getBy(array('import_id' => $import_id));
		if ( ! $posts->isEmpty() ):							
			foreach ($posts as $ipost) {
				$product = get_post($ipost['post_id']); 								
				if ($product->post_type == 'product'){
					$children = get_posts( array(
						'post_parent' 	=> $ipost['post_id'],
						'posts_per_page'=> -1,
						'post_type' 	=> 'product_variation',
						'fields' 		=> 'ids',
						'post_status'	=> 'publish'
					) );					
					
					$product_terms = wp_get_object_terms($ipost['post_id'], 'product_type');
					$is_variable = false;
					if( ! empty($product_terms)){
						if( ! is_wp_error( $product_terms )){					    	
					    	foreach($product_terms as $term){
					    		if ( $term->slug == 'variable' ){
					    			$is_variable = true;
					    			break;
					    		}
					    	}
					    }
					}

					if ($is_variable){
						$attributes = get_post_meta( $ipost['post_id'], '_product_attributes', true);

						if ( empty($children) or empty($attributes)){
							wp_set_object_terms( $ipost['post_id'], 'simple', 'product_type' );

							update_post_meta( $ipost['post_id'], '_regular_price', $tmp = get_post_meta( $ipost['post_id'], '_regular_price_tmp', true) );
							delete_post_meta( $ipost['post_id'], '_regular_price_tmp' );

							update_post_meta( $ipost['post_id'], '_sale_price', $tmp = get_post_meta( $ipost['post_id'], '_sale_price_tmp', true ) );
							delete_post_meta( $ipost['post_id'], '_sale_price_tmp' );

							update_post_meta( $ipost['post_id'], 'pmxi_wholesale_price', $tmp = get_post_meta( $ipost['post_id'], 'pmxi_wholesale_price_tmp', true ) );
							delete_post_meta( $ipost['post_id'], 'pmxi_wholesale_price_tmp' );

							update_post_meta( $ipost['post_id'], '_sale_price_dates_from', $tmp = get_post_meta( $ipost['post_id'], '_sale_price_dates_from_tmp', true ) );
							delete_post_meta( $ipost['post_id'], '_sale_price_dates_from_tmp' );

							update_post_meta( $ipost['post_id'], '_sale_price_dates_to', $tmp = get_post_meta( $ipost['post_id'], '_sale_price_dates_to_tmp', true ) );
							delete_post_meta( $ipost['post_id'], '_sale_price_dates_to_tmp' );

							update_post_meta( $ipost['post_id'], '_price', $tmp = get_post_meta( $ipost['post_id'], '_price_tmp', true ) );			
							delete_post_meta( $ipost['post_id'], '_price_tmp' );			
						}
					}
				}
			}
		endif;
	}
}
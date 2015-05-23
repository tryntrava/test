<?php

class PMWI_Import_Record extends PMWI_Model_Record {		

	/**
	 * Associative array of data which will be automatically available as variables when template is rendered
	 * @var array
	 */
	public $data = array();

	public $options = array();

	/**
	 * Initialize model instance
	 * @param array[optional] $data Array of record data to initialize object with
	 */
	public function __construct($data = array()) {
		parent::__construct($data);
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'imports');
	}	
	
	/**
	 * Perform import operation
	 * @param string $xml XML string to import
	 * @param callback[optional] $logger Method where progress messages are submmitted
	 * @return PMWI_Import_Record
	 * @chainable
	 */
	public function parse($parsing_data = array()) { //$import, $count, $xml, $logger = NULL, $chunk = false, $xpath_prefix = ""

		extract($parsing_data);

		if ($import->options['custom_type'] != 'product') return;

		add_filter('user_has_cap', array($this, '_filter_has_cap_unfiltered_html')); kses_init(); // do not perform special filtering for imported content
		
		$this->options = $import->options;

		$cxpath = $xpath_prefix . $import->xpath;

		$this->data = array();
		$records = array();
		$tmp_files = array();

		$chunk == 1 and $logger and call_user_func($logger, __('Composing product data...', 'pmxi_plugin'));

		// Composing product types
		if ($import->options['is_multiple_product_type'] != 'yes' and "" != $import->options['single_product_type']){
			$this->data['product_types'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_type'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_types'] = array_fill(0, $count, $import->options['multiple_product_type']);
		}

		// Composing product is Virtual									
		if ($import->options['is_product_virtual'] == 'xpath' and "" != $import->options['single_product_virtual']){
			$this->data['product_virtual'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_virtual'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_virtual'] = array_fill(0, $count, $import->options['is_product_virtual']);
		}

		// Composing product is Downloadable									
		if ($import->options['is_product_downloadable'] == 'xpath' and "" != $import->options['single_product_downloadable']){
			$this->data['product_downloadable'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_downloadable'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_downloadable'] = array_fill(0, $count, $import->options['is_product_downloadable']);
		}

		// Composing product is Variable Enabled									
		if ($import->options['is_product_enabled'] == 'xpath' and "" != $import->options['single_product_enabled']){
			$this->data['product_enabled'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_enabled'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_enabled'] = array_fill(0, $count, $import->options['is_product_enabled']);
		}

		// Composing product is Featured									
		if ($import->options['is_product_featured'] == 'xpath' and "" != $import->options['single_product_featured']){
			$this->data['product_featured'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_featured'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_featured'] = array_fill(0, $count, $import->options['is_product_featured']);
		}

		// Composing product is Visibility									
		if ($import->options['is_product_visibility'] == 'xpath' and "" != $import->options['single_product_visibility']){
			$this->data['product_visibility'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_visibility'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_visibility'] = array_fill(0, $count, $import->options['is_product_visibility']);
		}

		if ("" != $import->options['single_product_sku']){
			$this->data['product_sku'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_sku'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sku'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_url']){
			$this->data['product_url'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_url'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_url'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_button_text']){
			$this->data['product_button_text'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_button_text'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_button_text'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_regular_price']){
			$this->data['product_regular_price'] = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['single_product_regular_price'], $file)->parse($records)); $tmp_files[] = $file;			
		}
		else{
			$count and $this->data['product_regular_price'] = array_fill(0, $count, "");
		}

		if ($import->options['is_regular_price_shedule'] and "" != $import->options['single_sale_price_dates_from']){
			$this->data['product_sale_price_dates_from'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_sale_price_dates_from'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price_dates_from'] = array_fill(0, $count, "");
		}

		if ($import->options['is_regular_price_shedule'] and "" != $import->options['single_sale_price_dates_to']){
			$this->data['product_sale_price_dates_to'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_sale_price_dates_to'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price_dates_to'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_sale_price']){
			$this->data['product_sale_price'] = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['single_product_sale_price'], $file)->parse($records)); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_sale_price'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_whosale_price']){
			$this->data['product_whosale_price'] = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['single_product_whosale_price'], $file)->parse($records)); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_whosale_price'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_files']){
			$this->data['product_files'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_files'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_files'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_files_names']){
			$this->data['product_files_names'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_files_names'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_files_names'] = array_fill(0, $count, "");
		}		

		if ("" != $import->options['single_product_download_limit']){
			$this->data['product_download_limit'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_download_limit'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_limit'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_download_expiry']){
			$this->data['product_download_expiry'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_download_expiry'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_expiry'] = array_fill(0, $count, "");
		}

		if ("" != $import->options['single_product_download_type']){
			$this->data['product_download_type'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_download_type'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_download_type'] = array_fill(0, $count, "");
		}
		
		// Composing product Tax Status									
		if ($import->options['is_multiple_product_tax_status'] != 'yes' and "" != $import->options['single_product_tax_status']){
			$this->data['product_tax_status'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_tax_status'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_tax_status'] = array_fill(0, $count, $import->options['multiple_product_tax_status']);
		}

		// Composing product Tax Class									
		if ($import->options['is_multiple_product_tax_class'] != 'yes' and "" != $import->options['single_product_tax_class']){
			$this->data['product_tax_class'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_tax_class'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_tax_class'] = array_fill(0, $count, $import->options['multiple_product_tax_class']);
		}

		// Composing product Manage stock?								
		if ($import->options['is_product_manage_stock'] == 'xpath' and "" != $import->options['single_product_manage_stock']){
			$this->data['product_manage_stock'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_manage_stock'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_manage_stock'] = array_fill(0, $count, $import->options['is_product_manage_stock']);
		}

		if ("" != $import->options['single_product_stock_qty']){
			$this->data['product_stock_qty'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_stock_qty'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_stock_qty'] = array_fill(0, $count, "");
		}					

		// Composing product Stock status							
		if ($import->options['product_stock_status'] == 'xpath' and "" != $import->options['single_product_stock_status']){
			$this->data['product_stock_status'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_stock_status'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_stock_status'] = array_fill(0, $count, $import->options['product_stock_status']);
		}

		// Composing product Allow Backorders?						
		if ($import->options['product_allow_backorders'] == 'xpath' and "" != $import->options['single_product_allow_backorders']){
			$this->data['product_allow_backorders'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_allow_backorders'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_allow_backorders'] = array_fill(0, $count, $import->options['product_allow_backorders']);
		}

		// Composing product Sold Individually?					
		if ($import->options['product_sold_individually'] == 'xpath' and "" != $import->options['single_product_sold_individually']){
			$this->data['product_sold_individually'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_sold_individually'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_sold_individually'] = array_fill(0, $count, $import->options['product_sold_individually']);
		}

		if ("" != $import->options['single_product_weight']){
			$this->data['product_weight'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_weight'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_weight'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_length']){
			$this->data['product_length'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_length'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_length'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_width']){
			$this->data['product_width'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_width'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_width'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_height']){
			$this->data['product_height'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_height'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_height'] = array_fill(0, $count, "");
		}

		// Composing product Shipping Class				
		if ($import->options['is_multiple_product_shipping_class'] != 'yes' and "" != $import->options['single_product_shipping_class']){
			$this->data['product_shipping_class'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_shipping_class'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_shipping_class'] = array_fill(0, $count, $import->options['multiple_product_shipping_class']);
		}

		if ("" != $import->options['single_product_up_sells']){
			$this->data['product_up_sells'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_up_sells'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_up_sells'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_cross_sells']){
			$this->data['product_cross_sells'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_cross_sells'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_cross_sells'] = array_fill(0, $count, "");
		}

		if ($import->options['is_multiple_grouping_product'] != 'yes'){
			
			if ($import->options['grouping_indicator'] == 'xpath'){
				
				if ("" != $import->options['single_grouping_product']){
					$this->data['product_grouping_parent'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_grouping_product'], $file)->parse($records); $tmp_files[] = $file;						
				}
				else{
					$count and $this->data['product_grouping_parent'] = array_fill(0, $count, $import->options['multiple_grouping_product']);
				}

			}
			else{
				if ("" != $import->options['custom_grouping_indicator_name'] and "" != $import->options['custom_grouping_indicator_value'] ){
					$this->data['custom_grouping_indicator_name'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_grouping_indicator_name'], $file)->parse($records); $tmp_files[] = $file;	
					$this->data['custom_grouping_indicator_value'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_grouping_indicator_value'], $file)->parse($records); $tmp_files[] = $file;	
				}
				else{
					$count and $this->data['custom_grouping_indicator_name'] = array_fill(0, $count, "");
					$count and $this->data['custom_grouping_indicator_value'] = array_fill(0, $count, "");
				}
			}		
		}
		else{
			$count and $this->data['product_grouping_parent'] = array_fill(0, $count, $import->options['multiple_grouping_product']);
		}

		if ("" != $import->options['single_product_purchase_note']){
			$this->data['product_purchase_note'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_purchase_note'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_purchase_note'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_menu_order']){
			$this->data['product_menu_order'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_menu_order'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['product_menu_order'] = array_fill(0, $count, "");
		}
		
		// Composing product Enable reviews		
		if ($import->options['is_product_enable_reviews'] == 'xpath' and "" != $import->options['single_product_enable_reviews']){
			$this->data['product_enable_reviews'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_enable_reviews'], $file)->parse($records); $tmp_files[] = $file;						
		}
		else{
			$count and $this->data['product_enable_reviews'] = array_fill(0, $count, $import->options['is_product_enable_reviews']);
		}

		if ("" != $import->options['single_product_id']){
			$this->data['single_product_ID'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_ID'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_parent_id']){
			$this->data['single_product_parent_ID'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_parent_id'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_parent_ID'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_id_first_is_parent_id']){
			$this->data['single_product_id_first_is_parent_ID'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id_first_is_parent_id'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_id_first_is_parent_ID'] = array_fill(0, $count, "");
		}		
		if ("" != $import->options['single_product_id_first_is_parent_title']){
			$this->data['single_product_id_first_is_parent_title'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id_first_is_parent_title'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_id_first_is_parent_title'] = array_fill(0, $count, "");
		}
		if ("" != $import->options['single_product_id_first_is_variation']){
			$this->data['single_product_id_first_is_variation'] = XmlImportParser::factory($xml, $cxpath, $import->options['single_product_id_first_is_variation'], $file)->parse($records); $tmp_files[] = $file;
		}
		else{
			$count and $this->data['single_product_id_first_is_variation'] = array_fill(0, $count, "");
		}

		if ($import->options['matching_parent'] != "auto") {					
			switch ($import->options['matching_parent']) {
				case 'first_is_parent_id':
					$this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_parent_ID'];
					break;
				case 'first_is_parent_title':
					$this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_parent_title'];
					break;
				case 'first_is_variation':
					$this->data['single_product_parent_ID'] = $this->data['single_product_ID'] = $this->data['single_product_id_first_is_variation'];
					break;						
			}					
		}
		
		if ($import->options['matching_parent'] == 'manual' and $import->options['parent_indicator'] == "custom field"){
			if ("" != $import->options['custom_parent_indicator_name']){
				$this->data['custom_parent_indicator_name'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_parent_indicator_name'], $file)->parse($records); $tmp_files[] = $file;
			}
			else{
				$count and $this->data['custom_parent_indicator_name'] = array_fill(0, $count, "");
			}
			if ("" != $import->options['custom_parent_indicator_value']){
				$this->data['custom_parent_indicator_value'] = XmlImportParser::factory($xml, $cxpath, $import->options['custom_parent_indicator_value'], $file)->parse($records); $tmp_files[] = $file;
			}
			else{
				$count and $this->data['custom_parent_indicator_value'] = array_fill(0, $count, "");
			}			
		}
		
		// Composing variations attributes					
		$chunk == 1 and $logger and call_user_func($logger, __('Composing variations attributes...', 'pmxi_plugin'));
		$attribute_keys = array(); 
		$attribute_values = array();	
		$attribute_in_variation = array(); 
		$attribute_is_visible = array();			
		$attribute_is_taxonomy = array();	
		$attribute_create_taxonomy_terms = array();		
				
		if (!empty($import->options['attribute_name'][0])){			
			foreach ($import->options['attribute_name'] as $j => $attribute_name) { if ($attribute_name == "") continue;								    											
				$attribute_keys[$j]   = XmlImportParser::factory($xml, $cxpath, $attribute_name, $file)->parse($records); $tmp_files[] = $file;
				$attribute_values[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['attribute_value'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_in_variation[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['in_variations'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_is_visible[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['is_visible'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_is_taxonomy[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['is_taxonomy'][$j], $file)->parse($records); $tmp_files[] = $file;
				$attribute_create_taxonomy_terms[$j] = XmlImportParser::factory($xml, $cxpath, $import->options['create_taxonomy_in_not_exists'][$j], $file)->parse($records); $tmp_files[] = $file;				
			}			
		}					
		
		// serialized attributes for product variations
		$this->data['serialized_attributes'] = array();
		if (!empty($attribute_keys)){
			foreach ($attribute_keys as $j => $attribute_name) {
							
				$this->data['serialized_attributes'][] = array(
					'names' => $attribute_name,
					'value' => $attribute_values[$j],
					'is_visible' => $attribute_is_visible[$j],
					'in_variation' => $attribute_in_variation[$j],
					'in_taxonomy' => $attribute_is_taxonomy[$j],
					'is_create_taxonomy_terms' => $attribute_create_taxonomy_terms[$j]
				);						

			}
		} 						

		remove_filter('user_has_cap', array($this, '_filter_has_cap_unfiltered_html')); kses_init(); // return any filtering rules back if they has been disabled for import procedure
		
		foreach ($tmp_files as $file) { // remove all temporary files created
			unlink($file);
		}

		return $this->data;
	}		

	public function filtering($var){
		return ("" == $var) ? false : true;
	}

	public function import($importData = array()){ //$pid, $i, $import, $articleData, $xml, $is_cron = false, $xpath_prefix = ""

		extract($importData);

		if ($import->options['custom_type'] != 'product') return;		

		$cxpath = $xpath_prefix . $import->xpath;		

		global $woocommerce;		

		extract($this->data);

		// Add any default post meta
		add_post_meta( $pid, 'total_sales', '0', true );

		// Get types
		$product_type 		= empty( $product_types[$i] ) ? 'simple' : sanitize_title( stripslashes( $product_types[$i] ) );

		if ( ! $import->options['is_update_product_type'] and ! empty($articleData['ID']) ){
			
			$product = get_product($pid);
			$product_type = $product->product_type;

			/*if ( strpos($articleData['post_title'], 'Variation') === 0 ) { 
				$this->wpdb->update( $this->wpdb->posts, array('post_type' => 'product_variation', array('ID' => $articleData['ID']) ));			
				$product_type = 'variable';
			}*/

		}

		$is_downloadable 	= $product_downloadable[$i];
		$is_virtual 		= $product_virtual[$i];
		$is_featured 		= $product_featured[$i];

		if ( class_exists('woocommerce_wholesale_pricing') ):			
			if ($product_type == "simple") add_action( 'woocommerce_process_product_meta_simple', array( &$this, 'process_product_meta' ), 2, 1 );
			if ($product_type == "variable") add_action( 'woocommerce_save_product_variation', array( &$this, 'process_product_meta_variable' ), 1000, 1 );
		endif;

		$existing_meta_keys = array();
		foreach (get_post_meta($pid, '') as $cur_meta_key => $cur_meta_val) $existing_meta_keys[] = $cur_meta_key;
				
		// Product type + Downloadable/Virtual
		if (empty($articleData['ID']) or $import->options['is_update_product_type']) wp_set_object_terms( $pid, $product_type, 'product_type' );
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_downloadable')) update_post_meta( $pid, '_downloadable', ($is_downloadable == "yes") ? 'yes' : 'no' );
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_virtual')) update_post_meta( $pid, '_virtual', ($is_virtual == "yes") ? 'yes' : 'no' );						

		// Update post meta
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_regular_price')) update_post_meta( $pid, '_regular_price', stripslashes( $product_regular_price[$i] ) );
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price')) update_post_meta( $pid, '_sale_price', stripslashes( $product_sale_price[$i] ) );
		if ( class_exists('woocommerce_wholesale_pricing') and (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, 'wholesale_price'))) update_post_meta( $pid, 'pmxi_wholesale_price', stripslashes( $product_whosale_price[$i] ) );
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_tax_status')) update_post_meta( $pid, '_tax_status', stripslashes( $product_tax_status[$i] ) );
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_tax_class')) update_post_meta( $pid, '_tax_class', stripslashes( $product_tax_class[$i] ) );			
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_visibility')) update_post_meta( $pid, '_visibility', stripslashes( $product_visibility[$i] ) );			
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_purchase_note')) update_post_meta( $pid, '_purchase_note', stripslashes( $product_purchase_note[$i] ) );
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_featured')) update_post_meta( $pid, '_featured', ($is_featured == "yes") ? 'yes' : 'no' );

		// Dimensions
		if ( $is_virtual == 'no' ) {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_weight')) update_post_meta( $pid, '_weight', stripslashes( $product_weight[$i] ) );
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_length')) update_post_meta( $pid, '_length', stripslashes( $product_length[$i] ) );
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_width')) update_post_meta( $pid, '_width', stripslashes( $product_width[$i] ) );
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_height')) update_post_meta( $pid, '_height', stripslashes( $product_height[$i] ) );
		} else {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_weight')) update_post_meta( $pid, '_weight', '' );
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_length')) update_post_meta( $pid, '_length', '' );
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_width')) update_post_meta( $pid, '_width', '' );
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_height')) update_post_meta( $pid, '_height', '' );
		}		

		$this->wpdb->update( $this->wpdb->posts, array('comment_status' => ($product_enable_reviews[$i] == 'yes') ? 'open' : 'closed' ), array('ID' => $pid));
		// update menu order		
		if ($import->options['is_update_menu_order']) $this->wpdb->update( $this->wpdb->posts, array('menu_order' => ($product_menu_order[$i] != '') ? (int) $product_menu_order[$i] : 0 ), array('ID' => $pid));

		// Save shipping class
		if ( pmwi_is_update_taxonomy($articleData, $import->options, 'product_shipping_class') ){
			$pshipping_class = is_numeric($product_shipping_class[$i]) && $product_shipping_class[$i] > 0 && $product_type != 'external' ? absint( $product_shipping_class[$i] ) : $product_shipping_class[$i];
			if ($pshipping_class == "-1")
				wp_set_object_terms( $pid, NULL, 'product_shipping_class');
			else
				wp_set_object_terms( $pid, $pshipping_class, 'product_shipping_class');
		}

		// Unique SKU
		$sku				= get_post_meta($pid, '_sku', true);
		$new_sku 			= esc_html( trim( stripslashes( $product_sku[$i] ) ) );
		
		if ( $new_sku == '' and $import->options['disable_auto_sku_generation'] ) {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sku')) 				
					update_post_meta( $pid, '_sku', '' );
		}
		elseif ( $new_sku == '' and ! $import->options['disable_auto_sku_generation'] ) {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sku')){				
				$unique_keys = XmlImportParser::factory($xml, $cxpath, $import->options['unique_key'], $file)->parse(); $tmp_files[] = $file;
				foreach ($tmp_files as $file) { // remove all temporary files created
					unlink($file);
				}
				$new_sku = substr(md5($unique_keys[$i]), 0, 12);
			}
		}
		if ( $new_sku != '' and $new_sku !== $sku ) {
			if ( ! empty( $new_sku ) ) {
				if ( ! $import->options['disable_sku_matching'] and 
					$this->wpdb->get_var( $this->wpdb->prepare("
						SELECT ".$this->wpdb->posts.".ID
					    FROM ".$this->wpdb->posts."
					    LEFT JOIN ".$this->wpdb->postmeta." ON (".$this->wpdb->posts.".ID = ".$this->wpdb->postmeta.".post_id)
					    WHERE ".$this->wpdb->posts.".post_type = 'product'
					    AND ".$this->wpdb->posts.".post_status = 'publish'
					    AND ".$this->wpdb->postmeta.".meta_key = '_sku' AND ".$this->wpdb->postmeta.".meta_value = '%s'
					 ", $new_sku ) )
					) {
					$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Product SKU must be unique.', 'pmxi_plugin')));
									
				} else {
					if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sku')) update_post_meta( $pid, '_sku', $new_sku );
				}
			} else {
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sku')) update_post_meta( $pid, '_sku', '' );
			}
		}

		// Save Attributes
		$attributes = array();

		$is_variation_attributes_defined = false;

		if ( $import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes']) or empty($articleData['ID'])): // Update Product Attributes		

			$is_update_attributes = true;

			if ( !empty($serialized_attributes) ) {
				
				$attribute_position = 0;

				$attr_names = array();

				foreach ($serialized_attributes as $anum => $attr_data) {	$attr_name = strtolower($attr_data['names'][$i]);					

					if (empty($attr_name) or in_array($attr_name, $attr_names)) continue;

					$attr_names[] = $attr_name;

					$is_visible 	= intval( $attr_data['is_visible'][$i] );
					$is_variation 	= intval( $attr_data['in_variation'][$i] );
					$is_taxonomy 	= intval( $attr_data['in_taxonomy'][$i] );

					// Update only these Attributes, leave the rest alone
					if ($import->options['update_all_data'] == "no" and $import->options['update_attributes_logic'] == 'only'){
						if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
							if ( ! in_array( ( ($is_taxonomy) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))){ 
								$attribute_position++;
								continue;
							}
						}
						else {
							$is_update_attributes = false;
							break;
						}
					}

					// Leave these attributes alone, update all other Attributes
					if ($import->options['update_all_data'] == "no" and $import->options['update_attributes_logic'] == 'all_except'){
						if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
							if ( in_array( ( ($is_taxonomy) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))){ 
								$attribute_position++;
								continue;
							}
						}
					}

					if ( $is_taxonomy ) {										

						if ( isset( $attr_data['value'][$i] ) ) {
					 		
					 		$values = array_map( 'stripslashes', array_map( 'strip_tags', explode( '|', $attr_data['value'][$i] ) ) );

						 	// Remove empty items in the array
						 	$values = array_filter( $values, array($this, "filtering") );			

						 	if (intval($attr_data['is_create_taxonomy_terms'][$i])) $this->create_taxonomy($attr_name, $logger);			 						 							

						 	if ( ! empty($values) and taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) )){

						 		$attr_values = array();						 								 		
						 			
						 		foreach ($values as $key => $value) {

						 			$term = term_exists($value, wc_attribute_taxonomy_name( $attr_name ), 0);	

						 			if ( empty($term) and !is_wp_error($term) ){																																
										$term = term_exists(htmlspecialchars($value), wc_attribute_taxonomy_name( $attr_name ), 0);	
										if ( empty($term) and !is_wp_error($term) and intval($attr_data['is_create_taxonomy_terms'][$i])){		
											
											$term = wp_insert_term(
												$value, // the term 
											  	wc_attribute_taxonomy_name( $attr_name ) // the taxonomy										  	
											);													
										}
									}
									if ( ! is_wp_error($term) )												
										$attr_values[] = (int) $term['term_id']; 

						 		}

						 		$values = $attr_values;
						 		$values = array_map( 'intval', $values );
								$values = array_unique( $values );
						 	} 
						 	else $values = array(); 					 							 	

					 	} 				 				 						 	
					 	
				 		// Update post terms
				 		if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))			 			
				 			wp_set_object_terms( $pid, $values, wc_attribute_taxonomy_name( $attr_name ) );			 		
				 		
				 		if ( !empty($values) ) {									 			
					 		// Add attribute to array, but don't set values
					 		$attributes[ sanitize_title(wc_attribute_taxonomy_name( $attr_name )) ] = array(
						 		'name' 			=> wc_attribute_taxonomy_name( $attr_name ),
						 		'value' 		=> '',
						 		'position' 		=> $attribute_position,
						 		'is_visible' 	=> $is_visible,
						 		'is_variation' 	=> $is_variation,
						 		'is_taxonomy' 	=> 1,
						 		'is_create_taxonomy_terms' => (!empty($attr_data['is_create_taxonomy_terms'][$i])) ? 1 : 0
						 	);

					 	}

				 	} else {

				 		if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))
				 			wp_set_object_terms( $pid, NULL, wc_attribute_taxonomy_name( $attr_name ) );			 		

				 		if (!empty($attr_data['value'][$i])){

					 		// Custom attribute - Add attribute to array and set the values
						 	$attributes[ sanitize_title( $attr_name ) ] = array(
						 		'name' 			=> sanitize_text_field( $attr_name ),
						 		'value' 		=> $attr_data['value'][$i],
						 		'position' 		=> $attribute_position,
						 		'is_visible' 	=> $is_visible,
						 		'is_variation' 	=> $is_variation,
						 		'is_taxonomy' 	=> 0
						 	);
						}

				 	}

				 	if ( $is_variation and ! empty($attr_data['value'][$i]) ) {
				 		$is_variation_attributes_defined = true;
				 	}

				 	$attribute_position++;
				}							
			}						
			
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_product_attributes') and $is_update_attributes) {
				
				$current_product_attributes = get_post_meta($pid, '_product_attributes', true);

				update_post_meta( $pid, '_product_attributes', (( ! empty($current_product_attributes)) ? array_merge($current_product_attributes, $attributes) : $attributes) );	
			}

		endif;	// is update attributes

		// Sales and prices
		if ( ! in_array( $product_type, array( 'grouped' ) ) ) {

			$date_from = isset( $product_sale_price_dates_from[$i] ) ? $product_sale_price_dates_from[$i] : '';
			$date_to   = isset( $product_sale_price_dates_to[$i] ) ? $product_sale_price_dates_to[$i] : '';

			// Dates
			if ( $date_from ){
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', strtotime( $date_from ) );
			}
			else{
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', '' );
			}

			if ( $date_to ){
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_to')) update_post_meta( $pid, '_sale_price_dates_to', strtotime( $date_to ) );
			}
			else{
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_to')) update_post_meta( $pid, '_sale_price_dates_to', '' );
			}

			if ( $date_to && ! $date_from ){
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );
			}

			// Update price if on sale
			if ( $product_sale_price[$i] != '' && $date_to == '' && $date_from == '' ){
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes( $product_sale_price[$i] ) );
			}
			else{
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes( $product_regular_price[$i] ) );									
			}

			if ( $product_sale_price[$i] != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ){
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes($product_sale_price[$i]) );				
			}

			if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_price')) update_post_meta( $pid, '_price', stripslashes($product_regular_price[$i]) );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_from')) update_post_meta( $pid, '_sale_price_dates_from', '');
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_to')) update_post_meta( $pid, '_sale_price_dates_to', '');
			}
		}	

		// Variable and grouped products have no prices
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_regular_price')){ 
			update_post_meta( $pid, '_regular_price_tmp', $tmp = get_post_meta( $pid, '_regular_price', true) );			
		}
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price')){ 
			update_post_meta( $pid, '_sale_price_tmp', $tmp = get_post_meta( $pid, '_sale_price', true ) );			
		}
		if ( class_exists('woocommerce_wholesale_pricing') and (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, 'wholesale_price'))){ 
			update_post_meta( $pid, 'pmxi_wholesale_price_tmp', $tmp = get_post_meta( $pid, 'pmxi_wholesale_price', true ) );			
		}
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_from')){ 
			update_post_meta( $pid, '_sale_price_dates_from_tmp', $tmp = get_post_meta( $pid, '_sale_price_dates_from', true ) );			
		}
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_to')){ 
			update_post_meta( $pid, '_sale_price_dates_to_tmp', $tmp = get_post_meta( $pid, '_sale_price_dates_to', true ) );			
		}
		if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_price')){ 
			update_post_meta( $pid, '_price_tmp', $tmp = get_post_meta( $pid, '_price', true ) );						
		}	

		if (in_array( $product_type, array( 'simple', 'external' ) )) { 
			if ($import->options['is_multiple_grouping_product'] != 'yes'){
				if ($import->options['grouping_indicator'] == 'xpath' and ! is_numeric($product_grouping_parent[$i])){
					$dpost = pmxi_findDuplicates(array(
						'post_type' => 'product',
						'ID' => $pid,
						'post_parent' => $articleData['post_parent'],
						'post_title' => $product_grouping_parent[$i]
					));				
					if (!empty($dpost))
						$product_grouping_parent[$i] = $dpost[0];	
					else				
						$product_grouping_parent[$i] = 0;
				}
				elseif ($import->options['grouping_indicator'] != 'xpath'){
					$dpost = pmxi_findDuplicates($articleData, $custom_grouping_indicator_name[$i], $custom_grouping_indicator_value[$i], 'custom field');
					if (!empty($dpost))
						$product_grouping_parent[$i] = array_shift($dpost);
					else				
						$product_grouping_parent[$i] = 0;
				}
			}

			if ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0){

				$this->wpdb->update( $this->wpdb->posts, array('post_parent' => absint( $product_grouping_parent[$i] ) ), array('ID' => $pid));
				
			}
		}

		// Update parent if grouped so price sorting works and stays in sync with the cheapest child
		if ( $product_type == 'grouped' || ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0)) {

			$clear_parent_ids = array();													

			if ( $product_type == 'grouped' )
				$clear_parent_ids[] = $pid;		

			if ( "" != $product_grouping_parent[$i] and absint($product_grouping_parent[$i]) > 0 )
				$clear_parent_ids[] = absint( $product_grouping_parent[$i] );					

			if ( $clear_parent_ids ) {
				foreach( $clear_parent_ids as $clear_id ) {

					$children_by_price = get_posts( array(
						'post_parent' 	=> $clear_id,
						'orderby' 		=> 'meta_value_num',
						'order'			=> 'asc',
						'meta_key'		=> '_price',
						'posts_per_page'=> 1,
						'post_type' 	=> 'product',
						'fields' 		=> 'ids'
					) );
					if ( $children_by_price ) {
						foreach ( $children_by_price as $child ) {
							$child_price = get_post_meta( $child, '_price', true );
							update_post_meta( $clear_id, '_price', $child_price );
						}
					}

					// Clear cache/transients
					wc_delete_product_transients( $clear_id );
				}
			}
		}

		// Sold Individuall
		if ( "yes" == $product_sold_individually[$i] ) {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sold_individually')) update_post_meta( $pid, '_sold_individually', 'yes' );
		} else {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sold_individually')) update_post_meta( $pid, '_sold_individually', '' );
		}
		
		// Stock Data
		if ( strtolower($product_manage_stock[$i]) == 'yes' ) {

			if ( $product_type == 'grouped' ) {

				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', '' );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_manage_stock')) update_post_meta( $pid, '_manage_stock', 'no' );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', 'no' );

			} elseif ( $product_type == 'external' ) {

				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', 'instock' );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', '' );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_manage_stock')) update_post_meta( $pid, '_manage_stock', 'no' );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', 'no' );

			} elseif ( ! empty( $product_manage_stock[$i] ) ) {

				// Manage stock
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', (int) $product_stock_qty[$i] );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', stripslashes( $product_allow_backorders[$i] ) );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_manage_stock'))	update_post_meta( $pid, '_manage_stock', 'yes' );					

				// Check stock level
				if ( $product_type !== 'variable' && $product_allow_backorders[$i] == 'no' && (int) $product_stock_qty[$i] < 1 ){
					if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', 'outofstock' );
				}

			} else {

				// Don't manage stock
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock')) update_post_meta( $pid, '_stock', '' );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_backorders')) update_post_meta( $pid, '_backorders', stripslashes( $product_allow_backorders[$i] ) );
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_manage_stock')) update_post_meta( $pid, '_manage_stock', 'no' );

			}

		} else {

			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_stock_status')) update_post_meta( $pid, '_stock_status', stripslashes( $product_stock_status[$i] ) );

		}

		// Upsells
		if ( !empty( $product_up_sells[$i] ) ) {
			$upsells = array();
			$ids = array_filter(explode(',', $product_up_sells[$i]), 'trim');
			foreach ( $ids as $id ){								
				$args = array(
					'post_type' => 'product',
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $id,						
						)
					)
				);			
				$query = new WP_Query( $args );
				
				if ( $query->have_posts() ) $upsells[] = $query->post->ID;

				wp_reset_postdata();
			}								

			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_upsell_ids')) update_post_meta( $pid, '_upsell_ids', $upsells );
		} else {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_upsell_ids')) delete_post_meta( $pid, '_upsell_ids' );
		}

		// Cross sells
		if ( !empty( $product_cross_sells[$i] ) ) {
			$crosssells = array();
			$ids = array_filter(explode(',', $product_cross_sells[$i]), 'trim');
			foreach ( $ids as $id ){
				$args = array(
					'post_type' => 'product',
					'meta_query' => array(
						array(
							'key' => '_sku',
							'value' => $id,						
						)
					)
				);			
				$query = new WP_Query( $args );
				
				if ( $query->have_posts() ) $crosssells[] = $query->post->ID;

				wp_reset_postdata();
			}								

			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_crosssell_ids')) update_post_meta( $pid, '_crosssell_ids', $crosssells );
		} else {
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_crosssell_ids')) delete_post_meta( $pid, '_crosssell_ids' );
		}

		// Downloadable options
		if ( $is_downloadable == 'yes' ) {

			$_download_limit = absint( $product_download_limit[$i] );
			if ( ! $_download_limit )
				$_download_limit = ''; // 0 or blank = unlimited

			$_download_expiry = absint( $product_download_expiry[$i] );
			if ( ! $_download_expiry )
				$_download_expiry = ''; // 0 or blank = unlimited
			
			// file paths will be stored in an array keyed off md5(file path)
			if ( !empty( $product_files[$i] ) ) {
				$_file_paths = array();
				
				$file_paths = explode( $import->options['product_files_delim'] , $product_files[$i] );
				$file_names = explode( $import->options['product_files_names_delim'] , $product_files_names[$i] );

				foreach ( $file_paths as $fn => $file_path ) {
					$file_path = trim( $file_path );					
					$_file_paths[ md5( $file_path ) ] = array('name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)), 'file' => $file_path);
				}								

				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_downloadable_files')) update_post_meta( $pid, '_downloadable_files', $_file_paths );
			}
			if ( isset( $product_download_limit[$i] ) )
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_download_limit')) update_post_meta( $pid, '_download_limit', esc_attr( $_download_limit ) );
			if ( isset( $product_download_expiry[$i] ) )
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_download_expiry')) update_post_meta( $pid, '_download_expiry', esc_attr( $_download_expiry ) );
			if ( isset( $product_download_type[$i] ) )
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_download_type')) update_post_meta( $pid, '_download_type', esc_attr( $product_download_type[$i] ) );
		}



		// Product url
		if ( $product_type == 'external' ) {
			if ( isset( $product_url[$i] ) && $product_url[$i] ){				
				$this->auto_cloak_links($import, $product_url[$i]);				
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_product_url')) update_post_meta( $pid, '_product_url', esc_attr( $product_url[$i] ) );
			}
			if ( isset( $product_button_text[$i] ) && $product_button_text[$i] ){
				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_button_text')) update_post_meta( $pid, '_button_text', esc_attr( $product_button_text[$i] ) );
			}
		}	

		wc_delete_product_transients( $pid );													

		// VARIATIONS
		if ( $product_type == 'variable' and ! $import->options['link_all_variations'] and "xml" != $import->options['matching_parent'] and $is_variation_attributes_defined ) {			

			$first_is_parent = "yes";

			if ( in_array($import->options['matching_parent'], array("auto", "first_is_parent_id", "first_is_parent_title", "first_is_variation")) ){
								
				$list = new PMXI_Post_List();
				$postTable = $list->getTable();

				if ("manual" != $import->options['duplicate_matching']){
					$list->setColumns("$postTable.*")->getBy(array(
						'product_key =' => $single_product_parent_ID[$i],
						'import_id =' => $import->id,
					), "id ASC", 1, 1)->convertRecords();
					if ($list->count()) { // matching product found
						$product_parent_post = get_post($product_parent_post_id = $list[0]->post_id);					
					}
				}
				else{											

					if ($articleData['post_type'] == 'product'){

						$args = array(
							'post_type' => 'product_variation',
							'meta_query' => array(
								array(
									'key' => '_sku',
									'value' => get_post_meta($pid, '_sku', true),						
								)
							)
						);			
						$query = new WP_Query( $args );													

						if ( $query->have_posts() ){ 

							$duplicate_id = $query->post->ID;

							if ($duplicate_id) {				

								$product_parent_post = get_post($product_parent_post_id = $pid);															

								$pid = $duplicate_id;

								$this->duplicate_post_copy_post_meta_info($pid, $product_parent_post, $import->options, true);																
								
								update_post_meta( $product_parent_post_id, '_stock_tmp', $tmp = get_post_meta( $product_parent_post_id, '_stock', true) );
								if ( empty($import->options['set_parent_stock']) ) 
									update_post_meta( $product_parent_post_id, '_stock', '');
								update_post_meta( $product_parent_post_id, '_regular_price_tmp', $tmp = get_post_meta( $product_parent_post_id, '_regular_price', true) );
								update_post_meta( $product_parent_post_id, '_regular_price', '' );
								update_post_meta( $product_parent_post_id, '_price_tmp', $tmp = get_post_meta( $product_parent_post_id, '_price', true) );
								update_post_meta( $product_parent_post_id, '_price', '');						

							}	

						}
						else{							
							update_post_meta( $pid, '_stock_tmp', $tmp = get_post_meta( $pid, '_stock', true) );
							if ( empty($import->options['set_parent_stock']) ) 
								update_post_meta( $pid, '_stock', '');
							update_post_meta( $pid, '_regular_price_tmp', $tmp = get_post_meta( $pid, '_regular_price', true) );
							update_post_meta( $pid, '_regular_price', '' );
							update_post_meta( $pid, '_price_tmp', $tmp = get_post_meta( $pid, '_price', true) );
							update_post_meta( $pid, '_price', '');
						}

						wp_reset_postdata();
						
					}
					elseif ($articleData['post_type'] == 'product_variation'){
						$variation_post = get_post($pid);
						$product_parent_post = get_post($product_parent_post_id = $variation_post->parent_post);							
					}
					
				}												

				$first_is_parent = ( in_array($import->options['matching_parent'], array("auto", "first_is_parent_title")) ) ? "yes" : "no";
				
			}
			else {								
				// handle duplicates according to import settings
				if ($duplicates = pmxi_findDuplicates($articleData, $custom_parent_indicator_name[$i], $custom_parent_indicator_value[$i], $import->options['parent_indicator'])) {															
					$duplicate_id = array_shift($duplicates);
					if ($duplicate_id) {														
						$product_parent_post = get_post($product_parent_post_id = $duplicate_id);
					}						
				}
			}								

			if ( ! empty($product_parent_post_id) and ($product_parent_post_id != $pid or ($product_parent_post_id == $pid and $first_is_parent == "no")) ) {

				$create_new_variation = ($product_parent_post_id == $pid and $first_is_parent == "no") ? true : false;
				
				if ($create_new_variation){

					$postRecord = new PMXI_Post_Record();
					
					$postRecord->clear();
					
					// find corresponding article among previously imported
					$postRecord->getBy(array(
						'unique_key' => 'Variation ' . $new_sku,
						'import_id' => $import->id,
					));

					if ( ! $postRecord->isEmpty() ) 
						$pid = $postRecord->post_id;
					else 
						$pid = false;
						
				}

				$variable_enabled = ($product_enabled[$i] == "yes") ? 'yes' : 'no'; 

				$attributes = array(); 

				// Enabled or disabled
				$post_status = ( $variable_enabled == 'yes' ) ? 'publish' : 'private';

				// Generate a useful post title
				$variation_post_title = sprintf( __( 'Variation #%s of %s', 'woocommerce' ), absint( $pid ), esc_html( $product_parent_post_id ) );

				// Update or Add post							

					$variation = array(
						'post_title' 	=> $variation_post_title,
						'post_content' 	=> '',
						'post_status' 	=> $post_status,
						'post_parent' 	=> $product_parent_post_id,
						'post_type' 	=> 'product_variation'									
					);

				if ( ! $pid ) {

					if ($import->options['create_new_records']){
						
						$pid = ($import->options['is_fast_mode']) ? pmxi_insert_post($variation) : wp_insert_post( $variation );	

						if ($create_new_variation){
							
							$this->duplicate_post_copy_post_meta_info($pid, $product_parent_post, $import->options);

							// associate variation with import
							$postRecord->isEmpty() and $postRecord->set(array(
								'post_id' => $pid,
								'import_id' => $import->id,
								'unique_key' => 'Variation ' . $new_sku,
								'product_key' => ''
							))->insert();

							$postRecord->set(array('iteration' => $import->iteration))->update();

						}
						
					}

					//do_action( 'woocommerce_create_product_variation', $pid );

				} else {

					if ($create_new_variation) {
						
						$this->duplicate_post_copy_post_meta_info($pid, $product_parent_post, $import->options);											

						$postRecord->set(array('iteration' => $import->iteration))->update();

					}

					$this->wpdb->update( $this->wpdb->posts, $variation, array( 'ID' => $pid ) );				

				}				

				if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_tax_class')){
					if ( $product_tax_class[ $i ] !== 'parent' )
						update_post_meta( $pid, '_tax_class', sanitize_text_field( $product_tax_class[ $i ] ) );
					else
						delete_post_meta( $pid, '_tax_class' );
				}

				if ( $is_downloadable == 'yes' ) {
					update_post_meta( $pid, '_download_limit', sanitize_text_field( $product_download_limit[ $i ] ) );
					update_post_meta( $pid, '_download_expiry', sanitize_text_field( $product_download_expiry[ $i ] ) );
					update_post_meta( $pid, '_download_type', sanitize_text_field( $product_download_type[ $i ] ) );

					$_file_paths = array();
					
					if ( !empty($product_files[$i]) ) {
						$file_paths = explode( $import->options['product_files_delim'] , $product_files[$i] );
						$file_names = explode( $import->options['product_files_names_delim'] , $product_names[$i] );

						foreach ( $file_paths as $fn => $file_path ) {
							$file_path = sanitize_text_field( $file_path );							
							$_file_paths[ md5( $file_path ) ] = array('name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)), 'file' => $file_path);
						}
					}

					update_post_meta( $pid, '_downloadable_files', $_file_paths );

				} else {
					update_post_meta( $pid, '_download_limit', '' );
					update_post_meta( $pid, '_download_expiry', '' );
					update_post_meta( $pid, '_download_type', '' );
					update_post_meta( $pid, '_downloadable_files', '' );					
				}

				// Remove old taxonomies attributes so data is kept up to date
				if ( $pid and ($import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes']))) {
					// Update all Attributes
					if ( $import->options['update_all_data'] == "yes" or $import->options['update_attributes_logic'] == 'full_update' ) $this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND post_id = %d;", $pid ) );					

					wp_cache_delete( $pid, 'post_meta');
				}

				// Update taxonomies
				if ( $import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes']) or empty($articleData['ID'])){
					
					$attr_names = array();

					foreach ($serialized_attributes as $anum => $attr_data) {

						$attr_name = strtolower($attr_data['names'][$i]);

						if (empty($attr_name) or in_array($attr_name, $attr_names)) continue;
								
						$attr_names[] = $attr_name;

						// Update only these Attributes, leave the rest alone
						if ( $import->options['update_all_data'] == "no" and $import->options['update_attributes_logic'] == 'only'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
								if ( ! in_array( ( (intval($attr_data['in_taxonomy'][$i])) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;
							}
							else break;								
						}	

						// Leave these attributes alone, update all other Attributes
						if ( $import->options['update_all_data'] == "no" and $import->options['update_attributes_logic'] == 'all_except'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
								if ( in_array( ( ($is_taxonomy) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;									
							}
						}						

						if ( intval($attr_data['in_taxonomy'][$i]) and ( strpos($attr_name, "pa_") === false or strpos($attr_name, "pa_") !== 0 ) ) $attr_name = "pa_" . $attr_name;														
						$is_variation 	= intval( $attr_data['in_variation'][$i]);													
							
						// Don't use woocommerce_clean as it destroys sanitized characters																								
						$values = (intval($attr_data['in_taxonomy'][$i])) ? $attr_data['value'][$i] : $attr_data['value'][$i];	
						
						if (intval($attr_data['in_taxonomy'][$i])){
							$cname = wc_attribute_taxonomy_name( preg_replace("%^pa_%", "", $attr_name) );

							$term = term_exists($values, $cname, 0);

							if ( empty($term) and !is_wp_error($term) ){																																
								$term = term_exists(htmlspecialchars($values), $cname, 0);																
							}															
							if ( ! empty($term) and ! is_wp_error($term) ){	
								$term = get_term_by('id', $term['term_id'], $cname);									
								if ( ! empty($term) and ! is_wp_error($term) )
									update_post_meta( $pid, 'attribute_' . sanitize_title( $attr_name ), $term->slug );
							}						 							 	
						} else {
							update_post_meta( $pid, 'attribute_' . sanitize_title( $attr_name ), $values );		
						}								
					}							
				}

				//do_action( 'woocommerce_save_product_variation', $pid );

				// Update parent if variable so price sorting works and stays in sync with the cheapest child
				$post_parent = $product_parent_post_id;

				$children = get_posts( array(
					'post_parent' 	=> $post_parent,
					'posts_per_page'=> -1,
					'post_type' 	=> 'product_variation',
					'fields' 		=> 'ids',
					'post_status'	=> array('draft', 'publish', 'trash', 'pending', 'future', 'private')
				) );

				$lowest_price = $lowest_regular_price = $lowest_sale_price = $highest_price = $highest_regular_price = $highest_sale_price = '';

				if ( $children ) {
					foreach ( $children as $child ) {

						$child_price 			= get_post_meta( $child, '_price', true );
						$child_regular_price 	= get_post_meta( $child, '_regular_price', true );
						$child_sale_price 		= get_post_meta( $child, '_sale_price', true );

						// Regular prices
						if ( ! is_numeric( $lowest_regular_price ) || $child_regular_price < $lowest_regular_price )
							$lowest_regular_price = $child_regular_price;

						if ( ! is_numeric( $highest_regular_price ) || $child_regular_price > $highest_regular_price )
							$highest_regular_price = $child_regular_price;

						// Sale prices
						if ( $child_price == $child_sale_price ) {
							if ( $child_sale_price !== '' && ( ! is_numeric( $lowest_sale_price ) || $child_sale_price < $lowest_sale_price ) )
								$lowest_sale_price = $child_sale_price;

							if ( $child_sale_price !== '' && ( ! is_numeric( $highest_sale_price ) || $child_sale_price > $highest_sale_price ) )
								$highest_sale_price = $child_sale_price;
						}
					}

			    	$lowest_price 	= $lowest_sale_price === '' || $lowest_regular_price < $lowest_sale_price ? $lowest_regular_price : $lowest_sale_price;
					$highest_price 	= $highest_sale_price === '' || $highest_regular_price > $highest_sale_price ? $highest_regular_price : $highest_sale_price;
				}

				update_post_meta( $post_parent, '_price', $lowest_price );
				update_post_meta( $post_parent, '_min_variation_price', $lowest_price );
				update_post_meta( $post_parent, '_max_variation_price', $highest_price );
				update_post_meta( $post_parent, '_min_variation_regular_price', $lowest_regular_price );
				update_post_meta( $post_parent, '_max_variation_regular_price', $highest_regular_price );
				update_post_meta( $post_parent, '_min_variation_sale_price', $lowest_sale_price );
				update_post_meta( $post_parent, '_max_variation_sale_price', $highest_sale_price );

				// Update default attribute options setting
				if ( $import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_attributes'] ) or empty($articleData['ID']) ){
					
					$default_attributes = array();
					$parent_attributes  = array();
					$unique_attributes  = array();
					$attribute_position = 0;
					$is_update_attributes = true;

					foreach ( $children as $child ) {

						$child_attributes = (array) maybe_unserialize( get_post_meta( $child, '_product_attributes', true ) );

						foreach ($child_attributes as $attr) 
							if ( ! in_array($attr['name'], $unique_attributes) and $attr['is_variation']) {
								$attributes[] = $attr;
								$unique_attributes[] = $attr['name'];
							}
					}				

					foreach ( $attributes as $attribute ) {															

						$default_attributes[ sanitize_title($attribute['name']) ] = array();

						$values = array();

						foreach ( $children as $child ) {
							
							$value = array_map( 'stripslashes', array_map( 'strip_tags',  explode("|", trim( get_post_meta($child, 'attribute_'.sanitize_title($attribute['name']), true)))));
							
							if ( ! empty($value) ){
								update_post_meta( $child, 'attribute_' . sanitize_title( $attribute['name'] ), sanitize_title($value[0]) );	
								foreach ($value as $val) {
								 	if ( ! in_array($val, $values) )  $values[] = $val;
								} 
							}

							if ( $attribute['is_variation'] ) {							

								if (!empty($value) and empty($default_attributes[ $attribute['name'] ]))
									$default_attributes[ sanitize_title($attribute['name']) ] = sanitize_title((is_array($value)) ? $value[0] : $value);
								
							}
						}

						// Update only these Attributes, leave the rest alone
						if ($import->options['update_all_data'] == "no" and $import->options['update_attributes_logic'] == 'only'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
								if ( ! in_array( $attribute['name'] , array_filter($import->options['attributes_list'], 'trim'))){ 
									$attribute_position++;		
									continue;
								}
							}
							else {
								$is_update_attributes = false;
								break;
							}
						}

						// Leave these attributes alone, update all other Attributes
						if ($import->options['update_all_data'] == "no" and $import->options['update_attributes_logic'] == 'all_except'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
								if ( in_array( $attribute['name'] , array_filter($import->options['attributes_list'], 'trim'))){ 
									$attribute_position++;
									continue;
								}
							}
						}

						if ( $attribute['is_taxonomy'] ){
							
							if ( ! empty($values) ) {				 												

							 	// Remove empty items in the array
							 	$values = array_filter( $values, array($this, "filtering") );						 	

						 		$attr_values = array();						 		

						 		foreach ($values as $key => $value) {
						 			
						 			$term = term_exists($value, $attribute['name'], 0);	

						 			if ( empty($term) and !is_wp_error($term) ){																																
										$term = term_exists(htmlspecialchars($value), $attribute['name'], 0);	
										if ( empty($term) and !is_wp_error($term) and $attribute['is_create_taxonomy_terms']){													
											$term = wp_insert_term(
												$value, // the term 
											  	$attribute['name'] // the taxonomy										  	
											);													
										}
									}
									if ( ! is_wp_error($term) )												
										$attr_values[] = (int) $term['term_id']; 
						 			
						 		}

						 		$values = $attr_values;
						 		$values = array_map( 'intval', $values );
								$values = array_unique( $values );

						 	} else {
						 		$values = array();
						 	}

					 		// Update post terms
					 		if ( $values and taxonomy_exists( $attribute['name'] ) )
					 			wp_set_object_terms( $post_parent, $values, $attribute['name'] );

					 		do_action('wpai_parent_set_object_terms', $post_parent, $attribute['name']);

					 		if ( $values ) {
					 			
						 		// Add attribute to array, but don't set values
						 		$parent_attributes[ sanitize_title( $attribute['name'] ) ] = array(
							 		'name' 			=> $attribute['name'],
							 		'value' 		=> '',
							 		'position' 		=> $attribute_position,
							 		'is_visible' 	=> $attribute['is_visible'],
							 		'is_variation' 	=> $attribute['is_variation'],
							 		'is_taxonomy' 	=> 1,
							 		'is_create_taxonomy_terms' => $attribute['is_create_taxonomy_terms'],
							 	);
						 	
						 	}						 	

						}
						else
						{
							if (!empty($values)){
								$parent_attributes[ sanitize_title( $attribute['name'] ) ] = array(
							 		'name' 			=> sanitize_text_field( $attribute['name'] ),
							 		'value' 		=> implode('|', $values),
							 		'position' 		=> $attribute_position,
							 		'is_visible' 	=> $attribute['is_visible'],
							 		'is_variation' 	=> $attribute['is_variation'],
							 		'is_taxonomy' 	=> 0
							 	);
							}
						}

					 	$attribute_position++;		
					}				
					
					if ($import->options['is_default_attributes'] and $is_update_attributes) update_post_meta( $post_parent, '_default_attributes', $default_attributes );																				

					if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_product_attributes') and $is_update_attributes){ 
						
						$current_product_attributes = get_post_meta($post_parent, '_product_attributes', true);						
						
						update_post_meta( $post_parent, '_product_attributes', (( ! empty($current_product_attributes)) ? array_merge($current_product_attributes, $parent_attributes) : $parent_attributes) );												
					}

					do_action('wpai_parent_product_sync', $post_parent);
				}				
			}								

		} elseif ( in_array( $product_type, array( 'variable', 'grouped' ) ) ){

			// Link All Variations
			if ( "variable" == $product_type and $import->options['link_all_variations'] and ($import->options['update_all_data'] == "yes" or ($import->options['update_all_data'] == "no" and $import->options['is_update_attributes']) or empty($articleData['ID']))){

				$added_variations = $this->pmwi_link_all_variations($pid, $import->options);

				$logger and call_user_func($logger, sprintf(__('<b>CREATED</b>: %s variations for parent product %s.', 'pmxi_plugin'), $added_variations, $articleData['post_title']));	

			}

			// Variable and grouped products have no prices
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_regular_price')){ 
				update_post_meta( $pid, '_regular_price_tmp', $tmp = get_post_meta( $pid, '_regular_price', true) );
				update_post_meta( $pid, '_regular_price', '' );
			}
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price')){ 
				update_post_meta( $pid, '_sale_price_tmp', $tmp = get_post_meta( $pid, '_sale_price', true ) );
				update_post_meta( $pid, '_sale_price', '' );
			}
			if ( class_exists('woocommerce_wholesale_pricing') and (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, 'wholesale_price'))){ 
				update_post_meta( $pid, 'pmxi_wholesale_price_tmp', $tmp = get_post_meta( $pid, 'pmxi_wholesale_price', true ) );
				update_post_meta( $pid, 'pmxi_wholesale_price', '' );
			}
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_from')){ 
				update_post_meta( $pid, '_sale_price_dates_from_tmp', $tmp = get_post_meta( $pid, '_sale_price_dates_from', true ) );
				update_post_meta( $pid, '_sale_price_dates_from', '' );
			}
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_sale_price_dates_to')){ 
				update_post_meta( $pid, '_sale_price_dates_to_tmp', $tmp = get_post_meta( $pid, '_sale_price_dates_to', true ) );
				update_post_meta( $pid, '_sale_price_dates_to', '' );
			}
			if (empty($articleData['ID']) or $this->is_update_custom_field($existing_meta_keys, $import->options, '_price')){ 
				update_post_meta( $pid, '_price_tmp', $tmp = get_post_meta( $pid, '_price', true ) );			
				update_post_meta( $pid, '_price', '' );			
			}

		}

		// Find children elements by XPath and create variations
		if ( "variable" == $product_type and "xml" == $import->options['matching_parent'] and "" != $import->options['variations_xpath'] and "" != $import->options['variable_sku'] and ! $import->options['link_all_variations']) {
			
			$variation_xpath = $cxpath . '[' . ( $i + 1 ) . ']/'.  ltrim(trim(str_replace("[*]", "", $import->options['variations_xpath']),'{}'), '/');
			
			$records = array();

			$variation_sku = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sku'], $file)->parse($records); $tmp_files[] = $file;
			$count_variations = count($variation_sku);			

			if ( $count_variations > 0 ):
				// Stock Qty
				if ($import->options['variable_stock'] != ""){
					if ($import->options['variable_stock_use_parent']){
						$parent_variation_stock = XmlImportParser::factory($xml, $cxpath, $import->options['variable_stock'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_stock = array_fill(0, count($variation_sku), $parent_variation_stock[$i]);						
					}
					else {
						$variation_stock = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_stock'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_stock = array_fill(0, count($variation_sku), '');
				}

				// Image			
				$variation_image = array();				
				if ($import->options['variable_image']) {
					
					if ($import->options['variable_image_use_parent']){
						$parent_image = XmlImportParser::factory($xml, $cxpath, $import->options['variable_image'], $file)->parse($records); $tmp_files[] = $file;						
						count($variation_sku) and $variation_image = array_fill(0, count($variation_sku), $parent_image[$i]);						
					}
					else {
						$variation_image = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_image'], $file)->parse($records); $tmp_files[] = $file;	
					}					
					
				} else {
					count($variation_sku) and $variation_image = array_fill(0, count($variation_sku), '');
				}

				// Regular Price
				if (!empty($import->options['variable_regular_price'])){
					if ($import->options['variable_regular_price_use_parent']){
						$parent_regular_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['variable_regular_price'], $file)->parse($records)); $tmp_files[] = $file;
						count($variation_sku) and $variation_regular_price = array_fill(0, count($variation_sku), $parent_regular_price[$i]);						
					}
					else {
						$variation_regular_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_regular_price'], $file)->parse($records)); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_regular_price = array_fill(0, count($variation_sku), '');
				}

				// Sale Price
				if (!empty($import->options['variable_sale_price'])){
					if ($import->options['variable_sale_price_use_parent']){
						$parent_sale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['variable_sale_price'], $file)->parse($records)); $tmp_files[] = $file;
						count($variation_sku) and $variation_sale_price = array_fill(0, count($variation_sku), $parent_sale_price[$i]);						
					}
					else {
						$variation_sale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sale_price'], $file)->parse($records)); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_sale_price = array_fill(0, count($variation_sku), '');
				}	

				// Who Sale Price
				if (!empty($import->options['variable_whosale_price'])){
					if ($import->options['variable_whosale_price_use_parent']){
						$parent_whosale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $cxpath, $import->options['variable_whosale_price'], $file)->parse($records)); $tmp_files[] = $file;
						count($variation_sku) and $variation_whosale_price = array_fill(0, count($variation_sku), $parent_whosale_price[$i]);						
					}
					else {
						$variation_whosale_price = array_map(array($this, 'prepare_price'), XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_whosale_price'], $file)->parse($records)); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_whosale_price = array_fill(0, count($variation_sku), '');
				}	

				if ( $import->options['is_variable_sale_price_shedule']){
					// Sale price dates from
					if (!empty($import->options['variable_sale_price_dates_from'])){

						if ($import->options['variable_sale_dates_use_parent']){
							$parent_sale_date_start = XmlImportParser::factory($xml, $cxpath, $import->options['variable_sale_price_dates_from'], $file)->parse($records); $tmp_files[] = $file;
							count($variation_sku) and $variation_sale_price_dates_from = array_fill(0, count($variation_sku), $parent_sale_date_start[$i]);							
						}
						else {
							$variation_sale_price_dates_from = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sale_price_dates_from'], $file)->parse($records); $tmp_files[] = $file;
						}
					}
					else{
						count($variation_sku) and $variation_sale_price_dates_from = array_fill(0, count($variation_sku), '');
					}

					// Sale price dates to
					if (!empty($import->options['variable_sale_price_dates_to'])){
						
						if ($import->options['variable_sale_dates_use_parent']){
							$parent_sale_date_end = XmlImportParser::factory($xml, $cxpath, $import->options['variable_sale_price_dates_to'], $file)->parse($records); $tmp_files[] = $file;
							count($variation_sku) and $variation_sale_price_dates_to = array_fill(0, count($variation_sku), $parent_sale_date_end[$i]);							
						}
						else {
							$variation_sale_price_dates_to = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_sale_price_dates_to'], $file)->parse($records); $tmp_files[] = $file;
						}						
					}
					else{
						count($variation_sku) and $variation_sale_price_dates_to = array_fill(0, count($variation_sku), '');
					}
				}			

				// Composing product is Virtual									
				if ($import->options['is_variable_product_virtual'] == 'xpath' and "" != $import->options['single_variable_product_virtual']){
					if ($import->options['single_variable_product_virtual_use_parent']){
						$parent_variable_product_virtual = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_virtual'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_virtual = array_fill(0, count($variation_sku), $parent_variable_product_virtual[$i]);						
					}
					else {
						$variation_product_virtual = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_virtual'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_virtual = array_fill(0, count($variation_sku), $import->options['is_variable_product_virtual']);
				}

				// Composing product is Downloadable									
				if ($import->options['is_variable_product_downloadable'] == 'xpath' and "" != $import->options['single_variable_product_downloadable']){
					if ($import->options['single_variable_product_downloadable_use_parent']){
						$parent_variable_product_downloadable = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_downloadable'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_downloadable = array_fill(0, count($variation_sku), $parent_variable_product_downloadable[$i]);						
					}
					else {
						$variation_product_downloadable = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_downloadable'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_downloadable = array_fill(0, count($variation_sku), $import->options['is_variable_product_downloadable']);
				}

				// Weigth										
				if (!empty($import->options['variable_weight'])){
					if ($import->options['variable_weight_use_parent']){
						$parent_weight = XmlImportParser::factory($xml, $cxpath, $import->options['variable_weight'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_weight = array_fill(0, count($variation_sku), $parent_weight[$i]);						
					}
					else {
						$variation_weight = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_weight'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_weight = array_fill(0, count($variation_sku), '');
				}

				// Length										
				if (!empty($import->options['variable_length'])){
					if ($import->options['variable_dimensions_use_parent']){
						$parent_length = XmlImportParser::factory($xml, $cxpath, $import->options['variable_length'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_length = array_fill(0, count($variation_sku), $parent_length[$i]);						
					}
					else {
						$variation_length = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_length'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_length = array_fill(0, count($variation_sku), '');
				}

				// Width
				if (!empty($import->options['variable_width'])){
					if ($import->options['variable_dimensions_use_parent']){
						$parent_width = XmlImportParser::factory($xml, $cxpath, $import->options['variable_width'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_width = array_fill(0, count($variation_sku), $parent_width[$i]);						
					}
					else {
						$variation_width = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_width'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_width = array_fill(0, count($variation_sku), '');
				}

				// Heigth										
				if (!empty($import->options['variable_height'])){
					if ($import->options['variable_dimensions_use_parent']){
						$parent_heigth = XmlImportParser::factory($xml, $cxpath, $import->options['variable_height'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_height = array_fill(0, count($variation_sku), $parent_heigth[$i]);						
					}
					else {
						$variation_height = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_height'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_height = array_fill(0, count($variation_sku), '');
				}
				
				// Composing product Shipping Class				
				if ($import->options['is_multiple_variable_product_shipping_class'] != 'yes' and "" != $import->options['single_variable_product_shipping_class']){
					if ($import->options['single_variable_product_shipping_class_use_parent']){
						$parent_shipping_class = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_shipping_class'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_shipping_class = array_fill(0, count($variation_sku), $parent_shipping_class[$i]);						
					}
					else {
						$variation_product_shipping_class = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_shipping_class'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_shipping_class = array_fill(0, count($variation_sku), $import->options['multiple_variable_product_shipping_class']);
				}

				// Composing product Tax Class				
				if ($import->options['is_multiple_variable_product_tax_class'] != 'yes' and "" != $import->options['single_variable_product_tax_class']){
					if ($import->options['single_variable_product_tax_class_use_parent']){
						$parent_tax_class = XmlImportParser::factory($xml, $cxpath, $import->options['single_variable_product_tax_class'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_product_tax_class = array_fill(0, count($variation_sku), $parent_tax_class[$i]);						
					}
					else {
						$variation_product_tax_class = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_tax_class'], $file)->parse($records); $tmp_files[] = $file;						
					}
				}
				else{
					count($variation_sku) and $variation_product_tax_class = array_fill(0, count($variation_sku), $import->options['multiple_variable_product_tax_class']);
				}

				// Download limit										
				if (!empty($import->options['variable_download_limit'])){
					if ($import->options['variable_download_limit_use_parent']){
						$parent_download_limit = XmlImportParser::factory($xml, $cxpath, $import->options['variable_download_limit'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_download_limit = array_fill(0, count($variation_sku), $parent_download_limit[$i]);						
					}
					else {
						$variation_download_limit = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_download_limit'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_download_limit = array_fill(0, count($variation_sku), '');
				}

				// Download expiry										
				if (!empty($import->options['variable_download_expiry'])){
					if ($import->options['variable_download_expiry_use_parent']){
						$parent_download_expiry = XmlImportParser::factory($xml, $cxpath, $import->options['variable_download_expiry'], $file)->parse($records); $tmp_files[] = $file;
						count($variation_sku) and $variation_download_expiry = array_fill(0, count($variation_sku), $parent_download_expiry[$i]);						
					}
					else {
						$variation_download_expiry = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_download_expiry'], $file)->parse($records); $tmp_files[] = $file;
					}
				}
				else{
					count($variation_sku) and $variation_download_expiry = array_fill(0, count($variation_sku), '');
				}

				// File paths								
				if (!empty($import->options['variable_file_paths'])){
					$variation_file_paths = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_file_paths'], $file)->parse($records); $tmp_files[] = $file;
				}
				else{
					count($variation_sku) and $variation_file_paths = array_fill(0, count($variation_sku), '');
				}

				// File names								
				if (!empty($import->options['variable_file_names'])){
					$variation_file_names = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_file_names'], $file)->parse($records); $tmp_files[] = $file;
				}
				else{
					count($variation_sku) and $variation_file_names = array_fill(0, count($variation_sku), '');
				}

				// Variation enabled								
				if ($import->options['is_variable_product_enabled'] == 'xpath' and "" != $import->options['single_variable_product_enabled']){
					$variation_product_enabled = XmlImportParser::factory($xml, $variation_xpath, $import->options['single_variable_product_enabled'], $file)->parse($records); $tmp_files[] = $file;						
				}
				else{
					count($variation_sku) and $variation_product_enabled = array_fill(0, count($variation_sku), $import->options['is_variable_product_enabled']);
				}

				$variation_attribute_keys = array(); 
				$variation_attribute_values = array();	
				$variation_attribute_in_variation = array(); 
				$variation_attribute_is_visible = array();
				$variation_attribute_in_taxonomy = array();			
				$variable_create_terms_in_not_exists = array();
									
				if (!empty($import->options['variable_attribute_name'][0])){
					foreach ($import->options['variable_attribute_name'] as $j => $attribute_name) { if ($attribute_name == "") continue;						
						$variation_attribute_keys[$j]   = XmlImportParser::factory($xml, $variation_xpath, $attribute_name, $file)->parse($records); $tmp_files[] = $file;
						$variation_attribute_values[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_attribute_value'][$j], $file)->parse($records); $tmp_files[] = $file;
						$variation_attribute_in_variation[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_in_variations'][$j], $file)->parse($records); $tmp_files[] = $file;
						$variation_attribute_is_visible[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_is_visible'][$j], $file)->parse($records); $tmp_files[] = $file;						
						$variation_attribute_in_taxonomy[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_is_taxonomy'][$j], $file)->parse($records); $tmp_files[] = $file;						
						$variable_create_terms_in_not_exists[$j] = XmlImportParser::factory($xml, $variation_xpath, $import->options['variable_create_taxonomy_in_not_exists'][$j], $file)->parse($records); $tmp_files[] = $file;
					}
				}					

				// serialized attributes for product variations
				$variation_serialized_attributes = array();
				if (!empty($variation_attribute_keys)){
					foreach ($variation_attribute_keys as $j => $attribute_name) {											
						if (!in_array($attribute_name[0], array_keys($variation_serialized_attributes))){
							$variation_serialized_attributes[$attribute_name[0]] = array(
								'value' => $variation_attribute_values[$j],
								'is_visible' => $variation_attribute_is_visible[$j],
								'in_variation' => $variation_attribute_in_variation[$j],
								'in_taxonomy' => $variation_attribute_in_taxonomy[$j],
								'is_create_taxonomy_terms' => $variable_create_terms_in_not_exists[$j]
							);						
						}							
					}
				} 

				// Create Variations
				foreach ($variation_sku as $j => $void) {	if ("" == $variation_sku[$j]) continue;

					if ($import->options['variable_sku_add_parent']) $variation_sku[$j] = $product_sku[$i] . '-' . $variation_sku[$j];

					$variable_enabled = ($variation_product_enabled[$j] == "yes") ? 'yes' : 'no'; 					

					// Enabled or disabled
					$post_status = ( $variable_enabled == 'yes' ) ? 'publish' : 'private';
					$variation_to_update_id = false;					
					$postRecord = new PMXI_Post_Record();
					$postRecord->clear();																					
						
					// Generate a useful post title
					$variation_post_title = sprintf( __( 'Variation #%s of %s', 'woocommerce' ), $variation_sku[$j], esc_html( $pid ) );

					// handle duplicates according to import settings
					if ($duplicates = pmxi_findDuplicates(array('post_title' => $variation_post_title, 'post_type' => 'product_variation', 'post_parent' => $pid),'','','parent')) {															
						$duplicate_id = array_shift($duplicates);							
						if ($duplicate_id) {														
							$variation_to_update = get_post($variation_to_update_id = $duplicate_id);
						}						
					}						

					// Update or Add post							

					$variation = array(
						'post_title' 	=> $variation_post_title,
						'post_content' 	=> '',
						'post_status' 	=> $post_status,									
						'post_parent' 	=> $pid,
						'post_type' 	=> 'product_variation'									
					);

					$variation_just_created = false;

					if ( ! $variation_to_update_id ) {

						$variation_to_update_id = ($import->options['is_fast_mode']) ? pmxi_insert_post($variation) : wp_insert_post( $variation );		

						// associate variation with import
						$postRecord->isEmpty() and $postRecord->set(array(
							'post_id' => $variation_to_update_id,
							'import_id' => $import->id,
							'unique_key' => 'Variation ' . $variation_sku[$j],
							'product_key' => ''
						))->insert();	

						$postRecord->set(array('iteration' => $import->iteration))->update();

						$variation_just_created = true;		

						$logger and call_user_func($logger, sprintf(__('`%s`: variation created successfully', 'pmxi_plugin'), sprintf( __( 'Variation #%s of %s', 'woocommerce' ), absint( $variation_to_update_id ), esc_html( get_the_title( $pid ) ) )));

					} else {

						$postRecord->getBy(array(
							'unique_key' => 'Variation ' . $variation_sku[$j],
							'import_id' => $import->id
						));
						if ( ! $postRecord->isEmpty() ) $postRecord->set(array('iteration' => $import->iteration))->update();
							
						$this->wpdb->update( $this->wpdb->posts, $variation, array( 'ID' => $variation_to_update_id ) );
						//do_action( 'woocommerce_update_product_variation', $variation_to_update_id );
						$logger and call_user_func($logger, sprintf(__('`%s`: variation updated successfully', 'pmxi_plugin'), $variation_post_title));
					}										

					$existing_variation_meta_keys = array();
					foreach (get_post_meta($variation_to_update_id, '') as $cur_meta_key => $cur_meta_val) $existing_variation_meta_keys[] = $cur_meta_key;

					// delete keys which are no longer correspond to import settings																
					if ( !empty($existing_variation_meta_keys) ) 

						foreach ($existing_variation_meta_keys as $cur_meta_key) { 
						
							// Do not delete post meta for features image 
							if ( in_array($cur_meta_key, array('_thumbnail_id','_product_image_gallery')) ) continue;

							// Update all data
							if ($import->options['update_all_data'] == 'yes') {
								delete_post_meta($variation_to_update_id, $cur_meta_key);
								continue;
							}
							
							// Do not update attributes
							if ( ! $import->options['is_update_attributes'] and (in_array($cur_meta_key, array('_default_attributes', '_product_attributes')) or strpos($cur_meta_key, "attribute_") === 0)) continue;
							
							// Update only these Attributes, leave the rest alone
							if ($import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'only'){
								
								if ($cur_meta_key == '_product_attributes'){
									$current_product_attributes = get_post_meta($variation_to_update_id, '_product_attributes', true);
									if ( ! empty($current_product_attributes) and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) 
										foreach ($current_product_attributes as $attr_name => $attr_value) {
											if ( in_array($attr_name, array_filter($import->options['attributes_list'], 'trim'))) unset($current_product_attributes[$attr_name]);
										}
										
									update_post_meta($variation_to_update_id, '_product_attributes', $current_product_attributes);
									continue;
								}

								if ( strpos($cur_meta_key, "attribute_") === 0 and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list']) and ! in_array(str_replace("attribute_", "", $cur_meta_key), array_filter($import->options['attributes_list'], 'trim'))) continue;

								if (in_array($cur_meta_key, array('_default_attributes'))) continue;
							}

							// Leave these attributes alone, update all other Attributes
							if ($import->options['is_update_attributes'] and $import->options['update_attributes_logic'] == 'all_except'){
								
								if ($cur_meta_key == '_product_attributes'){
									
									if (empty($import->options['attributes_list'])) { delete_post_meta($variation_to_update_id, $cur_meta_key); continue; }

									$current_product_attributes = get_post_meta($variation_to_update_id, '_product_attributes', true);
									if ( ! empty($current_product_attributes) and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) 
										foreach ($current_product_attributes as $attr_name => $attr_value) {
											if ( ! in_array($attr_name, array_filter($import->options['attributes_list'], 'trim'))) unset($current_product_attributes[$attr_name]);
										}
										
									update_post_meta($variation_to_update_id, '_product_attributes', $current_product_attributes);
									continue;
								}

								if ( strpos($cur_meta_key, "attribute_") === 0 and ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list']) and in_array(str_replace("attribute_", "", $cur_meta_key), array_filter($import->options['attributes_list'], 'trim'))) continue;

								if (in_array($cur_meta_key, array('_default_attributes'))) continue;
							}

							// Update all Custom Fields is defined
							if ($import->options['update_custom_fields_logic'] == "full_update"){
								delete_post_meta($variation_to_update_id, $cur_meta_key);								
							}
							// Update only these Custom Fields, leave the rest alone
							elseif ($import->options['update_custom_fields_logic'] == "only"){
								if ( ! empty($import->options['custom_fields_list']) and is_array($import->options['custom_fields_list']) and in_array($cur_meta_key, $import->options['custom_fields_list'])) delete_post_meta($variation_to_update_id, $cur_meta_key);
							}
							// Leave these fields alone, update all other Custom Fields
							elseif ($import->options['update_custom_fields_logic'] == "all_except"){
								if ( empty($import->options['custom_fields_list']) or ! in_array($cur_meta_key, $import->options['custom_fields_list'])) delete_post_meta($variation_to_update_id, $cur_meta_key);
							}
						}

					// Add any default post meta
					add_post_meta( $variation_to_update_id, 'total_sales', '0', true );
					
					// Product type + Downloadable/Virtual
					wp_set_object_terms( $variation_to_update_id, $product_type, 'product_type' );
					update_post_meta( $variation_to_update_id, '_downloadable', ($variation_product_downloadable[$j] == "yes") ? 'yes' : 'no' );
					update_post_meta( $variation_to_update_id, '_virtual', ($variation_product_virtual[$j] == "yes") ? 'yes' : 'no' );						

					// Update post meta
					if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_regular_price')) update_post_meta( $variation_to_update_id, '_regular_price', stripslashes( $variation_regular_price[$j] ) );
					if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_sale_price')) update_post_meta( $variation_to_update_id, '_sale_price', stripslashes( $variation_sale_price[$j] ) );
					if ( class_exists('woocommerce_wholesale_pricing') ) update_post_meta( $variation_to_update_id, 'pmxi_wholesale_price', stripslashes( $variation_whosale_price[$j] ) );

					// Dimensions
					if ( $variation_product_virtual[$j] == 'no' ) {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_weight')) update_post_meta( $variation_to_update_id, '_weight', stripslashes( $variation_weight[$i] ) );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_length')) update_post_meta( $variation_to_update_id, '_length', stripslashes( $variation_length[$i] ) );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_width')) update_post_meta( $variation_to_update_id, '_width', stripslashes( $variation_width[$i] ) );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_height')) update_post_meta( $variation_to_update_id, '_height', stripslashes( $variation_height[$i] ) );
					} else {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_weight')) update_post_meta( $variation_to_update_id, '_weight', '' );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_length')) update_post_meta( $variation_to_update_id, '_length', '' );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_width')) update_post_meta( $variation_to_update_id, '_width', '' );
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_height')) update_post_meta( $variation_to_update_id, '_height', '' );
					}										

					$term = get_term_by('id', $variation_product_shipping_class[$j], 'product_shipping_class');
					
					// Save shipping class					
					wp_set_object_terms( $variation_to_update_id, ((!empty($term) and !is_wp_error($term)) ? $term->slug : ""), 'product_shipping_class');

					// Unique SKU
					$sku				= get_post_meta($variation_to_update_id, '_sku', true);
					$new_sku 			= esc_html( trim( stripslashes( $variation_sku[$j] ) ) );
					
					if ( $new_sku == '' and $import->options['disable_auto_sku_generation'] ) {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_sku')) 				
								update_post_meta( $variation_to_update_id, '_sku', '' );
					}
					elseif ( $new_sku == '' and ! $import->options['disable_auto_sku_generation'] ) {
						if ($variation_just_created or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_sku')){				
							
							$new_sku = substr(md5($variation_post_title), 0, 12);
						}
					}

					if ( $new_sku == '' ) {
						update_post_meta( $variation_to_update_id, '_sku', '' );
					} elseif ( $new_sku !== $sku ) {
						if ( ! empty( $new_sku ) ) {
							if ( ! $import->options['disable_sku_matching']  and 
								$this->wpdb->get_var( $this->wpdb->prepare("
									SELECT ".$this->wpdb->posts.".ID
								    FROM ".$this->wpdb->posts."
								    LEFT JOIN ".$this->wpdb->postmeta." ON (".$this->wpdb->posts.".ID = ".$this->wpdb->postmeta.".post_id)
								    WHERE ".$this->wpdb->posts.".post_type = 'product'
								    AND ".$this->wpdb->posts.".post_status = 'publish'
								    AND ".$this->wpdb->postmeta.".meta_key = '_sku' AND ".$this->wpdb->postmeta.".meta_value = '%s'
								 ", $new_sku ) )
								) {
								$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Product SKU must be unique.', 'pmxi_plugin')));								
								
							} else {
								update_post_meta( $variation_to_update_id, '_sku', $new_sku );
							}
						} else {
							update_post_meta( $variation_to_update_id, '_sku', '' );
						}
					}

					$date_from = isset( $variation_sale_price_dates_from[$j] ) ? $variation_sale_price_dates_from[$j] : '';
					$date_to = isset( $variation_sale_price_dates_to[$i] ) ? $variation_sale_price_dates_to[$i] : '';

					// Dates
					if ( $date_from )
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', strtotime( $date_from ) );
					else
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', '' );

					if ( $date_to )
						update_post_meta( $variation_to_update_id, '_sale_price_dates_to', strtotime( $date_to ) );
					else
						update_post_meta( $variation_to_update_id, '_sale_price_dates_to', '' );

					if ( $date_to && ! $date_from )
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', strtotime( 'NOW', current_time( 'timestamp' ) ) );

					// Update price if on sale
					if ( $variation_sale_price[$j] != '' && $date_to == '' && $date_from == '' ){
						if (empty($articleData['ID']) or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_price')) update_post_meta( $variation_to_update_id, '_price', stripslashes( $variation_sale_price[$j] ) );
					}
					else{
						if (empty($articleData['ID']) or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_price')) update_post_meta( $variation_to_update_id, '_price', stripslashes( $variation_regular_price[$j] ) );
					}

					if ( $variation_sale_price[$j] != '' && $date_from && strtotime( $date_from ) < strtotime( 'NOW', current_time( 'timestamp' ) ) )
						update_post_meta( $variation_to_update_id, '_price', stripslashes($variation_sale_price[$j]) );

					if ( $date_to && strtotime( $date_to ) < strtotime( 'NOW', current_time( 'timestamp' ) ) ) {
						if (empty($articleData['ID']) or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_price')) update_post_meta( $variation_to_update_id, '_price', stripslashes($variation_regular_price[$j]) );
						update_post_meta( $variation_to_update_id, '_sale_price_dates_from', '');
						update_post_meta( $variation_to_update_id, '_sale_price_dates_to', '');
					}
					
					// Manage stock
					if (empty($articleData['ID']) or $this->is_update_custom_field($existing_variation_meta_keys, $import->options, '_stock')) update_post_meta( $variation_to_update_id, '_stock', $variation_stock[$j] );

					if ( $variation_product_tax_class[ $j ] !== 'parent' )
						update_post_meta( $variation_to_update_id, '_tax_class', sanitize_text_field( $variation_product_tax_class[ $j ] ) );
					else
						delete_post_meta( $variation_to_update_id, '_tax_class' );

					if ( $variation_product_downloadable[$j] == 'yes' ) {
						update_post_meta( $variation_to_update_id, '_download_limit', sanitize_text_field( $variation_download_limit[ $j ] ) );
						update_post_meta( $variation_to_update_id, '_download_expiry', sanitize_text_field( $variation_download_expiry[ $j ] ) );

						$_file_paths = array();
						
						if ( !empty($variation_file_paths[$j]) ) {
							$file_paths = explode( $import->options['variable_product_files_delim'] , $variation_file_paths[$j] );
							$file_names = explode( $import->options['variable_product_files_names_delim'] , $variation_file_names[$j] );

							foreach ( $file_paths as $fn => $file_path ) {
								$file_path = sanitize_text_field( $file_path );								
								$_file_paths[ md5( $file_path ) ] = array('name' => ((!empty($file_names[$fn])) ? $file_names[$fn] : basename($file_path)), 'file' => $file_path);
							}
						}

						// grant permission to any newly added files on any existing orders for this product						
						update_post_meta( $variation_to_update_id, '_downloadable_files', $_file_paths );
					} else {
						update_post_meta( $variation_to_update_id, '_download_limit', '' );
						update_post_meta( $variation_to_update_id, '_download_expiry', '' );
						update_post_meta( $variation_to_update_id, '_downloadable_files', '' );
						update_post_meta( $variation_to_update_id, '_download_type', '' );
					}

					// Remove old taxonomies attributes so data is kept up to date
					if ( $variation_to_update_id and ( $import->options['update_all_data'] == 'yes' or ($import->options['update_all_data'] == 'no' and $import->options['is_update_attributes']) or $variation_just_created) ) {
						if ($import->options['update_all_data'] == 'yes' or $import->options['update_attributes_logic'] == 'full_update' ) 
							$this->wpdb->query( $this->wpdb->prepare( "DELETE FROM {$this->wpdb->postmeta} WHERE meta_key LIKE 'attribute_%%' AND post_id = %d;", $variation_to_update_id ) );
						wp_cache_delete( $variation_to_update_id, 'post_meta');
					}

					// Update taxonomies
					if ( $import->options['update_all_data'] == 'yes' or ($import->options['update_all_data'] == 'no' and $import->options['is_update_attributes']) or $variation_just_created ){

						foreach ($variation_serialized_attributes as $attr_name => $attr_data) {																										

							// Update only these Attributes, leave the rest alone
							if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'only'){
								if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
									if ( ! in_array( ( (intval($attr_data['in_taxonomy'][$j])) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;
								}
								else break;								
							}	

							// Leave these attributes alone, update all other Attributes
							if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'all_except'){
								if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
									if ( in_array( ( (intval($attr_data['in_taxonomy'][$j])) ? wc_attribute_taxonomy_name( $attr_name ) : $attr_name ) , array_filter($import->options['attributes_list'], 'trim'))) continue;									
								}
							}	
															
							$is_variation 	= intval( $attr_data['in_variation'][$j]);													
								
							// Don't use woocommerce_clean as it destroys sanitized characters																								
							$values = (intval($attr_data['in_taxonomy'][$j])) ? $attr_data['value'][$j] : $attr_data['value'][$j];	
							
							if (intval($attr_data['in_taxonomy'][$j])){

								if (intval($attr_data['is_create_taxonomy_terms'][0])) $this->create_taxonomy($attr_name, $logger);

								$terms = get_terms( wc_attribute_taxonomy_name( preg_replace("%^pa_%", "", $attr_name) ), array('hide_empty' => false));		

								if ( ! is_wp_error($terms) ){
							 		
						 			$term_founded = false;	
									if ( count($terms) > 0 ){	
								    	foreach ( $terms as $term ) {									    										    										    	
									    	if ( strtolower($term->name) == trim(strtolower($values)) or $term->slug == sanitize_title(trim(strtolower($values))) ) {										    		
									    		update_post_meta( $variation_to_update_id, 'attribute_' . wc_attribute_taxonomy_name( $attr_name ), $term->slug );
									    		$term_founded = true;	
									    		break;
									    	}
									    }
									}	
								    if ( ! $term_founded and intval($attr_data['is_create_taxonomy_terms'][0]) ){
								    	$term = wp_insert_term(
											$values, // the term 
										  	wc_attribute_taxonomy_name( $attr_name ) // the taxonomy										  	
										);		
										if ( ! is_wp_error($term) ){
											$term = get_term_by( 'id', $term['term_id'], wc_attribute_taxonomy_name( $attr_name ));
											update_post_meta( $variation_to_update_id, 'attribute_' . wc_attribute_taxonomy_name( $attr_name ), $term->slug );
										}
								    }
							 		
							 	}
							 	else{
							 		$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: %s.', 'pmxi_plugin'), $terms->get_error_message()));
							 	}

							} else {
								update_post_meta( $variation_to_update_id, 'attribute_' . sanitize_title( $attr_name ), $values );		
							}							
							
						}
					}					

					if ( ! is_array($variation_image[$j]) ) $variation_image[$j] = array($variation_image[$j]);

					$uploads = wp_upload_dir();

					if ( ! empty($uploads) and false === $uploads['error'] and !empty($variation_image[$j]) and (empty($articleData['ID']) or $import->options['update_all_data'] == "yes" or ( $import->options['update_all_data'] == "no" and $import->options['is_update_images']))) {

						$gallery_attachment_ids = array();	

						foreach ($variation_image[$j] as $featured_image)
						{							
							$imgs = explode(',', $featured_image);

							if (!empty($imgs)) {	

								foreach ($imgs as $img_url) { if (empty($img_url)) continue;	

									$url = str_replace(" ", "%20", trim($img_url));
									$bn = preg_replace('/[\\?|&].*/', '', basename($url));
									
									$img_ext = pmxi_getExtensionFromStr($url);									
									$default_extension = pmxi_getExtension($bn);																									

									if ($img_ext == "") 										
										$img_ext = pmxi_get_remote_image_ext($url);																			

									// generate local file name
									$image_name = urldecode(sanitize_file_name((($img_ext) ? str_replace("." . $default_extension, "", $bn) : $bn))) . (("" != $img_ext) ? '.' . $img_ext : '');																	
									
									// if wizard store image data to custom field									
									$create_image = false;
									$download_image = true;

									$image_filename = wp_unique_filename($uploads['path'], $image_name);
									$image_filepath = $uploads['path'] . '/' . $image_filename;
									
									// keep existing and add newest images
									if ( ! empty($articleData['ID']) and $import->options['is_update_images'] and $import->options['update_images_logic'] == "add_new" and $import->options['update_all_data'] == "no"){ 																																											
										
										$attachment_imgs = get_posts( array(
											'post_type' => 'attachment',
											'posts_per_page' => -1,
											'post_parent' => $variation_to_update_id,												
										) );

										if ( $attachment_imgs ) {
											foreach ( $attachment_imgs as $attachment_img ) {													
												if ($attachment_img->guid == $uploads['url'] . '/' . $image_name){
													$download_image = false;
													$success_images = true;
													
													set_post_thumbnail($variation_to_update_id, $attachment_img->ID);													
													$gallery_attachment_ids[] = $attachment_img->ID;	

													$logger and call_user_func($logger, sprintf(__('<b>Image SKIPPED</b>: The image %s is always exists for the %s', 'pmxi_plugin'), basename($attachment_img->guid), $variation_post_title));							
												}
											}												
										}

									}

									if ($download_image){											

										// do not download images
										if ( ! $import->options['download_images'] or $import->options['variable_image_use_parent']){ 		

											$image_filename = $image_name;
											$image_filepath = $uploads['path'] . '/' . $image_filename;																							
											
											$existing_attachment = $this->wpdb->get_row( $this->wpdb->prepare( "SELECT * FROM " . $this->wpdb->prefix ."posts WHERE guid = '%s'", $uploads['url'] . '/' . $image_filename ) );
											
											if ( ! empty($existing_attachment->ID) ){

												$download_image = false;	
												$create_image = false;	
												
												set_post_thumbnail($variation_to_update_id, $existing_attachment->ID); 																							
												$gallery_attachment_ids[] = $existing_attachment->ID;	

												do_action( 'pmxi_gallery_image', $variation_to_update_id, $existing_attachment->ID, $image_filepath); 

											}
											else{													
												
												if ( @file_exists($image_filepath) ){
													$download_image = false;																				
													if( ! ($image_info = @getimagesize($image_filepath)) or ! in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
														$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: File %s is not a valid image and cannot be set as featured one', 'pmxi_plugin'), $image_filepath));														
														@unlink($image_filepath);
													} else {
														$create_image = true;											
													}
												}
											}											
										}	

										if ($download_image){
											
											$request = get_file_curl($url, $image_filepath);

											if ( (is_wp_error($request) or $request === false) and ! @file_put_contents($image_filepath, @file_get_contents($url))) {
												@unlink($image_filepath); // delete file since failed upload may result in empty file created
											} elseif( ($image_info = @getimagesize($image_filepath)) and in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
												$create_image = true;											
											}												
											
											if ( ! $create_image ){

												$url = str_replace(" ", "%20", trim(pmxi_convert_encoding($img_url)));
												
												$request = get_file_curl($url, $image_filepath);

												if ( (is_wp_error($request) or $request === false) and ! @file_put_contents($image_filepath, @file_get_contents($url))) {
													$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: File %s cannot be saved locally as %s', 'pmxi_plugin'), $url, $image_filepath));													
													@unlink($image_filepath); // delete file since failed upload may result in empty file created										
												} elseif( ! ($image_info = @getimagesize($image_filepath)) or ! in_array($image_info[2], array(IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG))) {
													$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: File %s is not a valid image and cannot be set as featured one', 'pmxi_plugin'), $url));													
													@unlink($image_filepath);
												} else {
													$create_image = true;											
												}
											}
										}
									}

									if ($create_image){

										// you must first include the image.php file
										// for the function wp_generate_attachment_metadata() to work
										require_once(ABSPATH . 'wp-admin/includes/image.php');	

										$attachment = array(
											'post_mime_type' => image_type_to_mime_type($image_info[2]),
											'guid' => $uploads['url'] . '/' . $image_filename,
											'post_title' => $image_filename,
											'post_content' => ''										
										);
										if (($image_meta = wp_read_image_metadata($image_filepath))) {
											if (trim($image_meta['title']) && ! is_numeric(sanitize_title($image_meta['title'])))
												$attachment['post_title'] = $image_meta['title'];
											if (trim($image_meta['caption']))
												$attachment['post_content'] = $image_meta['caption'];
										}

										$attid = ($import->options['is_fast_mode']) ? pmxi_insert_attachment($attachment, $image_filepath, $variation_to_update_id) : wp_insert_attachment($attachment, $image_filepath, $variation_to_update_id);										

										if (is_wp_error($attid)) {
											$logger and call_user_func($logger, __('<b>WARNING</b>', 'pmxi_plugin') . ': ' . $attid->get_error_message());											
										} else {
																						
											wp_update_attachment_metadata($attid, wp_generate_attachment_metadata($attid, $image_filepath));																																										

											do_action( 'pmxi_gallery_image', $variation_to_update_id, $attid, $image_filepath); 

											$success_images = true;											
											set_post_thumbnail($variation_to_update_id, $attid); 																						
											$gallery_attachment_ids[] = $attid;												

										}										
									}																									
								}							
							}						
						}
						// Set product gallery images
						if ( ! empty($gallery_attachment_ids) )
							update_post_meta($variation_to_update_id, '_product_image_gallery', implode(',', $gallery_attachment_ids));		
					}							

					wc_delete_product_transients( $variation_to_update_id );	
				}

				foreach ($tmp_files as $file) { // remove all temporary files created
					if (file_exists($file)) @unlink($file);
				}

				// Update parent if variable so price sorting works and stays in sync with the cheapest child				

				$children = get_posts( array(
					'post_parent' 	=> $pid,
					'posts_per_page'=> -1,
					'post_type' 	=> 'product_variation',
					'fields' 		=> 'ids',
					'post_status'	=> array('draft', 'publish', 'trash', 'pending', 'future', 'private')
				) );

				$lowest_price = $lowest_regular_price = $lowest_sale_price = $highest_price = $highest_regular_price = $highest_sale_price = '';

				if ( $children ) {
					foreach ( $children as $child ) {

						$child_price 			= get_post_meta( $child, '_price', true );
						$child_regular_price 	= get_post_meta( $child, '_regular_price', true );
						$child_sale_price 		= get_post_meta( $child, '_sale_price', true );

						// Regular prices
						if ( ! is_numeric( $lowest_regular_price ) || $child_regular_price < $lowest_regular_price )
							$lowest_regular_price = $child_regular_price;

						if ( ! is_numeric( $highest_regular_price ) || $child_regular_price > $highest_regular_price )
							$highest_regular_price = $child_regular_price;

						// Sale prices
						if ( $child_price == $child_sale_price ) {
							if ( $child_sale_price !== '' && ( ! is_numeric( $lowest_sale_price ) || $child_sale_price < $lowest_sale_price ) )
								$lowest_sale_price = $child_sale_price;

							if ( $child_sale_price !== '' && ( ! is_numeric( $highest_sale_price ) || $child_sale_price > $highest_sale_price ) )
								$highest_sale_price = $child_sale_price;
						}
					}

			    	$lowest_price 	= $lowest_sale_price === '' || $lowest_regular_price < $lowest_sale_price ? $lowest_regular_price : $lowest_sale_price;
					$highest_price 	= $highest_sale_price === '' || $highest_regular_price > $highest_sale_price ? $highest_regular_price : $highest_sale_price;
				}

				update_post_meta( $pid, '_price', $lowest_price );
				update_post_meta( $pid, '_min_variation_price', $lowest_price );
				update_post_meta( $pid, '_max_variation_price', $highest_price );
				update_post_meta( $pid, '_min_variation_regular_price', $lowest_regular_price );
				update_post_meta( $pid, '_max_variation_regular_price', $highest_regular_price );
				update_post_meta( $pid, '_min_variation_sale_price', $lowest_sale_price );
				update_post_meta( $pid, '_max_variation_sale_price', $highest_sale_price );

				// Update default attribute options setting
				if ( $import->options['update_all_data'] == 'yes' or ($import->options['update_all_data'] == 'no' and $import->options['is_update_attributes']) or $variation_just_created ){
					
					$default_attributes = array();
					$parent_attributes  = array();
					$attribute_position = 0;
					$is_update_attributes = true;

					foreach ($variation_serialized_attributes as $attr_name => $attr_data) {
						
						$values = array();

						// Update only these Attributes, leave the rest alone
						if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'only'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])){
								if ( ! in_array( (( intval($attr_data['in_taxonomy'][$j]) ) ? "pa_" . $attr_name : $attr_name), array_filter($import->options['attributes_list'], 'trim'))){ 
									$attribute_position++;		
									continue;
								}
							}
							else {
								$is_update_attributes = false;
								break;
							}
						}

						// Leave these attributes alone, update all other Attributes
						if ($import->options['update_all_data'] == 'no' and $import->options['update_attributes_logic'] == 'all_except'){
							if ( ! empty($import->options['attributes_list']) and is_array($import->options['attributes_list'])) {
								if ( in_array( (( intval($attr_data['in_taxonomy'][$j]) ) ? "pa_" . $attr_name : $attr_name) , array_filter($import->options['attributes_list'], 'trim'))){ 
									$attribute_position++;
									continue;
								}
							}
						}

						foreach ($variation_sku as $j => $void) {							

							$is_variation 	= ( intval($attr_data['in_variation'][$j]) ) ? 1 : 0;								

							if ($is_variation){

								$value = esc_attr(trim( $attr_data['value'][$j] ));

								if ( ! in_array($value, $values))  $values[] = $value;

								if ( ! empty($value) and empty($default_attributes[ (( intval($attr_data['in_taxonomy'][$j])) ? wc_attribute_taxonomy_name( $attr_name ) : sanitize_title($attr_name)) ]))
								
									$default_attributes[ (( intval($attr_data['in_taxonomy'][$j]) ) ? wc_attribute_taxonomy_name( $attr_name ) : sanitize_title($attr_name)) ] = sanitize_title($value);
							}
						}												

						if ( intval($attr_data['in_taxonomy'][0]) ){						

							if (intval($attr_data['is_create_taxonomy_terms'][0])) $this->create_taxonomy($attr_name, $logger);
																		 	
							if ( isset($values) and taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ) ) {				 							 		

							 	// Remove empty items in the array
							 	$values = array_filter( $values, array($this, "filtering") );

							 	if ( ! empty($values) ){

							 		$attr_values = array();
							 		
							 		$terms = get_terms( wc_attribute_taxonomy_name( $attr_name ), array('hide_empty' => false));								

							 		if ( ! is_wp_error($terms) ){

								 		foreach ($values as $key => $value) {
								 			$term_founded = false;	
											if ( count($terms) > 0 ){	
											    foreach ( $terms as $term ) {											    	
											    	if ( strtolower($term->name) == trim(strtolower($value)) or $term->slug == sanitize_title(trim(strtolower($value)))) {
											    		$attr_values[] = $term->slug;
											    		$term_founded = true;
											    		break;
											    	}
											    }
											}
										    if ( ! $term_founded and intval($attr_data['is_create_taxonomy_terms'][0]) ){
										    	$term = wp_insert_term(
													$value, // the term 
												  	wc_attribute_taxonomy_name( $attr_name ) // the taxonomy										  	
												);		
												if ( ! is_wp_error($term) )													
													$attr_values[] = (int) $term['term_id'];												
										    }
								 		}
								 	}
								 	else{
								 		$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: %s.', 'pmxi_plugin'), $terms->get_error_message()));
								 	}

							 		$values = $attr_values;
							 	}

						 	} else {
						 		$values = array();
						 	}					 						 	
					 		// Update post terms
					 		if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))
					 			wp_set_object_terms( $pid, $values, wc_attribute_taxonomy_name( $attr_name ));

					 		if ( $values ) {
						 		// Add attribute to array, but don't set values
						 		$parent_attributes[ wc_attribute_taxonomy_name( $attr_name ) ] = array(
							 		'name' 			=> wc_attribute_taxonomy_name( $attr_name ),
							 		'value' 		=> '',
							 		'position' 		=> $attribute_position,
							 		'is_visible' 	=> (!empty($attr_data['is_visible'][0])) ? 1 : 0,
							 		'is_variation' 	=> (!empty($attr_data['in_variation'][0])) ? 1 : 0,
							 		'is_taxonomy' 	=> 1,
							 		'is_create_taxonomy_terms' => (!empty( $attr_data['is_create_taxonomy_terms'][0] )) ? 1 : 0
							 	);
						 	}

						}
						else{

							if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ))
								wp_set_object_terms( $pid, NULL, wc_attribute_taxonomy_name( $attr_name ));

							$parent_attributes[ sanitize_title( $attr_name ) ] = array(
						 		'name' 			=> sanitize_text_field( $attr_name ),
						 		'value' 		=> implode('|', $values),
						 		'position' 		=> $attribute_position,
						 		'is_visible' 	=> (!empty($attr_data['is_visible'][0])) ? 1 : 0,
							 	'is_variation' 	=> (!empty($attr_data['in_variation'][0])) ? 1 : 0,
						 		'is_taxonomy' 	=> 0
						 	);
						}

					 	$attribute_position++;	
						
					}			

					if ($import->options['is_default_attributes'] and $is_update_attributes) {

						$current_default_attributes = get_post_meta($pid, '_default_attributes', true);		

						update_post_meta( $pid, '_default_attributes', (( ! empty($current_default_attributes)) ? array_merge($current_default_attributes, $default_attributes) : $default_attributes) );

					}
			
					if ($is_update_attributes) {
						
						$current_product_attributes = get_post_meta($pid, '_product_attributes', true);						
						
						update_post_meta( $pid, '_product_attributes', (( ! empty($current_product_attributes)) ? array_merge($current_product_attributes, $parent_attributes) : $parent_attributes) );	

					}
				}
				
			endif;	
		}	

		// Clear cache/transients
		wc_delete_product_transients( $pid );	

	}
	
	function create_taxonomy($attr_name, $logger){
		
		global $woocommerce;

		if ( ! taxonomy_exists( wc_attribute_taxonomy_name( $attr_name ) ) ) {

	 		// Grab the submitted data							
			$attribute_name    = ( isset( $attr_name ) ) ? wc_sanitize_taxonomy_name( stripslashes( (string) $attr_name ) ) : '';
			$attribute_label   = ucwords( stripslashes( (string) $attr_name ));
			$attribute_type    = 'select';
			$attribute_orderby = 'menu_order';

			$reserved_terms = array(
				'attachment', 'attachment_id', 'author', 'author_name', 'calendar', 'cat', 'category', 'category__and',
				'category__in', 'category__not_in', 'category_name', 'comments_per_page', 'comments_popup', 'cpage', 'day',
				'debug', 'error', 'exact', 'feed', 'hour', 'link_category', 'm', 'minute', 'monthnum', 'more', 'name',
				'nav_menu', 'nopaging', 'offset', 'order', 'orderby', 'p', 'page', 'page_id', 'paged', 'pagename', 'pb', 'perm',
				'post', 'post__in', 'post__not_in', 'post_format', 'post_mime_type', 'post_status', 'post_tag', 'post_type',
				'posts', 'posts_per_archive_page', 'posts_per_page', 'preview', 'robots', 's', 'search', 'second', 'sentence',
				'showposts', 'static', 'subpost', 'subpost_id', 'tag', 'tag__and', 'tag__in', 'tag__not_in', 'tag_id',
				'tag_slug__and', 'tag_slug__in', 'taxonomy', 'tb', 'term', 'type', 'w', 'withcomments', 'withoutcomments', 'year',
			);

			if ( in_array( $attribute_name, $reserved_terms ) ) {
				$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Slug “%s” is not allowed because it is a reserved term. Change it, please.', 'pmxi_plugin'), wc_attribute_taxonomy_name( $attribute_name )));
			}			
			else{
				// Register the taxonomy now so that the import works!
				$domain = wc_attribute_taxonomy_name( $attr_name );
				if (strlen($domain) <= 32){

					$this->wpdb->insert(
						$this->wpdb->prefix . 'woocommerce_attribute_taxonomies',
						array(
							'attribute_label'   => $attribute_label,
							'attribute_name'    => $attribute_name,
							'attribute_type'    => $attribute_type,
							'attribute_orderby' => $attribute_orderby,
						)
					);												
								
					register_taxonomy( $domain,
				        apply_filters( 'woocommerce_taxonomy_objects_' . $domain, array('product') ),
				        apply_filters( 'woocommerce_taxonomy_args_' . $domain, array(
				            'hierarchical' => true,
				            'show_ui' => false,
				            'query_var' => true,
				            'rewrite' => false,
				        ) )
				    );

					delete_transient( 'wc_attribute_taxonomies' );
					$attribute_taxonomies = $this->wpdb->get_results( "SELECT * FROM " . $this->wpdb->prefix . "woocommerce_attribute_taxonomies" );
					set_transient( 'wc_attribute_taxonomies', $attribute_taxonomies );
					apply_filters( 'woocommerce_attribute_taxonomies', $attribute_taxonomies );

					$logger and call_user_func($logger, sprintf(__('<b>CREATED</b>: Taxonomy attribute “%s” have been successfully created.', 'pmxi_plugin'), wc_attribute_taxonomy_name( $attribute_name )));	

				}
				else{
					$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Taxonomy “%s” name is more than 32 characters. Change it, please.', 'pmxi_plugin'), $attr_name));
				}

			}

	 	}
	}

	function pmwi_link_all_variations($product_id, $options = array()) {

		global $woocommerce;

		@set_time_limit(0);

		$post_id = intval( $product_id );

		if ( ! $post_id ) return 0;

		$variations = array();

		$_product = get_product( $post_id, array( 'product_type' => 'variable' ) );

		$v = $_product->get_attributes();		

		// Put variation attributes into an array
		foreach ( $_product->get_attributes() as $attribute ) {

			if ( ! $attribute['is_variation'] ) continue;

			$attribute_field_name = 'attribute_' . sanitize_title( $attribute['name'] );

			if ( $attribute['is_taxonomy'] ) {
				$post_terms = wp_get_post_terms( $post_id, $attribute['name'] );
				$options = array();
				foreach ( $post_terms as $term ) {
					$options[] = $term->slug;
				}
			} else {
				$options = explode( '|', $attribute['value'] );
			}

			$options = array_map( 'sanitize_title', array_map( 'trim', $options ) );

			$variations[ $attribute_field_name ] = $options;
		}

		// Quit out if none were found
		if ( sizeof( $variations ) == 0 ) return 0;

		// Get existing variations so we don't create duplicates
	    $available_variations = array();

	    foreach( $_product->get_children() as $child_id ) {
	    	$child = $_product->get_child( $child_id );

	        if ( ! empty( $child->variation_id ) ) {
	            $available_variations[] = $child->get_variation_attributes();
	        }
	    }	  

		// Created posts will all have the following data
		$variation_post_data = array(
			'post_title' => 'Product #' . $post_id . ' Variation',
			'post_content' => '',
			'post_status' => 'publish',
			'post_author' => get_current_user_id(),
			'post_parent' => $post_id,
			'post_type' => 'product_variation'
		);
		
		$variation_ids = array();
		$added = 0;
		$possible_variations = $this->array_cartesian( $variations );		

		foreach ( $possible_variations as $variation ) {

			// Check if variation already exists
			if ( in_array( $variation, $available_variations ) )
				continue;

			$variation_id = (!empty($options['is_fast_mode'])) ? pmxi_insert_post($variation_post_data) : wp_insert_post( $variation_post_data );			
			
			update_post_meta( $variation_id, '_regular_price', get_post_meta( $post_id, '_regular_price', true ) );
			update_post_meta( $variation_id, '_sale_price', get_post_meta( $post_id, '_sale_price', true ) );
			if ( class_exists('woocommerce_wholesale_pricing') ) update_post_meta( $variation_id, 'pmxi_wholesale_price', get_post_meta( $post_id, 'pmxi_wholesale_price', true ) );
			update_post_meta( $variation_id, '_sale_price_dates_from', get_post_meta( $post_id, '_sale_price_dates_from', true ) );
			update_post_meta( $variation_id, '_sale_price_dates_to', get_post_meta( $post_id, '_sale_price_dates_to', true ) );
			update_post_meta( $variation_id, '_price', get_post_meta( $post_id, '_price', true ) );			

			$variation_ids[] = $variation_id;

			foreach ( $variation as $key => $value ) {
				update_post_meta( $variation_id, $key, $value );
			}

			$added++;

			//do_action( 'product_variation_linked', $variation_id );
			
		}		

		wc_delete_product_transients( $post_id );

		return $added;
	}


	function array_cartesian( $input ) {

		    $result = array();

		    while ( list( $key, $values ) = each( $input ) ) {
		        // If a sub-array is empty, it doesn't affect the cartesian product
		        if ( empty( $values ) ) {
		            continue;
		        }

		        // Special case: seeding the product array with the values from the first sub-array
		        if ( empty( $result ) ) {
		            foreach ( $values as $value ) {
		                $result[] = array( $key => $value );
		            }
		        }
		        else {
		            // Second and subsequent input sub-arrays work like this:
		            //   1. In each existing array inside $product, add an item with
		            //      key == $key and value == first item in input sub-array
		            //   2. Then, for each remaining item in current input sub-array,
		            //      add a copy of each existing array inside $product with
		            //      key == $key and value == first item in current input sub-array

		            // Store all items to be added to $product here; adding them on the spot
		            // inside the foreach will result in an infinite loop
		            $append = array();
		            foreach( $result as &$product ) {
		                // Do step 1 above. array_shift is not the most efficient, but it
		                // allows us to iterate over the rest of the items with a simple
		                // foreach, making the code short and familiar.
		                $product[ $key ] = array_shift( $values );

		                // $product is by reference (that's why the key we added above
		                // will appear in the end result), so make a copy of it here
		                $copy = $product;

		                // Do step 2 above.
		                foreach( $values as $item ) {
		                    $copy[ $key ] = $item;
		                    $append[] = $copy;
		                }

		                // Undo the side effecst of array_shift
		                array_unshift( $values, $product[ $key ] );
		            }

		            // Out of the foreach, we can add to $results now
		            $result = array_merge( $result, $append );
		        }
		    }

		    return $result;
		}

	public function _filter_has_cap_unfiltered_html($caps)
	{
		$caps['unfiltered_html'] = true;
		return $caps;
	}	
	
	function duplicate_post_copy_post_meta_info($new_id, $post, $options = array(), $is_duplicate = false) {
		$post_meta_keys = get_post_custom_keys($post->ID);
		$meta_blacklist = array();
		$meta_keys = array_diff($post_meta_keys, $meta_blacklist);

		foreach ($meta_keys as $meta_key) {
			$meta_values = get_post_custom_values($meta_key, $post->ID);

			foreach ($meta_values as $meta_value) {
				$meta_value = maybe_unserialize($meta_value);
				if ( ! $is_duplicate or $this->is_update_custom_field($existing_meta_keys, $options, $meta_key)) update_post_meta($new_id, $meta_key, $meta_value);
			}
		}

		update_post_meta( $post->ID, '_stock_tmp', $tmp = get_post_meta( $post->ID, '_stock', true) );
		if ( empty($options['set_parent_stock']) ) 
			update_post_meta( $post->ID, '_stock', '');
		update_post_meta( $post->ID, '_regular_price_tmp', $tmp = get_post_meta( $post->ID, '_regular_price', true) );
		update_post_meta( $post->ID, '_regular_price', '' );
		update_post_meta( $post->ID, '_price_tmp', $tmp = get_post_meta( $post->ID, '_price', true) );
		update_post_meta( $post->ID, '_price', '');
		
	}	

	function auto_cloak_links($import, &$url){
		
		$url = apply_filters('pmwi_cloak_affiliate_url', trim($url), $import->id);
		
		// cloak urls with `WP Wizard Cloak` if corresponding option is set
		if ( ! empty($import->options['is_cloak']) and class_exists('PMLC_Plugin')) {														
			if (preg_match('%^\w+://%i', $url)) { // mask only links having protocol
				// try to find matching cloaked link among already registered ones
				$list = new PMLC_Link_List(); $linkTable = $list->getTable();
				$rule = new PMLC_Rule_Record(); $ruleTable = $rule->getTable();
				$dest = new PMLC_Destination_Record(); $destTable = $dest->getTable();
				$list->join($ruleTable, "$ruleTable.link_id = $linkTable.id")
					->join($destTable, "$destTable.rule_id = $ruleTable.id")
					->setColumns("$linkTable.*")
					->getBy(array(
						"$linkTable.destination_type =" => 'ONE_SET',
						"$linkTable.is_trashed =" => 0,
						"$linkTable.preset =" => '',
						"$linkTable.expire_on =" => '0000-00-00',
						"$ruleTable.type =" => 'ONE_SET',
						"$destTable.weight =" => 100,
						"$destTable.url LIKE" => $url,
					), NULL, 1, 1)->convertRecords();
				if ($list->count()) { // matching link found
					$link = $list[0];
				} else { // register new cloaked link
					global $wpdb;
					$slug = max(
						intval($wpdb->get_var("SELECT MAX(CONVERT(name, SIGNED)) FROM $linkTable")),
						intval($wpdb->get_var("SELECT MAX(CONVERT(slug, SIGNED)) FROM $linkTable")),
						0
					);
					$i = 0; do {
						is_int(++$slug) and $slug > 0 or $slug = 1;
						$is_slug_found = ! intval($wpdb->get_var("SELECT COUNT(*) FROM $linkTable WHERE name = '$slug' OR slug = '$slug'"));
					} while( ! $is_slug_found and $i++ < 100000);
					if ($is_slug_found) {
						$link = new PMLC_Link_Record(array(
							'name' => strval($slug),
							'slug' => strval($slug),
							'header_tracking_code' => '',
							'footer_tracking_code' => '',
							'redirect_type' => '301',
							'destination_type' => 'ONE_SET',
							'preset' => '',
							'forward_url_params' => 1,
							'no_global_tracking_code' => 0,
							'expire_on' => '0000-00-00',
							'created_on' => date('Y-m-d H:i:s'),
							'is_trashed' => 0,
						));
						$link->insert();
						$rule = new PMLC_Rule_Record(array(
							'link_id' => $link->id,
							'type' => 'ONE_SET',
							'rule' => '',
						));
						$rule->insert();
						$dest = new PMLC_Destination_Record(array(
							'rule_id' => $rule->id,
							'url' => $url,
							'weight' => 100,
						));
						$dest->insert();
					} else {
						$logger and call_user_func($logger, sprintf(__('<b>WARNING</b>: Unable to create cloaked link for %s', 'pmxi_plugin'), $url));						
						$link = NULL;
					}
				}
				if ($link) { // cloaked link is found or created for url
					$url = preg_replace('%' . preg_quote($url, '%') . '(?=([\s\'"]|$))%i', $link->getUrl(), $url);								
				}									
			}
		}
	}

	function is_update_custom_field($existing_meta_keys, $options, $meta_key){

		if ($options['update_all_data'] == 'yes') return true;

		if ( ! $options['is_update_custom_fields'] ) return false;			

		if ($options['update_custom_fields_logic'] == "full_update") return true;
		if ($options['update_custom_fields_logic'] == "only" and ! empty($options['custom_fields_list']) and is_array($options['custom_fields_list']) and in_array($meta_key, $options['custom_fields_list']) ) return true;
		if ($options['update_custom_fields_logic'] == "all_except" and ( empty($options['custom_fields_list']) or ! in_array($meta_key, $options['custom_fields_list']) )) return true;
		
		return false;
	}

	// process simple product meta
	function process_product_meta( $post_id, $post = '' ) {

		$wholesale_price = get_post_meta($post_id, 'pmxi_wholesale_price', true);

		if ( '' !==  $wholesale_price )
			update_post_meta( $post_id, 'wholesale_price', stripslashes( $wholesale_price ) );
		else
			delete_post_meta( $post_id, 'pmxi_wholesale_price' );
	}

	// process variable product meta
	function process_product_meta_variable( $post_id ) {

		$product = get_post($post_id);

		if ( $product->post_type == 'product_variation' ) {		

			$wholesale_price = get_post_meta($post_id, 'pmxi_wholesale_price', true);

			if ( '' !==  $wholesale_price )
				update_post_meta( $post_id, 'wholesale_price', stripslashes( $wholesale_price ) );
			else
				delete_post_meta( $post_id, 'pmxi_wholesale_price' );	

			$post_parent = $product->post_parent;			

		}
		else $post_parent = $post_id;		
		
		$children = get_posts( array(
				    'post_parent' 	=> $post_parent,
				    'posts_per_page'=> -1,
				    'post_type' 	=> 'product_variation',
				    'fields' 		=> 'ids'
			    ) );

		$lowest_price = '';

		$highest_price = '';

		if ( $children ) {

			foreach ( $children as $child ) {

				$child_price = get_post_meta($child, 'pmxi_wholesale_price', true);

			
				if ( !$child_price ) continue;
	
				// Low price
				if ( !is_numeric( $lowest_price ) || $child_price < $lowest_price ) $lowest_price = $child_price;

				
				// High price
				if ( $child_price > $highest_price )
					$highest_price = $child_price;

			}


		}

		update_post_meta( $post_parent, 'wholesale_price', $lowest_price );
		update_post_meta( $post_parent, 'min_variation_wholesale_price', $lowest_price );
		update_post_meta( $post_parent, 'max_variation_wholesale_price', $highest_price );

		
	}
	
	function prepare_price( $price ){   

		if ($this->options['disable_prepare_price']) return apply_filters('pmxi_price', $price);

	    $price = str_replace(",", ".", preg_replace("/[^0-9\.,]/","", $price));       	   	    

	    $price = str_replace(",", ".", str_replace(".", "", preg_replace("%\.([0-9]){1,2}?$%", ",$0", $price)));	    
	    
	    return ("" != $price) ? apply_filters('pmxi_price', number_format( (double) $price, 2, '.', '')) : "";
	}
}

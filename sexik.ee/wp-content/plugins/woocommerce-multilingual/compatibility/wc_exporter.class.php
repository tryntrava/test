<?php

class WCML_wcExporter{

    function __construct(){

        add_filter('woo_ce_product_fields',array($this,'woo_ce_fields'));
        add_filter('woo_ce_category_fields',array($this,'woo_ce_fields'));
        add_filter('woo_ce_tag_fields',array($this,'woo_ce_fields'));
        add_filter('woo_ce_order_fields',array($this,'woo_ce_order_fields'));
        add_filter('woo_ce_product_item',array($this,'woo_ce_product_item'),10,2);
        add_filter('woo_ce_category_item',array($this,'woo_ce_category_item'),10);
        add_filter('woo_ce_tags',array($this,'woo_ce_tags'),10);

    }

    function woo_ce_fields($fields){
        $fields[] = array(
            'name' => 'language',
            'label' => __( 'Language', 'woo_ce' ),
            'default' => 1
        );
        $fields[] = array(
            'name' => 'translation_of',
            'label' => __( 'Translation of', 'woo_ce' ),
            'default' => 1
        );
        return $fields;
    }

    function woo_ce_order_fields($fields){
        $fields[] = array(
            'name' => 'language',
            'label' => __( 'Language', 'woo_ce' ),
            'default' => 1
        );

        return $fields;
    }

    function woo_ce_product_item($data, $product_id){
        global $sitepress,$woocommerce_wpml;

        $data->language = $sitepress->get_language_for_element($product_id,'post_'.get_post_type($product_id));
        $data->translation_of = icl_object_id($product_id,get_post_type($product_id),true, $woocommerce_wpml->products->get_original_product_language( $product_id ) );

        return $data;
    }

    function woo_ce_category_item($data){
        global $sitepress;

        $data->language = $sitepress->get_language_for_element($data->term_taxonomy_id,'tax_product_cat');
        $data->translation_of = icl_object_id($data->term_taxonomy_id,'tax_product_cat',true,$sitepress->get_default_language());

        return $data;
    }

    function woo_ce_tags($tags){
        global $sitepress;

        foreach($tags as $key=>$tag){
            $tags[$key]->language = $sitepress->get_language_for_element($tag->term_taxonomy_id,'tax_product_tag');
            $tags[$key]->translation_of = icl_object_id($tag->term_taxonomy_id,'tax_product_tag',true,$sitepress->get_default_language());
        }

        return $tags;
    }

}

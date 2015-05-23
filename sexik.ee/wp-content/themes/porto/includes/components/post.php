<?php

/**
 * Post Component
 * 
 * @package		SpyroPress
 * @category	Components
 */

class SpyropressPost extends SpyropressComponent {

    private $path;
    
    function __construct() {

        $this->path = dirname(__FILE__);
        add_action( 'spyropress_register_taxonomy', array( $this, 'register' ) );
    }

    function register() {

        // Init Post Type
        $post = new SpyropressCustomPostType( 'Post' );
        
        // Add Meta Boxes
        $meta_fields['options'] = array(
            array(
                'label' => __( 'Portfolio', 'spyropress' ),
                'type' => 'heading',
                'slug' => 'portfolio'
            ),
            
            array(
                'label' => __( 'Gallery', 'spyropress' ),
                'desc' => __( 'Click to upload images', 'spyropress' ),
                'id' => 'gallery',
                'type' => 'gallery'
            )
        );
        
        $post->add_meta_box( 'post_options', __( 'Post Options', 'spyropress' ), $meta_fields, false, false, 'normal', 'high' );
    }
}

/**
 * Init the Component
 */
new SpyropressPost();
?>
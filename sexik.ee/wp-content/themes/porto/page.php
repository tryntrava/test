<?php

/**
 * Default Page Template
 */
?>
<?php get_header(); ?>

<?php spyropress_before_main_container(); ?>
<!-- content -->
<div role="main" class="main">
    <div id="content" class="content full">
    <?php
    spyropress_before_loop();
    while( have_posts() ) {
        the_post();

        spyropress_before_post();
        
            $page_options = get_post_meta( get_the_ID(), '_page_options', true );
            $layout = ( isset( $page_options['layout_type'] ) && !empty( $page_options['layout_type'] ) ) ? $page_options['layout_type'] : '';

            get_template_part( 'templates/page-content', $layout );

        spyropress_after_post();
    }
    spyropress_after_loop();
    ?>
    </div>
</div>
<!-- /content -->
<?php spyropress_after_main_container(); ?>
<?php get_footer(); ?>
<?php
/*
Template Name: Full Width Page
*/
?>
<?php get_header(); ?>

<div id="content-area" class="clearfix fullwidth">
	<div id="main-area">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', get_post_format() ); ?>

			<?php
				if ( comments_open() && 'on' == et_get_option( 'styleshop_show_pagescomments', 'false' ) )
					comments_template( '', true );
			?>

		<?php endwhile; ?>

	</div> <!-- #main-area -->
</div> <!-- #content-area -->

<?php get_footer(); ?>
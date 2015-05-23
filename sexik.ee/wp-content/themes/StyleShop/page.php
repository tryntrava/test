<?php get_header(); ?>

<div id="content-area" class="clearfix">
	<div id="main-area">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', get_post_format() ); ?>

			<?php
				if ( comments_open() && 'on' == et_get_option( 'styleshop_show_pagescomments', 'false' ) )
					comments_template( '', true );
			?>

		<?php endwhile; ?>

	</div> <!-- #main-area -->

	<?php get_sidebar(); ?>

</div> <!-- #content-area -->

<?php get_footer(); ?>
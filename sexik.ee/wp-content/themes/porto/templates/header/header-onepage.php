<header class="single-menu flat-menu">
	<div class="container">
		<?php if( $logo = get_setting( 'logo', false ) ) : ?>
        <h1 class="logo">
			<a href="<?php echo esc_url( home_url( '/' ) ) ?>">
				<img alt="<?php bloginfo( 'name' ) ?>" width="111" height="54" data-sticky-width="82" data-sticky-height="40" src="<?php echo $logo ?>">
			</a>
		</h1>
        <?php endif; ?>
		<button class="btn btn-responsive-nav btn-inverse" data-toggle="collapse" data-target=".nav-main-collapse">
			<i class="icon icon-bars"></i>
		</button>
	</div>
    <div class="navbar-collapse nav-main-collapse collapse">
        <div class="container">
            <?php spyropress_social_icons( 'header_social' ); ?>
            <?php
                $page_options = get_post_meta( get_the_ID(), '_page_options', true );
                $menu = wp_nav_menu( array(
                    'container' => 'nav',
                    'container_class' => 'nav-main',
                    'menu_class' => 'nav nav-pills nav-main',
                    'menu_id' => 'mainMenu',
                    'menu' => $page_options['onepage_menu'],
                    'echo' => false,
                    'walker' => new Bootstrapwp_Walker_Nav_Menu
                ) );

                $url = ( is_front_page() || is_page_template( 'one-page.php' ) ) ? '#' : home_url('/#');
                echo str_replace( '#HOME_URL#', $url, $menu );
            ?>
	   </div>
    </div>
</header>
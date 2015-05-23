<div class="header-container">
	<header class="narrow" data-after-header>
		<div class="container">
			<?php if( $logo = get_setting( 'logo', false ) ) : ?>
            <h1 class="logo">
    			<a href="<?php echo esc_url( home_url( '/' ) ) ?>">
    				<img alt="<?php bloginfo( 'name' ) ?>" width="82" height="40" src="<?php echo $logo ?>">
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
                    spyropress_get_nav_menu( array(
                        'container_class' => 'nav-main mega-menu',
                        'menu_class' => 'nav nav-pills nav-main',
                        'menu_id' => 'mainMenu'
                    ) );
                ?>
			</div>
		</div>
	</header>
</div>
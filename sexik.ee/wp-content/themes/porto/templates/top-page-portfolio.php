<?php

$translate['home'] = get_setting( 'translate' ) ? get_setting( 'home_title', 'Home' ) : __( 'Home', 'spyropress' );
$translate['portfolio'] = get_setting( 'translate' ) ? get_setting( 'portfolio_title', 'Portfolio' ) : __( 'Portfolio', 'spyropress' );
?>
<section class="page-top">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				<ul class="breadcrumb">
                    <li><a href="<?php echo home_url(); ?>"><?php echo $translate['home']; ?></a></li>
                    <li class="active"><?php echo $translate['portfolio']; ?></li>
				</ul>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<h2><?php the_title(); ?></h2>
			</div>
		</div>
	</div>
</section>
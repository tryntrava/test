<?php

$translate['search-placeholder'] = get_setting( 'translate' ) ? get_setting( 'search_placeholder', 'Search..' ) : __( 'Search..', 'spyropress' );
?>
<form class="form-search" role="search" method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
     <div class="input-group">
       	<input type="text" name="s" class="form-control" placeholder="<?php echo $translate['search-placeholder']; ?>" value="<?php echo get_search_query(); ?>">
        <span class="input-group-btn">
			<button type="submit" class="btn btn-primary btn-lg"><i class="icon icon-search"></i></button>
		</span>
    </div>
</form>
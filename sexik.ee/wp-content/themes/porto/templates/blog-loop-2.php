<?php

$translate['read-more-title'] = get_setting( 'translate' ) ? get_setting( 'read_more_title', 'Read more...' ) : __( 'Read more...', 'spyropress' );
?>
<article class="post post-medium">
	<div class="row">
		<?php
        if( $ids = get_post_meta( get_the_ID(), 'gallery', true ) ) {
            
            $ids = explode( ',', str_replace( array( '[gallery ids=', ']', '"' ), '', $ids ) );
            
            if ( !empty( $ids ) ) {
        ?>
        <div class="col-md-5">
            <div class="post-image">
                <div class="owl-carousel" data-plugin-options='{"items":1}'>
                <?php
                    foreach( $ids as $id ) {
                        $image = get_image( array(
                            'attachment' => $id,
                            'width' => 9999,
                            'responsive' => true,
                            'class' => 'img-responsive',
                            'before' => '<div><div class="img-thumbnail">',
                            'after' => '</div></div>'
                        ));
                    }
                ?>
            	</div>
            </div>
        </div>
        <?php
            }
        }
        elseif( has_post_thumbnail() ) {
        ?>
        <div class="col-md-5">
            <div class="post-image">
                <div class="owl-carousel" data-plugin-options='{"items":1}'>
                <?php
                    $image = get_image( array(
                        'attachment' => get_post_thumbnail_id(),
                        'width' => 9999,
                        'responsive' => true,
                        'class' => 'img-responsive',
                        'before' => '<div><div class="img-thumbnail">',
                        'after' => '</div></div>'
                    ));
                ?>
                </div>
            </div>
        </div>
        <?php } ?>
		<div class="col-md-7">
			<div class="post-content">
				<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php the_excerpt(); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="post-meta">
				<span><i class="icon icon-calendar"></i> <?php echo get_the_date(); ?> </span>
                <?php if( $author = get_the_author_link() ) { ?>
    			<span><i class="icon icon-user"></i><?php _e( 'By ', 'spyropress' ); echo $author; ?> </span>
                <?php } ?>
                <?php the_tags( '<span><i class="icon icon-tag"></i> ', ', ', ' </span>' ); ?>
    			<span><i class="icon icon-comments"></i> <?php comments_popup_link( __( '0 Comments', 'spyropress' ) ); ?></span>
                <a href="<?php the_permalink() ?>" class="btn btn-xs btn-primary pull-right"><?php echo $translate['read-more-title']; ?></a>
    		</div>
		</div>
	</div>
</article>
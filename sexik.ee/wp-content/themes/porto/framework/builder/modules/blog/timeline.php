<?php 

spyropress_before_loop();

$counter = 0;
$translate['read-more-title'] = get_setting( 'translate' ) ? get_setting( 'read_more_title', 'Read more...' ) : __( 'Read more...', 'spyropress' );

$args = $this->query( spyropress_clean_array( $instance ) );
$blog_query = new WP_Query( $args );
?>
<div class="blog-posts">
    <section class="timeline">
        <div class="timeline-body" id="timeline-body">
            <?php                
                if( $blog_query->have_posts() ) {
                    while( $blog_query->have_posts() ) {
                        $blog_query->the_post();
                        
                        $date = the_date( '', '', '', false );
                        if( $date ) {
                            $counter = 0;
                            echo '<div class="timeline-date"><h3>' . $date . '</h3></div>';
                        }
                        
                        $post_alt = ( ++$counter % 2 ) ? 'left' : 'right';
                        
                        spyropress_before_post();
            ?>
            <article class="timeline-box post post-medium <?php echo $post_alt; ?>">
                <div class="row">
                	<div class="col-md-12">
                        <?php
                        if( $ids = get_post_meta( get_the_ID(), 'gallery', true ) ) {
                            
                            $ids = explode( ',', str_replace( array( '[gallery ids=', ']', '"' ), '', $ids ) );
                            
                            if ( !empty( $ids ) ) {
                        ?>
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
                        <?php
                            }
                        }
                        elseif( has_post_thumbnail() ) {
                        ?>
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
                        <?php } ?>
                        <div class="post-content">
                    		<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                    		<?php the_excerpt(); ?>
                    	</div>
                        <div class="post-meta">
                			<span><i class="icon icon-calendar"></i> <?php echo get_the_date(); ?> </span><br>
                		</div>
                        <div class="post-meta">
                			<?php if( $author = get_the_author_link() ) { ?>
                			<span><i class="icon icon-user"></i><?php _e( 'By ', 'spyropress' ); echo $author; ?> </span>
                            <?php } ?>
                			<?php the_tags( '<span><i class="icon icon-tag"></i> ', ', ', ' </span>' ); ?>
                            <span><i class="icon icon-comments"></i> <?php comments_popup_link( __( '0 Comments', 'spyropress' ) ); ?></span>
                		</div>
                        
                        <a href="<?php the_permalink() ?>" class="btn btn-xs btn-primary pull-right"><?php echo $translate['read-more-title']; ?></a>
                        
                	</div>
                </div>
            </article>
            <?php
                        spyropress_after_post();
                    } 
                    wp_reset_query();
                }
            ?>
            <div class="timeline-date load-more-posts">
                <h3><a href="#" data-target="#timeline-body" data-loading="Loading...">Load More...</a></h3>
                <?php wp_pagenavi( array( 'container_class' => 'time-pagination hidden', 'query' => $blog_query ) ); ?>
            </div>
        </div>
    </section>
</div>
<?php spyropress_after_loop(); ?>
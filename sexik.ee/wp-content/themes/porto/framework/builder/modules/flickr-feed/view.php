<?php if( empty( $username ) ) return; ?>
<div class="featured-box featured-box-secundary">
	<div class="box-content clearfix">
		<?php if( $title ) echo '<h4>' . $title . '</h4>'; ?>
		<ul class="thumbnail-gallery flickr-feed" data-plugin-options='{"qstrings": { "id": "<?php echo $username; ?>" }}'></ul>
        <?php if( !empty( $points ) ) { ?>
        <hr />
        <ul class="list icons pull-left list-unstyled">
        <?php foreach( $points as $item ) {
            echo '<li><i class="icon icon-check"></i>' . $item['point'] . '</li>';
        }?>
        </ul>
        <?php } ?>
	</div>
</div>
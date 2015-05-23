<?php

$knob_color = stripslashes( sanitize_text_field( $knob_color ) );
$knob_color = str_replace( '#', '', $knob_color );
if( !is_str_starts_with( 'rgb', $knob_color ) ) $knob_color = '#' . $knob_color;

?>
<div class="circular-bar center">
	<input class="knob" data-linecap="round" data-fgColor="<?php echo $knob_color; ?>" data-thickness=".2" value="<?php echo $percentage ?>" data-readOnly="true" data-displayInput="false">
	<div class="circular-bar-content">
		<strong><?php echo $title; ?></strong>
		<label><?php echo $percentage ?>%</label>
	</div>
</div>
<?php 

echo $before_widget;

if ( $title ) echo $before_title . $title . $after_title;

echo '
<div id="tweet" class="twitter" data-account-id="' . $username . '" data-tweets-count="' . $post_count . '">
    <p>Please wait...</p>
</div>';  
echo $after_widget;
?>
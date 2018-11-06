<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/*echo '<pre>';
print_r($atts); 
echo '</pre>'; die();*/
?>
<div class="hentry service-entry">
    <header class="service-entry-header entry-header">
    <?php
	    // Featured image
	    if ( false != $atts['image'] ) {
	        echo essential_content_get_service_thumbnail_link( get_the_ID(), 'ect-service' );
	    }
    ?>

	    <h2 class="service-entry-title entry-title"><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( the_title_attribute( ) ); ?>"><?php the_title(); ?></a></h2>
	    <?php if ( false != $atts['display_types'] || false != $atts['display_tags'] || false != $atts['display_author'] ) : ?>
	        <div class="service-entry-meta entry-meta">
	        <?php
	        if ( false != $atts['display_types'] ) {
	            echo Essential_Content_Service::get_content_type( get_the_ID() );
	        }

	        if ( false != $atts['display_tags'] ) {
	            echo Essential_Content_Service::get_content_tags( get_the_ID() );
	        }

	        if ( false != $atts['display_author'] ) {
	            echo Essential_Content_Service::get_content_author( get_the_ID() );
	        }
	        ?>
	        </div>
	    <?php endif; ?>
    </header>

	<?php
	// The content
	if ( false !== $atts['display_content'] ) {
	    if ( 'full' === $atts['display_content'] ) {
	    ?>
	        <div class="service-entry-content entry-content"><?php the_content(); ?></div>
	    <?php
	    } else {
	    ?>
	        <div class="service-entry-content entry-content"><?php the_excerpt(); ?></div>
	    <?php
	    }
	}
	?>
</div><!-- close .service-entry -->
<?php $atts['service_index_number']++;

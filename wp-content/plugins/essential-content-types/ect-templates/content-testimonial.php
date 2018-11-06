<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<article class="hentry testimonial-entry">
    <div class="hentry-inner">
    <?php
        // Featured image
        if ( false !== $atts['image'] ) :
            echo essential_content_get_testimonial_thumbnail_link( get_the_ID(), 'thumbnail' );
        endif;
        // The content
        if ( false !== $atts['display_content'] ) {
            ?>
            <div class="testimonial-entry-content entry-content">
                <?php 
                    the_content();
                ?>
            </div>
            <?php
        }
    ?>
    <header class="entry-header">
        <?php
            the_title( '<h2 class="entry-title">', '</h2>' );
            $position = get_post_meta( get_the_ID(), 'ect_testimonial_position', false );
            if ( $position[0] ) : ?>
            <p class="entry-meta"><span class="position ect-testimonial-position"><?php echo esc_html( $position[0] ); ?></span></p>
        <?php endif; ?>
    </header>
</article><!-- close .testimonial-entry -->

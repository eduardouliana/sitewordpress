<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<article class="ect hentry portfolio-entry">
    <header class="portfolio-entry-header entry-header">
	    <?php
	    	// Featured image
		    echo essential_content_get_portfolio_thumbnail_link( get_the_ID(), 'ect-jetpack-portfolio-featured' );
	    ?>

	    <h2 class="portfolio-entry-title entry-title"><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( the_title_attribute( ) ); ?>"><?php the_title(); ?></a></h2>
	    <?php if ( false != $atts['display_types'] || false != $atts['display_tags'] || false != $atts['display_author'] ) : ?>
	        <div class="portfolio-entry-meta entry-meta">
		        <?php
		        if ( false != $atts['display_types'] ) {
		            echo Essential_Content_Jetpack_Portfolio::get_project_type( get_the_ID() );
		        }

		        if ( false != $atts['display_tags'] ) {
		            echo Essential_Content_Jetpack_Portfolio::get_project_tags( get_the_ID() );
		        }

		        if ( false != $atts['display_author'] ) {
		            echo Essential_Content_Jetpack_Portfolio::get_project_author( get_the_ID() );
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
	        <div class="portfolio-entry-content entry-content"><?php the_content(); ?></div>
	    <?php
	    } else {
	    ?>
	        <div class="portfolio-entry-content entry-content"><?php the_excerpt(); ?></div>
	    <?php
	    }
	}
	?>
</article><!-- close .portfolio-entry -->
<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

?>

<div class="hentry featured-content-entry">
    <header class="featured-content-entry-header entry-header">
    <?php
    // Featured image
    if ( false != $atts['image'] ) {
        echo essential_content_get_featured_content_thumbnail_link( get_the_ID(), 'ect-featured' );
    }
    ?>

    <h2 class="featured-content-entry-title entry-title"><a href="<?php echo esc_url( get_permalink() ); ?>" title="<?php echo esc_attr( the_title_attribute( ) ); ?>"><?php the_title(); ?></a></h2>
    <?php if ( false != $atts['display_types'] || false != $atts['display_tags'] || false != $atts['display_author'] ) : ?>
        <div class="featured-content-entry-meta entry-meta">
        <?php
        if ( false != $atts['display_types'] ) {
            echo Essential_Content_Featured_Content::get_content_type( get_the_ID() );
        }

        if ( false != $atts['display_tags'] ) {
            echo Essential_Content_Featured_Content::get_content_tags( get_the_ID() );
        }

        if ( false != $atts['display_author'] ) {
            echo Essential_Content_Featured_Content::get_content_author( get_the_ID() );
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
        <div class="featured-content-entry-content entry-content"><?php the_content(); ?></div>
    <?php
    } else {
    ?>
        <div class="featured-content-entry-content entry-content"><?php the_excerpt(); ?></div>
    <?php
    }
}
?>
</div><!-- close .featured-content-entry -->
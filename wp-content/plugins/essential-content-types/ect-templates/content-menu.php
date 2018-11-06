<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="hentry-inner">
		<div class="entry-container">
			<div class="entry-description">
				<header class="entry-header">
					<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_the_permalink() ) . '">', '</a></h2>' ); ?>
				</header>

				<div class="entry-content">
					<?php the_excerpt(); ?>
				</div>
			</div>

			<div class="entry-price">
				<p class="item-price"><?php echo esc_html( get_post_meta( get_the_ID(), 'ect_food_price', true ) ); ?></p>
			</div>
		</div>
	</div><!-- .hentry-inner -->
</article><!-- .hentry -->

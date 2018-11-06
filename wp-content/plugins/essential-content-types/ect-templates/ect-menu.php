<?php
/**
 * The template for displaying food_menu items
 *
 * @package Foodie_World
 */
?>

<?php
$cat_list = array();

$args['taxonomy'] = 'ect_food_menu';

$menu_categories = get_categories( $args );

foreach( $menu_categories as $category ) {
	if( false != $atts['include_type'] ) {
		if ( in_array( $category->slug, $atts['include_type'] ) ) {
		    $cat_list[] = $category->term_id;
		}
	} else {
		$cat_list[] = $category->term_id;
	}
}
?>
<div id="tabs" class="tabs">
	<div class="tabs-nav">
		<ul class="ui-tabs-nav menu-tabs-nav">
			<?php
			$taxonomy = 'ect_food_menu';

			$i = 0;
			foreach ( $cat_list as $cat ) :
				$term_obj = get_term_by( 'id', absint( $cat ), $taxonomy );
				if( $term_obj ) {
					$term_name = $cat_name[] = $term_obj->name;

					$class = 'ui-tabs-tab menu-tabs-tab';

					if ( 0 === $i ) {
						$class .= ' ui-state-active';
					}

					?>
					<li class="<?php echo $class; ?>"><a href="#tab-<?php echo esc_attr( $i + 1 ); ?>" class="ui-tabs-anchor"><?php echo esc_html( $term_obj->name ) ?></a></li>
					<?php
				}
				$i++;
			endforeach;
			?>
		</ul>
	</div><!-- .tabs-nav -->

	<?php
	$i = 0;
	foreach ( $cat_list as $cat ) :
		if( isset( $cat_name ) ) {
	?>

		<div class="ui-tabs-panel-wrap">
			<h4 class="menu-nav-collapse ui-nav-collapse<?php  echo ( 0 === $i ) ? ' ui-state-active' : ''; ?>"><a href="#tab-<?php echo esc_attr( $i + 1 ); ?>" class="ui-tabs-anchor"><?php echo esc_html( $cat_name[ $i ] ); ?></a></h4>
			<div id="tab-<?php echo esc_attr( $i + 1 ); ?>" class="menu-tabs-panel ui-tabs-panel<?php  echo ( 0 === $i ) ? ' active-tab' : ''; ?>">
				<?php
				
				$args = array();
				$args['post_type'] = Essential_Content_Food_Menu::MENU_ITEM_POST_TYPE;

				$tax_query = array(
					array(
						'taxonomy'         => Essential_Content_Food_Menu::MENU_TAX,
						'terms'            => absint( $cat ),
						'field'            => 'term_id',
					),
				);

				$args['tax_query'] = $tax_query;

				if ( false != $atts['include_tag'] ) {
		            array_push( $args['tax_query'], array(
		                'taxonomy' => Essential_Content_Food_Menu::MENU_ITEM_LABEL_TAX,
		                'field'    => 'slug',
		                'terms'    => $atts['include_tag'],
		            ) );
		        }

		        if ( false != $atts['include_type'] && false != $atts['include_tag'] ) {
		            $args['tax_query']['relation'] = 'AND';
		        }

				$loop = new WP_Query( $args );
				if ( $loop->have_posts() ) :
					while ( $loop->have_posts() ) :
						$loop->the_post();
						ect_get_template_part( 'content', 'menu', $atts );
					endwhile;
				endif;
				wp_reset_postdata();
				?>
			</div><!-- #tab-1 -->
		</div><!-- .ui-tabs-panel-wrap -->

	<?php
		}
		$i++;
	endforeach;
	?>
</div><!-- .tabs -->

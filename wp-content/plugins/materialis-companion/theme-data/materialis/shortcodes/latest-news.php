<?php

function materialis_companion_latest_news_excerpt_length()
{
    return 15;
}

function materialis_companion_latest_news_excerpt_more()
{
    return "[&hellip;]";
}


function materialis_companion_latest_news_item_meta()
{
    ?>
    <a class="post-footer-link" href="<?php echo esc_url(get_permalink()); ?>">
        <i class="mdi small mdi-comment-outline mdc-card__action mdc-card__action--icon color-darkgray" title="Comments"></i>
        <span class="post-footer-value"><?php echo get_comments_number(); ?></span>
    </a>
    <a class="post-footer-link" href="<?php echo esc_url(get_permalink()); ?>">
        <i class="mdi small mdi-clock mdc-card__action mdc-card__action--icon color-darkgray" title="Post Time"></i>
        <span class="post-footer-value"><?php the_time(get_option('date_format')); ?></span>
    </a>
    <?php
}

function materialis_companion_latest_news_normal_item($atts)
{
    ?>
    <div class="post-content no-padding <?php echo $atts['item_class']; ?>">
        <?php
        if ($atts['thumb']) : ?>
            <div class="">
                <?php materialis_print_post_thumb(); ?>
            </div>
        <?php endif; ?>

        <div class="post-content-body col-padding <?php echo($atts['meta_position'] !== 'footer' || $atts['layout'] === 'list' ? 'col-no-padding-bottom' : ''); ?> ">
            <?php if ($atts['category'] && materialis_has_category()): ?>
                <div class="space-bottom-small">
                    <?php materialis_the_category(); ?>
                </div>
            <?php endif; ?>

            <h4 class="latest-news-item-title">
                <a href="<?php the_permalink(); ?>" rel="bookmark">
                    <?php the_title(); ?>
                </a>
            </h4>

            <?php if ($atts['meta'] && $atts['meta_position'] === 'after_title'): ?>
                <div class="latest-news-item-meta after-title text-dark  space-bottom-small">
                    <?php materialis_companion_latest_news_item_meta(); ?>
                </div>
            <?php endif; ?>

            <div class="latest-news-item-excerpt">
                <?php the_excerpt(); ?>
            </div>


        </div>
        <?php if ($atts['meta_position'] !== 'footer' || $atts['layout'] === 'list'): ?>
            <div class="latest-news-item-read-more col-padding col-no-padding-bottom col-no-padding-top space-top-small space-bottom-small">
                <a href="<?php esc_url(the_permalink()); ?>" class="<?php echo esc_attr($atts['readmore_class']); ?>"><?php _e('Read more', 'materialis') ?></a>
            </div>
        <?php endif; ?>
        <?php if ($atts['layout'] === 'cards' && $atts['meta'] && $atts['meta_position'] === 'footer'): ?>
            <div class="mdc-card__actions box-padding-lr-small text-dark">
                <div class="mdc-card__action-icons col-xs-12 text-dark col-sm-fit no-padding-left">
                    <?php materialis_companion_latest_news_item_meta(); ?>
                </div>
                <div class="mdc-card__action-buttons col-xs-12 col-sm-fit no-padding">
                    <a href="<?php esc_url(the_permalink()); ?>" class="<?php echo esc_attr($atts['readmore_class']); ?>"><?php _e('Read more', 'materialis') ?></a>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
}

function materialis_companion_latest_news_overlap_item($atts)
{
    ?>
    <div class="post-content full-height-row flex-grow <?php echo $atts['item_class']; ?>">

        <div class="background-image">
            <?php materialis_print_post_thumb_image(); ?>
        </div>
        <div class="post-content-body col-padding bg-color-white">
            <?php if ($atts['category'] && materialis_has_category()): ?>
                <div class="negative-margin">
                    <?php materialis_the_category(true); ?>
                </div>
            <?php endif; ?>
            <h4 class="latest-news-item-title">
                <a href="<?php the_permalink(); ?>" rel="bookmark">
                    <?php the_title(); ?>
                </a>
            </h4>

            <?php if ($atts['meta'] && $atts['meta_position'] === 'after_title'): ?>
                <div class="latest-news-item-meta after-title space-bottom-small">
                    <?php materialis_companion_latest_news_item_meta(); ?>
                </div>
            <?php endif; ?>

            <div class="latest-news-item-excerpt">
                <?php the_excerpt(); ?>
            </div>

            <div class="latest-news-item-read-mode space-top-small">
                <a href="<?php esc_url(the_permalink()); ?>" class="button link color1 read-more negative-margin">
                    <?php _e('Read more', 'materialis') ?>
                    <i class="mdi mdi-arrow-right-thick"></i>
                </a>
            </div>
        </div>
    </div>
    <?php
}

function materialis_companion_latest_news($attrs)
{
    ob_start(); ?>
    <?php

    $atts = shortcode_atts(
        array(
            'columns'        => "4",
            'tablet_columns' => "6",
            'item_class'     => '',
            'readmore_class' => 'button color2 link',
            'posts'          => '',
            'advanced_mode'  => 'false',

            'category'      => 'yes', // none, footer, after_title
            'meta'          => 'yes', //  footer, after_title
            'meta_position' => 'footer', //  footer, after_title
            'thumb'         => 'yes',
            'spaced_posts'  => 'yes',
            'layout'        => 'cards', // cards, overlap, list
            'shadow_depth'  => '2',
        ),
        $attrs
    );

    $recentPosts = new WP_Query();

    $cols        = intval($atts['columns']);
    $tablet_cols = intval($atts['tablet_columns']);

    $post_numbers = ($atts['posts']) ? $atts['posts'] : 12 / $cols;

    add_filter('excerpt_length', 'materialis_companion_latest_news_excerpt_length');
    add_filter('excerpt_more', 'materialis_companion_latest_news_excerpt_more');

    $atts['thumb']        = materialis_to_bool($atts['thumb']);
    $atts['meta']         = materialis_to_bool($atts['meta']);
    $atts['category']     = materialis_to_bool($atts['category']);
    $atts['spaced_posts'] = materialis_to_bool($atts['spaced_posts']);

    $row_classes = array('row', 'materialis-latest-news');
    if ( ! $atts['spaced_posts']) {
        $row_classes[] = 'no-gutter-sm';
    }

    if ($atts['layout'] === 'cards') {
        $atts['item_class'] .= " mdc-card overflow-hidden mdc-elevation--z" . esc_attr($atts['shadow_depth']);
    }

    if ($atts['layout'] === 'overlap') {
        $atts['item_class'] .= " mdc-elevation--z" . esc_attr($atts['shadow_depth']);
    }


    $atts['item_class'] .= " latest-news-layout-" . esc_attr($atts['layout']);

    ?>
    <div class="<?php echo implode(" ", $row_classes); ?>">
        <?php
        $recentPosts->query('posts_per_page=' . $post_numbers . ';post_status=publish;post_type=post;ignore_sticky_posts=1;');
        while ($recentPosts->have_posts()):
            $recentPosts->the_post();
            if (is_sticky()) {
                continue;
            }
            if ($atts['advanced_mode']) {
                $categories = get_the_category();
            }
            ?>
            <div id="post-<?php the_ID(); ?>" class="col-sm-<?php echo $tablet_cols; ?> col-md-<?php echo $cols; ?> space-bottom space-bottom-xs">
                <?php
                if ($atts['layout'] === "cards" || $atts['layout'] === "list") {
                    materialis_companion_latest_news_normal_item($atts);
                } else {
                    materialis_companion_latest_news_overlap_item($atts);
                }
                ?>
            </div>
        <?php
        endwhile;
        wp_reset_postdata();
        ?>
    </div>
    <?php
    remove_filter('excerpt_length', 'materialis_companion_latest_news_excerpt_length');
    remove_filter('excerpt_more', 'materialis_companion_latest_news_excerpt_more');
    $content = ob_get_contents();
    ob_end_clean();

    return $content;

}

add_shortcode('materialis_latest_news', 'materialis_companion_latest_news');

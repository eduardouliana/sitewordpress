<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <div class="mdc-text-field">
        <input type="text" id="s" class="search-field mdc-text-field__input" name="s" value="<?php echo get_search_query(); ?>">
        <label class="mdc-text-field__label" for="s"><?php _ex('Search for:', 'label', 'materialis'); ?></label>
        <div class="mdc-line-ripple"></div>
    </div>
</form>

<?php
/**
 * PRODUCT PAGE IMAGE SLIDER MOBILE
 *
 * Moved from WPCode snippet #38453 (location: everywhere). Code unchanged.
 */

// Add shortcode [product_images_slider]
add_shortcode('product_images_slider', 'dynamic_product_images_slider');
function dynamic_product_images_slider() {
    global $product;

    if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
        return ''; // Only show on product pages
    }

    // Get gallery images (including main image)
    $attachment_ids = $product->get_gallery_image_ids();
    $main_image_id = $product->get_image_id();

    if ( $main_image_id && ! in_array( $main_image_id, $attachment_ids ) ) {
        array_unshift( $attachment_ids, $main_image_id );
    }

    if ( empty( $attachment_ids ) ) return '';

    // Enqueue Swiper JS/CSS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);

    $group_id = 'lightbox-product-gallery';

    ob_start(); ?>
    <span class="elementor-widget-container">
        <span class="elementor-shortcode">
            <span class="wood-images-container" style="display: flex; flex-wrap: wrap; gap: 0px;">
                <div class="swiper product-images-slider">
                    <div class="swiper-wrapper">
                        <?php foreach ( $attachment_ids as $attachment_id ) :
                            $image_url = wp_get_attachment_image_url( $attachment_id, 'large' );
                            $alt = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
                        ?>
                            <div class="swiper-slide">
                                <div class="custom-lightbox-attribute-wrapper" data-group="<?php echo esc_attr( $group_id ); ?>">
                                    <div class="custom-lightbox-attribute"
                                        data-image="<?php echo esc_url( $image_url ); ?>"
                                        data-caption="<?php echo esc_attr( $alt ); ?>"
                                        data-group="<?php echo esc_attr( $group_id ); ?>">
                                        <img src="<?php echo esc_url( $image_url ); ?>"
                                             alt="<?php echo esc_attr( $alt ); ?>"
                                             class="wood-images-custom-thumb swatch-trigger"
                                             style="width:100%; height:auto; cursor:pointer;">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Progress Bar Pagination (BOTTOM) -->
                    <div class="swiper-pagination progress-bar"></div>
                </div>
            </span>
        </span>
    </span>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Force progress bar to be a div (avoid blue native progress)
        document.querySelectorAll('.swiper-pagination.progress-bar').forEach(el => {
            if (el.tagName.toLowerCase() === 'progress') {
                const div = document.createElement('div');
                div.className = el.className;
                el.replaceWith(div);
            }
        });

        new Swiper('.product-images-slider', {
            loop: true,
            slidesPerView: 1,
            spaceBetween: 10,
            pagination: {
                el: '.swiper-pagination',
                type: 'progressbar',
            },
        });
    });
    </script>

    <style>
    /* Container setup */
    .product-images-slider {
        width: 100%;
        max-width: 600px;
        margin: auto;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .product-images-slider img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Progress bar (bottom position) */
    .swiper-pagination.progress-bar {
        order: 2;
        position: relative;
        width: 100%;
        height: 4px;
        background: rgba(0,0,0,0.15);
        overflow: hidden;

        border: none;
    }

    .swiper-pagination-progressbar-fill {
        background: #000 !important;
        height: 100%;
        transition: transform 0.3s ease;
        transform-origin: left center;
    }

    /* Remove native iOS blue bar rendering */
    .swiper-pagination.progress-bar,
    .swiper-pagination.progress-bar *,
    .swiper-pagination-progressbar,
    .swiper-pagination-progressbar * {
        -webkit-appearance: none !important;
        appearance: none !important;
        accent-color: transparent !important;
        color: transparent !important;
        border: none !important;
        outline: none !important;
    }

    /* Prevent blue active/focus glow */
    .swiper-pagination.progress-bar:active,
    .swiper-pagination.progress-bar:focus,
    .swiper-pagination-progressbar:active,
    .swiper-pagination-progressbar:focus {
        outline: none !important;
        background: rgba(0,0,0,0.15) !important;
        -webkit-tap-highlight-color: transparent !important;
    }

    .product-images-slider .custom-lightbox-attribute-wrapper {
        margin: -1px;
    }
    </style>
    <?php

    return ob_get_clean();
}


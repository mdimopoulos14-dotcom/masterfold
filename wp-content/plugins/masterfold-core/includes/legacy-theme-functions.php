<?php
/**
 * Custom functionality formerly in themes/hello-elementor/functions.php (moved verbatim).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/*CUSTOM START*/


// --- 1. Shortcode Function: Outputs Filters and Pre-loaded Grid ---
function get_dynamic_filtering_shortcode() {
    ob_start();

    if ( ! is_product_category() ) {
        echo '<p>This feature is active only on WooCommerce category archive pages.</p>';
        return ob_get_clean();
    }

    $current_term = get_queried_object();
    if ( ! $current_term || ! isset( $current_term->term_id ) ) {
        return ob_get_clean();
    }
    $current_id = $current_term->term_id;

    // --- Collect Data ---
    $product_ids_in_current_cat = get_objects_in_term( $current_id, 'product_cat' );

    $products_query = new WP_Query([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'post__in'       => $product_ids_in_current_cat,
        'orderby'        => 'menu_order title', 
        'order'          => 'ASC',
    ]);

    $product_data = [];
    if ($products_query->have_posts()) {
        foreach ($products_query->posts as $p) {
            $product_data[$p->ID] = [
                'cats' => wp_get_post_terms($p->ID, 'product_cat', ['fields' => 'slugs']),
                'tags' => wp_get_post_terms($p->ID, 'product_tag', ['fields' => 'slugs']),
            ];
        }
    }

    $sub_categories = get_terms( 'product_cat', ['parent' => $current_id, 'hide_empty' => true] );
    $product_tags = get_terms( 'product_tag', [
        'hide_empty' => true,
        'object_ids' => $product_ids_in_current_cat,
    ] );

    ?>
    <div class="dynamic-filter-container">
        <script type="application/json" id="product-data-json">
            <?php echo json_encode($product_data); ?>
        </script>
        
        <div class="filter-controls-wrapper">
            <div class="filter-group subcategories-group">
                <select class="filter-dropdown category-dropdown" data-filter-type="category">
                    <option value="all-results" selected>All Format</option>
                    <?php foreach ($sub_categories as $cat) : ?>
                        <option value="<?php echo esc_attr($cat->slug); ?>"><?php echo esc_html($cat->name); ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="filter-tabs category-tabs" data-filter-type="category">
                    <a href="#" class="filter-tab category-filter active" data-slug="all-results">All Format</a>
                    <?php foreach ($sub_categories as $cat) : ?>
                        <a href="#" class="filter-tab category-filter" data-slug="<?php echo esc_attr($cat->slug); ?>">
                            <?php echo esc_html($cat->name); ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-group tags-group">
                <select class="filter-dropdown tag-dropdown" data-filter-type="tag">
                    <option value="all-results" selected>All Usage</option>
                    <?php foreach ($product_tags as $tag) : ?>
                        <option value="<?php echo esc_attr($tag->slug); ?>"><?php echo esc_html($tag->name); ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="filter-tabs tag-tabs" data-filter-type="tag">
                    <?php foreach ($product_tags as $tag) : ?>
                        <a href="#" class="filter-tab tag-filter" data-slug="<?php echo esc_attr($tag->slug); ?>">
                            <span><?php echo esc_html($tag->name); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div id="filtered-products-grid" class="woocommerce columns-3">
            <ul class="products">
                <?php
                if ( $products_query->have_posts() ) {
                    while ( $products_query->have_posts() ) {
                        $products_query->the_post();
                        // Wrap in a div for JS to target
                        echo '<div class="js-filterable-product" data-pid="' . get_the_ID() . '">';
                        wc_get_template_part( 'content', 'product' );
                        echo '</div>';
                    }
                }
                wp_reset_postdata();
                ?>
            </ul>
            <p class="no-products-found" style="display:none; text-align:center; width:100%;">No products match the selected filters.</p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'dynamic_filtering_tabs', 'get_dynamic_filtering_shortcode' );

// --- 2. Enqueue Assets ---
function dynamic_filtering_enqueue_assets() {
    if ( ! is_product_category() ) return;

    wp_enqueue_script( 'jquery' );
    wp_register_script( 'dynamic-filtering-js', false, array('jquery'), null, true );
    wp_enqueue_script( 'dynamic-filtering-js' );

    $js_code = <<<EOT
    jQuery(document).ready(function($) {
        const \$allProducts = $(".js-filterable-product");
        const \$noResults = $(".no-products-found");
        const allProductData = JSON.parse($("#product-data-json").html() || "{}");

        let activeCategory = "all-results"; 
        let activeTag = "all-results"; 

        // 1. Lock Dropdowns
        const lockDropdownWidths = () => {
            if ($(window).width() > 768) return;
            $(".filter-tabs").show();
            $(".filter-dropdown").each(function() {
                const \$dropdown = $(this);
                let maxWidth = 0;
                const \$tempSpan = $("<span>").css({
                    "visibility": "hidden", "white-space": "nowrap", "position": "absolute",
                    "font-size": \$dropdown.css("font-size"), "font-family": \$dropdown.css("font-family")
                }).appendTo("body");
                \$dropdown.find("option").each(function() {
                    \$tempSpan.text($(this).text());
                    maxWidth = Math.max(maxWidth, \$tempSpan.width());
                });
                \$tempSpan.remove();
                const lockedPixelWidth = maxWidth + 50; 
                \$dropdown.css({"width": lockedPixelWidth + "px", "min-width": lockedPixelWidth + "px"});
            });
            $(".filter-tabs").hide(); 
        };

        // 2. Clean Images (Runs once)
        const cleanImageUrls = () => {
            \$allProducts.find('img').each(function() {
                const \$img = $(this);
                let src = \$img.attr('src');
                if (src) {
                    \$img.attr('src', src.replace(/-\d+x\d+(\.[\w\d]+)$/, '$1')).removeAttr('srcset');
                }
            });
        };

        // 3. Sync UI States
        const syncUI = () => {
            $(".category-filter").removeClass("active");
            $(".category-filter[data-slug='" + activeCategory + "']").addClass("active");
            $(".category-dropdown").val(activeCategory);
            $(".tag-filter").removeClass("active");
            if(activeTag !== "all-results") $(".tag-filter[data-slug='" + activeTag + "']").addClass("active");
            $(".tag-dropdown").val(activeTag);
        };

        // 4. Instant Filter Logic
        const renderProducts = () => {
            let count = 0;
            \$allProducts.each(function() {
                const pid = $(this).data("pid");
                const data = allProductData[pid];
                const matchCat = (activeCategory === "all-results" || data.cats.includes(activeCategory));
                const matchTag = (activeTag === "all-results" || data.tags.includes(activeTag));
                
                if (matchCat && matchTag) {
                    $(this).show();
                    count++;
                } else {
                    $(this).hide();
                }
            });
            count === 0 ? \$noResults.show() : \$noResults.hide();
            syncUI();
            updateTagOpacity();
        };

        // 5. Tag Opacity Logic
        const updateTagOpacity = () => {
            $(".tag-filter").removeClass("dimmed");
            $(".tag-dropdown option").prop("disabled", false);
            if (activeCategory === "all-results") return;

            $(".tag-filter").each(function() {
                const tagSlug = $(this).data("slug");
                if (tagSlug === "all-results") return;
                const possible = Object.keys(allProductData).some(id => 
                    allProductData[id].cats.includes(activeCategory) && allProductData[id].tags.includes(tagSlug)
                );
                if (!possible) {
                    $(this).addClass("dimmed");
                    $(".tag-dropdown option[value='" + tagSlug + "']").prop("disabled", true);
                }
            });
        };

        // Listeners
        $(".filter-tabs").on("click", ".filter-tab", function(e) {
            e.preventDefault();
            const type = $(this).parent().data("filter-type");
            const slug = $(this).data("slug");
            if (type === "category") activeCategory = slug;
            else activeTag = (activeTag === slug) ? "all-results" : slug;
            renderProducts();
        });

        $(".filter-dropdown").on("change", function() {
            const type = $(this).data("filter-type");
            const slug = $(this).val();
            if (type === "category") activeCategory = slug;
            else activeTag = slug;
            renderProducts();
        });

        cleanImageUrls();
        lockDropdownWidths();
        renderProducts();
        $(window).on("resize orientationchange", lockDropdownWidths);
    });
EOT;

    wp_add_inline_script( 'dynamic-filtering-js', $js_code );

$css_code = '
        /* Desktop Grid */
        .js-filterable-product { display: inline-block; width: 32.333%; vertical-align: top; }
        .dynamic-filter-container { margin-bottom: 30px; }
        #filtered-products-grid ul.products { display: flex !important; flex-wrap: wrap !important; gap: 15px; justify-content: flex-start; list-style: none; padding: 0; }
        #filtered-products-grid ul.products li.product { width: 100% !important; margin: 0 !important; }

        /* UI Styles */
        .filter-controls-wrapper { display: flex; flex-direction: column; align-items: center; }
        .filter-group { width: 100%; margin-bottom: 15px; }
        .filter-tabs { display: flex; flex-wrap: wrap; justify-content: center; }
        .filter-tab { display: inline-block; padding: 8px 15px; margin: 5px; border: 1px solid #ccc; background: #f8f8f8; cursor: pointer; text-decoration: none; color: #333; border-radius: 4px; }
        .category-filter { min-width: 167px; font-weight: bold; text-transform: uppercase; border-color: #414042; background: white; border-radius: 8px; }
        .filter-tab.active, .tag-filter.active span { background-color: #e4e5e6 !important; color: #414042 !important; }
        .tag-filter.dimmed { opacity: 0.2 !important; pointer-events: none; }

        /* Mobile Optimization (2 Columns) */
        @media (max-width: 768px) {
            .js-filterable-product { width: calc(50% - 10px); margin-bottom: 20px; }
            #filtered-products-grid ul.products { justify-content: space-between; gap: 0; }
            .filter-dropdown { display: block !important; width: 100% !important; padding: 10px; border-radius: 8px; border: 1px solid #ccc; -webkit-appearance: none; appearance: none; background: #fff url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23414042%22 stroke-width=%223%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 10px center / 14px 14px; }
            .filter-tabs { display: none !important; }
            .filter-controls-wrapper { flex-direction: row; justify-content: space-between; gap: 10px; }
            .filter-group { width: 50% !important; margin-bottom: 0; }
            .woocommerce-loop-product__title { font-size: 12px !important; }
        }
        .no-products-found { text-align: center; padding: 30px; width: 100%; font-weight: bold; }
		
		
		
		
		
		
		
		
		.dynamic-filter-container { margin-bottom: 30px; }
 
/* Width Lock Class (For Dropdown Stability) */
.filter-controls-wrapper.width-locked {
    width: 100% !important; /* Lock the wrapper to full width */
    max-width: 100% !important; 
}

/* PC/Desktop Layout (DEFAULT) - Stacked Vertically */
.filter-controls-wrapper { 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
}
.filter-group { 
    width: 100%; 
    max-width: 100%;
    margin: 0 0 15px 0; 
}
.filter-tabs { 
    display: flex; 
    flex-wrap: wrap; 
    margin-bottom: 15px; 
    justify-content: center; 
}
.filter-dropdown { 
    display: none; 
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    background-color: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    box-sizing: border-box; 
    
    /* Color Fixes */
    color: #333 !important; 
    border-color: #ccc !important; 
}

/* General Filter Tab Styles */
.filter-tab {
    display: inline-block; padding: 8px 15px; margin: 5px 5px 5px 0; border: 1px solid #ccc;
    background-color: #f8f8f8; color: #333; font-size: 14px; 
    text-decoration: none; border-radius: 4px; transition: all 0.2s, opacity 0.3s;
    cursor: pointer;
}

/* Category Filter Specific Styling (PC/Tabs) */
.category-filter {
    min-width: 167px; border-color: #414042; text-align: center; text-transform: uppercase;
    background-color: white; padding: 5px 0px; border-radius: 8px; font-weight: bold;
}
.category-filter:hover {
    background-color: #414042 !important; color: white !important;
}
.filter-tab.category-filter.active {
    background-color: #e4e5e6 !important; color: #414042!important; border-color: #e4e5e6 !important;
}
.subcategories-group .category-tabs { gap: 10px; margin-top: 50px; }

/* Tag Filter Specific Styling (PC/Tabs) */
a.filter-tab.tag-filter {
    border: 1px solid white; background-color: white; font-weight: normal; position: relative;
    padding: 0px 0px !important; margin-left: 10px !important; margin-right: 15px !important; 
}
a.filter-tab.tag-filter span {
    width: 100px !important; text-align: center; padding: 5px 0px !important;
    display: flex; justify-content: center; border-radius: 6px;
}
a.filter-tab.tag-filter:hover {
    font-weight: bold; border-bottom: 1px solid #414042; border-radius: 0px; color: #414042;
}
a.filter-tab.tag-filter.active { 
    border-bottom: 1px solid white; 
}
 
/* Pseudo-element separator for all tag filters EXCEPT the last one */
.tag-tabs a.filter-tab.tag-filter:not(:last-child)::after {
    content: ""; position: absolute; top: 50%; right: -10px;
    transform: translateY(-50%); width: 1px; height: 90%;
    background-color: #ccc; opacity: 0.7;
}
.tags-group .tag-tabs { margin-bottom: 55px; margin-top: 15px; }
 
/* Active State for Tags */
.tag-filter.active span {
    background-color: #e4e5e6 !important; color: #414042 !important;
}
a.filter-tab.category-filter.active {
    font-weight: normal;
}
.qwfw-add-to-wishlist-wrapper.qwfw--loop.qwfw-position--before-add-to-cart.qwfw-item-type--icon {
    display: none;
}

/* Dimmed State (No matching products) */
.tag-filter.dimmed { opacity: 0.2!important; pointer-events: none; }
.dimmed-option { background-color: #f0f0f0; color: #999; }
 
/* Product Grid Rendering FIX (PC - 3 Columns) */
#filtered-products-grid { margin-top: 20px; clear: both; min-height: 1000px; }
#filtered-products-grid ul.products { list-style: none; padding: 0; margin: 0; display: flex; flex-wrap: wrap; }
#filtered-products-grid ul.products > li { float: left !important; clear: none !important; margin-right: 0 !important; }
#filtered-products-grid.woocommerce.columns-3 ul.products li.product {
    width: calc(100% - 0px)!important; margin-right: 15px; margin-bottom: 30px;
}
#filtered-products-grid.woocommerce.columns-3 ul.products li.product:nth-child(3n) { margin-right: 0 !important; }

/* Mobile/Tablet Breakpoint (Max 768px wide screens) */
@media (max-width: 768px) {
#filtered-products-grid h2.woocommerce-loop-product__title {
    font-size: 12px ! Important;
}


body .woocommerce ul.products li.product .woocommerce-loop-category__title, .woocommerce ul.products li.product .woocommerce-loop-product__title, .woocommerce ul.products li.product h3 {
    font-size: 12px !important;
}
p.product-loop-id {
    font-size: 12px !important;
}
    
    /* Filter Layout: Side-by-Side Dropdowns */
    .filter-controls-wrapper {
        flex-direction: row; 
        justify-content: space-between; 
    }
    
    /* Ensure parent container is stable (reverting to fluid calc) */
    .filter-group {
        width: calc(50% - 10px) !important; 
        min-width: calc(50% - 10px) !important;
        margin: 0 5px 15px 5px; 
    }

    /* AGGRESSIVE FIX: Lock the select element itself */
    .filter-dropdown { 
        display: block; 
        width: 100% !important; 
        min-width: 100% !important;

        /* --- START OF ARROW ADDITION --- */
        /* Hide default arrow for maximum browser consistency */
        -webkit-appearance: none !important; 
        -moz-appearance: none !important; 
        appearance: none !important; 
        
        /* Add custom down arrow using an SVG data URI (Black/Dark Gray) */
        background-image: url("data:image/svg+xml;charset=UTF-8,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2212%22 height=%2212%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%23414042%22 stroke-width=%223%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22%3E%3Cpolyline points=%226 9 12 15 18 9%22%3E%3C/polyline%3E%3C/svg%3E") !important;
        background-repeat: no-repeat !important;
        background-position: right 10px center !important;
        background-size: 14px 14px !important;
        
        /* Add padding on the right to make space for the arrow */
        padding-right: 30px !important;
        /* --- END OF ARROW ADDITION --- */
        
        box-sizing: border-box !important;
        
        /* Color Fixes (Mobile specific) */
        color: #f0f0f0 !important
        border-color: #ccc !important; 
        
        /* Setting active state/focus color to override browser default blue */
        border-color: #f0f0f0 !important

    }
    
    .filter-dropdown:focus {
        border-color: #f0f0f0 !important
 
    }
    
    /* Product Grid Layout: 2 Columns */
    #filtered-products-grid.woocommerce.columns-3 ul.products li.product {
        width: calc(100% - 0px)!important; 
        margin-right: 20px;
        margin-bottom: 20px;
    }
    #filtered-products-grid.woocommerce.columns-3 ul.products li.product:nth-child(2n) {
        margin-right: 0 !important;
    }
    #filtered-products-grid.woocommerce.columns-3 ul.products li.product:nth-child(3n) {
        margin-right: 20px; 
    }
    #filtered-products-grid.woocommerce.columns-3 ul.products li.product:nth-child(odd) {
        clear: both !important;
    }
    
    /* Remove separator from tags on mobile */
    .tag-tabs a.filter-tab.tag-filter:not(:last-child)::after {
        content: none;
    }
    /* Reset specific margins */
    .subcategories-group .category-tabs, .tags-group .tag-tabs {
         margin-top: 0; margin-bottom: 0;
    }
}
.filter-controls-wrapper select {
    min-width: 180px !important;
    max-width: 180px !important;
    margin: auto;
}
#filtered-products-grid ul.products {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
}





.woocommerce .products ul::after, .woocommerce .products ul::before, .woocommerce ul.products::after, .woocommerce ul.products::before{display:none!important}

/* Utility Styles */
.loading-products, .error-products, .initial-prompt { text-align: center; padding: 30px; font-style: italic; color: #555; }
.no-products-found { text-align: center; padding: 30px; font-weight: bold; color: #d9534f; }
.product-loop-id { margin-left: 10px; margin-top: 5px; font-size: 0.8em; color: #777; }







/* Default (PC / Desktop) */
.js-filterable-product {
    display: inline-block;
    width: 32.7%;
    vertical-align: top;
}

/* Tablet */
@media (max-width: 1024px) {
#filtered-products-grid ul.products {
    gap: 6px;
}
    .js-filterable-product {
        width: 49.06%;
    }
}


#filtered-products-grid ul.products {
    justify-content: left !important;
}

    ';
    wp_register_style( 'dynamic-filtering-css', false );
    wp_enqueue_style( 'dynamic-filtering-css' );
    wp_add_inline_style( 'dynamic-filtering-css', $css_code );
}
add_action( 'wp_enqueue_scripts', 'dynamic_filtering_enqueue_assets' );





/**
 * Capture custom fields from Qode Wishlist and store them for the email
 */
add_filter( 'qode_wishlist_for_woocommerce_filter_estimate_params', function( $params ) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // If the message is trapped inside 'options', pull it out to the main level
    if (isset($data['options']['message'])) {
        $params['message'] = sanitize_textarea_field($data['options']['message']);
    }

    return $params;
}, 99);








add_action( 'wp_footer', function() {
    ?>
    <script type="text/javascript">
    (function($) {
        // 1. The Sync Logic
        function syncDataToRealMessage() {
            // Get basic values
            var country   = $('#country_code').val() || '+30';
            var phone     = $('input[name="phone_number"]').val() || '';
            var fullPhone = (phone.trim() === '') ? 'Not Provided' : (country + " " + phone); 

            var firstName = $('input[name="first_name"]').val() || '';
            var lastName  = $('input[name="last_name"]').val() || '';
            var fullName  = (firstName + " " + lastName).trim() || 'Not Provided';

            var reqId     = $('input[name="request_id"]').val() || 'XXX-XXX-XXX';
            var dateVal   = $('input[name="today_date"]').val() || '';
            var userNote  = $('#fake_message').val() || '';

            // NEW FIELDS: We must include these so the PHP Regex matches correctly
            var company   = $('input[name="company_name"]').val() || '-';
            var job       = $('input[name="job_title"]').val() || '-';
            var industry  = $('input[name="industry"]').val() || '-';

            // IMPORTANT: The order here must match the order in the PHP email template exactly
            var dataString = "\n\n[DATA] ID: " + reqId + 
                             " | Name: " + fullName + 
                             " | Company: " + company + 
                             " | Phone: " + fullPhone + 
                             " | Job: " + job + 
                             " | Industry: " + industry + 
                             " | Date: " + dateVal + " [/DATA]";

            $('#real_user_message').val(userNote + dataString);
        }

        // 2. The Country Code Guard
        $(document).on('input', '#country_code', function() {
            var val = $(this).val();
            if (!val.startsWith('+')) {
                val = '+' + val.replace(/\+/g, '');
            }
            var cleanVal = '+' + val.substring(1).replace(/[^\d]/g, '');
            $(this).val(cleanVal);
        });

        $(document).on('keydown', '#country_code', function(e) {
            var val = $(this).val();
            if ((e.which === 8 || e.which === 46) && val.length <= 1) {
                e.preventDefault();
            }
        });

        // 3. Event Triggers
        $(document).on('keyup blur change', 'input, textarea, #fake_message', function() {
            syncDataToRealMessage();
        });
        
        // Initial run
        setTimeout(syncDataToRealMessage, 1000);

    })(jQuery);
    </script>
    <?php
}, 20);


//VIEW IN BROWSER WISHLIST
/**
 * 1. Register a hidden Archive for the Web Version of quotes
 */
add_action( 'init', function() {
    register_post_type( 'quote_web_version', array(
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'exclude_from_search'=> true,
        'rewrite'            => array( 'slug' => 'view-quote' ),
        'supports'           => array( 'title', 'editor' ),
    ) );
});

/**
 * 2. Catch the email, save it to the database, and generate the link
 */


/**
 * 1. Handle Admin Email Formatting & Web Version
 */
add_filter( 'wp_mail', function( $args ) {
    if ( strpos( $args['subject'], 'Confirmation:' ) !== false ) return $args;

    if ( strpos( $args['message'], 'QUOTE REQUEST' ) !== false ) {
        
        // 1. EXTRACT ID
        preg_match( '/ID:\s*([^\s|]+)/', $args['message'], $id_matches );
        $quote_id = !empty($id_matches[1]) ? trim($id_matches[1]) : 'Request';

        // 2. EXTRACT NAME (Your original multi-source logic)
        $extracted_name = '';
        if ( preg_match( '/Name:\s*(.*?)\s*\|/s', $args['message'], $name_matches ) ) {
            $extracted_name = trim($name_matches[1]);
        }
        if ( empty($extracted_name) ) {
            if ( !empty($_POST['full_name']) ) { $extracted_name = sanitize_text_field($_POST['full_name']); }
            elseif ( !empty($_POST['name']) ) { $extracted_name = sanitize_text_field($_POST['name']); }
            elseif ( !empty($_POST['billing_first_name']) ) { $extracted_name = sanitize_text_field($_POST['billing_first_name']); }
        }
        if ( empty($extracted_name) && is_user_logged_in() ) {
            $extracted_name = wp_get_current_user()->display_name;
        }
        // Extract first name only
if ( !empty($extracted_name) ) {
    $name_parts = explode(' ', trim($extracted_name));
    $display_name = $name_parts[0]; // Gets just the first word
} else {
    $display_name = 'Customer';
}
        // 3. CLEAN CONTENT FOR WEB VERSION (Stops CSS showing as text)
        $clean_web_content = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $args['message']);
        $clean_web_content = preg_replace('/\s*\[DATA\].*?\[\/DATA\]/s', '', $clean_web_content);

        // 4. INSERT POST
        $post_id = wp_insert_post( array(
            'post_title'   => "Quote #$quote_id",
            'post_content' => $clean_web_content, 
            'post_status'  => 'publish',
            'post_type'    => 'quote_web_version',
            'post_name'    => 'q-' . $quote_id . '-' . wp_generate_password(4, false),
        ) );

        $web_url = '';
        if ( $post_id ) {
            $web_url = get_permalink( $post_id );
            $args['message'] = str_replace( '%%BROWSER_LINK%%', $web_url, $args['message'] );
        }

// 5. SEND CUSTOMER CONFIRMATION
$sender_email = !empty($_POST['email']) ? sanitize_email($_POST['email']) : (is_user_logged_in() ? wp_get_current_user()->user_email : '');

if ( !empty($sender_email) ) {
    $display_id   = $quote_id;
    $display_name = $display_name; // Ensure name is passed
    $view_link    = $web_url;      // This is the link to the ACTUAL QUOTE for the button

    // Generate the HTML for the confirmation email
    ob_start();
    // Use locate_template to ensure we get your custom version in the theme
    if ( $template = locate_template( 'email-confirmation.php' ) ) {
        include( $template );
    } else {
        // Fallback to plugin path if theme version isn't found
        include( WP_PLUGIN_DIR . '/qode-wishlist-for-woocommerce-premium/inc/ask-for-estimate/templates/parts/email-confirmation.php' );
    }
    $cust_body_raw = ob_get_clean();

    // Create a Web Version for the CONFIRMATION email (the "View in Browser" page)
    $conf_post_id = wp_insert_post( array(
        'post_title'   => "Confirmation #$quote_id",
        'post_content' => $cust_body_raw, 
        'post_status'  => 'publish',
        'post_type'    => 'quote_web_version',
        'post_name'    => 'c-' . $quote_id . '-' . wp_generate_password(4, false),
    ) );

    $confirmation_web_url = $conf_post_id ? get_permalink($conf_post_id) : '';

    // Replace placeholders: 
    // 1. View in Browser link (at top)
    // 2. View Request button (inside content)
    $cust_body = str_replace('%%BROWSER_LINK%%', $confirmation_web_url, $cust_body_raw);
    $cust_body = str_replace('<?php echo esc_url($view_link); ?>', $view_link, $cust_body); // Safety check

    $cust_headers = array('Content-Type: text/html; charset=UTF-8', 'From: MasterFold <' . get_option( 'admin_email' ) . '>');
    wp_mail( $sender_email, "Confirmation: We've received your Request | MasterFold", $cust_body, $cust_headers );
}

        // 6. ADMIN SUBJECT
        $args['subject'] = "Quote Request - MasterFold";
    }
    return $args;
}, 10);
/**
 * 2. Send Customer Confirmation Separately
 * This hook fires only after the main mail attempt is processed.
 */
//add_action( 'wp_mail_failed', 'send_customer_confirmation_on_success', 10, 1 ); // Just in case
add_action( 'phpmailer_init', function($phpmailer) {
    // We use a global variable to store data temporarily during the request
    global $customer_quote_data;
    
    // Check if this is the Quote Request being sent
    if ( strpos( $phpmailer->Body, 'QUOTE REQUEST' ) !== false ) {
        preg_match( '/\[DATA\] ID: (.*?) \|/', $phpmailer->Body, $matches );
        $customer_quote_data = !empty($matches[1]) ? trim($matches[1]) : 'Request';
    }
});

// This fires after wp_mail finishes
add_action( 'shutdown', function() {
    global $customer_quote_data;

    // Only run if we actually captured a Quote ID in this session
    if ( !empty($customer_quote_data) ) {
        $sender_email = !empty($_POST['email']) ? sanitize_email($_POST['email']) : (is_user_logged_in() ? wp_get_current_user()->user_email : '');

        if ( !empty($sender_email) ) {
            $confirm_subject = "We've received your Request #$customer_quote_data - MasterFold";
            $display_id = $customer_quote_data;

            ob_start();
            if ( $template = locate_template( 'email-confirmation.php' ) ) {
                include( $template );
            }
            $confirm_body = ob_get_clean();

            $headers = array(
                'Content-Type: text/html; charset=UTF-8',
                'From: MasterFold <' . get_option( 'admin_email' ) . '>'
            );

            // Using standard mail or wp_mail here is safe because the 
            // main process is shutting down and won't loop back.
            wp_mail( $sender_email, $confirm_subject, $confirm_body, $headers );
        }
    }
});
/**
 * 3. Force the web page to look like the email (renders CSS correctly)
 */
add_filter( 'template_include', function( $template ) {
    if ( is_singular('quote_web_version') ) {
        if ( have_posts() ) : the_post();
            
            $content = get_the_content();
            
            // 1. Hide the "View in Browser" text so it doesn't show on the actual web page
            $content = preg_replace('/Trouble viewing.*?View in browser/is', '', $content);
            
            echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Quote Archive</title>';
            
            // 2. If the saved content doesn't have its own <style> tags, add basic layout
            if ( strpos( $content, '<style' ) === false ) {
                echo '<style>body { background:#eeeeee; margin:0; padding:20px; font-family: Helvetica, Arial, sans-serif; }</style>';
            }
            
            echo '</head><body style="margin:0; padding:0;">';
            echo '<div style="max-width:600px; margin:0 auto; padding: 20px 0;">';
            
            // 3. Output the email content
            echo $content;
            
            echo '</div></body></html>';
        endif;
        exit;
    }
    return $template;
});

add_action( 'phpmailer_init', function( $phpmailer ) {
    // Only apply this to your quote/wishlist emails
    if ( strpos( $phpmailer->Subject, 'Request' ) !== false ) {
        // Force the mailer to use the server's binary directly
        // This bypasses the local 'SMTP' check that Plesk uses
        $phpmailer->Mailer = 'sendmail';
        $phpmailer->Sendmail = '/usr/sbin/sendmail -t -i -f info@masterfold.com';
        
        // This 'f' flag above is the "Envelope Sender" 
        // It tells the server 'I am authorized'
    }
}, 999 );

add_shortcode( 'only_account_details', 'render_only_account_details' );
function render_only_account_details() {
    if ( ! is_user_logged_in() ) return 'Please log in to view your details.';
    
    // 1. Hide the password fieldset using CSS specifically for this shortcode
    $html = '<style>.only-details-form fieldset { display: none !important; }</style>';
    $html .= '<div class="woocommerce only-details-form">';
    
    // 2. Capture the standard edit-account form
    ob_start();
    wc_get_template( 'myaccount/form-edit-account.php', array( 
        'user' => get_user_by( 'id', get_current_user_id() ) 
    ) );
    $html .= ob_get_clean();
    
    $html .= '</div>';
    return $html;
}













/**
 * Send a vibrant Welcome Email with a working "View in Browser" link.
 */
/**
 * Custom Vibrant Welcome Email for WooCommerce Registration
 */
/**
 * Custom Vibrant Welcome Email for WPEverest User Registration
 */
/**
 * Custom Vibrant Welcome Email with Verification Link for WPEverest User Registration
 */
/**
 * Custom Vibrant Welcome Email for WPEverest User Registration
 */
/**
 * Generates the "View in Browser" version for User Registration Emails
 */
/**
 * Automatically creates a browser-viewable version of the registration email
 */
/**
 * Automatically creates a browser-viewable version of the registration email
 */
/**
 * Captures the email and saves it to a post
 */
/**
 * Force Register the post type correctly to ensure it is viewable
 */
/**
 * Saves the email to user meta and generates a safe browser link
 */
/**
 * 1. Capture the email and save it to the user's profile
 */
/**
 * 1. Capture the email and save it to user meta
 */
/**
 * 1. Capture the email and save it to user meta (High Priority)
 */
/**
 * 1. Capture the email and save it to user meta
 */
/**
 * CUSTOM MASTERFOLD WELCOME EMAIL
 * Triggered after a user registers via User Registration plugin
 */
/**
 * CUSTOM MASTERFOLD WELCOME EMAIL
 * Triggered after the user is fully registered and metadata is saved
 */
/**
 * CUSTOM MASTERFOLD WELCOME EMAIL
 * Triggered after registration - Forces token creation if missing
 */
/**
 * CUSTOM MASTERFOLD WELCOME EMAIL
 * Triggered after registration - Includes User ID in link to fix plugin warning
 */
/**
 * FINAL MASTERFOLD WELCOME EMAIL
 * Sends custom black-themed email with working Browser Link and Verification Link
 */
/**
 * FINAL MASTERFOLD WELCOME EMAIL (Merged Token Fix)
 */
/**
 * FINAL MASTERFOLD WELCOME EMAIL
 * Uses the plugin's internal generator to ensure the link is 100% correct.
 */


/**
 * MASTERFOLD FINAL WELCOME EMAIL
 * Uses the plugin's own internal logic to generate the long hashed URL
 */
/**
 * MASTERFOLD FINAL WELCOME EMAIL
 * Sends custom email AFTER successful registration without blocking form fields.
 */
/**
 * 1. Register a hidden post type to store emails
 */
/**
 * 1. Register a hidden post type to store emails
 */
add_action('init', function() {
    register_post_type('sent_email_web_view', [
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => false,
        'exclude_from_search' => true,
        'has_archive' => false,
        'rewrite' => ['slug' => 'view-email'],
    ]);
});

/**
 * 2. Catch every email and inject the link
 */
add_filter('wp_mail', function($args) {
    if (empty($args['message'])) return $args;

    // Create a unique secret token
    $token = wp_generate_password(24, false);
    
    $post_id = wp_insert_post([
        'post_type'   => 'sent_email_web_view',
        'post_title'  => $args['subject'],
        'post_content'=> $args['message'],
        'post_status' => 'publish',
        'post_name'   => $token 
    ]);

    if ($post_id) {
        $view_url = home_url("/view-email/{$token}/");
        
        // Link style
        $browser_link = '<div style="text-align:center; font-size:12px; padding:10px; color:#777; font-family:sans-serif;">';
        $browser_link .= 'Email not displaying correctly? <a style="color:#f7941d;" href="'.$view_url.'" target="_blank">View in Browser</a>';
        $browser_link .= '</div><hr style="border:0; border-top:1px solid #eee; margin-bottom:20px;">';

        // Check if message is a string (sometimes it's an array, though rare for body)
        if (is_string($args['message'])) {
            $args['message'] = $browser_link . $args['message'];
        }
    }

    return $args;
});
/**
 * 3. Display the email content (Fixed the "Fatal Error")
 */
add_action('template_redirect', function() {
    if (is_singular('sent_email_web_view')) {
        $post = get_queried_object(); // Corrected function
        
        if (!$post) return;

        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html><head><title>'.esc_html($post->post_title).'</title>';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
        //echo '<style>body{margin:0; padding:20px; background:#f0f2f5; font-family:sans-serif;} .email-container{max-width:700px; margin:auto; background:#fff; padding:30px; border:1px solid #d1d1d1; box-shadow: 0 2px 10px rgba(0,0,0,0.05);}</style>';
        echo '</head><body style=" background-color: #eeeeee; "><div class="email-container">';
        // We use the raw content because email templates already contain their own HTML
        echo $post->post_content; 
        echo '</div></body></html>';
        exit;
    }
});

/**
 * 4. Cleanup: Delete emails older than 7 days automatically
 */
if (!wp_next_scheduled('cleanup_old_web_emails')) {
    wp_schedule_event(time(), 'daily', 'cleanup_old_web_emails');
}

add_action('cleanup_old_web_emails', function() {
    $old_emails = get_posts([
        'post_type'  => 'sent_email_web_view',
        'numberposts' => 100,
        'date_query' => [
            'before' => '7 days ago',
        ],
        'fields' => 'ids',
    ]);

    foreach ($old_emails as $id) {
        wp_delete_post($id, true); // true = bypass trash
    }
});




/**
 * Disable the new user registration email sent to the user
 */
add_filter( 'wp_new_user_notification_email', 'disable_new_user_activation_email', 10, 3 );

function disable_new_user_activation_email( $wp_new_user_notification_email, $user, $blogname ) {
    // Returning null or an empty array prevents the email from being sent
    return;
}



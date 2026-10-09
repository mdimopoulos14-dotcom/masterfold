<?php
/**
 * PRODUCT SEARCH
 *
 * Moved from WPCode snippet #30858 (location: everywhere). Code unchanged.
 */

add_shortcode('ajax_woocommerce_search', 'ajax_woocommerce_search_shortcode');

function ajax_woocommerce_search_shortcode() {
    ob_start();
    ?>
    <span class="span-product-search-input">
        <svg style="width:33px; height:33px;" class="product-search-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18">
            <path d=" M 16.722523,17.901412 C 16.572585,17.825208 15.36088,16.670476 14.029846,15.33534 L 11.609782,12.907819 11.01926,13.29667 C 8.7613237,14.783493 5.6172703,14.768302 3.332423,13.259528 -0.07366363,11.010358 -1.0146502,6.5989684 1.1898146,3.2148776 1.5505179,2.6611594 2.4056498,1.7447266 2.9644271,1.3130497 3.4423015,0.94387379 4.3921825,0.48568469 5.1732652,0.2475835 5.886299,0.03022609 6.1341883,0 7.2037391,0 8.2732897,0 8.521179,0.03022609 9.234213,0.2475835 c 0.781083,0.23810119 1.730962,0.69629029 2.208837,1.0654662 0.532501,0.4113763 1.39922,1.3400096 1.760153,1.8858877 1.520655,2.2998531 1.599025,5.3023778 0.199549,7.6451086 -0.208076,0.348322 -0.393306,0.668209 -0.411622,0.710863 -0.01831,0.04265 1.065556,1.18264 2.408603,2.533307 1.343046,1.350666 2.486621,2.574792 2.541278,2.720279 0.282475,0.7519 -0.503089,1.456506 -1.218488,1.092917 z M 8.4027892,12.475062 C 9.434946,12.25579 10.131043,11.855461 10.99416,10.984753 11.554519,10.419467 11.842507,10.042366 12.062078,9.5863882 12.794223,8.0659672 12.793657,6.2652398 12.060578,4.756293 11.680383,3.9737304 10.453587,2.7178427 9.730569,2.3710306 8.6921295,1.8729196 8.3992147,1.807606 7.2037567,1.807606 6.0082984,1.807606 5.7153841,1.87292 4.6769446,2.3710306 3.9539263,2.7178427 2.7271301,3.9737304 2.3469352,4.756293 1.6138384,6.2652398 1.6132726,8.0659672 2.3454252,9.5863882 c 0.4167354,0.8654208 1.5978784,2.0575608 2.4443766,2.4671358 1.0971012,0.530827 2.3890403,0.681561 3.6130134,0.421538 z "></path>
        </svg>
        <input type="text" id="ajax-product-search" placeholder="Search products..." autocomplete="off" />
        <svg style="width:33px; height:33px;" class="product-cancel-icon" xmlns="http://www.w3.org/2000/svg" fill="#000000" height="800px" width="800px" viewBox="0 0 460.775 460.775"><path d="M285.08,230.397L456.218,59.27c6.076-6.077,6.076-15.911,0-21.986L423.511,4.565c-2.913-2.911-6.866-4.55-10.992-4.55 c-4.127,0-8.08,1.639-10.993,4.55l-171.138,171.14L59.25,4.565c-2.913-2.911-6.866-4.55-10.993-4.55 c-4.126,0-8.08,1.639-10.992,4.55L4.558,37.284c-6.077,6.075-6.077,15.909,0,21.986l171.138,171.128L4.575,401.505 c-6.074,6.077-6.074,15.911,0,21.986l32.709,32.719c2.911,2.911,6.865,4.55,10.992,4.55c4.127,0,8.08-1.639,10.994-4.55 l171.117-171.12l171.118,171.12c2.913,2.911,6.866,4.55,10.993,4.55c4.128,0,8.081-1.639,10.992-4.55l32.709-32.719 c6.074-6.075,6.074-15.909,0-21.986L285.08,230.397z"></path></svg>
    </span>
    <div id="ajax-search-results-wrapper">
        <div id="ajax-search-results"></div>
    </div>

    <script>
    (function(){
        const searchInput = document.getElementById('ajax-product-search');
        const resultsContainer = document.getElementById('ajax-search-results');
        const resultsWrapper = document.getElementById('ajax-search-results-wrapper');
        const cancelIcon = document.querySelector('.product-cancel-icon');
        let timeout = null;

        // Logic to handle searching and toggling visibility of external divs
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            const suggested = document.querySelectorAll('.suggested-products-search');

            // 1. Toggle Cancel Icon and Wrapper visibility
            cancelIcon.style.display = query ? 'inline' : 'none';
            resultsWrapper.style.display = query ? 'block' : 'none';

            // 2. Hide "Suggested" div when user types
            suggested.forEach(el => {
                if (query) {
                    el.setAttribute('style', 'display: none !important;');
                } else {
                    el.removeAttribute('style');
                }
            });

            // 3. Perform AJAX search
            clearTimeout(timeout);
            if (query.length < 2) {
                resultsContainer.innerHTML = '';
                return;
            }

            timeout = setTimeout(function() {
               fetch('<?php echo esc_url(admin_url("admin-ajax.php")); ?>?action=ajax_product_search&term=' + encodeURIComponent(query))
                .then(response => response.text())
                .then(html => {
                    resultsContainer.innerHTML = html;
                    highlightMatches(resultsContainer, query);
                });
            }, 300);
        });

        // Clear input and reset UI when "X" is clicked
        cancelIcon.addEventListener('click', function () {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input', { bubbles: true }));
        });

        function highlightMatches(container, term) {
            if (!term) return;
            const elements = container.querySelectorAll(".search-product-title");
            const regex = new RegExp(`(${term})`, "gi");
            elements.forEach(el => {
                el.innerHTML = el.textContent.replace(regex, "<mark>$1</mark>");
            });
        }

        // Initialize visibility on load
        cancelIcon.style.display = 'none';
        resultsWrapper.style.display = 'none';
    })();
    </script>

    <style>
        #ajax-search-results {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        @media (max-width: 767px) {
            #ajax-search-results {
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }
            .search-product-title { font-size: 0.85em !important; }
            .product-loop-id { font-size: 9px !important; }
        }

        .product-loop-id {
            font-size: 11px;
            color: #888;
            margin: 2px 0 5px 0;
        }

        svg.product-cancel-icon {
            cursor: pointer;
            margin-left: -40px;
            z-index: 9;
            opacity: 0.5;
            width: 14px !important;
            height: 14px !important;
            display: none; /* Hidden by default */
        }

        .search-product-title {
            color: black !important;
            margin-top: 8px;
            font-size: 1em;
            text-align: left;
            width: 100%;
        }

        svg.product-search-icon {
            margin-right: -40px;
            z-index: 9;
            width: 20px !important;
            height: 20px !important;
        }

        span.span-product-search-input {
            display: flex;
            margin-left: 20px;
            justify-content: center;
            max-height: 80px;
            align-items: center;
            margin-bottom: 50px;
        }

        input#ajax-product-search {
            display: block;
            background-color: #f1f1f1;
            border: 0px;
            padding: 18px 60px !important;
            border-radius: 100px;
            margin-bottom: 0px !important;
            width: 100%;
            max-width: 400px;
        }

        #ajax-search-results-wrapper {
            max-height: 600px;
            overflow-y: auto;
            padding: 10px;
            margin-top: 10px;
            display: none; /* Hidden by default */
        }

        span.woocommerce-Price-amount.amount {
            display: none !important;
        }

        .search-product-item {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-decoration: none;
            color: #333;
        }

        .search-product-item img {
            width: 100%;
            aspect-ratio: 250 / 307;
            object-fit: cover;
        }
    </style>
    <?php
    return ob_get_clean();
}

add_action('wp_ajax_ajax_product_search', 'ajax_product_search_callback');
add_action('wp_ajax_nopriv_ajax_product_search', 'ajax_product_search_callback');

function ajax_product_search_callback() {
    $term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : '';
    if (!$term) wp_send_json_error('Empty search term');

    $is_numeric = is_numeric($term);

    // 1. Join taxonomy tables and modify search logic
    add_filter('posts_join', function($join) {
        global $wpdb;
        $join .= " LEFT JOIN {$wpdb->term_relationships} tr ON ({$wpdb->posts}.ID = tr.object_id) ";
        $join .= " LEFT JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id) ";
        $join .= " LEFT JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id) ";
        return $join;
    });

    add_filter('posts_search', function($search, $wp_query) use ($term, $is_numeric) {
        global $wpdb;
        $term_esc = esc_sql($wpdb->esc_like($term));
        
        // Search Logic: Title LIKE OR ID = OR Attribute Term Slug/Name LIKE
        $search = " AND ({$wpdb->posts}.post_title LIKE '%{$term_esc}%'";
        
        if ($is_numeric) {
            $search .= " OR {$wpdb->posts}.ID = " . intval($term);
        }

        // This part searches the attribute "code" (taxonomy pa_code)
        $search .= " OR (tt.taxonomy = 'pa_code' AND (t.slug LIKE '%{$term_esc}%' OR t.name LIKE '%{$term_esc}%'))";
        
        $search .= ") ";
        
        return $search;
    }, 10, 2);

    // 2. Ensure results are unique (searching taxonomies can cause duplicates)
    add_filter('posts_distinct', function($distinct) {
        return "DISTINCT";
    });

$args = [
        'post_type'      => 'product',
        'posts_per_page' => 30,
        's'              => $term, 
        'post_status'    => 'publish',
        // Add this tax_query to exclude hidden products
        'tax_query'      => [
            [
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'exclude-from-catalog',
                'operator' => 'NOT IN',
            ],
        ],
    ];

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            global $product;

            echo '<a href="' . get_permalink() . '" class="search-product-item">';
            echo $product->get_image('woocommerce_thumbnail');
            echo '<div class="search-product-title">' . get_the_title() . '</div>';
            echo '<div class="search-product-price" style="display:none;">' . $product->get_price_html() . '</div>';
            echo '</a>';
        }
    } else {
        echo '<p>No products found.</p>';
    }

    wp_reset_postdata();
    // Clean up filters so they don't affect other site searches
    remove_all_filters('posts_search');
    remove_all_filters('posts_join');
    remove_all_filters('posts_distinct');
    wp_die();
}

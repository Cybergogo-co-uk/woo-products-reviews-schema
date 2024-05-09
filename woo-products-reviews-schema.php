<?php
/*
Plugin Name: Custom Woo product Reviews Schema
Description: Adds schema markup for WooCommerce product reviews.
Version: 1.0
Plugin URI:  https://cybergogo.co.uk/
Author: Konstantinos Pap Cybergogo
Author URI:  https://cybergogo.co.uk/
Author Email: konstantinos@cybergogo.co.uk

 * Copyright: © 2024 cybergogo
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/


function add_woocommerce_product_reviews_schema() {
    // Check if WooCommerce is active
    if ( ! class_exists( 'WooCommerce' ) ) {
        return;
    }

    // Get the current product
    global $product;

    // Check if we are on a single product page and if the product has reviews
    if ( is_product() && $product && $product->get_review_count() > 0 ) {
        $schema = array(
            '@context'        => 'http://schema.org',
            '@type'           => 'Product',
            'aggregateRating' => array(
                '@type'       => 'AggregateRating',
                'ratingValue' => $product->get_average_rating(),
                'reviewCount' => $product->get_review_count(),
            ),
            'review'          => array(),
        );

        // Get product reviews
        $args = array(
            'post_id' => $product->get_id(),
            'status'  => 'approve',
            'type'    => 'review',
        );
        $reviews = get_comments( $args );

        if ( $reviews ) {
            foreach ( $reviews as $review ) {
                $review_data = array(
                    '@type'         => 'Review',
                    'author'        => array(
                        '@type' => 'Person',
                        'name'  => get_comment_author( $review->comment_ID ),
                    ),
                    'datePublished' => get_comment_date( 'c', $review->comment_ID ),
                    'description'   => wp_strip_all_tags( $review->comment_content ),
                    'reviewRating'  => array(
                        '@type'       => 'Rating',
                        'ratingValue' => get_comment_meta( $review->comment_ID, 'rating', true ),
                    ),
                );

                // Add the comment text if available
                if ( ! empty( $review_data['description'] ) ) {
                    $schema['review'][] = $review_data;
                }
            }
        }

        // Output the schema markup
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
    }
}
add_action( 'wp_footer', 'add_woocommerce_product_reviews_schema' );


function custom_woocommerce_tools_page() {
    // Your page HTML and PHP code here
    echo '<div class="wrap"><h2>Custom Product Reviews schema markup</h2>
    <h2>Instructions:</h2>
    <h4>- Activate the plugin</h4>
    <h4>Ready to go </h4>
    <h2>Check the schema:</h2>
    <h4>Go to https://search.google.com/test/rich-results</h4>
    <p>add the page link and click VIEW TESTED PAGE. The results in the right part of the page.</p>

    
    
   </div>';
}

function add_custom_woocommerce_tools_page() {
    add_submenu_page(
        'tools.php',            // Parent menu slug
        'Custom Reviews schema markup', // Page title
        'Product Reviews schema',         // Menu title
        'manage_options',       // Capability required
        'custom-reviews-schema', // Menu slug
        'custom_woocommerce_tools_page' // Callback function to display page content
    );
}
add_action('admin_menu', 'add_custom_woocommerce_tools_page');


function enqueue_custom_woocommerce_tools_scripts() {
    // Enqueue your stylesheets and scripts here
}
add_action('admin_enqueue_scripts', 'enqueue_custom_woocommerce_tools_scripts');

function add_settings_link($links) {
    $settings_link = '<a href="tools.php?page=custom-reviews-schema">Settings</a>';
    array_unshift($links, $settings_link); // Add the settings link at the beginning of the array
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_settings_link');



// // OLD CODE Add WooCommerce product reviews schema
// function add_woocommerce_product_reviews_schema() {
//     // Check if WooCommerce is active
//     if ( ! class_exists( 'WooCommerce' ) ) {
//         return;
//     }

//     // Get the current product
//     global $product;

//     // Check if we are on a single product page and if the product has reviews
//     if ( is_product() && $product && $product->get_review_count() > 0 ) {
//         $schema = array(
//             '@context'        => 'http://schema.org',
//             '@type'           => 'Product',
//             'aggregateRating' => array(
//                 '@type'       => 'AggregateRating',
//                 'ratingValue' => $product->get_average_rating(),
//                 'reviewCount' => $product->get_review_count(),
//             ),
//             'review'          => array(),
//         );

//         // Get product reviews
//         $args = array(
//             'post_id' => $product->get_id(),
//             'status'  => 'approve',
//             'type'    => 'review',
//         );
//         $reviews = get_comments( $args );

//         if ( $reviews ) {
//             foreach ( $reviews as $review ) {
//                 $schema['review'][] = array(
//                     '@type'         => 'Review',
//                     'author'        => array(
//                         '@type' => 'Person',
//                         'name'  => get_comment_author( $review->comment_ID ),
//                     ),
//                     'datePublished' => get_comment_date( 'c', $review->comment_ID ),
//                     'description'   => wp_strip_all_tags( $review->comment_content ),
//                     'reviewRating'  => array(
//                         '@type'       => 'Rating',
//                         'ratingValue' => get_comment_meta( $review->comment_ID, 'rating', true ),
//                     ),
//                 );
//             }
//         }

//         // Output the schema markup
//         echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
//     }
// }
// add_action( 'wp_footer', 'add_woocommerce_product_reviews_schema' );
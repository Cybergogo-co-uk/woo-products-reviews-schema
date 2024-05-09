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

// Add WooCommerce product reviews schema
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
                $schema['review'][] = array(
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
            }
        }

        // Output the schema markup
        echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
    }
}
add_action( 'wp_footer', 'add_woocommerce_product_reviews_schema' );
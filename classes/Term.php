<?php


namespace Kntnt\Posts_Export;


class Term {

    public $term_id;

    public $slug;

    public $name;

    public $parent;

    public $taxonomy;

    public $description;

    public $metadata;

    public $default;

    private static $default_metadata_keys = [];

    private static $all_terms = null;

    public static function export() {
        if ( is_null( self::$all_terms ) ) {

            self::$default_metadata_keys = apply_filters( 'kntnt-post-export-term-metadata-keys', self::$default_metadata_keys );

            $taxonomies = get_object_taxonomies( 'post' );
            $taxonomies = apply_filters( 'kntnt-post-export-taxonomies', $taxonomies, null ); // Same as in Post::terms().

            $default_term_ids = [];
            foreach ( $taxonomies as $taxonomy ) {
                if ( 'category' == $taxonomy ) {
                    $default_term_ids[ (int) get_option( 'default_category' ) ] = true;
                }
                else {
                    $taxonomy_object = get_taxonomy( $taxonomy );
                    if ( $taxonomy_object->default_term ) {
                        $default_term_ids[ (int) get_option( "default_term_$taxonomy" ) ] = true;
                    }
                }
            }

            $terms = get_terms( $taxonomies );
            if ( is_array( $terms ) ) { // If not false nor WP_Error
                $terms = apply_filters( 'kntnt-post-export-terms', $terms );
                 foreach ( $terms as $term ) {
                    self::$all_terms[ $term->term_id ] = new Term( $term, isset( $default_term_ids[ $term->term_id ] ) );
                }
            }

        }
        return self::$all_terms;
    }

    public function __construct( $term, $is_default_term ) {

        $this->id = $term->term_id;
        $this->slug = $term->slug;
        $this->name = $term->name;
        $this->parent = $term->parent;
        $this->taxonomy = $term->taxonomy;
        $this->description = $term->description;
        $this->metadata = (array) $this->metadata( $term ); // Associative arrays becomes objets in JSON.
        $this->default = $is_default_term;

        Plugin::log( 'Created %s', $this );

    }

    private function metadata( $term ) {
        $metadata = get_metadata_raw( 'term', $term->term_id );
        $metadata = array_intersect_key( $metadata, array_flip( self::$default_metadata_keys ) );
        $metadata = apply_filters( 'kntnt-post-export-term-metadata', $metadata );
        return $metadata;
    }

}
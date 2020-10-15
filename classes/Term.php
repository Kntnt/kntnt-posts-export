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

    private static $default_metadata_keys = [];

    private static $all_terms = null;

    public static function export() {
        if ( is_null( self::$all_terms ) ) {

            self::$default_metadata_keys = apply_filters( 'kntnt-post-export-term-metadata-keys', self::$default_metadata_keys );

            $taxonomies = get_object_taxonomies( 'post' );
            $taxonomies = apply_filters( 'kntnt-post-export-taxonomies', $taxonomies );
            $terms = get_terms( $taxonomies );
            if ( is_array( $terms ) ) { // If not false nor WP_Error
                $terms = apply_filters( 'kntnt-post-export-terms', $terms );
                foreach ( $terms as $term ) {
                    self::$all_terms[ $term->term_id ] = new Term( $term );
                }
            }

        }
        return self::$all_terms;
    }

    public function __construct( $term ) {

        $this->term_id = $term->term_id;
        $this->slug = $term->slug;
        $this->name = $term->name;
        $this->parent = $term->parent;
        $this->taxonomy = $term->taxonomy;
        $this->description = $term->description;

        $metadata = get_metadata_raw( 'term', $this->term_id );
        $metadata = array_intersect_key( $metadata, array_flip( self::$default_metadata_keys ) );
        $this->metadata = apply_filters( 'kntnt-post-export-term-metadata', $metadata );

        Plugin::log( 'Created %s', $this );

    }

}
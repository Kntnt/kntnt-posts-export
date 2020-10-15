<?php


namespace Kntnt\Posts_Export;


class Post {

    public $id;

    public $slug;

    public $guid;

    public $title;

    public $content;

    public $excerpt;

    public $author;

    public $date;

    public $terms;

    public $metadata;

    private static $default_metadata_keys = [
        '_thumbnail_id',
    ];

    private static $all_posts = null;

    public static function export() {
        if ( is_null( self::$all_posts ) ) {

            self::$default_metadata_keys = apply_filters( 'kntnt-post-export-post-metadata-keys', self::$default_metadata_keys );

            $posts = get_posts( [ 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => null ] );
            $posts = apply_filters( 'kntnt-post-export-posts', $posts );
            foreach ( $posts as $post ) {
                self::$all_posts[ $post->ID ] = new Post( $post );
            }

        }
        return self::$all_posts;
    }

    public function __construct( $post ) {

        $this->id = $post->ID;
        $this->slug = $post->post_name;
        $this->guid = $post->guid;
        $this->title = $post->post_title;
        $this->content = $post->post_content;
        $this->excerpt = $post->post_excerpt;
        $this->author = $post->post_author;
        $this->date = $post->post_date;

        $this->terms = [];
        $taxonomies = get_object_taxonomies( $post );
        foreach ( $taxonomies as $taxonomy ) {
            if ( is_array( $terms = get_the_terms( $post, $taxonomy ) ) ) {
                foreach ( $terms as $term ) {
                    $this->terms[] = $term->term_id;
                }
            }
        }

        $metadata = get_metadata_raw( 'post', $this->id );
        $metadata = array_intersect_key( $metadata, array_flip( self::$default_metadata_keys ) );
        $this->metadata = apply_filters( 'kntnt-post-export-post-metadata', $metadata );

        Plugin::log( 'Created %s', $this );

    }

}
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

    public $attachments;

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

    private function __construct( $post ) {

        $this->id = $post->ID;
        $this->slug = $post->post_name;
        $this->guid = $post->guid;
        $this->title = $post->post_title;
        $this->content = $post->post_content;
        $this->excerpt = $post->post_excerpt;
        $this->author = $post->post_author;
        $this->date = $post->post_date;
        $this->terms = $this->terms( $post );
        $this->attachments = $this->attachments( $post );
        $this->metadata = $this->metadata( $post );

        Plugin::log( 'Created %s', $this );

    }

    private function terms( $post ) {
        $terms = [];
        $taxonomies = get_object_taxonomies( $post );
        $taxonomies = apply_filters( 'kntnt-post-export-taxonomies', $taxonomies, $post ); // Same as in Term::export()
        foreach ( $taxonomies as $taxonomy ) {
            if ( is_array( $taxonomy_terms = get_the_terms( $post, $taxonomy ) ) ) {
                foreach ( $taxonomy_terms as $term ) {
                    $terms[$taxonomy][] = $term->term_id;
                }
            }
        }
        return $terms;
    }

    private function attachments( $post ) {

        $attachments = [];

        // Get featured image
        if ( $featured_image_attachment_id = (int) get_metadata_raw( 'post', $post->ID, '_thumbnail_id', true ) ) {
            $attachments[ $featured_image_attachment_id ] = $featured_image_attachment_id;
        }

        // Get proper attachments (that is attachments that are attached to the
        // post as a child post)
        $proper_attachments = array_keys( get_children( [ 'post_parent' => $post->ID, 'post_type' => 'attachment' ] ) );
        $attachments += array_combine( $proper_attachments, $proper_attachments );

        // Scan content for references to attachments.
        $upload_dir = $this->upload_dir();
        $re = "@(?<=/{$upload_dir}/)([^ \"'`=<>]+)(?=-\d+x\d+\.(jpg|jpeg|png|gif))|(?<=/{$upload_dir}/)(?:[^ \"'`=<>](?!.+-\d+x\d+\.(?:jpg|jpeg|png|gif)))+@iu";
        preg_match_all( $re, $post->post_content, $matches, PREG_SET_ORDER );
        foreach ( $matches as $match ) {
            $path = count( $match ) == 1 ? $match[0] : "{$match[1]}.{$match[2]}";
            if ( $id = $this->get_attachment_id( $path ) ) {
                $attachments[ $id ] = $id;
            }
        }

        // Apply filter
        $attachments = apply_filters( 'kntnt-posts-export-post-attachments', array_values( $attachments ), $post );

        return $attachments;

    }

    private function metadata( $post ) {
        $metadata = get_metadata_raw( 'post', $post->ID );
        $metadata = array_intersect_key( $metadata, array_flip( self::$default_metadata_keys ) );
        $metadata = apply_filters( 'kntnt-post-export-post-metadata', $metadata );
        return $metadata;
    }

    private function get_attachment_id( $path ) {

        $potential_attachment_ids = ( new \WP_Query )->query( [
            'post_type' => 'attachment',
            'post_status' => get_post_stati(), // Any status
            'fields' => 'ids',
            'meta_query' => [
                [
                    'value' => $path,
                    'compare' => 'LIKE',
                    'key' => '_wp_attachment_metadata',
                ],
            ],
        ] );

        foreach ( $potential_attachment_ids as $id ) {
            $meta = get_metadata_raw( 'post', $id, '_wp_attachment_metadata', true );
            if ( $path == $meta['file'] ) {
                return $id;
            }
        }

        return null;

    }

    // See _wp_upload_dir().
    private function upload_dir() {
        static $upload_dir = null;
        if ( is_null( $upload_dir ) ) {
            $upload_path = trim( get_option( 'upload_path' ) );
            if ( empty( $upload_path ) || 'wp-content/uploads' === $upload_path ) {
                $upload_dir = WP_CONTENT_DIR . '/uploads';
            }
            else if ( 0 !== strpos( $upload_path, ABSPATH ) ) {
                $upload_dir = path_join( ABSPATH, $upload_path );
            }
            else {
                $upload_dir = $upload_path;
            }
            $upload_dir = substr( $upload_dir, strlen( ABSPATH ) );
        }
        return $upload_dir;
    }


}
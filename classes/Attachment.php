<?php


namespace Kntnt\Posts_Export;


class Attachment {

    public $id;

    public $slug;

    public $guid;

    public $mime_type;

    public $title;

    public $content;

    public $excerpt;

    public $author;

    public $date;

    public $metadata;

    public $src;

    private static $default_metadata_keys = [
        '_wp_attached_file',
        '_wp_attachment_metadata',
        '_wp_attachment_image_alt',
    ];

    private static $all_attachments = null;

    private static $upload_dir = null;

    public static function export() {
        if ( is_null( self::$all_attachments ) ) {

            self::$default_metadata_keys = apply_filters( 'kntnt-post-export-attachment-metadata-keys', self::$default_metadata_keys );

            $attachments = get_posts( [ 'post_type' => 'attachment', 'post_status' => null, 'numberposts' => null ] );
            $attachments = apply_filters( 'kntnt-post-export-attachments', $attachments );
            foreach ( $attachments as $attachment ) {
                self::$all_attachments[ $attachment->ID ] = new Attachment( $attachment );
            }

        }
        return self::$all_attachments;
    }

    private function __construct( $attachment ) {

        $this->id = $attachment->ID;
        $this->slug = $attachment->post_name;
        $this->guid = $attachment->guid;
        $this->mime_type = $attachment->post_mime_type;
        $this->title = $attachment->post_title;
        $this->content = $attachment->post_content;
        $this->excerpt = $attachment->post_excerpt;
        $this->author = $attachment->post_author;
        $this->date = $attachment->post_date;
        $this->metadata = $this->metadata( $attachment );
        $this->src = isset( $this->metadata['_wp_attached_file'] ) && isset( $this->metadata['_wp_attached_file'][0] ) ? Plugin::upload_url( $this->metadata['_wp_attached_file'][0] ) : false;

        Plugin::log( 'Created %s', $this );

    }

    private function metadata( $attachment ) {
        $metadata = get_metadata_raw( 'post', $attachment->ID );
        $metadata = array_intersect_key( $metadata, array_flip( self::$default_metadata_keys ) );
        $metadata = apply_filters( 'kntnt-post-export-attachment-metadata', $metadata );
        return $metadata;
    }

}
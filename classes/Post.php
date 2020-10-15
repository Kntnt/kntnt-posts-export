<?php


namespace Kntnt\Posts_Export;


class Post {

    public $id;

    public $slug;

    public $categories;

    public $tags;

    public $collections;

    public $author;

    public $publish_time;

    public $images;

    public $featured_image;

    public $title;

    public $lead;

    public $body;

    public $excerpt;

    public function __construct( $post ) {
        $this->id = $post->ID;
        $this->slug = $post->post_name;
        $this->categories = $this->categories( $post );
        $this->tags = $this->tags( $post->ID );
        $this->collections = $this->collections( $post );
        $this->author = new Author( $post->post_author );
        $this->publish_time = $post->post_date_gmt;
        $this->images = $this->images( $post );
        $this->featured_image = $this->featured_image( $post );
        $this->title = $post->post_title;
        $this->lead = $this->lead( $post );
        $this->body = $post->post_content;
        $this->excerpt = $post->post_excerpt;
        Plugin::log( 'Export object for post %s: %s', $post->ID, $this );
    }

    private function categories( $post ) {
        $categories = [];
        $terms = get_the_terms( $post, 'category' );
        if ( $terms ) {
            foreach ( $terms as $term ) {
                $categories[] = new Category( $term );
            }
        }
        return $categories;
    }

    private function tags( $post ) {
        $tags = [];
        $terms = get_the_terms( $post, 'post_tag' );
        if ( $terms ) {
            foreach ( $terms as $term ) {
                $tags[] = new Tag( $term );
            }
        }
        return $tags;
    }

    private function collections( $post ) {
        $collections = [];
        $terms = get_the_terms( $post, 'collections' );
        if ( $terms ) {
            foreach ( $terms as $term ) {
                $collections[] = new Collection( $term );
            }
        }
        return $collections;
    }

    private function images( $post ) {
        return []; // TODO: Sök alla bilder i body.
    }

    private function featured_image( $post ) {
        return new Image( (int) get_post_meta( $post->ID, '_thumbnail_id', true ) );
    }

    private function lead( $post ) {
        return get_post_meta( $post->ID, 'lead', true );
    }

}
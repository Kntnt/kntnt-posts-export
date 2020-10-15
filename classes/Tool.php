<?php


namespace Kntnt\Posts_Export;


class Tool {

    private $export = '';

    public function run() {
        add_management_page( 'Kntnt Posts export', 'Posts export', 'manage_options', 'kntnt-posts-export', [ $this, 'tool' ] );
    }

    public function tool() {

        Plugin::log();

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( __( 'Unauthorized use.', 'kntnt-posts-export' ) );
        }

        if ( $_POST ) {

            if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], Plugin::ns() ) ) {
                return;
            }

            $this->export();

        }

        $this->render_page();

    }

    public function export() {

        $args = [
            'nopaging' => true,
            'post_type' => 'post',
            'post_status' => 'publish',
            'suppress_filters' => true,
        ];

        Plugin::log( 'Retrieve posts matching these arguments: %s', $args );

        $posts = ( new \WP_Query )->query( $args );

        Plugin::log( 'Number of retrieved posts: %s', count( $posts ) );

        $export = [];
        foreach ( $posts as $post ) {
            $export[] = new Post( $post );
        }

        $this->export = json_encode( $export );

    }

    public function render_page() {

        Plugin::log();

        Plugin::load_from_includes( 'tool.php', [
            'ns' => Plugin::ns(),
            'title' => __( 'Kntnt Posts export', 'kntnt-posts-export' ),
            'submit_button_text' => __( 'Export', 'kntnt-posts-export' ),
            'export' => $this->export,
        ] );

    }

}
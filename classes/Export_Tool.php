<?php


namespace Kntnt\Posts_Export;


class Export_Tool {

    private $errors = [];

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
            if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], Plugin::ns() ) ) {
                $this->export();
            }
            else {
                $this->errors[] = __( "Couldn't export; the form has expired. Please, try again.", 'kntnt-posts-export' );
                Plugin::error( "Couldn't verify nonce." );
            }
        }
        $this->render_page();
    }

    public function render_page() {

        Plugin::log();

        Plugin::load_from_includes( 'tool.php', [
            'ns' => Plugin::ns(),
            'title' => __( 'Kntnt Posts export', 'kntnt-posts-export' ),
            'submit_button_text' => __( 'Export', 'kntnt-posts-export' ),
            'export' => $this->export,
            'errors' => $this->errors,
        ] );

    }

    private function export() {
        $export = new \stdClass();
        $export->attachments = Attachment::export();
        $export->users = User::export();
        $export->post_terms = Term::export();
        $export->posts = Post::export();
        $this->export = json_encode( $export );
    }

}
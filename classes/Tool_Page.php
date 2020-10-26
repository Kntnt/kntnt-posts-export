<?php


namespace Kntnt\Posts_Export;


class Tool_Page {

    private $export_file_url = null;

    private $export_file_valid_until = null;

    public function run() {
        add_filter( 'upload_mimes', [ $this, 'allow_json' ], 10 );
        add_management_page( 'Kntnt Posts Export', 'Posts export', 'manage_options', 'kntnt-posts-export', [ $this, 'tool' ] );
    }

    public function allow_json( $mime_types ) {
        $mime_types['json'] = 'application/json';
        return $mime_types;
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
                Plugin::error( __( "Couldn't export. The form has expired. Please, try again.", 'kntnt-posts-export' ) );
            }
        }

        $this->render_page();

    }

    private function render_page() {

        Plugin::log();

        Plugin::load_from_includes( 'tool.php', [
            'ns' => Plugin::ns(),
            'title' => get_admin_page_title(),
            'submit_button_text' => __( 'Export', 'kntnt-posts-export' ),
            'export_file_url' => $this->export_file_url,
            'export_file_valid_until' => $this->export_file_valid_until,
            'errors' => Plugin::errors(),
        ] );

    }

    private function export() {

        @ini_set( 'max_execution_time', '0' );

        date_default_timezone_set( get_option( 'timezone_string', 'UTC' ) );
        setlocale( LC_TIME, get_user_locale() );

        do_action( 'kntnt-posts-export-add-local-code' );

        $export = new \stdClass();
        $export->attachments = Attachment::export();
        $export->users = User::export();
        $export->post_terms = Term::export();
        $export->posts = Post::export();
        $export = json_encode( $export );

        $name = 'kntnt-posts-export-' . date( 'ymd-His' );

        $file_info = Plugin::save_to_file( $export, $suffix = 'json', $name );

        if ( $file_info['error'] ) {
            Plugin::error( __( 'Failed to save export file. %s', 'kntnt-posts-export' ), $file_info['error'] );
        }
        else {

            $valid_until = time() + 5 * MINUTE_IN_SECONDS;

            wp_schedule_single_event( $valid_until, 'kntnt-posts-export-file-delete', [ $file_info['file'] ] );

            $this->export_file_url = $file_info['url'];
            $this->export_file_valid_until = strftime( '%c', $valid_until );

        }

    }

}
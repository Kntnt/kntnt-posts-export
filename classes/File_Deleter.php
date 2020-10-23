<?php


namespace Kntnt\Posts_Export;


class File_Deleter {

    public function run() {
        add_action( 'kntnt-posts-export-file-delete', [ $this, 'delete' ] );
    }

    public function delete( $file ) {
        Plugin::log( 'Deleting file %s', $file );
        $ok = @unlink( $file );
        if ( ! $ok ) {
            Plugin::error( 'Failed deleting %s', $file );
        }
    }

}
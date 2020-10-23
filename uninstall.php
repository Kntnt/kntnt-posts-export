<?php

defined( 'WP_UNINSTALL_PLUGIN' ) && new Uninstaller;

class Uninstaller {

    public function __construct() {
        $this->remove_files( [ 'kntnt-posts-export' ] );
    }

    private function remove_files( $subdirs ) {
        $upload_dir = wp_upload_dir()['basedir'];
        foreach ( $subdirs as $subdir ) {
            $base_dir = "$upload_dir/$subdir";
            $dir_it = new RecursiveDirectoryIterator( $base_dir, RecursiveDirectoryIterator::SKIP_DOTS );
            $files = new RecursiveIteratorIterator( $dir_it, RecursiveIteratorIterator::CHILD_FIRST );
            foreach ( $files as $file ) {
                if ( $file->isDir() ) {
                    @rmdir( $file->getRealPath() );
                }
                else {
                    @unlink( $file->getRealPath() );
                }
            }
            @rmdir( $base_dir );
        }
    }

}
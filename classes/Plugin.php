<?php


namespace Kntnt\Posts_Export;


class Plugin extends Abstract_Plugin {

    use Logger;
    use Includes;
    use File_Save;

    private static $errors = [];

    // Removes the element with the provided key and returns it value or
    // $default if it didn't exist.
    public static function peel_off( $key, &$array, $default = null ) {
        if ( array_key_exists( $key, $array ) ) {
            $val = $array[ $key ];
            unset( $array[ $key ] );
        }
        else {
            $val = $default;
        }
        return $val;
    }

    public static function error( $message, ...$args ) {
        foreach ( $args as &$arg ) {
            $arg = Plugin::stringify( $arg );
        }
        $message = sprintf( $message, ...$args );
        self::$errors[] = $message;
        self::_log( $message );
    }

    public static function errors() {
        $errors = self::$errors;
        self::$errors = [];
        return $errors;
    }

    public function classes_to_load() {
        return [
            'admin' => [
                'admin_menu' => [
                    'Tool_Page',
                ],
                'kntnt-posts-export-add-local-code' => [
                    'Local_Loader',
                ],
            ],
            'cron' => [
                'init' => [
                    'File_Deleter',
                ],
            ],
        ];
    }

}

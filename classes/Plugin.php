<?php


namespace Kntnt\Posts_Export;


class Plugin extends Abstract_Plugin {

    use Logger;
    use Includes;

    public function classes_to_load() {
        return [
            'admin' => [
                'admin_menu' => [
                    'Export_Tool',
                ],
            ],
        ];
    }

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

}

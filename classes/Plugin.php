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

}

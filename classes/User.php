<?php


namespace Kntnt\Posts_Export;


class User {

    public $id;

    public $login;

    public $pass;

    public $nicename;

    public $email;

    public $url;

    public $registered;

    public $status;

    public $display_name;

    public $roles;

    public $metadata;

    private static $default_metadata_keys = [
        'first_name',
        'last_name',
        'nickname',
        'description',
        'rich_editing',
        'syntax_highlighting',
        'comment_shortcuts',
        'admin_color',
        'show_admin_bar_front',
        'locale',
    ];

    private static $all_users = null;

    public static function export() {
        if ( is_null( self::$all_users ) ) {

            self::$default_metadata_keys = apply_filters( 'kntnt-post-export-user-metadata-keys', self::$default_metadata_keys );

            $users = get_users();
            $users = apply_filters( 'kntnt-post-export-users', $users );
            foreach ( $users as $user ) {
                self::$all_users[ $user->ID ] = new User( $user->data, $user->roles );
            }

        }
        return self::$all_users;
    }

    private function __construct( $user, $roles ) {

        $this->id = $user->ID;
        $this->login = $user->user_login;
        $this->pass = $user->user_pass;
        $this->nicename = $user->user_nicename;
        $this->email = $user->user_email;
        $this->url = $user->user_url;
        $this->registered = $user->user_registered;
        $this->status = $user->user_status;
        $this->name = $user->display_name;
        $this->roles = $roles;
        $this->metadata = $this->metadata( $user );

        Plugin::log( 'Created %s', $this );

    }

    private function metadata( $user ) {
        $metadata = get_metadata_raw( 'user', $user->ID );
        $metadata = array_intersect_key( $metadata, array_flip( self::$default_metadata_keys ) );
        $metadata = apply_filters( 'kntnt-post-export-user-metadata', $metadata );
        return $metadata;
    }

}
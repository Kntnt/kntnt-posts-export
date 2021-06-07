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

    public $display_name;

    public $role;

    public $first_name;

    public $last_name;

    public $nickname;

    public $description;

    public $rich_editing;

    public $syntax_highlighting;

    public $comment_shortcuts;

    public $admin_color;

    public $show_admin_bar_front;

    public $locale;

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
                self::$all_users[ $user->ID ] = new User( $user->data, $user->roles[0] );
            }

        }
        return self::$all_users;
    }

    private function __construct( $user, $role ) {

        $this->id = $user->ID;
        $this->login = $user->user_login;
        $this->pass = $user->user_pass;
        $this->display_name = $user->display_name;
        $this->nicename = $user->user_nicename;
        $this->email = $user->user_email;
        $this->url = $user->user_url;
        $this->registered = $user->user_registered;
        $this->role = $role;

        $this->metadata = (array) $this->metadata( $user ); // Associative arrays becomes objets in JSON.

        $this->first_name = Plugin::peel_off( 'first_name', $this->metadata, '' )[0];
        $this->last_name = Plugin::peel_off( 'last_name', $this->metadata, '' )[0];
        $this->nickname = Plugin::peel_off( 'nickname', $this->metadata, '' )[0];
        $this->description = Plugin::peel_off( 'description', $this->metadata, '' )[0];
        $this->rich_editing = Plugin::peel_off( 'rich_editing', $this->metadata, 'true' )[0]; // Must be string, not boolean.
        $this->syntax_highlighting = Plugin::peel_off( 'syntax_highlighting', $this->metadata, 'true' )[0]; // Must be string, not boolean.
        $this->comment_shortcuts = Plugin::peel_off( 'comment_shortcuts', $this->metadata, 'false' )[0]; // Must be string, not boolean.
        $this->admin_color = Plugin::peel_off( 'admin_color', $this->metadata, 'fresh' )[0];
        $this->show_admin_bar_front = Plugin::peel_off( 'show_admin_bar_front', $this->metadata, 'true' )[0]; // Must be string, not boolean.
        $this->locale = Plugin::peel_off( 'locale', $this->metadata, '' )[0];

        Plugin::debug( 'Created %s', $this );

    }

    private function metadata( $user ) {
        $metadata = get_metadata_raw( 'user', $user->ID );
        $metadata = array_intersect_key( $metadata, array_flip( self::$default_metadata_keys ) );
        $metadata = apply_filters( 'kntnt-post-export-user-metadata', $metadata );
        return $metadata;
    }

}
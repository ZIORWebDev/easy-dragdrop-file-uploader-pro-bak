<?php
if ( ! function_exists( 'fwip_fs' ) ) {
    // Create a helper function for easy SDK access.
    function fwip_fs() {
        global $fwip_fs;

        if ( ! isset( $fwip_fs ) ) {
            // Include Freemius SDK.
            // SDK is auto-loaded through composer
            $fwip_fs = fs_dynamic_init( array(
                'id'                  => '18123',
                'slug'                => 'filepond-wp-integration-pro',
                'type'                => 'plugin',
                'public_key'          => 'pk_64f50903a560199b67276f6786e72',
                'is_premium'          => true,
                'has_addons'          => true,
                'has_paid_plans'      => true,
                'menu'                => array(
                    'slug'           => 'filepond-wp-integration',
                    'support'        => true,
                    'parent'         => array(
                        'slug' => 'options-general.php',
                    ),
                ),
            ) );
        }

        return $fwip_fs;
    }

    // Init Freemius.
    fwip_fs();
    // Signal that SDK was initiated.
    do_action( 'fwip_fs_loaded' );
}
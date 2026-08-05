<?php
/**
 * Welcome Screen Class
 */
class Sparkling_Welcome {

    /**
     * Constructor for the welcome screen
     */
    public function __construct() {
        /* create dashboard page */
        add_action( 'admin_menu', array( $this, 'sparkling_welcome_register_menu' ) );

        /* activation notice */
        add_action( 'load-themes.php', array( $this, 'sparkling_activation_admin_notice' ) );

        /* enqueue script and style for welcome screen */
        add_action( 'admin_enqueue_scripts', array( $this, 'sparkling_welcome_style_and_scripts' ) );

        /* ajax callback for dismissable required actions */
        add_action(
            'wp_ajax_sparkling_dismiss_required_action', array(
                $this,
                'sparkling_dismiss_required_action_callback',
            )
        );

        add_action(
            'wp_ajax_sparkling_dismiss_recommended_plugins', array(
                $this,
                'sparkling_dismiss_recommended_plugins_callback',
            )
        );

        add_action(
            'wp_ajax_sparkling_set_frontpage', array(
                $this,
                'sparkling_set_pages',
            )
        );

        add_filter( 'admin_body_class', array( $this, 'sparkling_admin_body_class' ) );

    }

    /**
     * Borrow core's plugin-install body class on the tabs that render plugin cards.
     *
     * Core's layout for .plugin-card is scoped to the .plugin-install-php body
     * class, which WordPress only sets on plugin-install.php:
     *
     *     .plugin-install-php #the-list    { display: flex; flex-wrap: wrap; }
     *     .plugin-install-php .plugin-card { display: flex; flex-direction: column;
     *                                        justify-content: space-between; }
     *
     * Without it the floated cards never clear, so #the-list collapses to zero
     * height and everything after it overlaps. Adding the class gives us core's
     * flex grid and equal-height cards without the theme shipping any CSS.
     *
     * Scoped to the plugin tabs so the other tabs, which use the about-page
     * layout classes, are untouched.
     *
     * @param string $classes Space-separated admin body classes.
     * @return string
     */
    public function sparkling_admin_body_class( $classes ) {
        $screen = get_current_screen();

        if ( ! $screen || 'appearance_page_sparkling-welcome' !== $screen->id ) {
            return $classes;
        }

        $tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'getting_started';

        if ( in_array( $tab, array( 'recommended_plugins', 'recommended_actions' ), true ) ) {
            $classes .= ' plugin-install-php';
        }

        return $classes;
    }

    /**
     * Look up a published page by title without the deprecated get_page_by_title().
     *
     * @param string $title Page title to match.
     * @return WP_Post|null
     */
    private function get_page_by_title( $title ) {
        $query = new WP_Query(
            array(
                'post_type'              => 'page',
                'title'                  => $title,
                'post_status'            => 'publish',
                'posts_per_page'         => 1,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_term_cache' => false,
                'update_post_meta_cache' => false,
            )
        );

        return empty( $query->posts ) ? null : $query->posts[0];
    }

    /**
     * AJAX: point the front page at the "Homepage" page and the blog at "Blog".
     *
     * Only ever runs for an authenticated user who can manage options and who
     * presents a valid nonce.
     */
    public function sparkling_set_pages() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( esc_html__( 'You are not allowed to do that.', 'sparkling' ), 403 );
        }

        check_ajax_referer( 'sparkling_welcome_nonce', 'nonce' );

        $about = $this->get_page_by_title( 'Homepage' );
        $blog  = $this->get_page_by_title( 'Blog' );

        if ( ! $about instanceof WP_Post ) {
            wp_send_json_error( esc_html__( 'No page titled "Homepage" was found.', 'sparkling' ), 404 );
        }

        update_option( 'page_on_front', $about->ID );
        update_option( 'show_on_front', 'page' );

        if ( $blog instanceof WP_Post ) {
            update_option( 'page_for_posts', $blog->ID );
        }

        wp_send_json_success( 'success' );
    }

    /**
     * Note: sparkling_activate_plugin() and sparkling_deactivate_plugin() were removed.
     *
     * They were hooked to admin_init and acted on ?action=activate_plugin /
     * ?action=deactivate_plugin without verifying a nonce, which made them
     * CSRF-exploitable against any administrator. Nothing in the theme ever
     * generated those links: the recommended-plugins tab builds its buttons with
     * create_action_link(), which points at core's plugins.php/update.php using
     * core's own nonces. The methods were therefore unreachable dead code and
     * pure attack surface, so they have been deleted rather than patched.
     */

    /**
     * Creates the dashboard page
     *
     * @see   add_theme_page()
     * @since 1.8.2.4
     */
    public function sparkling_welcome_register_menu() {
        $action_count = $this->count_actions();
        $title        = $action_count > 0 ? 'About Sparkling <span class="badge-action-count">' . esc_html( $action_count ) . '</span>' : 'About Sparkling';

        add_theme_page(
            'About Sparkling', $title, 'edit_theme_options', 'sparkling-welcome', array(
                $this,
                'sparkling_welcome_screen',
            )
        );
    }

    /**
     * Adds an admin notice upon successful activation.
     *
     * @since 1.8.2.4
     */
    public function sparkling_activation_admin_notice() {
        global $pagenow;

        if ( is_admin() && ( 'themes.php' == $pagenow ) && isset( $_GET['activated'] ) ) {
            add_action( 'admin_notices', array( $this, 'sparkling_welcome_admin_notice' ), 99 );
        }
    }

    /**
     * Display an admin notice linking to the welcome screen
     *
     * @since 1.8.2.4
     */
    public function sparkling_welcome_admin_notice() {
        ?>
        <div class="updated notice is-dismissible">
            <p><?php
            /* translators: 1: opening anchor tag, 2: closing anchor tag */
            echo sprintf( esc_html__( 'Welcome! Thank you for choosing Sparkling! To fully take advantage of the best our theme can offer please make sure you visit our %1$swelcome page%2$s.', 'sparkling' ), '<a href="' . esc_url( admin_url( 'themes.php?page=sparkling-welcome' ) ) . '">', '</a>' ); ?></p>
            <p><a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome' ) ); ?>" class="button"
                  style="text-decoration: none;"><?php _e( 'Get started with Sparkling', 'sparkling' ); ?></a></p>
        </div>
        <?php
    }

    /**
     * Load welcome screen css and javascript
     *
     * @since  1.8.2.4
     */
    public function sparkling_welcome_style_and_scripts( $hook_suffix ) {

        $screen = get_current_screen();

        /*
         * Only load on the welcome page itself. This used to run on every admin
         * screen, putting the theme's stylesheet on pages that never use it.
         */
        $is_welcome_page = ( 'appearance_page_sparkling-welcome' === $hook_suffix );

        if ( ! $is_welcome_page && 'customize' !== $screen->base ) {
            return;
        }

        wp_enqueue_style( 'sparkling-welcome-screen-css', get_template_directory_uri() . '/inc/welcome-screen/css/welcome.css', array(), SPARKLING_VERSION );

        /*
         * The recommended-plugins tab renders core's plugin-card markup. Enqueue
         * core's assets here rather than inside the section template, which runs
         * after the head has been printed and so only landed via late styles.
         */
        if ( $is_welcome_page ) {
            wp_enqueue_style( 'plugin-install' );
            wp_enqueue_script( 'plugin-install' );
            wp_enqueue_script( 'updates' );
            add_thickbox();
        }

        if ( 'customize' != $screen->base ) {
            wp_enqueue_script( 'sparkling-welcome-screen-js', get_template_directory_uri() . '/inc/welcome-screen/js/welcome.js', array( 'jquery' ), '1.0', true );

            wp_localize_script(
                'sparkling-welcome-screen-js', 'sparklingWelcomeScreenObject', array(
                    'nr_actions_required'      => $this->count_actions(),
                    'ajaxurl'                  => admin_url( 'admin-ajax.php' ),
                    'template_directory'       => get_template_directory_uri(),
                    'nonce'                    => wp_create_nonce( 'sparkling_welcome_nonce' ),
                    'no_required_actions_text' => __( 'Hooray! There are no required actions for you right now.', 'sparkling' ),
                )
            );
        }

    }

    /**
     * Dismiss required actions
     *
     * @since 1.8.2.4
     */
    public function sparkling_dismiss_required_action_callback() {
        global $sparkling_required_actions;

        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( esc_html__( 'You are not allowed to do that.', 'sparkling' ), 403 );
        }

        check_ajax_referer( 'sparkling_welcome_nonce', 'nonce' );

        $action_id = isset( $_GET['id'] ) ? sanitize_key( wp_unslash( $_GET['id'] ) ) : 0;
        $todo      = isset( $_GET['todo'] ) ? sanitize_key( wp_unslash( $_GET['todo'] ) ) : '';
        echo esc_html( $action_id ); /* this is needed and it's the id of the dismissable required action */
        if ( ! empty( $action_id ) ) :
            /* if the option exists, update the record for the specified id */
            if ( get_option( 'sparkling_show_required_actions' ) ) :
                $sparkling_show_required_actions = get_option( 'sparkling_show_required_actions' );
                switch ( $todo ) {
                    case 'add':
                        $sparkling_show_required_actions[ $action_id ] = true;
                        break;
                    case 'dismiss':
                        $sparkling_show_required_actions[ $action_id ] = false;
                        break;
                }
                update_option( 'sparkling_show_required_actions', $sparkling_show_required_actions );
                /* create the new option,with false for the specified id */
            else :
                $sparkling_show_required_actions_new = array();
                if ( ! empty( $sparkling_required_actions ) ) :
                    foreach ( $sparkling_required_actions as $sparkling_required_action ) :
                        if ( $sparkling_required_action['id'] == $action_id ) :
                            $sparkling_show_required_actions_new[ $sparkling_required_action['id'] ] = false;
                        else :
                            $sparkling_show_required_actions_new[ $sparkling_required_action['id'] ] = true;
                        endif;
                    endforeach;
                    update_option( 'sparkling_show_required_actions', $sparkling_show_required_actions_new );
                endif;
            endif;
        endif;
        die(); // this is required to return a proper result
    }

    public function sparkling_dismiss_recommended_plugins_callback() {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_send_json_error( esc_html__( 'You are not allowed to do that.', 'sparkling' ), 403 );
        }

        check_ajax_referer( 'sparkling_welcome_nonce', 'nonce' );

        $action_id = isset( $_GET['id'] ) ? sanitize_key( wp_unslash( $_GET['id'] ) ) : 0;
        $todo      = isset( $_GET['todo'] ) ? sanitize_key( wp_unslash( $_GET['todo'] ) ) : '';
        echo esc_html( $action_id ); /* this is needed and it's the id of the dismissable required action */
        if ( ! empty( $action_id ) ) :
            /* if the option exists, update the record for the specified id */
            $sparkling_show_recommended_plugins = get_option( 'sparkling_show_recommended_plugins' );

            if ( ! is_array( $sparkling_show_recommended_plugins ) ) {
                $sparkling_show_recommended_plugins = array();
            }

            switch ( $todo ) {
                case 'add':
                    $sparkling_show_recommended_plugins[ $action_id ] = false;
                    break;
                case 'dismiss':
                    $sparkling_show_recommended_plugins[ $action_id ] = true;
                    break;
            }
                update_option( 'sparkling_show_recommended_plugins', $sparkling_show_recommended_plugins );
            /* create the new option,with false for the specified id */
        endif;
        die(); // this is required to return a proper result
    }

    /**
     * Count the number of required actions
     */
    public function count_actions() {
        global $sparkling_required_actions;

        $sparkling_show_required_actions = get_option( 'sparkling_show_required_actions' );
        if ( ! is_array( $sparkling_show_required_actions ) ) {
            $sparkling_show_required_actions = array();
        }

        if ( ! is_array( $sparkling_required_actions ) ) {
            return 0;
        }

        $i = 0;
        foreach ( $sparkling_required_actions as $action ) {
            $true = false;

            if ( ! isset( $action['id'] ) ) {
                continue;
            }

            if ( empty( $action['check'] ) ) {
                $true = true;
            }

            if ( ! empty( $sparkling_show_required_actions ) && isset( $sparkling_show_required_actions[ $action['id'] ] ) && ! $sparkling_show_required_actions[ $action['id'] ] ) {
                $true = false;
            }

            if ( $true ) {
                $i ++;
            }
        }

        return $i;
    }

    /**
     * Call plugin API to get plugins info
     */
    public function call_plugin_api( $slug ) {
        include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
        $call_api = get_transient( 'sparkling_plugin_information_transient_' . $slug );
        if ( false === $call_api ) {
            $call_api = plugins_api(
                'plugin_information', array(
                    'slug'   => $slug,
                    'fields' => array(
                        'downloaded'        => false,
                        'rating'            => false,
                        'description'       => false,
                        'short_description' => true,
                        'donate_link'       => false,
                        'tags'              => false,
                        'sections'          => true,
                        'homepage'          => true,
                        'added'             => false,
                        'last_updated'      => false,
                        'compatibility'     => false,
                        'tested'            => false,
                        'requires'          => false,
                        'downloadlink'      => false,
                        'icons'             => true,
                    ),
                )
            );
            set_transient( 'sparkling_plugin_information_transient_' . $slug, $call_api, 30 * MINUTE_IN_SECONDS );
        }

        return $call_api;
    }

    /**
     * Check if a plugin is active
     */
    public function check_active( $slug ) {
        if ( file_exists( ABSPATH . 'wp-content/plugins/' . $slug . '/' . $slug . '.php' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            $needs = is_plugin_active( $slug . '/' . $slug . '.php' ) ? 'deactivate' : 'activate';

            return array(
                'status' => is_plugin_active( $slug . '/' . $slug . '.php' ),
                'needs'  => $needs,
            );
        }

        return array(
            'status' => false,
            'needs'  => 'install',
        );
    }

    /**
     * Check for icon
     */
    public function check_for_icon( $arr ) {
        if ( ! empty( $arr['svg'] ) ) {
            $plugin_icon_url = $arr['svg'];
        } elseif ( ! empty( $arr['2x'] ) ) {
            $plugin_icon_url = $arr['2x'];
        } elseif ( ! empty( $arr['1x'] ) ) {
            $plugin_icon_url = $arr['1x'];
        } else {
            $plugin_icon_url = $arr['default'];
        }

        return $plugin_icon_url;
    }

    /**
     * Create action link
     */
    public function create_action_link( $state, $slug ) {
        switch ( $state ) {
            case 'install':
                return wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'install-plugin',
                            'plugin' => $slug,
                        ),
                        network_admin_url( 'update.php' )
                    ),
                    'install-plugin_' . $slug
                );
                break;
            case 'deactivate':
                return add_query_arg(
                    array(
                        'action'        => 'deactivate',
                        'plugin'        => rawurlencode( $slug . '/' . $slug . '.php' ),
                        'plugin_status' => 'all',
                        'paged'         => '1',
                        '_wpnonce'      => wp_create_nonce( 'deactivate-plugin_' . $slug . '/' . $slug . '.php' ),
                    ), network_admin_url( 'plugins.php' )
                );
                break;
            case 'activate':
                return add_query_arg(
                    array(
                        'action'        => 'activate',
                        'plugin'        => rawurlencode( $slug . '/' . $slug . '.php' ),
                        'plugin_status' => 'all',
                        'paged'         => '1',
                        '_wpnonce'      => wp_create_nonce( 'activate-plugin_' . $slug . '/' . $slug . '.php' ),
                    ), network_admin_url( 'plugins.php' )
                );
                break;
        }
    }

    /**
     * Welcome screen content
     *
     * @since 1.8.2.4
     */
    public function sparkling_welcome_screen() {
        if ( ! current_user_can( 'edit_theme_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to access this page.', 'sparkling' ) );
        }

        $sparkling  = wp_get_theme();
        $allowed    = array( 'getting_started', 'recommended_actions', 'recommended_plugins', 'changelog', 'support' );
        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'getting_started';

        if ( ! in_array( $active_tab, $allowed, true ) ) {
            $active_tab = 'getting_started';
        }

        ?>

        <div class="wrap about-wrap epsilon-wrap">

            <h1><?php echo esc_html( __( 'Welcome to Sparkling! - Version ', 'sparkling' ) . $sparkling['Version'] ); ?></h1>

            <div
                class="about-text"><?php echo esc_html__( 'Sparkling is now installed and ready to use! Get ready to build something beautiful. We hope you enjoy it! We want to make sure you have the best experience using Sparkling and that is why we gathered here all the necessary information for you. We hope you will enjoy using Sparkling, as much as we enjoy creating great products.', 'sparkling' ); ?></div>

            <div class="wp-badge epsilon-welcome-logo"></div>


            <h2 class="nav-tab-wrapper wp-clearfix">
                <a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome&tab=getting_started' ) ); ?>"
                   class="nav-tab <?php echo 'getting_started' == $active_tab ? 'nav-tab-active' : ''; ?>"><?php echo esc_html__( 'Getting Started', 'sparkling' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome&tab=recommended_plugins' ) ); ?>"
                   class="nav-tab <?php echo 'recommended_plugins' == $active_tab ? 'nav-tab-active' : ''; ?> "><?php echo esc_html__( 'Recommended Plugins', 'sparkling' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome&tab=changelog' ) ); ?>"
                   class="nav-tab <?php echo 'changelog' == $active_tab ? 'nav-tab-active' : ''; ?> "><?php echo esc_html__( 'Changelog', 'sparkling' ); ?></a>
                <a href="<?php echo esc_url( admin_url( 'themes.php?page=sparkling-welcome&tab=support' ) ); ?>"
                   class="nav-tab <?php echo 'support' == $active_tab ? 'nav-tab-active' : ''; ?> "><?php echo esc_html__( 'Support', 'sparkling' ); ?></a>
            </h2>

            <?php
            /*
             * The plugin tabs render core's plugin-card markup, which core styles
             * for a plain .wrap page. about.css restyles p, h3 and img for
             * everything inside .about-wrap and loads after list-tables.css, so
             * nesting the cards here inflated their type and blew the card heights
             * out. Close .about-wrap after the tab nav and let those tabs render in
             * the context core designed the component for.
             */
            $sparkling_plugin_tabs = array( 'recommended_plugins', 'recommended_actions' );
            $sparkling_bare_wrap   = in_array( $active_tab, $sparkling_plugin_tabs, true );

            if ( $sparkling_bare_wrap ) {
                echo '</div><div class="wrap sparkling-welcome-plugins">';
            }

            switch ( $active_tab ) {
                case 'getting_started':
                    get_template_part( 'inc/welcome-screen/sections/getting-started' );
                    break;
                case 'recommended_actions':
                    get_template_part( 'inc/welcome-screen/sections/actions-required' );
                    break;
                case 'recommended_plugins':
                    get_template_part( 'inc/welcome-screen/sections/recommended-plugins' );
                    break;
                case 'changelog':
                    get_template_part( 'inc/welcome-screen/sections/changelog' );
                    break;
                case 'support':
                    get_template_part( 'inc/welcome-screen/sections/support' );
                    break;
                default:
                    get_template_part( 'inc/welcome-screen/sections/getting-started' );
                    break;
            }
            ?>


        </div><!--/.wrap.about-wrap-->

        <?php
    }
} 
<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

session_start();

/**
 * AOKranj administration
 *
 * @package aokranj
 * @link http://aokranj.com/
 * @author Bojan Hribernik <bojan.hribernik@gmail.com>
 */
class AOKranj_Admin extends AOKranj
{

    public function __construct() {
        if (is_multisite()) {
            $admin_menu = 'network_admin_menu';
            $admin_notices = 'network_admin_notices';

            $options_page = 'settings.php';
            $options_form_action = '../options.php';
            $options_capability = 'manage_network_options';
        } else {
            $admin_menu = 'admin_menu';
            $admin_notices = 'admin_notices';

            $options_page = 'options-general.php';
            $options_form_action = 'options.php';
            $options_capability = 'manage_options';
        }

        // activate/deactivate
        register_activation_hook(__FILE__, array(&$this, 'activate'));
        register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));

        // init
        add_action('admin_init', array(&$this, 'admin_init'));

        // menu
        add_action($admin_menu, array(&$this, 'admin_menu'));

        // add scripts and styles
        add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));

        // dashboard setup
        add_action('wp_dashboard_setup', array(&$this, 'wp_dashboard_setup'));

        // filter posts hook
        add_action('pre_get_posts', array(&$this, 'pre_get_posts'));

        // submit actions
        add_action('admin_post_dodaj_vzpon', array(&$this, 'dodaj_vzpon'));
        add_action('admin_post_uredi_vzpon', array(&$this, 'uredi_vzpon'));
        add_action('admin_post_prenos_podatkov', array(&$this, 'prenos_podatkov'));

        // ajax actions
        //add_action('wp_ajax_vzponi', array(&$this, 'ajax_vzponi'));
        //add_action('wp_ajax_dodaj_vzpon', array(&$this, 'ajax_dodaj_vzpon'));
        //add_action('wp_ajax_prenos_podatkov', array(&$this, 'ajax_prenos_podatkov'));
    }

    // init

    public function activate() {
        global $wpdb;

        if (is_multisite() && !is_network_admin()) {
            die(self::NAME . ' must be activated via the Network Admin interface'
                    . 'when WordPress is in multistie network mode.');
        }

        /*
         * Create or alter the plugin's tables as needed.
         */

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // Note: dbDelta() requires two spaces after "PRIMARY KEY".  Weird.
        // WP's insert/prepare/etc don't handle NULL's (at least in 3.3).
        // It also requires the keys to be named and there to be no space
        // the column name and the key length.
        /*
        $sql = "CREATE TABLE IF NOT EXISTS " . AOKRANJ_TABLE_VZPONI . " (
                id int(10) unsigned NOT NULL AUTO_INCREMENT,
                user_id bigint(20) unsigned NOT NULL,
                tip varchar(4) NOT NULL DEFAULT '',
                datum date NOT NULL DEFAULT '0000-00-00',
                destinacija varchar(50) NOT NULL DEFAULT '',
                smer varchar(50) NOT NULL DEFAULT '',
                ocena varchar(30) DEFAULT NULL,
                cas varchar(30) DEFAULT NULL,
                vrsta varchar(4) NOT NULL DEFAULT '',
                visina_smer varchar(15) NOT NULL DEFAULT '',
                visina_izstop varchar(15) NOT NULL DEFAULT '',
                pon_vrsta varchar(4) DEFAULT NULL,
                pon_nacin varchar(4) DEFAULT NULL,
                stil varchar(4) DEFAULT NULL,
                mesto varchar(4) DEFAULT NULL,
                partner varchar(50) DEFAULT NULL,
                opomba varchar(5) DEFAULT NULL,
                deleted int(1) unsigned DEFAULT NULL,
                PRIMARY KEY  (id)
            )";

        dbDelta($sql);

        if ($wpdb->last_error) {
            die($wpdb->last_error);
        }
        */
    }

    public function deactivate() {
        return;

        /*
        global $wpdb;

        $show_errors = $wpdb->show_errors;
        $wpdb->show_errors = false;
        $denied = 'command denied to user';

        $wpdb->query("DROP TABLE " . AOKRANJ_TABLE_VZPONI);

        if ($wpdb->last_error) {
            if (strpos($wpdb->last_error, $denied) === false) {
                die($wpdb->last_error);
            }
        }

        $wpdb->show_errors = $show_errors;
         *
         */
    }

    public function admin_init() {
        /*
        add_action('show_user_profile', array(&$this, 'user_profile_extra_fields'));
        add_action('edit_user_profile', array(&$this, 'user_profile_extra_fields'));
        */
    }

    public function admin_menu() {
        if (!current_user_can('manage_options')) {
            remove_menu_page('tools.php');
        }

        $svg = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 32 32"><g></g><path d="M28.438 0.438c-1.247-0.44-2.476-0.19-3.5 0.375-0.971 0.536-1.775 1.342-2.313 2.25h-0.063c-0.024 0.033-0.038 0.092-0.063 0.125-1.135 1.509-3.033 2.978-3.688 5.438v0.188c-0.144 1.653 0.755 3.048 1.875 3.938l0.25 0.25h0.313c1.479 0.112 2.641-0.593 3.563-1.313s1.722-1.464 2.438-1.875v-0.063c1.884-1.267 4.115-2.982 4.688-5.688v-0.125c0.070-1.186-0.699-2.113-1.438-2.563s-1.464-0.65-1.875-0.875l-0.063-0.063h-0.125zM27.75 2.313c0.019 0.010 0.044-0.010 0.063 0 0.626 0.317 1.298 0.576 1.688 0.813 0.386 0.235 0.437 0.31 0.438 0.625-0.411 1.754-1.963 3.145-3.688 4.313-0.025 0.017-0.038 0.046-0.063 0.063-1.027 0.608-1.877 1.416-2.625 2-0.639 0.499-1.182 0.693-1.813 0.75-0.514-0.519-0.94-1.134-0.938-1.75 0.477-1.656 2.038-3.039 3.375-4.875l0.063-0.063c0.354-0.639 0.978-1.268 1.625-1.625 0.626-0.346 1.26-0.447 1.875-0.25z" fill="#ffffff" /><path d="M13.172 21.246c0.105-0.162 0.505-0.571 1.041-1.204l0.008-0.050c1.129-1.389 3.059-2.774 4.973-4.857l0.126-0.126c0.855-1.066 1.692-1.925 2.518-2.46l-1.24-2.66c-1.68 1.087-2.89 2.463-3.884 3.69l-0.048-0.037c-1.286 1.399-3.322 2.823-5.17 5.095-0.308 0.363-0.879 0.892-1.451 1.781l3.127 0.828z" fill="#ffffff" /><path d="M0.96 28.029c-0.429-1.251-0.168-2.478 0.407-3.496 0.545-0.966 1.358-1.762 2.271-2.292l0.001-0.063c0.033-0.024 0.093-0.037 0.126-0.061 1.52-1.121 3.006-3.006 5.471-3.638l0.188 0.002c1.654-0.129 3.041 0.783 3.92 1.911l0.248 0.252-0.003 0.313c0.099 1.48-0.617 2.636-1.345 3.55s-1.48 1.708-1.897 2.42l-0.063-0.001c-1.284 1.872-3.020 4.087-5.73 4.635l-0.125-0.001c-1.187 0.059-2.107-0.718-2.549-1.461s-0.637-1.47-0.858-1.883l-0.062-0.063 0.001-0.125zM2.841 27.358c0.010 0.019-0.010 0.043-0.001 0.063 0.311 0.629 0.564 1.303 0.797 1.695 0.231 0.388 0.306 0.44 0.621 0.443 1.757-0.395 3.163-1.935 4.346-3.648 0.017-0.025 0.046-0.037 0.063-0.062 0.618-1.021 1.433-1.864 2.024-2.607 0.505-0.634 0.704-1.175 0.767-1.806-0.515-0.518-1.126-0.95-1.741-0.953-1.66 0.462-3.057 2.010-4.906 3.33l-0.063 0.062c-0.642 0.348-1.277 0.967-1.64 1.61-0.351 0.623-0.458 1.256-0.267 1.873z" fill="#ffffff" /><path d="M12.455 21.093c0.099-0.165 0.487-0.586 1.003-1.236l0.006-0.050c1.086-1.423 2.971-2.868 4.819-5.009l0.122-0.129c0.822-1.093 1.631-1.977 2.44-2.537l-1.323-2.62c-1.645 1.139-2.812 2.552-3.767 3.809l-0.049-0.036c-1.241 1.439-3.232 2.925-5.009 5.254-0.296 0.372-0.85 0.919-1.395 1.825l3.151 0.73z" fill="#ffffff" /></svg>';
        $icon = 'data:image/svg+xml;base64,' . base64_encode($svg);

        add_menu_page('Vzponi', 'Vzponi', 'read', 'aokranj-vzponi', array(&$this, 'page_vzponi'), $icon, 5);
        add_submenu_page('aokranj-vzponi', 'Dodaj vzpon', 'Dodaj vzpon', 'read', 'aokranj-vzpon', array(&$this, 'page_vzpon'));
        add_submenu_page('aokranj-vzponi', 'Prenos podatkov', 'Prenos podatkov', 'activate_plugins', 'aokranj-prenos', array(&$this, 'page_prenos'));
    }

    // pages

    public function page_vzponi() {
        require_once AOKRANJ_PLUGIN_DIR . '/vzponi.php';
    }

    public function page_vzpon() {
        require_once AOKRANJ_PLUGIN_DIR . '/vzpon.php';
    }

    public function page_prenos() {
        require_once AOKRANJ_PLUGIN_DIR . '/prenos.php';
    }

    // core

    public function admin_enqueue_scripts() {
        global $hook_suffix;

        if (AOKRANJ_DEBUG === true) {
            wp_enqueue_script('livereload', 'http://localhost:35729/livereload.js?snipver=1', null, false, true);
        }

        wp_register_script('moment', AOKRANJ_PLUGIN_URL . '/js/moment.min.js', array());

        wp_register_style('pikaday', AOKRANJ_PLUGIN_URL . '/js/pikaday/pikaday.css', array());
        wp_register_script('pikadayjs', AOKRANJ_PLUGIN_URL . '/js/pikaday/pikaday.js', array());
        wp_register_script('pikaday', AOKRANJ_PLUGIN_URL . '/js/pikaday/pikaday.jquery.js', array('pikadayjs'));

        wp_enqueue_style('aokranj-plugin-admin', AOKRANJ_PLUGIN_URL . '/css/admin.css', array(), AOKRANJ_PLUGIN_VERSION);
        wp_enqueue_script('aokranj-plugin-admin', AOKRANJ_PLUGIN_URL . '/js/admin.js', array('jquery'), AOKRANJ_PLUGIN_VERSION );
    }

    public function wp_dashboard_setup() {
        global $wp_meta_boxes;
        //unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
    }

    public function user_profile_extra_fields() {
        echo '
        <h3>Extra profile information</h3>

        <table class="form-table">

            <tr>
                <th><label for="twitter">Twitter</label></th>

                <td>
                    <input type="text" name="twitter" id="twitter" value="' . esc_attr(get_the_author_meta('twitter', get_current_user_id())) . '" class="regular-text" /><br />
                    <span class="description">Please enter your Twitter username.</span>
                </td>
            </tr>

        </table>';
    }

    // overrides

    public function pre_get_posts($query) {
    		global $current_user;

    		// do not limit user with Administrator role
    		if (current_user_can('administrator')) {
    			return;
    		}

    		if (current_user_can('edit_posts') && !current_user_can('edit_others_posts')) {
    			$query->set('author', $current_user->ID);

                add_filter('views_edit-post', array(&$this, 'fix_post_counts'));
    			add_filter('views_upload', array(&$this, 'fix_media_counts'));
    		}
    }

    public function fix_post_counts($views) {
    		global $current_user, $wp_query;

    		unset($views['mine']);

    		$types = array(
    			array('status' => NULL),
    			array('status' => 'publish'),
    			array('status' => 'draft'),
    			array('status' => 'pending'),
    			array('status' => 'trash')
    		);

    		foreach ($types as $type) {
      			$query = array(
      				'author' => $current_user->ID,
      				'post_type' => 'post',
      				'post_status' => $type['status']
      			);
            $result = new WP_Query($query);
      			if ($type['status'] == NULL):
        				$class = (empty($wp_query->query_vars['post_status']) || $wp_query->query_vars['post_status'] == NULL) ? ' class="current"' : '';
        				$views['all'] = sprintf('<a href="%s"' . $class . '>' . __('All', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_type=post'), $result->found_posts);
      			elseif ($type['status'] == 'publish'):
        				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'publish') ? ' class="current"' : '';
        				$views['publish'] = sprintf('<a href="%s"' . $class . '>' . __('Published', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=publish&post_type=post'), $result->found_posts);
      			elseif ($type['status'] == 'draft'):
        				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'draft') ? ' class="current"' : '';
        				$views['draft'] = sprintf('<a href="%s"' . $class . '>' . __('Drafts', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=draft&post_type=post'), $result->found_posts);
      			elseif ($type['status'] == 'pending'):
        				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'pending') ? ' class="current"' : '';
        				$views['pending'] = sprintf('<a href="%s"' . $class . '>' . __('Pending', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=pending&post_type=post'), $result->found_posts);
      			elseif ($type['status'] == 'trash'):
        				$class = (!empty($wp_query->query_vars['post_status']) && $wp_query->query_vars['post_status'] == 'trash') ? ' class="current"' : '';
        				$views['trash'] = sprintf('<a href="%s"' . $class . '>' . __('Trash', 'vopmo') . ' <span class="count">(%d)</span></a>', admin_url('edit.php?post_status=trash&post_type=post'), $result->found_posts);
      			endif;
    		}
    		return $views;
    }

    public function fix_media_counts($views) {
    		global $wpdb, $current_user, $post_mime_types, $avail_post_mime_types;
    		$views = array();
    		$_num_posts = array();
    		$count = $wpdb->get_results("
            SELECT post_mime_type, COUNT( * ) AS num_posts
            FROM $wpdb->posts
            WHERE post_type = 'attachment'
            AND post_author = $current_user->ID
            AND post_status != 'trash'
            GROUP BY post_mime_type
        ", ARRAY_A);
    		foreach ($count as $row)
    			$_num_posts[$row['post_mime_type']] = $row['num_posts'];
    		if (!empty($_num_posts)) {
    			$_total_posts = array_sum($_num_posts);
    		} else {
    			$_total_posts = 0;
    		}
    		$detached = isset($_REQUEST['detached']) || isset($_REQUEST['find_detached']);
    		if (!isset($total_orphans))
    			$total_orphans = $wpdb->get_var("
                SELECT COUNT( * )
                FROM $wpdb->posts
                WHERE post_type = 'attachment'
                AND post_author = $current_user->ID
                AND post_status != 'trash'
                AND post_parent < 1
            ");
    		$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
    		foreach ($matches as $type => $reals)
    			foreach ($reals as $real)
    				$num_posts[$type] = ( isset($num_posts[$type]) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];
    		$class = ( empty($_GET['post_mime_type']) && !$detached && !isset($_GET['status']) ) ? ' class="current"' : '';
    		$views['all'] = "<a href='upload.php'$class>" . sprintf(__('All <span class="count">(%s)</span>'), number_format_i18n($_total_posts)) . '</a>';
    		foreach ($post_mime_types as $mime_type => $label) {
    			$class = '';
    			if (!wp_match_mime_types($mime_type, $avail_post_mime_types))
    				continue;
    			if (!empty($_GET['post_mime_type']) && wp_match_mime_types($mime_type, $_GET['post_mime_type']))
    				$class = ' class="current"';
    			if (!empty($num_posts[$mime_type]))
    				$views[$mime_type] = "<a href='upload.php?post_mime_type=$mime_type'$class>" . sprintf(translate_nooped_plural($label[2], $num_posts[$mime_type]), $num_posts[$mime_type]) . '</a>';
    		}
    		$views['detached'] = '<a href="upload.php?detached=1"' . ( $detached ? ' class="current"' : '' ) . '>' . sprintf(__('Unattached <span class="count">(%s)</span>'), $total_orphans) . '</a>';
    		return $views;
    }

    // submit

    public function dodaj_vzpon() {
        // check nonce
        check_admin_referer('dodaj_vzpon');

        // set url
        $url = admin_url('/admin.php?page=aokranj-vzpon');

        // load vzpon class
        require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzpon.php';

        // create new vzpon instance from post data
        $data = AOKranj_Vzpon::getPostData();
        $Vzpon = new AOKranj_Vzpon($data);

        // validate
        $errors = $Vzpon->validate();
        if (count($errors) > 0) {
            $_SESSION['vzpon'] = $vzpon;
            $_SESSION['errors'] = $errors;
            wp_redirect($url);
            die;
        }

        // insert into db
        try {
            $id = $Vzpon->create();
        } catch (Exception $e) {
            $_SESSION['error'] = __('Prišlo je do napake pri dodajanju vzpona! Prosimo obvestite administratorja.') . '<br />' . $e->getError();
            wp_redirect($url);
            die;
        }

        // set session message
        $_SESSION['message'] = __('Vzpon je bil uspešno shranjen.');
        unset($_SESSION['vzpon'], $_SESSION['errors']);

        // redirect to vzpon
        wp_redirect($url . '&id=' . $id);
        die;
    }

    public function uredi_vzpon() {
        // check nonce
        check_admin_referer('uredi_vzpon');

        global $wpdb;

        // get id and user_id
        $id = (int)filter_input(INPUT_POST, 'id');
        $user_id = (int)get_current_user_id();

        // url
        $url = admin_url('/admin.php?page=aokranj-vzpon&id=' . $id);

        // create vzpon instance
        require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzpon.php';
        $Vzpon = new AOKranj_Vzpon();

        // load vzpon
        if (!$Vzpon->load($id)) {
            $_SESSION['error'] = __('Vzpon #' . $id . ' ne obstaja!');
            wp_redirect($url);
            die;
        }

        // overwrite vzpon data from post data
        $data = AOKranj_Vzpon::getPostData();
        $Vzpon->setData($data);

        // validate vzpon
        $errors = $Vzpon->validate();
        if (count($errors) > 0) {
            $_SESSION['vzpon'] = $vzpon;
            $_SESSION['errors'] = $errors;
            wp_redirect($url);
            die;
        }

        // update vzpon or send error
        try {
            $Vzpon->update();
        } catch (Exception $e) {
            $_SESSION['error'] = __('Prišlo je do napake pri urejanju vzpona! Prosimo obvestite administratorja.') . '<br />' . $e->getMessage();
            wp_redirect($url);
            die;
        }

        // set session message
        $_SESSION['message'] = __('Vzpon je bil uspešno shranjen.');
        unset($_SESSION['vzpon'], $_SESSION['errors']);

        // redirect back to vzpon
        wp_redirect($url);
        die;
    }

    public function prenos_podatkov() {
        // check nonce
        check_admin_referer('prenos_podatkov');

        // get db connections
        global $wpdb;
        $aodb = $this->aodb();

        // do it!!!
        require_once AOKRANJ_PLUGIN_DIR . '/admin/class-prenos-podatkov.php';
        $prenos = new AOKranj_Prenos_Podatkov($wpdb, $aodb);
        $response = $prenos->start();

        // set session
        $_SESSION['prenos'] = $response;

        // redirect back
        wp_redirect(admin_url('/admin.php?page=aokranj-prenos'));
    }

}

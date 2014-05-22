<?php

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
    public function __construct()
    {
        if (is_multisite())
        {
            $options_page = 'settings.php';
            $options_form_action = '../options.php';
            $options_capability = 'manage_network_options';
        }
        else
        {
            $options_page = 'options-general.php';
            $options_form_action = 'options.php';
            $options_capability = 'manage_options';
        }
        
        add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
            
        add_action('wp_ajax_vzponi', array(&$this, 'vzponi'));
        add_action('wp_ajax_dodaj_vzpon', array(&$this, 'dodaj_vzpon'));
        add_action('wp_ajax_prenos_podatkov', array(&$this, 'prenos_podatkov'));
    }
    
    /**
     * Wordpress hooks
     */

    public function activate()
    {
        global $wpdb;

        if (is_multisite() && !is_network_admin())
        {
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
        $sql = "CREATE TABLE " . $this->table_vzponi . " (
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
        
        if ($wpdb->last_error)
        {
            die($wpdb->last_error);
        }
    }

    public function deactivate()
    {
        return;
        
        /*
        global $wpdb;

        $show_errors = $wpdb->show_errors;
        $wpdb->show_errors = false;
        $denied = 'command denied to user';

        $wpdb->query("DROP TABLE " . $this->table_vzponi);
        
        if ($wpdb->last_error)
        {
            if (strpos($wpdb->last_error, $denied) === false)
            {
                die($wpdb->last_error);
            }
        }

        $wpdb->show_errors = $show_errors;
         *
         */
    }

    public function admin_menu()
    {
        $svg = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="32" height="32" viewBox="0 0 32 32"><g></g><path d="M28.438 0.438c-1.247-0.44-2.476-0.19-3.5 0.375-0.971 0.536-1.775 1.342-2.313 2.25h-0.063c-0.024 0.033-0.038 0.092-0.063 0.125-1.135 1.509-3.033 2.978-3.688 5.438v0.188c-0.144 1.653 0.755 3.048 1.875 3.938l0.25 0.25h0.313c1.479 0.112 2.641-0.593 3.563-1.313s1.722-1.464 2.438-1.875v-0.063c1.884-1.267 4.115-2.982 4.688-5.688v-0.125c0.070-1.186-0.699-2.113-1.438-2.563s-1.464-0.65-1.875-0.875l-0.063-0.063h-0.125zM27.75 2.313c0.019 0.010 0.044-0.010 0.063 0 0.626 0.317 1.298 0.576 1.688 0.813 0.386 0.235 0.437 0.31 0.438 0.625-0.411 1.754-1.963 3.145-3.688 4.313-0.025 0.017-0.038 0.046-0.063 0.063-1.027 0.608-1.877 1.416-2.625 2-0.639 0.499-1.182 0.693-1.813 0.75-0.514-0.519-0.94-1.134-0.938-1.75 0.477-1.656 2.038-3.039 3.375-4.875l0.063-0.063c0.354-0.639 0.978-1.268 1.625-1.625 0.626-0.346 1.26-0.447 1.875-0.25z" fill="#000000" /><path d="M13.172 21.246c0.105-0.162 0.505-0.571 1.041-1.204l0.008-0.050c1.129-1.389 3.059-2.774 4.973-4.857l0.126-0.126c0.855-1.066 1.692-1.925 2.518-2.46l-1.24-2.66c-1.68 1.087-2.89 2.463-3.884 3.69l-0.048-0.037c-1.286 1.399-3.322 2.823-5.17 5.095-0.308 0.363-0.879 0.892-1.451 1.781l3.127 0.828z" fill="#000000" /><path d="M0.96 28.029c-0.429-1.251-0.168-2.478 0.407-3.496 0.545-0.966 1.358-1.762 2.271-2.292l0.001-0.063c0.033-0.024 0.093-0.037 0.126-0.061 1.52-1.121 3.006-3.006 5.471-3.638l0.188 0.002c1.654-0.129 3.041 0.783 3.92 1.911l0.248 0.252-0.003 0.313c0.099 1.48-0.617 2.636-1.345 3.55s-1.48 1.708-1.897 2.42l-0.063-0.001c-1.284 1.872-3.020 4.087-5.73 4.635l-0.125-0.001c-1.187 0.059-2.107-0.718-2.549-1.461s-0.637-1.47-0.858-1.883l-0.062-0.063 0.001-0.125zM2.841 27.358c0.010 0.019-0.010 0.043-0.001 0.063 0.311 0.629 0.564 1.303 0.797 1.695 0.231 0.388 0.306 0.44 0.621 0.443 1.757-0.395 3.163-1.935 4.346-3.648 0.017-0.025 0.046-0.037 0.063-0.062 0.618-1.021 1.433-1.864 2.024-2.607 0.505-0.634 0.704-1.175 0.767-1.806-0.515-0.518-1.126-0.95-1.741-0.953-1.66 0.462-3.057 2.010-4.906 3.33l-0.063 0.062c-0.642 0.348-1.277 0.967-1.64 1.61-0.351 0.623-0.458 1.256-0.267 1.873z" fill="#000000" /><path d="M12.455 21.093c0.099-0.165 0.487-0.586 1.003-1.236l0.006-0.050c1.086-1.423 2.971-2.868 4.819-5.009l0.122-0.129c0.822-1.093 1.631-1.977 2.44-2.537l-1.323-2.62c-1.645 1.139-2.812 2.552-3.767 3.809l-0.049-0.036c-1.241 1.439-3.232 2.925-5.009 5.254-0.296 0.372-0.85 0.919-1.395 1.825l3.151 0.73z" fill="#000000" /></svg>';
        $icon = 'data:image/svg+xml;base64,' . base64_encode($svg);
        
        add_menu_page('AO Kranj', 'AO Kranj', 'read', self::ID . '/app.php', null, $icon, 3);
    }

    public function admin_init()
    {
        /*
        add_action('show_user_profile', array(&$this, 'user_profile_extra_fields'));
        add_action('edit_user_profile', array(&$this, 'user_profile_extra_fields'));
         * 
         */
    }
    
    public function user_profile_extra_fields()
    {
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

    public function admin_enqueue_scripts()
    {
        global $hook_suffix;
        
        switch ($hook_suffix)
        {
            case 'aokranj/app.php':
                // ext
                wp_enqueue_style('aokranj-bootstrap', AOKRANJ_PLUGIN_URL . 'app/bootstrap.css', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-ext', AOKRANJ_PLUGIN_URL . 'app/ext/ext-dev.js', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-bootstrap', AOKRANJ_PLUGIN_URL . 'app/bootstrap.js', array(), AOKRANJ_VERSION);
                wp_enqueue_script('aokranj-app', AOKRANJ_PLUGIN_URL . 'app/app.js', array(), AOKRANJ_VERSION);
                break;
        }
        
        wp_enqueue_style('aokranj-css', AOKRANJ_PLUGIN_URL . 'aokranj.css', array(), AOKRANJ_VERSION);
    }
    
    private function getRequestSort()
    {
        $properties = array(
            'destinacija',
            'smer',
            'partner',
            'ocena',
            'datum',
            'tip',
            'cas',
            'visina_smer',
            'visina_izstop',
            'pon_vrsta',
            'pon_nacin',
            'stil',
            'mesto',
            'opomba',
        );
        $directions = array('ASC', 'DESC');
        
        $property = 'datum';
        $direction = 'DESC';
        
        $s = filter_input(INPUT_GET, 'sort');
        $s = json_decode($s, true);
        if (is_array($s))
        {
            $s = $s[0];
            if (isset($s['property']) && in_array($s['property'], $properties))
            {
                $property = $s['property'];
            }
            if (isset($s['direction']) && in_array($s['direction'], $directions))
            {
                $direction = strtoupper($s['direction']);
            }
        }
        
        return array(
            'property' => $property,
            'direction' => $direction,
        );
    }
    
    private function getRequestPage()
    {
        $page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_NUMBER_INT);
        return (!empty($page)) ? $page : 1;
    }
    
    private function getRequestStart()
    {
        $start = filter_input(INPUT_GET, 'start', FILTER_SANITIZE_NUMBER_INT);
        return (!empty($start)) ? $start : 1;
    }
    
    private function getRequestLimit()
    {
        $limit = filter_input(INPUT_GET, 'limit', FILTER_SANITIZE_NUMBER_INT);
        return (!empty($limit)) ? $limit : 1;
    }
    
    /**
     * API functions
     */
    
    public function vzponi()
    {
        global $wpdb;
        
        $page = $this->getRequestPage();
        $start = $this->getRequestStart();
        $limit = $this->getRequestLimit();
        $sort = $this->getRequestSort();
        
        $vzponi = $wpdb->get_results(sprintf('
            SELECT *
            FROM %s
            WHERE user_id = %d
            ORDER BY %s %s
            LIMIT %d, %d',
            $this->table_vzponi,
            get_current_user_id(),
            $sort['property'],
            $sort['direction'],
            $start,
            $limit
        ));
        
        $total = $wpdb->get_var(sprintf('
            SELECT COUNT(id)
            FROM %s
            WHERE user_id = %d',
            $this->table_vzponi,
            get_current_user_id()
        ));
        
        $response = array(
            'success' => true,
            'data'    => $vzponi,
            'total'   => $total,
        );

        die(json_encode($response));
    }
    
    public function dodaj_vzpon()
    {
        $nonce = filter_input(INPUT_POST, 'nonce');
        wp_verify_nonce($nonce, 'aokranj-app');
        
        global $wpdb;
        
        // get values from $_POST
        $vzpon = array(
            'user_id' => get_current_user_id(),
            'tip' => filter_input(INPUT_POST, 'tip'),
            'destinacija' => filter_input(INPUT_POST, 'destinacija'),
            'smer' => filter_input(INPUT_POST, 'smer'),
            'datum' => filter_input(INPUT_POST, 'datum'),
            'ocena' => filter_input(INPUT_POST, 'ocena'),
            'cas' => filter_input(INPUT_POST, 'cas'),
            'vrsta' => filter_input(INPUT_POST, 'vrsta'),
            'visina_smer' => filter_input(INPUT_POST, 'visina_smer'),
            'visina_izstop' => filter_input(INPUT_POST, 'visina_izstop'),
            'pon_vrsta' => filter_input(INPUT_POST, 'pon_vrsta'),
            'pon_nacin' => filter_input(INPUT_POST, 'pon_nacin'),
            'stil' => filter_input(INPUT_POST, 'stil'),
            'mesto' => filter_input(INPUT_POST, 'mesto'),
            'partner' => filter_input(INPUT_POST, 'partner'),
            'opomba' => filter_input(INPUT_POST, 'opomba'),
        );
        
        // insert into db
        $wpdb->insert($this->table_vzponi, array_filter($vzpon));

        // read from db
        $vzpon = $wpdb->get_row("SELECT * FROM " . $this->table_vzponi . " WHERE id = " . $wpdb->insert_id);
        
        $response = array(
            'success' => true,
            'data'    => $vzpon,
            'msg'     => 'Vzpon je bil uspešno dodan.'
        );

        die(json_encode($response));
    }
    
    /**
     * Prenos podatkov
     */
    
    private $currentUser;
    private $users = array();
    private $usersById = array();
    private $usersByUserName = array();
    
    public function prenos_podatkov()
    {
        define('WP_IMPORTING', true);
        
        $nonce = filter_input(INPUT_POST, 'nonce');
        wp_verify_nonce($nonce, 'aokranj-app');
        
        ini_set('max_execution_time', 600);
        set_time_limit(600);
        
        $this->prenesiUporabnike();
        
        $this->prenesiUtrinke();
        
        // extract user data
        $users = array();
        foreach ($this->users as $user)
        {
            $data = $user->data;
            unset($data->user_pass);
            $users[] = $data;
        }
        
        // build response
        $response = array(
            'success' => true,
            'data'    => array(
                'users' => $users,
            ),
            'msg' => 'Uspešno prenešenih uporabnikov: ' . count($users),
        );
        
        die(json_encode($response));
    }
    
    private function prenesiUporabnike()
    {
        global $wpdb;
        $aodb = $this->aodb();
        
        $users = array();
        $ao_users = $aodb->get_results('SELECT * FROM member');
        $total = count($ao_users);
        
        foreach ($ao_users as $i => $ao_user)
        {
            // check if user already exists
            $wp_user = get_user_by('login', $ao_user->userName);
            if ($wp_user)
            {
                $this->users[] = $wp_user;
                $this->usersById[$ao_user->memberId] = $wp_user;
                $this->usersByUserName[$ao_user->userName] = $wp_user;
                continue;
            }
            
            // skip if no username or email
            if (empty($ao_user->userName) && empty($ao_user->email))
            {
                print_r(['no data',$ao_user]);
                continue;
            }
            
            // insert wordpress user
            $wp_user_data = array(
                'user_login'    => $ao_user->userName,
                'user_pass'     => wp_generate_password(12, false),
                'user_nicename' => $ao_user->name . ' ' . $ao_user->surname,
                'first_name'    => $ao_user->name,
                'last_name'     => $ao_user->surname,
            );
            if (!empty($ao_user->email) && strlen(trim($ao_user->email)))
            {
                $wp_user_data['user_email'] = $ao_user->email;
            }
            
            $wp_user_id = wp_insert_user($wp_user_data);

            // error inserting user
            if (is_wp_error($wp_user_id))
            {
                print_r(['unable to insert user', $ao_user, $wp_user_id]);
                continue;
            }
            
            // set user status
            $wpdb->query(sprintf(
                'UPDATE %s SET user_status = %d WHERE ID = %d',
                esc_sql($wpdb->users),
                self::USER_STATUS_WAITING,
                $wp_user_id
            ));
            
            // load wordpress user
            $wp_user = get_user_by('id', $wp_user_id);

            // unable to load user
            if (is_wp_error($wp_user))
            {
                print_r(['unable to load user', $ao_user, $wp_user]);
                continue;
            }
            
            // add to collection mapped by old user id
            $this->users[] = $wp_user;
            $this->usersById[$ao_user->memberId] = $wp_user;
            $this->usersByUserName[$ao_user->userName] = $wp_user;
        }
    }
    
    private function prenesiUtrinke()
    {
        /**
         * DELETE FROM `wp_posts` WHERE ID > 17;
         * DELETE FROM `wp_postmeta` WHERE post_id > 17;
         */
        
        add_filter('upload_dir', array(&$this, 'utrinekUploadDir'));
        
        global $wpdb;
        $aodb = $this->aodb();
        
        $posts = array();
        
        //$utrinki = $aodb->get_results('SELECT * FROM utrinek LIMIT 0, 10');
        $utrinki = $aodb->get_results('SELECT * FROM utrinek');
        
        $path_root = '/home/bojan/www/aokranj';
        $path_utrinki = $path_root . '/pic/utrinek';
        
        $tmp_dir = sys_get_temp_dir();
        
        foreach ($utrinki as $utrinek)
        {
            if (isset($this->usersByUserName[$utrinek->author]))
            {
                // get wordpress user reference
                $user = $this->usersByUserName[$utrinek->author];
                
                $this->currentUser = $user;
                
                // check if post exists
                $exists = $wpdb->get_var(sprintf(
                    'SELECT COUNT(ID) FROM %s WHERE post_author = %d AND post_title = \'%s\' AND post_date = \'%s\'',
                    $wpdb->posts,
                    $user->ID,
                    esc_sql($utrinek->destination),
                    esc_sql(date('Y-m-d H:i:s', strtotime($utrinek->valid_from)))
                ));
                if ($exists)
                {
                    continue;
                }
                
                // create post
                $data = array(
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'post_author' => $user->ID,
                    'post_content' => $utrinek->content, 
                    'post_title' => $utrinek->destination,
                    'post_date' => $utrinek->valid_from,
                    'post_date_gmt' => $utrinek->valid_from,
                );
                $post_id = wp_insert_post($data);
                
                // read old images
                $path_utrinek = $path_utrinki . '/' . $utrinek->author;
                
                $attachments = array();

                // insert attachments
                for ($i = 1; $i < 6; $i++)
                {
                    $file_name = 'utrinek_' . $utrinek->utrinekId . '_' . $i . '.jpg';
                    $source = $path_utrinek . '/' . $file_name;
                    
                    if (!file_exists($source))
                    {
                        continue;
                    }
                    
                    $tmp_name = $tmp_dir . '/' . $file_name;
                    
                    // create a copy because media_handle_sideload() moves the file
                    if (!copy($source, $tmp_name))
                    {
                        print_r(['unable to create temp image', $source, $tmp_name]);
                        continue;
                    }
                    
                    $file = array(
                        'tmp_name' => $tmp_name,
                        'name' => basename($source),
                        'type' => 'image/jpeg',
                        'size' => filesize($source)
                    );
                    
                    $post_data = array(
                        'post_author' => $user->ID
                    );
                    
                    $file_id = media_handle_sideload($file, $post_id, null, $post_data);
                    
                    if (is_wp_error($file_id))
                    {
                        print_r(['unable to add image', $source, $tmp_name]);
                        continue;
                    }
                    
                    $attachments[] = $file_id;
                }
                
                // insert gallery
                if (count($attachments) > 0)
                {
                    $gallery = '[gallery link="file" ids="' . implode(',', $attachments) . '"]';
                    $content = $utrinek->content . PHP_EOL . PHP_EOL . $gallery;
                    
                    $data = array(
                        'ID' => $post_id,
                        'post_content' => $content
                    );
                    
                    $post_id = wp_update_post($data);
                }
            }
        }
        
        return $posts;
    }
    
    public function utrinekUploadDir($param)
    {
        $param['subdir'] = '/pic/utrinek/' . $this->currentUser->user_login;
        $param['path'] = $param['basedir'] . $param['subdir'];
        $param['url'] = $param['baseurl'] . $param['subdir'];
        
        return $param;
    }
}

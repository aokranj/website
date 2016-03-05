<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Prenos podatkov
 *
 * Prenese podatke iz stare baze v wordpress
 */
class AOKranj_Prenos_Podatkov
{
    private $wpdb;
    private $aodb;

    private $currentUser;
    private $currentSlug;

    private $users = array();
    private $usersById = array();
    private $usersByUserName = array();
    private $posts = array();
    private $reports = array();
    private $vzponi = array();

    public function __construct($wpdb, $aodb) {
        $this->wpdb = $wpdb;
        $this->aodb = $aodb;
    }

    public function start() {
        // check permissions
        if (!current_user_can('activate_plugins')) {
            return array(
                'success' => false,
                'msg' => 'Nimaš pravic za to akcijo!',
            );
        }

        // used by wordpress functions to skip some checks
        define('WP_IMPORTING', true);

        // set max execution time to 1h
        ini_set('max_execution_time', 3600);
        set_time_limit(3600);

        // just a precaution
        /*
        return array(
            'success' => false,
            'msg' => 'Pazi kaj delaš ;)',
        );
        */

        $this->prenesiUporabnike();
        $this->prenesiVzpone();
        $this->prenesiUtrinke();
        $this->prenesiReportaze();

        // build response
        return array(
            'success' => true,
            'data'    => array(
                'users' => count($this->users),
                'posts' => count($this->posts),
                'reports' => count($this->reports),
                'vzponi' => count($this->vzponi),
            ),
            'msg' => 'Prenos je uspel :)',
        );
    }

    private function prenesiUporabnike() {
        // ao users
        $users = array();
        $ao_users = $this->aodb->get_results('SELECT * FROM member');
        $total = count($ao_users);

        foreach ($ao_users as $i => $ao_user) {
            // check if user already exists
            $wp_user = get_user_by('login', $ao_user->userName);
            if ($wp_user) {
                $this->addUserToCollection($wp_user, $ao_user);
                continue;
            }

            // skip if no username or email
            if (empty($ao_user->userName) && empty($ao_user->email)) {
                print_r(['no data',$ao_user]);
                continue;
            }

            // insert wordpress user
            $wp_user_data = array(
                'user_login'    => $ao_user->userName,
                'user_pass'     => wp_generate_password(12, false),
                'user_nicename' => strtolower($ao_user->userName),
                'first_name'    => $ao_user->name,
                'last_name'     => $ao_user->surname,
                'role'          => 'author',
            );
            if (!empty($ao_user->email) && strlen(trim($ao_user->email))) {
                $wp_user_data['user_email'] = $ao_user->email;
            }

            $wp_user_id = wp_insert_user($wp_user_data);

            // error inserting user
            if (is_wp_error($wp_user_id)) {
                print_r(['unable to insert user', $ao_user, $wp_user_id]);
                continue;
            }

            // set user status
            $this->wpdb->query(sprintf(
                'UPDATE %s SET user_status = %d WHERE ID = %d',
                esc_sql($this->wpdb->users),
                AOKranj::USER_STATUS_WAITING,
                $wp_user_id
            ));

            // load wordpress user
            $wp_user = get_user_by('id', $wp_user_id);

            // unable to load user
            if (is_wp_error($wp_user)) {
                print_r(['unable to load user', $ao_user, $wp_user]);
                continue;
            }

            // add user to collection
            $this->addUserToCollection($wp_user, $ao_user);
        }
    }

    private function prenesiVzpone() {
        // fields and values for the query
        $fields = array();
        $values = array();

        // get all ascents
        $vzponi = $this->aodb->get_results('SELECT * FROM vzpon WHERE deleted IS NULL');
        foreach ($vzponi as $i => $vzpon) {
            // get wordpress user for vzpon
            if (!isset($this->usersById[$vzpon->memberId])) {
                continue;
            }
            $user = $this->usersById[$vzpon->memberId];

            // fix ascent
            unset($vzpon->vzponId, $vzpon->memberId);
            $vzpon->user_id = $user->ID;

            // add ascent reference
            $this->vzponi[] = $vzpon;

            // process fields
            $item = array();
            foreach ($vzpon as $k => $v) {
                if ($i === 0) {
                    $fields[] = $k;
                }

                switch ($k) {
                    case 'deleted':
                        $item[] = (int)$v;
                        break;
                    default:
                        $item[] = "'" . esc_sql($v) . "'";
                        break;
                }
            }
            $values[] = '(' . implode(',', $item) . ')';
        }

        // build query
        $query = sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            AOKRANJ_TABLE_VZPONI,
            implode(',', $fields),
            implode(',', $values)
        );

        // insert ascents
        $this->wpdb->query($query);
    }

    private function prenesiUtrinke() {
        // add upload folder filter
        add_filter('upload_dir', array(&$this, 'utrinekUploadDir'));

        // set root paths
        $utrinki_dir = AOKRANJ_OLD_DIR . '/pic/utrinek';
        $tmp_dir = sys_get_temp_dir();

        // fetch comments
        $all_comments = $this->aodb->get_results('SELECT * FROM utrinek_comment');
        $comments = array();
        foreach ($all_comments as $comment) {
            $comments[$comment->utrinekId][] = $comment;
        }

        // select old posts
        $query = 'SELECT * FROM utrinek WHERE deleted IS NULL AND valid_from != \'0000-00-00\'';
        $utrinki = $this->aodb->get_results($query);

        // get report category
        $utrinkiCategory = get_category_by_slug('utrinki');

        foreach ($utrinki as $utrinek) {
            // find wordpress user
            if (!isset($this->usersByUserName[$utrinek->author])) {
                continue;
            }
            $user = $this->usersByUserName[$utrinek->author];

            // set current user for utrinekUploadDir()
            $this->currentUser = $user;

            // check if post already exists
            $exists = $this->wpdb->get_var(sprintf(
                'SELECT COUNT(ID) FROM %s WHERE post_author = %d AND post_title = \'%s\' AND post_date = \'%s\'',
                $this->wpdb->posts,
                $user->ID,
                esc_sql($utrinek->destination),
                esc_sql(date('Y-m-d H:i:s', strtotime($utrinek->valid_from)))
            ));
            if ($exists) {
                continue;
            }

            // get category
            if (isset(AOKranj_Utrinek::$tipi[$utrinek->type])) {
                $categoryName = AOKranj_Utrinek::$tipi[$utrinek->type];
                $categorySlug = sanitize_title($categoryName);
                $category = get_category_by_slug($categorySlug);
                if (!$category) {
                    wp_create_category($categoryName, $utrinkiCategory->cat_ID);
                    $category = get_category_by_slug($categorySlug);
                }
            }

            // create post
            $data = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'post_author' => $user->ID,
                'post_title' => $utrinek->destination,
                'post_content' => $utrinek->content,
                'post_date' => $utrinek->valid_from,
                'post_date_gmt' => $utrinek->valid_from,
            );
            $post_id = wp_insert_post($data);
            $post = get_post($post_id);
            $this->posts[] = get_post($post_id);
            $permalink = get_permalink($post_id);

            // write old id to new id/slug
            $file = AOKRANJ_PLUGIN_DIR . '/post_old_to_new.txt';
            $line = implode(':', array($utrinek->utrinekId, $post_id, $permalink)) . "\n";
            file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

            // set post categories
            $postCategories = array($utrinkiCategory->cat_ID);
            if (isset($category)) {
                $postCategories[] = $category->cat_ID;
            }
            wp_set_post_categories($post_id, $postCategories);

            // add comments
            if (isset($comments[$utrinek->utrinekId])) {
                foreach ($comments[$utrinek->utrinekId] as $comment) {
                    if (isset($this->usersByUserName[$comment->editor]))
                    {
                        $commentUser = $this->usersByUserName[$comment->editor];

                        $data = array(
                            'comment_post_ID' => $post_id,
                            'comment_author' => $commentUser->user_nicename,
                            'comment_author_email' => $commentUser->user_email,
                            'comment_author_url' => $commentUser->user_url,
                            'comment_content' => $comment->comment,
                            'comment_type' => '',
                            'comment_parent' => 0,
                            'user_id' => $commentUser->ID,
                            'comment_author_IP' => '',
                            'comment_agent' => '',
                            'comment_date' => $comment->timestamp,
                            'comment_approved' => 1,
                        );

                        wp_insert_comment($data);
                    }
                }
            }

            if (empty($post)) {
                print_r(['NO_POST' => $utrinek]);
            }

            //set current slug for utrinekUploadDir()
            $this->currentSlug = $post->post_name;

            // main utrinek dir
            $utrinek_dir = $utrinki_dir . '/' . $utrinek->author;

            // load texts
            $text_file = $utrinek_dir . '/utrinek_' . $utrinek->utrinekId . '.txt';
            $texts = $this->getPostTexts($text_file);

            // read old images and insert attachments
            $attachments = array();
            for ($i = 1; $i < 6; $i++) {
                // filename can be utrinek_ID_1.jpg or utrinek_ID_01.jpg
                $file_name1 = 'utrinek_' . $utrinek->utrinekId . '_' . $i . '.jpg';
                $file_name2 = 'utrinek_' . $utrinek->utrinekId . '_0' . $i . '.jpg';
                $source1 = $utrinek_dir . '/' . $file_name1;
                $source2 = $utrinek_dir . '/' . $file_name2;
                if (file_exists($source1)) {
                    $file_name = $file_name1;
                    $source = $source1;
                } else if (file_exists($source2)) {
                    $file_name = $file_name2;
                    $source = $source2;
                } else {
                    continue;
                }

                // copy file to tmp folder because media_handle_sideload() moves the file
                $tmp_name = $tmp_dir . '/' . $file_name;
                if (!copy($source, $tmp_name)) {
                    print_r(['unable to create temp image', $source, $tmp_name]);
                    continue;
                }

                // upload file to wordpress
                $file = array(
                    'tmp_name' => $tmp_name,
                    'name' => basename($source),
                    'type' => 'image/jpeg',
                    'size' => filesize($source)
                );
                $post_data = array(
                    'post_title' => isset($texts[$i]) ? $texts[$i] : '',
                    'post_author' => $user->ID
                );
                $file_id = media_handle_sideload($file, $post_id, null, $post_data);
                if (is_wp_error($file_id)) {
                    print_r(['unable to add image', $source, $tmp_name]);
                    continue;
                }

                // add attachment id to collection
                $attachments[] = $file_id;
            }

            // insert gallery if we have some attachments
            if (count($attachments) > 0) {
                $gallery = '[gallery link="file" ids="' . implode(',', $attachments) . '"]';
                $content = $utrinek->content . PHP_EOL . PHP_EOL . $gallery;

                $data = array(
                    'ID' => $post_id,
                    'post_content' => $content
                );

                $post_id = wp_update_post($data);
            }
        }

        // remove utrinek upload dir filter
        remove_filter('upload_dir', array(&$this, 'utrinekUploadDir'));
    }

    private function prenesiReportaze() {
        // add resport upload dir filter
        add_filter('upload_dir', array(&$this, 'reportUploadDir'));

        // set paths
        $reports_dir = AOKRANJ_OLD_DIR . '/pic/report/gallery';
        $tmp_dir = sys_get_temp_dir();

        // get report category
        $category = get_category_by_slug('reportaze');

        // set current user for reportUploadDir()
        $user = get_user_by('login', 'aokranj');

        // select all reports
        $reports = $this->aodb->get_results('SELECT * FROM report WHERE deleted IS NULL');
        foreach ($reports as $report) {
            // check if post already exists
            $exists = $this->wpdb->get_var(sprintf(
                'SELECT COUNT(ID) FROM %s WHERE post_author = %d AND post_title = \'%s\' AND post_date = \'%s\'',
                $this->wpdb->posts,
                $user->ID,
                esc_sql($report->title),
                esc_sql(date('Y-m-d H:i:s', strtotime($report->last_change)))
            ));
            if ($exists) {
                continue;
            }

            // create post
            $data = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'post_author' => $user->ID,
                'post_title' => $report->title,
                'post_excerpt' => $report->abstract,
                'post_content' => $report->content,
                'post_date' => $report->last_change,
                'post_date_gmt' => $report->last_change,
            );
            $post_id = wp_insert_post($data);
            $post = get_post($post_id);
            $this->reports[] = $post;
            $permalink = get_permalink($post_id);

            // write old id to new id/slug
            $file = AOKRANJ_PLUGIN_DIR . '/report_old_to_new.txt';
            $line = implode(':', array($report->reportId, $post_id, $permalink)) . "\n";
            file_put_contents($file, $line, FILE_APPEND | LOCK_EX);

            //set current slug for reportUploadDir()
            $this->currentSlug = $post->post_name;

            // set post category
            wp_set_post_categories($post_id, array($category->cat_ID));

            // load texts
            $text_file = $reports_dir . '/report_' . $report->reportId . '.txt';
            $texts = $this->getPostTexts($text_file);

            // read old images and insert attachments
            $attachments = array();
            for ($i = 1; $i < 100; $i++) {
                // filename can be utrinek_ID_1.jpg or utrinek_ID_01.jpg
                $file_name1 = 'report_' . $report->reportId . '_' . $i . '.jpg';
                $file_name2 = 'report_' . $report->reportId . '_0' . $i . '.jpg';
                $source1 = $reports_dir . '/' . $file_name1;
                $source2 = $reports_dir . '/' . $file_name2;
                if (file_exists($source1)) {
                    $file_name = $file_name1;
                    $source = $source1;
                } else if (file_exists($source2)) {
                    $file_name = $file_name2;
                    $source = $source2;
                } else {
                    continue;
                }

                // copy file to tmp folder because media_handle_sideload() moves the file
                $tmp_name = $tmp_dir . '/' . $file_name;
                if (!copy($source, $tmp_name)) {
                    print_r(['unable to create temp image', $source, $tmp_name]);
                    continue;
                }

                // upload file to wordpress
                $file = array(
                    'tmp_name' => $tmp_name,
                    'name' => basename($source),
                    'type' => 'image/jpeg',
                    'size' => filesize($source)
                );
                $post_data = array(
                    'post_title' => isset($texts[$i]) ? $texts[$i] : '',
                    'post_author' => $user->ID
                );
                $file_id = media_handle_sideload($file, $post_id, null, $post_data);
                if (is_wp_error($file_id)) {
                    print_r(['unable to add image', $source, $tmp_name]);
                    continue;
                }

                // add attachment id to collection
                $attachments[] = $file_id;
            }

            // insert gallery if we have some attachments
            if (count($attachments) > 0) {
                $gallery = '[gallery link="file" ids="' . implode(',', $attachments) . '"]';
                $content = $report->content . PHP_EOL . PHP_EOL . $gallery;

                $data = array(
                    'ID' => $post_id,
                    'post_content' => $content
                );

                $post_id = wp_update_post($data);
            }
        }

        remove_filter('upload_dir', array(&$this, 'reportUploadDir'));
    }

    public function utrinekUploadDir($param) {
        $param['subdir'] = '/arhiv/utrinki/' . strtolower($this->currentUser->user_login) . '/' . $this->currentSlug;
        $param['path'] = $param['basedir'] . $param['subdir'];
        $param['url'] = $param['baseurl'] . $param['subdir'];

        return $param;
    }

    public function reportUploadDir($param) {
        $param['subdir'] = '/arhiv/reportaze/' . $this->currentSlug;
        $param['path'] = $param['basedir'] . $param['subdir'];
        $param['url'] = $param['baseurl'] . $param['subdir'];

        return $param;
    }

    private function addUserToCollection($wp_user, $ao_user) {
        $this->users[] = $wp_user;
        $this->usersById[$ao_user->memberId] = $wp_user;
        $this->usersByUserName[$ao_user->userName] = $wp_user;
    }

    private function getPostTexts($text_file) {
        $texts = array();

        if (is_file($text_file)) {
            $lines = file($text_file);

            if ($lines) {
                foreach ($lines as $line) {
                    list($num, $text) = explode(':', $line);
                    $num = (int)$num;
                    $text = trim($text);
                    if (strlen($text) > 0)
                    {
                        $texts[$num] = $text;
                    }
                }
            }
        }

        return $texts;
    }

}

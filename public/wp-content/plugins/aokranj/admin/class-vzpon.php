<?php

/**
 * Simple vzpon model
 */
class AOKranj_Vzpon
{

    public static $tipi = array(
        'ALP'  => 'Alpinistična smer',
        'ŠP'   => 'Športno plezalna smer',
        'SMUK' => 'Smuk',
        'PR'   => 'Pristop',
    );

    public static $vrste = array(
        'K'  => 'Kopna',
        'L'  => 'Ledna (snežna)',
        'LK' => 'Ledna kombinirana',
    );

    public static $ponovitve = array(
        'PRV' => 'Prvenstvena',
        '1P'  => 'Prva ponovitev',
        '2P'  => 'Druga ponovitev',
        'ZP'  => 'Zimska ponovitev',
    );

    public static $nacini = array(
        'PP' => 'Prosta ponovitev',
        'NP' => 'Na pogled',
        'RP' => 'Z rdečo piko',
    );

    public static $stili = array(
        'A'  => 'Alpski',
        'K'  => 'Kombinirani',
        'OS' => 'Odpravarski',
    );

    public static $mesta = array(
        'V' => 'Vodstvo',
        'D' => 'Drugi',
        'Ž' => 'Žimarjenje',
        'I' => 'Izmenjaje',
    );

    /**
     * Vzpon fields
     *
     * @var array
     */
    public static $fields = array(
        'tip',
        'destinacija',
        'smer',
        'datum',
        'ocena',
        'cas',
        'vrsta',
        'visina_smer',
        'visina_izstop',
        'pon_vrsta',
        'pon_nacin',
        'stil',
        'mesto',
        'partner',
        'opomba',
        'slap',
    );

    /**
     * Vzpon data
     *
     * @var array
     */
    private $data = array();

    /**
     * Vzpon loaded or not?
     *
     * @var boolean
     */
    private $loaded = false;

    /**
     * Constructor
     *
     * @param array $data Initial data
     */
    public function __construct(array $data = null) {
        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * Get vzpon data
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Set vzpon data
     *
     * @param array $data
     */
    public function setData(array $data) {
        foreach ($data as $k => $v) {
            if (in_array($k, self::$fields)) {
                $this->data[$k] = strip_tags($v);
            }
        }
    }

    /**
     * Load vzpon by id
     * @param  integer $id
     * @return boolean
     */
    public function load($id, $deleted = true) {
        global $wpdb;

        $id = (int)$id;
        $user_id = (int)get_current_user_id();

        if ($deleted) {
            $deleted = 'AND (deleted = 0 OR deleted IS NULL)';
        }

        $query = sprintf('
            SELECT * FROM %s WHERE id = %d AND user_id = %d %s',
            AOKRANJ_TABLE_VZPONI,
            $id,
            $user_id,
            $deleted
        );

        $data = $wpdb->get_row($query, ARRAY_A);

        if (!$data) {
            return false;
        }

        $this->data = $data;
        $this->loaded = true;

        return true;
    }

    /**
     * Validate vzpon
     *
     * @return array Array of errors
     */
    public function validate() {
        $errors = [];

        // check required fields
        if (empty($this->data['tip']))
            $errors['tip'] = __('Tip vzpona je obvezen!');
        if (empty($this->data['datum']))
            $errors['datum'] = __('Datum je obvezen!');
        if (empty($this->data['destinacija']))
            $errors['destinacija'] = __('Destinacija je obvezna!');
        if (empty($this->data['smer']))
            $errors['smer'] = __('Smer je obvezna!');

        // check maxlengths
        if (strlen($this->data['destinacija']) > 50)
            $errors['destinacija'] = __('Maksimalna dolžina je 50 znakov.');
        if (strlen($this->data['smer']) > 50)
            $errors['smer'] = __('Maksimalna dolžina je 50 znakov.');
        if (!empty($this->data['ocena']) && strlen($this->data['ocena']) > 30)
            $errors['ocena'] = __('Maksimalna dolžina je 30 znakov.');
        if (!empty($this->data['cas']) && strlen($this->data['cas']) > 30)
            $errors['cas'] = __('Maksimalna dolžina je 30 znakov.');
        if (!empty($this->data['visina_smer']) && strlen($this->data['visina_smer']) > 15)
            $errors['visina_smer'] = __('Maksimalna dolžina je 15 znakov.');
        if (!empty($this->data['visina_izstop']) && strlen($this->data['visina_izstop']) > 15)
            $errors['visina_izstop'] = __('Maksimalna dolžina je 15 znakov.');
        if (!empty($this->data['opomba']) && strlen($this->data['opomba']) > 500)
            $errors['opomba'] = __('Maksimalna dolžina je 500 znakov.');

        // check enums
        if (!isset($this->data['tip'], self::$tipi))
            $errors['tip'] = __('Napačen tip vzpona!');
        if (!empty($this->data['vrsta']) && !isset($this->data['vrsta'], self::$vrste))
            $errors['vrsta'] = __('Napačna vrsta vzpona!');
        if (!empty($this->data['pon_vrsta']) && !isset($this->data['pon_vrsta'], self::$ponovitve))
            $errors['pon_vrsta'] = __('Napačna ponovitev vzpona!');
        if (!empty($this->data['pon_nacin']) && !isset($this->data['pon_nacin'], self::$nacini))
            $errors['pon_nacin'] = __('Napačen način vzpona!');
        if (!empty($this->data['stil']) && !isset($this->data['stil'], self::$stili))
            $errors['stil'] = __('Napačen stil vzpona!');
        if (!empty($this->data['mesto']) && !isset($this->data['mesto'], self::$mesta))
            $errors['mesto'] = __('Napačno mesto vzpona!');

        return $errors;
    }

    /**
     * Create vzpon
     *
     * @throws Exception
     * @return integer Newly inserted vzpon id
     */
    public function create() {
        global $wpdb;

        $data = $this->data;
        $data['user_id'] = (int)get_current_user_id();

        $success = $wpdb->insert(AOKRANJ_TABLE_VZPONI, $data);

        if ($success === false) {
            throw new Exception($wpdb->last_error);
        }

        $this->data['id'] = $wpdb->insert_id;
        $this->loaded = true;

        return $wpdb->insert_id;
    }

    /**
     * Update vzpon
     *
     * @throws Exception
     * @return boolean
     */
    public function update() {
        if (!$this->loaded) {
            throw new Exception(__('Vzpon mora biti naložen pri urejanju.'));
        }

        $id = (int)$this->data['id'];
        $user_id = (int)get_current_user_id();

        global $wpdb;

        // https://codex.wordpress.org/Class_Reference/wpdb#UPDATE_rows
        $data = $this->data;
        $format = array(
            '%s', // tip
            '%s', // destinacija
            '%s', // smer
            '%s', // datum
            '%s', // ocena
            '%s', // cas
            '%s', // vrsta
            '%s', // visina_smer
            '%s', // visina_izstop
            '%s', // pon_vrsta
            '%s', // pon_nacin
            '%s', // stil
            '%s', // mesto
            '%s', // partner
        );
        $where = array('id' => $id, 'user_id' => $user_id);
        $where_format = array('%d', '%d');

        $user_id = (int)get_current_user_id();

        $success = $wpdb->update(AOKRANJ_TABLE_VZPONI, $data, $where, $format, $where_format);

        if ($success === false) {
            throw new Exception($wpdb->last_error);
        }

        return true;
    }

    /**
     * Soft delete vzpon
     *
     * Only sets deleted=1
     *
     * @static
     * @param  integer $id
     * @throws Exception
     * @return boolean
     */
    public static function softDelete($id) {
        global $wpdb;

        $id = (int)$id;
        $user_id = (int)get_current_user_id();

        // https://codex.wordpress.org/Class_Reference/wpdb#UPDATE_rows
        $data = array('deleted' => '1');
        $format = array('%d');
        $where = array('id' => $id, 'user_id' => $user_id);
        $where_format = array('%d', '%d');

        $success = $wpdb->update(AOKRANJ_TABLE_VZPONI, $data, $where, $format, $where_format);

        if ($success === false) {
            throw new Exception($wpdb->last_error);
        }

        return true;
    }

    /**
     * Hard delete vzpon
     *
     * Delete from database
     *
     * @param  integer $id
     * @throws Exception
     * @return boolean
     */
    public static function delete($id) {
        global $wpdb;

        $id = (int)$id;
        $user_id = (int)get_current_user_id();

        // https://codex.wordpress.org/Class_Reference/wpdb#DELETE_Rows
        $where = array('id' => $id, 'user_id' => $user_id);
        $where_format = array('%d', '%d');

        $success = $wpdb->delete(AOKRANJ_TABLE_VZPONI, $where, $where_format);

        if ($success === false) {
            throw new Exception($wpdb->last_error);
        }

        return true;
    }

    /**
     * Get vzpon data from $_POST
     *
     * @static
     * @return array
     */
    public static function getPostData() {
        return array(
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
            'slap' => filter_input(INPUT_POST, 'slap'),
        );
    }

}

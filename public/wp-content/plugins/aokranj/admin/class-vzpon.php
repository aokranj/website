<?php

class AOKranj_Vzpon
{

    public static $tipi = array('ALP', 'ŠP', 'SMUK', 'PR');
    public static $vrste = array('K', 'L', 'LK');
    public static $ponovitve = array('Prv', '1P', '2P', 'ZP');
    public static $nacini = array('PP', 'NP', 'RP');
    public static $stili = array('A', 'K', 'OS');
    public static $mesta = array('V', 'D', 'Ž', 'I');

    public static function create($data) {
        global $wpdb;

        $success = $wpdb->insert(AOKRANJ_TABLE_VZPONI, $data);

        if (false === $success) {
            return false;
        }

        return true;
    }

    public static function update($id, $data) {

    }

    public static function delete($id) {

    }

    public static function validate($data) {
        $errors = [];

        $tipi = array('ALP', 'ŠP', 'SMUK', 'PR');
        $vrste = array('K', 'L', 'LK');
        $ponovitve = array('Prv', '1P', '2P', 'ZP');
        $nacini = array('PP', 'NP', 'RP');
        $stili = array('A', 'K', 'OS');
        $mesta = array('V', 'D', 'Ž', 'I');

        // check required fields
        if (empty($data['tip']))
            $errors['tip'] = __('Tip vzpona je obvezen!');
        if (empty($data['datum']))
            $errors['datum'] = __('Datum je obvezen!');
        if (empty($data['destinacija']))
            $errors['destinacija'] = __('Destinacija je obvezna!');
        if (empty($data['smer']))
            $errors['smer'] = __('Smer je obvezna!');

        // check maxlengths
        if (strlen($data['destinacija']) > 50)
            $errors['destinacija'] = __('Maksimalna dolžina je 50 znakov.');
        if (strlen($data['smer']) > 50)
            $errors['smer'] = __('Maksimalna dolžina je 50 znakov.');
        if (!empty($data['ocena']) && strlen($data['ocena']) > 30)
            $errors['ocena'] = __('Maksimalna dolžina je 30 znakov.');
        if (!empty($data['cas']) && strlen($data['cas']) > 30)
            $errors['cas'] = __('Maksimalna dolžina je 30 znakov.');
        if (!empty($data['visina_smer']) && strlen($data['visina_smer']) > 15)
            $errors['visina_smer'] = __('Maksimalna dolžina je 15 znakov.');
        if (!empty($data['visina_izstop']) && strlen($data['visina_izstop']) > 15)
            $errors['visina_izstop'] = __('Maksimalna dolžina je 15 znakov.');
        if (!empty($data['opomba']) && strlen($data['opomba']) > 500)
            $errors['opomba'] = __('Maksimalna dolžina je 500 znakov.');

        // check enums
        if (!in_array($data['tip'], $tipi))
            $errors['tip'] = __('Napačen tip vzpona!');
        if (!empty($data['vrsta']) && !in_array($data['vrsta'], $vrste))
            $errors['vrsta'] = __('Napačna vrsta vzpona!');
        if (!empty($data['pon_vrsta']) && !in_array($data['pon_vrsta'], $ponovitve))
            $errors['pon_vrsta'] = __('Napačna ponovitev vzpona!');
        if (!empty($data['pon_nacin']) && !in_array($data['pon_nacin'], $nacini))
            $errors['pon_nacin'] = __('Napačen način vzpona!');
        if (!empty($data['stil']) && !in_array($data['stil'], $stili))
            $errors['stil'] = __('Napačen stil vzpona!');
        if (!empty($data['mesto']) && !in_array($data['mesto'], $mesta))
            $errors['mesto'] = __('Napačno mesto vzpona!');

        return $errors;
    }

}

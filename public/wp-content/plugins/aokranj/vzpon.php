<?php
/**
 * Seznam vzponov
 *
 * @package AO Kranj Plugin
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wp_enqueue_script('wp-ajax-response');

wp_enqueue_script('moment');
wp_enqueue_style('pikaday');
wp_enqueue_script('pikaday');

$vzpon = isset($_SESSION['vzpon']) && is_array($_SESSION['vzpon']) ? $_SESSION['vzpon'] : array();
$errors = isset($_SESSION['errors']) && is_array($_SESSION['errors']) ? $_SESSION['errors'] : array();
unset($_SESSION['vzpon'], $_SESSION['errors']);

$title = __('Dodaj vzpon');

if (isset($_GET['id'])) {
    global $wpdb;
    $id = (int)$_GET['id'];
    $user_id = (int)get_current_user_id();
    $dbvzpon = $wpdb->get_row('SELECT * FROM ' . AOKRANJ_TABLE_VZPONI . ' WHERE id = ' . $id . ' AND user_id = ' . $user_id, ARRAY_A);
    if ($dbvzpon) {
        $vzpon = array_merge($dbvzpon, $vzpon);
        $title = __('Uredi vzpon');
    }
}

?>

<div class="wrap">

    <h1><?= $title; ?></h1>

    <form id="vzpon" class="validate" novalidate="novalidate" action="<?= admin_url('admin-post.php') ?>" method="post">

        <!-- tip -->
        <table class="form-table tip">
            <tr class="form-field">
                <th scope="row">
                    <label for="tip"><?= __('Tip vzpona') ?></label>
                </th>
                <td>
                    <select name="tip" id="tip">
                        <option value="ALP"<?= isset($vzpon['tip']) && $vzpon['tip'] === 'ALP' ? ' selected' : '' ?>>
                            <?= __('Alpinistična smer') ?>
                        </option>
                        <option value="ŠP"<?= isset($vzpon['tip']) && $vzpon['tip'] === 'ŠP' ? ' selected' : '' ?>>
                            <?= __('Športno plezalna smer') ?>
                        </option>
                        <option value="SMUK"<?= isset($vzpon['tip']) && $vzpon['tip'] === 'SMUK' ? ' selected' : '' ?>>
                            <?= __('Smuk') ?>
                        </option>
                        <option value="PR"<?= isset($vzpon['tip']) && $vzpon['tip'] === 'PR' ? ' selected' : '' ?>>
                            <?= __('Pristop') ?>
                        </option>
                        <?php /*
                        <?php foreach(AOKranjAdmin::$tipi as $value => $label): ?>
                            <?php $selected = isset($vzpon['tip']) && $vzpon['tip'] === $value ? ' selected' : false; ?>
                            <option value="<?= $value ?>"<?= $selected ?>><?= __($label); ?></option>
                        <?php endforeach; ?>
                        */ ?>
                    </select>
                    <?php $error = isset($errors['tip']) ? $errors['tip'] : false; ?>
                    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
                </td>
            </tr>
        </table>

        <table class="form-table left">

            <!-- datum -->
            <tr class="form-field form-datepicker form-required">
                <th scope="row"><label for="datum"><?= __('Datum') ?></label></th>
                <td>
                    <input type="text" name="datum" readonly class="datepicker" id="datum" value="<?= isset($vzpon['datum']) ? $vzpon['datum'] : '' ?>" />
                    <?php if (isset($errors['datum'])): ?>
                        <p class="error"><?= $errors['datum'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- destinacija -->
            <tr class="form-field form-required<?= isset($errors['destinacija']) ? ' form-invalid' : '' ?>">
                <th scope="row"><label for="destinacija"><?= __('Destinacija') ?></label></th>
                <td>
                    <input type="text" name="destinacija" id="destinacija" value="<?= isset($vzpon['destinacija']) ? $vzpon['destinacija'] : '' ?>" />
                    <?php if (isset($errors['destinacija'])): ?>
                        <p class="error"><?= $errors['destinacija'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- smer -->
            <tr class="form-field form-required<?= isset($errors['smer']) ? ' form-invalid' : '' ?>">
                <th scope="row"><label for="sner"><?= __('Smer') ?></label></th>
                <td>
                    <input type="text" name="smer" id="smer" value="<?= isset($vzpon['smer']) ? $vzpon['smer'] : '' ?>" />
                    <?php if (isset($errors['smer'])): ?>
                        <p class="error"><?= $errors['smer'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- ocena -->
            <tr class="form-field">
                <th scope="row"><label for="ocena"><?= __('Ocena') ?></label></th>
                <td>
                    <input type="text" name="ocena" id="ocena" value="<?= isset($vzpon['ocena']) ? $vzpon['ocena'] : '' ?>" />
                    <?php if (isset($errors['ocena'])): ?>
                        <p class="error"><?= $errors['ocena'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- partner -->
            <tr class="form-field">
                <th scope="row"><label for="partner"><?= __('Soplezalec') ?></label></th>
                <td>
                    <input type="text" name="partner" id="partner" value="<?= isset($vzpon['partner']) ? $vzpon['partner'] : '' ?>" />
                    <?php if (isset($errors['partner'])): ?>
                        <p class="error"><?= $errors['partner'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- cas -->
            <tr class="form-field">
                <th scope="row"><label for="cas"><?= __('Čas') ?></label></th>
                <td>
                    <input type="text" name="cas" id="cas" value="<?= isset($vzpon['cas']) ? $vzpon['cas'] : '' ?>" />
                    <?php if (isset($errors['cas'])): ?>
                        <p class="error"><?= $errors['cas'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- visina_smer -->
            <tr class="form-field">
                <th scope="row"><label for="visina_smer"><?= __('Višina smeri') ?></label></th>
                <td>
                    <input type="text" name="visina_smer" id="visina_smer" value="<?= isset($vzpon['visina_smer']) ? $vzpon['visina_smer'] : '' ?>" />
                    <?php if (isset($errors['visina_smer'])): ?>
                        <p class="error"><?= $errors['visina_smer'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- visina_izstop -->
            <tr class="form-field">
                <th scope="row"><label for="visina_izstop"><?= __('Nadmorska višina izstopa') ?></label></th>
                <td>
                    <input type="text" name="visina_izstop" id="visina_izstop" value="<?= isset($vzpon['visina_izstop']) ? $vzpon['visina_izstop'] : '' ?>" />
                    <?php if (isset($errors['visina_izstop'])): ?>
                        <p class="error"><?= $errors['visina_izstop'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

        </table>

        <table class="form-table right">

            <!-- vrsta -->
            <tr class="form-field">
                <th scope="row"><label for="vrsta"><?= __('Vrsta vzpona') ?></label></th>
                <td>
                    <select name="vrsta" id="vrsta">
                    	<option value=""<?= isset($vzpon['vrsta']) && $vzpon['vrsta'] === '' ? ' selected' : '' ?>>
                            <?= __('-- Izberite vrsto vzpona --') ?>
                        </option>
                    	<option value="K"<?= isset($vzpon['vrsta']) && $vzpon['vrsta'] === 'K' ? ' selected' : '' ?>>
                            <?= __('Kopna') ?>
                        </option>
                    	<option value="L"<?= isset($vzpon['vrsta']) && $vzpon['vrsta'] === 'L' ? ' selected' : '' ?>>
                            <?= __('Ledna (snežna)') ?>
                        </option>
                    	<option value="LK"<?= isset($vzpon['vrsta']) && $vzpon['vrsta'] === 'LK' ? ' selected' : '' ?>>
                            <?= __('Ledna kombinirana') ?>
                        </option>
                    </select>
                    <?php if (isset($errors['vrsta'])): ?>
                        <p class="error"><?= $errors['vrsta'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- pon_vrsta -->
            <tr class="form-field">
                <th scope="row"><label for="pon_vrsta"><?= __('Vrsta ponovitve') ?></label></th>
                <td>
                    <select name="pon_vrsta" id="pon_vrsta">
                    	<option value=""<?= isset($vzpon['pon_vrsta']) && $vzpon['pon_vrsta'] === '' ? ' selected' : '' ?>>
                            <?= __('-- Ni ponovitev --') ?>
                        </option>
                    	<option value="Prv"<?= isset($vzpon['pon_vrsta']) && $vzpon['pon_vrsta'] === 'Prv' ? ' selected' : '' ?>>
                            <?= __('Prvenstvena') ?>
                        </option>
                    	<option value="1P"<?= isset($vzpon['pon_vrsta']) && $vzpon['pon_vrsta'] === '1P' ? ' selected' : '' ?>>
                            <?= __('Prva ponovitev') ?>
                        </option>
                    	<option value="2P"<?= isset($vzpon['pon_vrsta']) && $vzpon['pon_vrsta'] === '2P' ? ' selected' : '' ?>>
                            <?= __('Druga ponovitev') ?>
                        </option>
                    	<option value="ZP"<?= isset($vzpon['pon_vrsta']) && $vzpon['pon_vrsta'] === 'ZP' ? ' selected' : '' ?>>
                            <?= __('Zimska ponovitev') ?>
                        </option>
                    </select>
                    <?php if (isset($errors['pon_vrsta'])): ?>
                        <p class="error"><?= $errors['pon_vrsta'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- pon_nacin -->
            <tr class="form-field">
                <th scope="row"><label for="pon_nacin"><?= __('Način ponovitve') ?></label></th>
                <td>
                    <select name="pon_nacin" id="pon_nacin">
                    	<option value=""<?= isset($vzpon['pon_nacin']) && $vzpon['pon_nacin'] === '' ? ' selected' : '' ?>>
                            <?= __('-- Ni ponovitev --') ?>
                        </option>
                    	<option value="PP"<?= isset($vzpon['pon_nacin']) && $vzpon['pon_nacin'] === 'PP' ? ' selected' : '' ?>>
                            <?= __('Prosta ponovitev') ?>
                        </option>
                    	<option value="NP"<?= isset($vzpon['pon_nacin']) && $vzpon['pon_nacin'] === 'NP' ? ' selected' : '' ?>>
                            <?= __('Na pogled') ?>
                        </option>
                    	<option value="RP"<?= isset($vzpon['pon_nacin']) && $vzpon['pon_nacin'] === 'RP' ? ' selected' : '' ?>>
                            <?= __('Z rdečo piko') ?>
                        </option>
                    </select>
                    <?php if (isset($errors['pon_nacin'])): ?>
                        <p class="error"><?= $errors['pon_nacin'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- stil -->
            <tr class="form-field">
                <th scope="row"><label for="stil"><?= __('Stil') ?></label></th>
                <td>
                    <select name="stil" id="stil">
                    	<option value=""<?= isset($vzpon['stil']) && $vzpon['stil'] === '' ? ' selected' : '' ?>>
                            <?= __('-- Izberite stil vzpona --') ?>
                        </option>
                    	<option value="A"<?= isset($vzpon['stil']) && $vzpon['stil'] === 'A' ? ' selected' : '' ?>>
                            <?= __('Alpski') ?>
                        </option>
                    	<option value="K"<?= isset($vzpon['stil']) && $vzpon['stil'] === 'K' ? ' selected' : '' ?>>
                            <?= __('Kombinirani') ?>
                        </option>
                    	<option value="OS"<?= isset($vzpon['stil']) && $vzpon['stil'] === 'OS' ? ' selected' : '' ?>>
                            <?= __('Odpravarski') ?>
                        </option>
                    </select>
                    <?php if (isset($errors['stil'])): ?>
                        <p class="error"><?= $errors['stil'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- mesto -->
            <tr class="form-field">
                <th scope="row"><label for="mesto"><?= __('Mesto') ?></label></th>
                <td>
                    <select name="mesto" id="mesto">
                    	<option value=""<?= isset($vzpon['mesto']) && $vzpon['mesto'] === '' ? ' selected' : '' ?>>
                            <?= __('-- Izberite vaše mesto pri vzponu --') ?>
                        </option>
                    	<option value="V"<?= isset($vzpon['mesto']) && $vzpon['mesto'] === 'V' ? ' selected' : '' ?>>
                            <?= __('Vodstvo') ?>
                        </option>
                    	<option value="D"<?= isset($vzpon['mesto']) && $vzpon['mesto'] === 'D' ? ' selected' : '' ?>>
                            <?= __('Drugi') ?>
                        </option>
                    	<option value="Ž"<?= isset($vzpon['mesto']) && $vzpon['mesto'] === 'Ž' ? ' selected' : '' ?>>
                            <?= __('Žimarjenje') ?>
                        </option>
                    	<option value="I"<?= isset($vzpon['mesto']) && $vzpon['mesto'] === 'I' ? ' selected' : '' ?>>
                            <?= __('Izmenjaje') ?>
                        </option>
                    </select>
                    <?php if (isset($errors['mesto'])): ?>
                        <p class="error"><?= $errors['mesto'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- opombe -->
            <tr class="form-field">
                <th scope="row"><label for="opomba"><?= __('Opombe') ?></label></th>
                <td>
                    <textarea name="opomba" id="opomba" class="all-options">
                        <?= isset($vzpon['opomba']) ? $vzpon['opomba'] : '' ?>
                    </textarea>
                    <br />
                    <label for="slap">
                        <input name="slap" type="checkbox" id="slap" value="1" />
                        Zaledeneli slap
                    </label>
                </td>
            </tr>

        </table>

        <p class="submit">
            <?php wp_nonce_field('dodaj_vzpon') ?>
            <input type="hidden" name="action" value="dodaj_vzpon" />
            <input type="hidden" name="data" value="vzpon" />
            <input class="button button-primary" type="submit" value="<?= $title ?>" />
        </p>

    </form>

</div>

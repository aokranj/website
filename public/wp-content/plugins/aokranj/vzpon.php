<?php
/**
 * Seznam vzponov
 *
 * @package AO Kranj Plugin
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// get id from $_GET
$id = (int)filter_input(INPUT_GET, 'id');

// stupid wordpress ...
$action = filter_input(INPUT_GET, 'action');
if ($action === 'delete') {
    require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzpon.php';
    AOKranj_Vzpon::softDelete($id);
    wp_redirect(admin_url('/admin.php?page=aokranj-vzponi'));
    die;
}

// load scripts and styles
wp_enqueue_script('wp-ajax-response');
wp_enqueue_script('moment');
wp_enqueue_style('pikaday');
wp_enqueue_script('pikaday');

// load vzpon and error from session
$vzpon = isset($_SESSION['vzpon']) && is_array($_SESSION['vzpon']) ? $_SESSION['vzpon'] : array();
$errors = isset($_SESSION['errors']) && is_array($_SESSION['errors']) ? $_SESSION['errors'] : array();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;
$message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
unset($_SESSION['vzpon'], $_SESSION['errors'], $_SESSION['error'], $_SESSION['message']);

// defaults
$title = __('Dodaj vzpon');
$action = 'dodaj_vzpon';
$disable = array();

// edit?
if ($id) {
    require_once AOKRANJ_PLUGIN_DIR . '/admin/class-vzpon.php';
    $Vzpon = new AOKranj_Vzpon();
    if (!$Vzpon->load($id)) {
        die('<script type="text/javascript">window.location = \'' . admin_url('/admin.php?page=aokranj-vzpon') . '\';</script>');
    }
    $title = __('Uredi vzpon');
    $action = 'uredi_vzpon';
    $vzpon = array_merge($Vzpon->getData(), $vzpon);
}

if (isset($vzpon['tip'])) {
    switch ($vzpon['tip']) {
        case 'ŠP':
            $disable = array('partner','cas','visina_izstop','vrsta','stil','mesto');
            break;
        case 'SMUK':
        case 'PR':
            $disable = array('cas','visina_izstop','vrsta','pon_vrsta','pon_nacin','stil','mesto');
            break;
    }
}

?>

<div class="wrap">

    <h1><?= $title; ?></h1>

    <?php if ($message): ?>
        <div class="updated settings-error notice is-dismissible">
            <p><strong><?= $message ?></strong></p>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="settings-error notice is-dismissible">
            <p><strong><?= $error ?></strong></p>
        </div>
    <?php endif; ?>

    <form id="vzpon" class="validate" novalidate="novalidate" action="<?= admin_url('/admin-post.php') ?>" method="post">

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

        <!-- left -->
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
                    <input type="text" name="destinacija" id="destinacija" value="<?= isset($vzpon['destinacija']) ? $vzpon['destinacija'] : '' ?>" maxlength="50" />
                    <?php if (isset($errors['destinacija'])): ?>
                        <p class="error"><?= $errors['destinacija'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- smer -->
            <tr class="form-field form-required<?= isset($errors['smer']) ? ' form-invalid' : '' ?>">
                <th scope="row"><label for="sner"><?= __('Smer') ?></label></th>
                <td>
                    <input type="text" name="smer" id="smer" value="<?= isset($vzpon['smer']) ? $vzpon['smer'] : '' ?>" maxlength="50" />
                    <?php if (isset($errors['smer'])): ?>
                        <p class="error"><?= $errors['smer'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- ocena -->
            <tr class="form-field">
                <th scope="row"><label for="ocena"><?= __('Ocena') ?></label></th>
                <td>
                    <input type="text" name="ocena" id="ocena" value="<?= isset($vzpon['ocena']) ? $vzpon['ocena'] : '' ?>" maxlength="30" />
                    <?php if (isset($errors['ocena'])): ?>
                        <p class="error"><?= $errors['ocena'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- partner -->
            <tr class="form-field<?= in_array('partner', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="partner"><?= __('Soplezalec') ?></label></th>
                <td>
                    <input type="text" name="partner" id="partner" value="<?= isset($vzpon['partner']) ? $vzpon['partner'] : '' ?>" maxlength="50"<?= in_array('partner', $disable) ? ' disabled' : '' ?> />
                    <?php if (isset($errors['partner'])): ?>
                        <p class="error"><?= $errors['partner'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- cas -->
            <tr class="form-field<?= in_array('cas', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="cas"><?= __('Čas') ?></label></th>
                <td>
                    <input type="text" name="cas" id="cas" value="<?= isset($vzpon['cas']) ? $vzpon['cas'] : '' ?>" maxlength="30"<?= in_array('cas', $disable) ? ' disabled' : '' ?> />
                    <?php if (isset($errors['cas'])): ?>
                        <p class="error"><?= $errors['cas'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- visina_smer -->
            <tr class="form-field<?= in_array('visina_smer', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="visina_smer"><?= __('Višina smeri') ?></label></th>
                <td>
                    <input type="text" name="visina_smer" id="visina_smer" value="<?= isset($vzpon['visina_smer']) ? $vzpon['visina_smer'] : '' ?>" maxlength="15"<?= in_array('visina_smer', $disable) ? ' disabled' : '' ?> />
                    <?php if (isset($errors['visina_smer'])): ?>
                        <p class="error"><?= $errors['visina_smer'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

            <!-- visina_izstop -->
            <tr class="form-field<?= in_array('visina_izstop', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="visina_izstop"><?= __('Nadmorska višina izstopa') ?></label></th>
                <td>
                    <input type="text" name="visina_izstop" id="visina_izstop" value="<?= isset($vzpon['visina_izstop']) ? $vzpon['visina_izstop'] : '' ?>" maxlength="15"<?= in_array('visina_izstop', $disable) ? ' disabled' : '' ?> />
                    <?php if (isset($errors['visina_izstop'])): ?>
                        <p class="error"><?= $errors['visina_izstop'] ?></p>
                    <?php endif; ?>
                </td>
            </tr>

        </table>

        <!-- right -->
        <table class="form-table right">

            <!-- vrsta -->
            <tr class="form-field<?= in_array('vrsta', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="vrsta"><?= __('Vrsta vzpona') ?></label></th>
                <td>
                    <select name="vrsta" id="vrsta"<?= in_array('vrsta', $disable) ? ' disabled' : '' ?>>
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
            <tr class="form-field<?= in_array('pon_vrsta', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="pon_vrsta"><?= __('Vrsta ponovitve') ?></label></th>
                <td>
                    <select name="pon_vrsta" id="pon_vrsta"<?= in_array('pon_vrsta', $disable) ? ' disabled' : '' ?>>
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
            <tr class="form-field<?= in_array('pon_nacin', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="pon_nacin"><?= __('Način ponovitve') ?></label></th>
                <td>
                    <select name="pon_nacin" id="pon_nacin"<?= in_array('pon_nacin', $disable) ? ' disabled' : '' ?>>
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
            <tr class="form-field<?= in_array('stil', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="stil"><?= __('Stil') ?></label></th>
                <td>
                    <select name="stil" id="stil"<?= in_array('stil', $disable) ? ' disabled' : '' ?>>
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
            <tr class="form-field<?= in_array('mesto', $disable) ? ' hidden' : '' ?>">
                <th scope="row"><label for="mesto"><?= __('Mesto') ?></label></th>
                <td>
                    <select name="mesto" id="mesto"<?= in_array('mesto', $disable) ? ' disabled' : '' ?>>
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
                    <textarea name="opomba" id="opomba" class="all-options"><?= isset($vzpon['opomba']) ? $vzpon['opomba'] : '' ?></textarea>
                    <br />
                    <label for="slap">
                        <input name="slap" type="checkbox" id="slap" value="1"<?= isset($vzpon['slap']) && $vzpon['slap'] ? ' checked' : '' ?> />
                        Zaledeneli slap
                    </label>
                </td>
            </tr>

        </table>

        <!-- button -->
        <p class="submit">
            <input class="button button-primary" type="submit" value="<?= $title ?>" />
        </p>

        <!-- id? -->
        <?php if (isset($Vzpon)): ?>
            <input type="hidden" name="id" value="<?= $vzpon['id'] ?>" />
        <?php endif; ?>

        <!-- action -->
        <input type="hidden" name="action" value="<?= $action ?>" />

        <!-- data -->
        <input type="hidden" name="data" value="vzpon" />

        <!-- nonce -->
        <?php wp_nonce_field($action) ?>

    </form>

</div>

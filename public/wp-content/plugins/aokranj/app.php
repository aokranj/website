<div id="aokranj"></div>
<?php
global $user_level;
$user = wp_get_current_user();
$data = $user->data;
$data->user_level = $user_level;
unset($data->user_pass);
?>
<script type="text/javascript">
var AO = {
    nonce: '<?= wp_create_nonce('aokranj-app') ?>',
    User: <?= json_encode($data) ?>
};
</script>

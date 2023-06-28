<div style="padding: 0 10px;border: 1px solid #24282e;border-radius: 10px;background-color: #fafafa;">

    <h2><span style="padding-right:10px">Pro Features<span>
        <?php
            if (!get_option('pro_version_enabled')) {
                echo '<a class="button-primary" href="https://rpetersendev.gumroad.com/l/simple-banner" target="_blank">Purchase Pro License</a>';
            }
        ?>
    </h2>

    <table class="form-table">
        <!-- Activation Code -->
        <tr valign="top" style="<?php if (get_option('pro_version_enabled')) { echo 'display: none;'; } ?>">
            <th scope="row">
                License key
            </th>
            <td>
                <input type="text" style="border: 2px solid gold;border-radius: 5px;width:60%;" id="simple_banner_pro_license_key" name="simple_banner_pro_license_key" value="<?php echo esc_attr(get_option('simple_banner_pro_license_key')); ?>" />
            </td>
        </tr>
        <!-- Permissions -->
        <?php if ( in_array( 'administrator', (array) wp_get_current_user()->roles ) ): ?>
            <tr valign="top">
                <th scope="row">
                    Permissions
                    <div>Allow roles to edit Simple Banner.</div>
                </th>
                <td>
                    <div id="simple_banner_pro_permissions">
                        <?php
                            $roles = get_editable_roles();
                            $disabled = !get_option('pro_version_enabled');
                            $permissions_array = get_option('permissions_array');
                            foreach (get_editable_roles() as $role_name => $role_info) {
                                if ($role_name == 'administrator') {
                                    continue;
                                }
                                $allowed = current_user_can( 'manage_simple_banners' );
                                $checkbox = '<input type="checkbox"';
                                $checkbox .= $disabled ? 'disabled ' : '';
                                $checkbox .= (!$disabled && in_array($role_name, explode(",", $permissions_array))) ? 'checked ' : '';
                                $checkbox .= 'value="' . $role_name . '">';
                                $checkbox .= $role_name;
                                $checkbox .= '</input><br>';
                                echo $checkbox;
                            }
                        ?>
                        </dl>
                    </div>
                </td>
            </tr>
        <?php endif; ?>
        <?php
            if (get_option('pro_version_enabled')) {
                echo '<input type="text" hidden id="permissions_array" name="permissions_array" value="'. get_option('permissions_array') . '" />';
            }
        ?>
        <!-- Insert After Element -->
        <tr valign="top">
            <th scope="row">
                <span style="color: limegreen;">NEW</span>
                Insert Inside Element
                <div>
                    Insert the banner inside a specific element on your page.
                    (e.g. <code>header</code> for the header element or <code>#main-navigation</code> for an id attribute). Default is <code>body</code>.
                </div>
            </th>
            <td style="vertical-align:top;">
                <?php
                    if (get_option('pro_version_enabled')) {
                        echo '<input id="simple_banner_insert_inside_element" name="simple_banner_insert_inside_element" style="width:60%;" value="'. esc_attr(get_option('simple_banner_insert_inside_element')) . '" />';
                        echo '<div>
                            <strong>
                                Note: This feature uses <code>document.querySelector()</code> and will select the first element match.
                                It will also except combinations of CSS selectors. More information <a target="_blank" href="https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Selectors">here</a>.
                            </strong>
                        </div>';
                    } else {
                        echo '<input style="width:60%;" disabled />';
                    }
                ?>
            </td>
        </tr>
        <!-- Remove After Date -->
        <tr valign="top">
            <th scope="row">
                <span style="color: limegreen;">NEW</span>
                Remove After Date
                <div>
                    This can remove the banner after a given date. Enter the exact day and time (e.g. <code>21 Feb 2022 15:53:22 GMT</code>).
                </div>
            </th>
            <td style="vertical-align:top;">
                <?php
                    if (get_option('pro_version_enabled')) {
                        echo '<input id="simple_banner_remove_after_date" name="simple_banner_remove_after_date" style="width:60%;" value="'. esc_attr(get_option('simple_banner_remove_after_date')) . '" />';
                    } else {
                        echo '<input style="width:60%;" disabled />';
                    }
                ?>
            </td>
        </tr>
        <!-- Disabled on Posts -->
        <tr valign="top">
            <th scope="row">
                Disabled on Posts
                <div>
                    Disable Simple Banner on all posts.
                </div>
            </th>
            <td style="padding-top:0;">
                <?php
                    if (get_option('pro_version_enabled')) {
                        $checked = get_option('disabled_on_posts') ? 'checked ' : '';
                        echo '<input type="checkbox" id="disabled_on_posts" '. $checked . ' name="disabled_on_posts" />';
                    } else {
                        echo '<input type="checkbox" disabled />';
                    }
                ?>
            </td>
        </tr>
        <!-- Disabled Pages -->
        <tr valign="top">
            <th scope="row">
                Disabled Pages
                <div>Disable Simple Banner on the following pages.</div>
            </th>
            <td>
                <div id="simple_banner_pro_disabled_pages">
                    <?php
                        $disabled = !get_option('pro_version_enabled');
                        $disabled_pages_array = array_filter(explode(',', get_option('disabled_pages_array')));
                        $frontpage_id = get_option( 'page_on_front' ); // page_on_front returns 0 if value hasn't been set
                        if ($frontpage_id == 0) {
                            $frontpage_id = 1;
                        }
                        $parent_checkbox = '<input type="checkbox" ';
                        $parent_checkbox .= $disabled ? 'disabled ' : '';
                        $parent_checkbox .= (!$disabled && in_array($frontpage_id, $disabled_pages_array)) ? 'checked ' : '';
                        $parent_checkbox .= 'value="' . $frontpage_id . '">';
                        $parent_checkbox .= get_option( 'blogname' ) . ' | ' . get_site_url() . ' ';
                        $parent_checkbox .= '</input><br>';
                        echo $parent_checkbox;

                        $pages = get_pages(array(
                            'exclude' => array($frontpage_id) // exclude frontpage_id
                        ));
                        foreach ( $pages as $page ) {
                            $checkbox = '<input type="checkbox"';
                            $checkbox .= $disabled ? 'disabled ' : '';
                            $checkbox .= (!$disabled && in_array($page->ID, $disabled_pages_array)) ? 'checked ' : '';
                            $checkbox .= 'value="' . $page->ID . '">';
                            $checkbox .= $page->post_title . ' | ' . get_page_link( $page->ID ) . ' ';
                            $checkbox .= '</input><br>';
                            echo $checkbox;
                        }
                    ?>
                </div>
            </td>
        </tr>
        <!-- Website Custom CSS -->
        <tr valign="top">
            <th scope="row">
                Website Custom CSS
                <div>CSS will be applied to the entire website</div>
            </th>
            <td>
                <?php
                    if (get_option('pro_version_enabled')) {
                        echo '<textarea id="site_custom_css" style="height: 150px;width: 75%;" name="site_custom_css">'. esc_textarea(get_option('site_custom_css')) . '</textarea>';
                    } else {
                        echo '<textarea style="height: 150px;width: 75%;" disabled></textarea>';
                    }
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" style="padding-top:0;">
                Keep CSS if banner is hidden, disabled, or closed?
            </th>
            <td style="padding-top:0;">
                <?php
                    if (get_option('pro_version_enabled')) {
                        $checked = get_option('keep_site_custom_css') ? 'checked ' : '';
                        echo '<input type="checkbox" id="keep_site_custom_css" '. $checked . ' name="keep_site_custom_css" />';
                    } else {
                        echo '<input type="checkbox" disabled />';
                    }
                ?>
            </td>
        </tr>
        <!-- Website Custom JS -->
        <tr valign="top">
            <th scope="row">
                Website Custom JS
                <div>JavaScript will be applied to the entire website</div>
            </th>
            <td>
                <?php
                    if (get_option('pro_version_enabled')) {
                        echo '<textarea id="site_custom_js" style="height: 150px;width: 75%;" name="site_custom_js">'. esc_textarea(get_option('site_custom_js')) . '</textarea>';
                    } else {
                        echo '<textarea style="height: 150px;width: 75%;" disabled></textarea>';
                    }
                ?>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row" style="padding-top:0;">
                Keep JS if banner is hidden, disabled, or closed?
            </th>
            <td style="padding-top:0;">
                <?php
                    if (get_option('pro_version_enabled')) {
                        $checked = get_option('keep_site_custom_js') ? 'checked ' : '';
                        echo '<input type="checkbox" id="keep_site_custom_js" '. $checked . ' name="keep_site_custom_js" />';
                    } else {
                        echo '<input type="checkbox" disabled />';
                    }
                ?>
            </td>
        </tr>
        <!-- Debug Mode -->
        <tr valign="top">
            <th scope="row">
                Debug Mode
                <div>If enabled, will log all variables in the console of your browser</div>
            </th>
            <td>
                <?php
                    if (get_option('pro_version_enabled')) {
                        $checked = get_option('debug_mode') ? 'checked ' : '';
                        echo '<input type="checkbox" id="debug_mode" '. $checked . ' name="debug_mode" />';
                    } else {
                        echo '<input type="checkbox" disabled />';
                    }
                ?>
            </td>
        </tr>
    </table>
</div>

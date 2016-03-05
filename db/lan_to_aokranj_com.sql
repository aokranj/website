UPDATE wp_options SET option_value = replace(option_value, 'http://aokranj2.lan', 'http://nov.aokranj.com') WHERE option_name = 'home' OR option_name = 'siteurl';
UPDATE wp_posts SET guid = replace(guid, 'http://aokranj2.lan', 'http://nov.aokranj.com');
UPDATE wp_posts SET post_content = replace(post_content, 'http://aokranj2.lan', 'http://nov.aokranj.com');
UPDATE wp_postmeta SET meta_value = replace(meta_value, 'http://aokranj2.lan', 'http://nov.aokranj.com');

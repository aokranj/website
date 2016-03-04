SET SQL_SAFE_UPDATES = 0;
UPDATE wp_usermeta SET meta_value = 'a:1:{s:11:"author";b:1;}' WHERE meta_value = 'a:1:{s:11:"contributor";b:1;}';
SET SQL_SAFE_UPDATES = 1;

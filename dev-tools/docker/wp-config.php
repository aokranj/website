<?php



/*
 * Force URI
 */
define('WP_HOME',    'http://docker.dev.aokranj.com:8000');
define('WP_SITEURL', 'http://docker.dev.aokranj.com:8000');
define('FORCE_SSL_ADMIN', true);



/*
 * Database access
 */
define('DB_NAME',     'ao_web_dev_docker');
define('DB_USER',     'ao_web_dev_d');
define('DB_PASSWORD', 'ao_web_dev_docker');
define('DB_HOST',     'mysql');



/*
 * Authentication Unique Keys and Salts.
 *
 * May be generated here:
 * https://api.wordpress.org/secret-key/1.1/salt/
 */
define('AUTH_KEY',         'to6>-RGo8j(Do|9y0OFX,Ou|>jB/z}k[vZ1EL%pxtv6]siEtXWrb]tRzn?q6-Tv8');
define('SECURE_AUTH_KEY',  'h>JQ)x:[S+CF*Jm+cu3+_IYaVC+?BzZmD)-]t,iDRoqN-9i{ N[G#3JD]C |_9W/');
define('LOGGED_IN_KEY',    'Blzk1RnfqZrw}v(k8|ZNL]EC|k?F?U0nJ9o s-pq/$WezVo/_/uoE*tK|gIIs@uV');
define('NONCE_KEY',        'nzCtV|dwx/8#Df<C{-D+a[|nVcwNO%RFGSBQuk#AH+|50f]=iH*B^x$x1pi^?>bC');
define('AUTH_SALT',        'IKkk5cYWH{>d$9%0DR,Y-y8}:u~-2B^MSzb]U@_Uo2c5+{x%RfDM!]h)SrWJrRsx');
define('SECURE_AUTH_SALT', 'wo:t[D.9qKuAVUDp.)pnr$Y1:b&>m+`JZ<%rq[Jdj%?-Eh]),|Cb<]pqz2&Yb,j+');
define('LOGGED_IN_SALT',   'eZhD%YthO|dqFsa(l)X<Y-A195T!qR(-Sh*GT5OO)O?>{SH[^&44k2+bh!!x1S~]');
define('NONCE_SALT',       '0<2)X%VARpG;^b_s%%##Q^GSVAN|n0n##JL$X[9j!KGqDjY/eO7,:|i@dGw$t;:N');

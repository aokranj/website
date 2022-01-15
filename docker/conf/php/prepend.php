<?php

/*
 * Change file creation umask
 *
 * PHP is running under Apache HTTPD, which is running as a `www-data` unprivileged user.
 * If default umask (0022) is used, files created by PHP (thus owned by `www-data`) have
 * 0644 permission mode (and 0755 for directories). This makes it hard to manipulate these
 * files by user who owns the PHP code files.
 *
 * Since `www-data` is the lowest ranking member of security on these systems anyway, we
 * can just make all files created by application as world-writable and thus enable the
 * manual manipulation of these files by the code owner/deployer.
 */
umask(0000);

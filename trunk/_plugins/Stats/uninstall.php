<?php

query( 'drop table if exists ' . DB_PREFIX . 'stats');

remove_dir( USERS_DIR . 'cache/BROWSCAP' );

?>

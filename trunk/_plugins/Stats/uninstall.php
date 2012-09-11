<?php

query( 'drop table if exists ' . DB_PREFIX . 'stats');

remove_dir( USERS_FILES . 'cache/BROWSCAP' );

?>

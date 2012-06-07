<?php

query( 'drop table if exists ' . PREFIX . 'stats');

remove_dir( USER_FILES . 'cache/BROWSCAP' );

?>

<?php



query('drop table if exists '.DB_PREFIX.'downloadManager');

remove_dir(USERS_DIR.'files/downloads');

?>

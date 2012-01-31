<?php



query('drop table if exists '.PREFIX.'downloadManager');

remove_dir(USER_FILES.'files/downloads');

?>

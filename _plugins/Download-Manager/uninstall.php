<?php



query('drop table if exists '.PREFIX.'downloadManager');

remove_dir(USERFILES.'files/downloads');

?>

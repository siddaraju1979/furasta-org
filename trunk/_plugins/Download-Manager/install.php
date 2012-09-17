<?php



query('drop table if exists '.DB_PREFIX.'downloadManager');
query('create table '.DB_PREFIX.'downloadManager (id int auto_increment primary key,name text,filename text,count int,page_id int)');

mkdir(USERS_DIR.'files/downloads');
?>

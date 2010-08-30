<?php



query('drop table if exists '.PREFIX.'downloadManager');
query('create table '.PREFIX.'downloadManager (id int auto_increment primary key,name text,filename text,count int,page_id int)');

mkdir(USERFILES.'files/downloads');
?>
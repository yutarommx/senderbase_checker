ウンコード


 how use
------------------

 # ./senderbase_checker.rb listfile

 # cat listfile
 192.168.0.0/16
 10.0.0.0/8


 install packeges
------------------

 # apt-get install ruby rubygems libmysql-ruby libmysqlclient-dev

 # gem install mysql2

 create database
------------------

 mysql> grant all privileges on senderbase_db.* to 'sbchkr'@'localhost' identified by 'password'; 

 mysql> create database senderbase_db;

 mysql> use senderbase_db

 mysql> create table senderbase_db.result(ipaddr int(10) unsigned not null primary key, ptr tinytext , score float(3,1) , status tinytext , checkdate datetime );

 mysql> create table senderbase_db.iplist(iprange tinytext not null, primary key(iprange(18)));


 query
------------------

 mysql> select iprange from senderbase_db.iplist;

 mysql> insert into senderbase_db.result values ( INET_ATON('ipaddr') , 'ptr' , 'score' , 'status' , 'date' );

 mysql> replace into senderbase_db.result values ( INET_ATON('ipaddr') , 'ptr' , 'score' , 'status' , 'date' );

 mysql> select inet_ntoa(ipaddr),ptr,score,status,checkdate  from senderbase_db.result;


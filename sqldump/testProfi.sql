CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'идентификатор',
  `name` varchar(150) NOT NULL COMMENT 'имя',
  `age` int(3) DEFAULT NULL COMMENT 'возраст',
  `token` varchar(24) DEFAULT NULL COMMENT 'токен',
  `login` varchar(30) NOT NULL COMMENT 'логин',
  `password` varchar(30) NOT NULL COMMENT 'пароль',
  `permission` int(1) DEFAULT '0' COMMENT '0-пользователь; 1 - администратор; 2 - суперадмин',
  `tokenExpired` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ukUserLogin` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `user` */

insert  into `user`(`id`,`name`,`age`,`token`,`login`,`password`,`permission`,`tokenExpired`) values (1,'Суперадмин',20,'','sadmin','sadmin',2,'0000-00-00 00:00:00');
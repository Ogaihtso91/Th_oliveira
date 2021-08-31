/*
SQLyog Ultimate v8.55 
MySQL - 5.5.5-10.1.19-MariaDB : Database - yolanda_flores
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`yolanda_flores` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `yolanda_flores`;

/*Table structure for table `baixa` */

DROP TABLE IF EXISTS `baixa`;

CREATE TABLE `baixa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usuarioId` int(11) unsigned NOT NULL,
  `data` datetime NOT NULL,
  `valorTotal` float NOT NULL,
  `tipo` enum('v','q') NOT NULL DEFAULT 'v',
  `comentario` text,
  `ativo` enum('s','n') DEFAULT 's',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;

/*Data for the table `baixa` */

insert  into `baixa`(`id`,`usuarioId`,`data`,`valorTotal`,`tipo`,`comentario`,`ativo`) values (1,1,'2017-02-03 00:00:00',137.5,'v','','n'),(2,1,'2017-02-03 00:00:00',55,'v','','n'),(3,1,'2017-02-03 00:00:00',82.5,'v','','n'),(4,1,'2017-02-03 00:00:00',27.5,'v','','s'),(5,1,'2017-02-03 00:00:00',27.5,'v','','s'),(6,1,'2017-02-03 00:00:00',80,'v','','s'),(7,1,'2017-02-03 00:00:00',40,'v','','s'),(8,1,'2017-02-03 00:00:00',36,'v','','s'),(9,1,'2017-02-03 00:00:00',160.02,'v','','s'),(10,1,'2017-02-06 00:00:00',262,'v','teste','s'),(11,1,'2017-02-06 00:00:00',2489.5,'v','','n'),(12,1,'2017-02-07 00:00:00',90,'v','','n'),(13,1,'2017-02-07 00:00:00',280,'v','','s'),(14,1,'2017-02-07 00:00:00',920,'v','','s'),(15,1,'2017-02-07 00:00:00',79.5,'q','','s'),(16,1,'2017-02-08 00:00:00',59.5,'q','','s'),(17,1,'2017-02-08 00:00:00',0,'q','','s'),(18,1,'2017-02-08 00:00:00',59.5,'q','','s'),(19,1,'2017-02-08 00:00:00',59.5,'v','','s'),(20,1,'2017-02-08 00:00:00',6,'q','','s'),(21,1,'2017-02-08 00:00:00',119,'q','qwe','s'),(22,1,'2017-02-08 00:00:00',59.5,'q','tttt','s'),(23,1,'2017-02-08 00:00:00',119,'q','','s'),(24,1,'2017-02-08 00:00:00',202.4,'v','','s'),(25,1,'2017-02-09 00:00:00',12,'v','','s'),(26,1,'2017-02-09 00:00:00',59.5,'v','','s'),(27,1,'2017-02-09 00:00:00',12,'v','teste','s'),(28,1,'2017-02-09 00:00:00',119,'v','teste2','s'),(29,1,'2017-02-09 00:00:00',18,'v','','s'),(30,1,'2017-02-09 00:00:00',59.5,'q','','s'),(31,1,'2017-02-09 00:00:00',120,'v','','s');

/*Table structure for table `estoque` */

DROP TABLE IF EXISTS `estoque`;

CREATE TABLE `estoque` (
  `produtoId` int(11) unsigned NOT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `quantidade` int(11) unsigned NOT NULL,
  `valor` float NOT NULL,
  `lucro` int(11) DEFAULT NULL,
  `ativo` enum('s','n') DEFAULT 's',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

/*Data for the table `estoque` */

insert  into `estoque`(`produtoId`,`id`,`data`,`quantidade`,`valor`,`lucro`,`ativo`) values (1,1,'2017-02-03 00:00:00',2,10,100,'n'),(1,2,'2017-02-03 00:00:00',3,25,10,'n'),(1,3,'2017-02-03 00:00:00',3,20,100,'n'),(1,4,'2017-02-03 00:00:00',10,10,10,'n'),(2,5,'2017-02-03 00:00:00',50,3,100,'s'),(3,6,'2017-02-03 00:00:00',60,5,100,'n'),(1,7,'2017-02-06 00:00:00',38,50,19,'s'),(3,8,'2017-02-07 00:00:00',6,10,100,'n'),(3,9,'2017-02-08 00:00:00',5,20,18,'n'),(1,10,'2017-02-08 00:00:00',4,2.22,18,'s'),(2,11,'2017-02-08 00:00:00',2,2.22,18,'s'),(3,12,'2017-02-08 00:00:00',1,22.22,15,'n'),(3,13,'2017-02-08 00:00:00',6,2.22,19,'s'),(3,14,'2017-02-08 00:00:00',10,44.44,17,'s');

/*Table structure for table `produto` */

DROP TABLE IF EXISTS `produto`;

CREATE TABLE `produto` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) NOT NULL,
  `nome` varchar(155) NOT NULL,
  `ativo` enum('s','n') DEFAULT 's',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `produto` */

insert  into `produto`(`id`,`codigo`,`nome`,`ativo`) values (1,'vaso 1','vaso 1','s'),(2,'vaso 2','vaso 2','s'),(3,'vaso 3 ','vaso 3','s');

/*Table structure for table `produtobaixa` */

DROP TABLE IF EXISTS `produtobaixa`;

CREATE TABLE `produtobaixa` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `produtoId` int(11) unsigned NOT NULL,
  `baixaId` int(11) unsigned NOT NULL,
  `valor` float NOT NULL,
  `quantidade` int(10) unsigned NOT NULL,
  `ativo` enum('s','n') DEFAULT 's',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=latin1;

/*Data for the table `produtobaixa` */

insert  into `produtobaixa`(`id`,`produtoId`,`baixaId`,`valor`,`quantidade`,`ativo`) values (1,1,1,27.5,5,'n'),(2,1,2,27.5,2,'n'),(3,1,3,27.5,3,'n'),(4,1,4,27.5,1,'s'),(5,1,5,27.5,1,'s'),(6,1,6,40,2,'s'),(7,1,7,40,1,'s'),(8,3,8,12,3,'s'),(9,2,9,60.02,1,'s'),(10,3,9,50,2,'s'),(11,1,10,27.5,8,'s'),(12,2,10,6,7,'s'),(13,1,11,59.5,41,'n'),(14,3,11,10,5,'n'),(15,3,12,30,3,'n'),(16,3,13,20,14,'s'),(17,3,14,20,46,'s'),(18,3,15,20,1,'s'),(19,1,15,59.5,1,'s'),(20,1,16,59.5,1,'s'),(21,2,17,0,1,'s'),(22,1,18,59.5,1,'s'),(23,1,19,59.5,1,'s'),(24,2,20,6,1,'s'),(25,1,21,59.5,2,'s'),(26,1,22,59.5,1,'s'),(27,1,23,59.5,2,'s'),(28,3,24,25.3,8,'s'),(29,2,25,6,2,'s'),(30,1,26,59.5,1,'s'),(31,2,27,6,2,'s'),(32,1,28,59.5,2,'s'),(33,2,29,6,3,'s'),(34,1,30,59.5,1,'s'),(35,1,31,60,2,'s');

/*Table structure for table `produtobaixaestoque` */

DROP TABLE IF EXISTS `produtobaixaestoque`;

CREATE TABLE `produtobaixaestoque` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `baixaId` int(11) DEFAULT NULL,
  `estoqueId` int(11) DEFAULT NULL,
  `valor` float DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=latin1;

/*Data for the table `produtobaixaestoque` */

insert  into `produtobaixaestoque`(`id`,`baixaId`,`estoqueId`,`valor`,`quantidade`) values (6,4,2,27.5,1),(7,5,2,27.5,1),(8,6,3,40,2),(9,7,3,40,1),(10,8,6,12,3),(11,9,5,60.02,1),(12,9,6,50,2),(13,10,4,27.5,8),(14,10,5,6,7),(16,13,6,20,14),(17,14,6,20,41),(18,14,8,20,5),(19,15,8,20,1),(20,15,4,59.5,1),(21,16,2,59.5,1),(22,17,5,0,1),(23,18,4,59.5,1),(24,19,1,59.5,1),(25,20,5,6,1),(26,21,1,59.5,1),(27,21,7,59.5,1),(28,22,7,59.5,1),(29,23,7,59.5,2),(30,24,9,25.3,5),(31,24,12,25.3,1),(32,24,13,25.3,2),(33,25,5,6,2),(34,26,7,59.5,1),(35,27,5,6,2),(36,28,7,59.5,2),(37,29,5,6,3),(38,30,7,59.5,1),(39,31,7,60,2);

/*Table structure for table `usuario` */

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `idUsuario` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `criado_em` datetime NOT NULL,
  `criado_por` int(11) unsigned DEFAULT NULL,
  `modificado_em` datetime NOT NULL,
  `modificado_por` int(11) unsigned DEFAULT NULL,
  `perfil` enum('usuario','admistrador') NOT NULL DEFAULT 'usuario',
  `nome` varchar(80) NOT NULL,
  `email` varchar(80) NOT NULL,
  `senha` varchar(120) NOT NULL,
  `tokenSenha` varchar(255) DEFAULT NULL,
  `tokenExpira` date DEFAULT NULL,
  `ativo` enum('sim','nao') NOT NULL DEFAULT 'sim',
  PRIMARY KEY (`idUsuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `usuario` */

insert  into `usuario`(`idUsuario`,`criado_em`,`criado_por`,`modificado_em`,`modificado_por`,`perfil`,`nome`,`email`,`senha`,`tokenSenha`,`tokenExpira`,`ativo`) values (1,'2016-06-22 21:56:32',NULL,'2016-06-22 21:56:32',NULL,'admistrador','Thiago','th@th.com','202cb962ac59075b964b07152d234b70','201702091335241202cb962ac59075b964b07152d234b70','2017-02-09','sim');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

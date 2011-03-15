/*
SQLyog Community v8.82 
MySQL - 5.1.41 : Database - codedump
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`codedump` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `codedump`;

/*Table structure for table `antwoorden` */

DROP TABLE IF EXISTS `antwoorden`;

CREATE TABLE `antwoorden` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `vraagid` int(5) NOT NULL,
  `gebruikersid` int(3) NOT NULL,
  `antwoord` varchar(255) NOT NULL,
  `votes` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `antwoorden` */

insert  into `antwoorden`(`id`,`vraagid`,`gebruikersid`,`antwoord`,`votes`) values (1,1,1,'Met een scanner object',2);

/*Table structure for table `gebruikers` */

DROP TABLE IF EXISTS `gebruikers`;

CREATE TABLE `gebruikers` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(50) NOT NULL,
  `tussenvoegsel` varchar(20) DEFAULT NULL,
  `achternaam` varchar(50) NOT NULL,
  `gebruikersnaam` varchar(20) NOT NULL,
  `wachtwoord` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `land` varchar(50) NOT NULL,
  `provincie` varchar(50) DEFAULT NULL,
  `stad` varchar(50) DEFAULT NULL,
  `geslacht` varchar(5) NOT NULL,
  `msn` varchar(50) DEFAULT NULL,
  `skype` varchar(50) DEFAULT NULL,
  `geboortedatum` varchar(50) NOT NULL,
  `baan` tinyint(1) NOT NULL DEFAULT '0',
  `rank` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `gebruikers` */

insert  into `gebruikers`(`id`,`voornaam`,`tussenvoegsel`,`achternaam`,`gebruikersnaam`,`wachtwoord`,`email`,`land`,`provincie`,`stad`,`geslacht`,`msn`,`skype`,`geboortedatum`,`baan`,`rank`) values (1,'Giedo',NULL,'Terol','Darkrulerz','test','','',NULL,NULL,'',NULL,NULL,'',0,0);

/*Table structure for table `ownedtitles` */

DROP TABLE IF EXISTS `ownedtitles`;

CREATE TABLE `ownedtitles` (
  `id` int(3) NOT NULL,
  `titleId` int(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `ownedtitles` */

/*Table structure for table `skills` */

DROP TABLE IF EXISTS `skills`;

CREATE TABLE `skills` (
  `id` int(3) NOT NULL,
  `taalid` int(3) NOT NULL,
  `taalniveau` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `skills` */

insert  into `skills`(`id`,`taalid`,`taalniveau`) values (1,1,100);

/*Table structure for table `talen` */

DROP TABLE IF EXISTS `talen`;

CREATE TABLE `talen` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `naam` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `talen` */

insert  into `talen`(`id`,`naam`) values (1,'Java');

/*Table structure for table `titles` */

DROP TABLE IF EXISTS `titles`;

CREATE TABLE `titles` (
  `id` int(4) NOT NULL,
  `title` varchar(50) NOT NULL,
  `titledescription` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `titles` */

/*Table structure for table `vragen` */

DROP TABLE IF EXISTS `vragen`;

CREATE TABLE `vragen` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `taalid` int(3) NOT NULL,
  `vraag` varchar(255) NOT NULL,
  `beantwoord` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

/*Data for the table `vragen` */

insert  into `vragen`(`id`,`taalid`,`vraag`,`beantwoord`) values (1,1,'Hoe lees ik een txt file?',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

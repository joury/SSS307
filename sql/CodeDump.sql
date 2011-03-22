DROP TABLE IF EXISTS `antwoorden`;

CREATE TABLE `antwoorden` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `vraagid` int(5) NOT NULL,
  `taalid` int(5) NOT NULL,
  `gebruikersid` int(3) NOT NULL,
  `antwoord` varchar(255) NOT NULL,
  `votes` int(5) NOT NULL,
  `posttijd` datetime NOT NULL,
  PRIMARY KEY (`id`,`vraagid`,`taalid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

insert  into `antwoorden`(`id`,`vraagid`,`taalid`,`gebruikersid`,`antwoord`,`votes`,`posttijd`) values (1,1,1,7,'Met een scanner object',2,'2011-03-20 20:42:35');

DROP TABLE IF EXISTS `gebruikers`;

CREATE TABLE `gebruikers` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `voornaam` varchar(50) NOT NULL,
  `tussenvoegsel` varchar(20) DEFAULT NULL,
  `achternaam` varchar(50) NOT NULL,
  `gebruikersnaam` varchar(20) NOT NULL,
  `wachtwoord` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `taal` varchar(50) NOT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

insert  into `gebruikers`(`id`,`voornaam`,`tussenvoegsel`,`achternaam`,`gebruikersnaam`,`wachtwoord`,`email`,`taal`,`land`,`provincie`,`stad`,`geslacht`,`msn`,`skype`,`geboortedatum`,`baan`,`rank`) values (7,'Giedo','','Terol','darkrulerz','b6124273dc83bc3b6d2aee48a0a98f5a8e6f4f22','dark_rulerz@hotmail.com','Dutch','Netherlands','','','Male','','','12-10-1992',0,0);

DROP TABLE IF EXISTS `landen`;

CREATE TABLE `landen` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=227 DEFAULT CHARSET=utf8;

insert  into `landen`(`id`,`name`) values (1,'Antarctica'),(2,'Argentina'),(3,'Falkland Islands'),(4,'Chile'),(5,'French Southern Territories'),(6,'New Zealand'),(7,'Saint Helena'),(8,'Uruguay'),(9,'South Africa'),(10,'Brazil'),(11,'Lesotho'),(12,'Namibia'),(13,'French Polynesia'),(14,'Paraguay'),(15,'Swaziland'),(16,'Smaller Territories of Chile'),(17,'Botswana'),(18,'Mozambique'),(19,'Madagascar'),(20,'Smaller Territories of the UK'),(21,'Bolivia'),(22,'New Caledonia'),(23,'Zimbabwe'),(24,'Cook Islands'),(25,'Reunion'),(26,'Tonga'),(27,'Mauritius'),(28,'Vanuatu'),(29,'Fiji Islands'),(30,'Peru'),(31,'Zambia'),(32,'Angola'),(33,'Malawi'),(34,'American Samoa'),(35,'Wallis and Futuna'),(36,'Samoa'),(37,'Mayotte'),(38,'Comoros'),(39,'External Territories of Australia'),(40,'Congo (Dem. Rep.)'),(41,'Solomon Islands'),(42,'Tanzania'),(43,'Papua New Guinea'),(44,'Indonesia'),(45,'Tokelau'),(46,'Tuvalu'),(47,'East Timor'),(48,'Congo'),(49,'Kenya'),(50,'Ecuador'),(51,'Colombia'),(52,'Burundi'),(53,'Gabon'),(54,'Kiribati'),(55,'Rwanda'),(56,'Equatorial Guinea'),(57,'Uganda'),(58,'Somalia'),(59,'Maldives'),(60,'Nauru'),(61,'Malaysia'),(62,'Cameroon'),(63,'Palau'),(64,'French Guiana'),(65,'Guyana'),(66,'Central African Republic'),(67,'Ethiopia'),(68,'Micronesia'),(69,'Sudan'),(70,'Nigeria'),(71,'Liberia'),(72,'Ivory Coast'),(73,'Brunei'),(74,'Marshall Islands'),(75,'Philippines'),(76,'Ghana'),(77,'Suriname'),(78,'Venezuela'),(79,'Thailand'),(80,'Sri Lanka'),(81,'Togo'),(82,'Benin'),(83,'Sierra Leone'),(84,'Panama'),(85,'Guinea'),(86,'Chad'),(87,'India'),(88,'Costa Rica'),(89,'Vietnam'),(90,'Burkina Faso'),(91,'Trinidad and Tobago'),(92,'Cambodia'),(93,'Nicaragua'),(94,'Mali'),(95,'Djibouti'),(96,'Guinea-Bissau'),(97,'Niger'),(98,'Grenada'),(99,'Netherlands Antilles'),(100,'Myanmar'),(101,'Senegal'),(102,'Yemen'),(103,'Saint Vincent and The Grenadines'),(104,'Eritrea'),(105,'Barbados'),(106,'Honduras'),(107,'Gambia'),(108,'El Salvador'),(109,'Guam'),(110,'Saint Lucia'),(111,'Guatemala'),(112,'Northern Mariana Islands'),(113,'Martinique'),(114,'Mexico'),(115,'Cape Verde'),(116,'Laos'),(117,'Mauritania'),(118,'Dominica'),(119,'Guadeloupe'),(120,'Belize'),(121,'Saudi Arabia'),(122,'Oman'),(123,'Antigua and Barbuda'),(124,'Saint Kitts and Nevis'),(125,'Virgin Islands of the United States'),(126,'Jamaica'),(127,'Dominican Republic'),(128,'Puerto Rico'),(129,'Haiti'),(130,'China'),(131,'British Virgin Islands'),(132,'Cayman Islands'),(133,'Algeria'),(134,'Cuba'),(135,'Bangladesh'),(136,'Bahamas'),(137,'Turks and Caicos Islands'),(138,'Taiwan'),(139,'Western Sahara'),(140,'Egypt'),(141,'Pakistan'),(142,'Libya'),(143,'United Arab Emirates'),(144,'Japan'),(145,'Qatar'),(146,'Iran'),(147,'Bahrain'),(148,'Nepal'),(149,'Bhutan'),(150,'Spain'),(151,'Morocco'),(152,'Kuwait'),(153,'Jordan'),(154,'Israel'),(155,'Iraq'),(156,'Afghanistan'),(157,'Palestine'),(158,'Tunisia'),(159,'Bermuda'),(160,'Syria'),(161,'Portugal'),(162,'Lebanon'),(163,'Korea (South)'),(164,'Cyprus'),(165,'Greece'),(166,'Malta'),(167,'Turkey'),(168,'Italy'),(169,'Uzbekistan'),(170,'Tajikistan'),(171,'Turkmenistan'),(172,'Korea (North)'),(173,'Azerbaijan'),(174,'Armenia'),(175,'Albania'),(176,'Kyrgyzstan'),(177,'Kazakhstan'),(178,'Macedonia'),(179,'Georgia'),(180,'Russia'),(181,'France'),(182,'Bulgaria'),(183,'Serbia and Montenegro'),(184,'Andorra'),(185,'Croatia'),(186,'Bosnia and Herzegovina'),(187,'Mongolia'),(188,'Romania'),(189,'Monaco'),(190,'San Marino'),(191,'Ukraine'),(192,'Slovenia'),(193,'Moldova'),(194,'Hungary'),(195,'Switzerland'),(196,'Austria'),(197,'Saint Pierre and Miquelon'),(198,'Liechtenstein'),(199,'Germany'),(200,'Slovakia'),(201,'Czech Republic'),(202,'Jersey'),(203,'Poland'),(204,'Guernsey and Alderney'),(205,'Luxembourg'),(206,'Belgium'),(207,'Netherlands'),(208,'Ireland'),(209,'Belarus'),(210,'Lithuania'),(211,'Isle of Man'),(212,'Denmark'),(213,'Sweden'),(214,'Latvia'),(215,'Estonia'),(216,'Norway'),(217,'Finland'),(218,'Greenland'),(219,'Faroe Islands'),(220,'Iceland'),(221,'Svalbard and Jan Mayen'),(222,'United States'),(223,'Canada'),(224,'United Kingdom'),(225,'Australia');

DROP TABLE IF EXISTS `ownedtitles`;

CREATE TABLE `ownedtitles` (
  `id` int(3) NOT NULL,
  `titleId` int(4) NOT NULL,
  PRIMARY KEY (`id`,`titleId`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `ownedtitles` */

/*Table structure for table `skills` */

DROP TABLE IF EXISTS `skills`;

CREATE TABLE `skills` (
  `id` int(3) NOT NULL,
  `taalid` int(3) NOT NULL,
  `taalniveau` int(3) NOT NULL,
  PRIMARY KEY (`id`,`taalid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

insert  into `skills`(`id`,`taalid`,`taalniveau`) values (1,1,100);

DROP TABLE IF EXISTS `spreektalen`;

CREATE TABLE `spreektalen` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;

insert  into `spreektalen`(`id`,`name`) values (1,'Mandarin'),(2,'Spanish'),(3,'English'),(4,'Hindi-Urdu'),(5,'Arabic'),(6,'Bengali'),(7,'Portuguese'),(8,'Russian'),(9,'Japanese'),(10,'Punjabi'),(11,'German'),(12,'Javanese'),(13,'Wu'),(14,'Telugu'),(15,'Marathi'),(16,'French'),(17,'Vietnamese'),(18,'Turkish'),(19,'Korean'),(20,'Tamil'),(21,'Italian'),(22,'Yue'),(23,'Min Nan'),(24,'Gujarati'),(25,'Pashto'),(26,'Polish'),(27,'Persian'),(28,'Bhojpuri'),(29,'Awadhi'),(30,'Ukrainian'),(31,'Malay'),(32,'Xiang'),(33,'Malayalam'),(34,'Kannada'),(35,'Maithili'),(36,'Sundanese'),(37,'Burmese'),(38,'Oriya'),(39,'Marwari'),(40,'Hakka'),(41,'Thai'),(42,'Hausa'),(43,'Tagalog'),(44,'Romanian'),(45,'Dutch'),(46,'Gan'),(47,'Sindhi'),(48,'Azerbaijani'),(49,'Uzbek'),(50,'Lao-Isan'),(51,'Yoruba'),(52,'Igbo'),(53,'Northern Berber'),(54,'Amharic'),(55,'Oromo'),(56,'Assamese'),(57,'Kurdish'),(58,'Serbo-Croatian'),(59,'Cebuano'),(60,'Sinhalese'),(61,'Rangpuri'),(62,'Malagasy'),(63,'Khmer'),(64,'Zhuang'),(65,'Sotho-Tswana'),(66,'Nepali'),(67,'Rwanda-Rundi'),(68,'Somali'),(69,'Madurese'),(70,'Greek'),(71,'Fula'),(72,'Hungarian'),(73,'Catalan'),(74,'Bulgarian-Macedonian'),(75,'Shona'),(76,'Zulu'),(77,'Min Bei');

DROP TABLE IF EXISTS `talen`;

CREATE TABLE `talen` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `naam` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

insert  into `talen`(`id`,`naam`) values (1,'Java'),(2,'C++');

DROP TABLE IF EXISTS `titles`;

CREATE TABLE `titles` (
  `id` int(4) NOT NULL,
  `title` varchar(50) NOT NULL,
  `titledescription` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `vragen`;

CREATE TABLE `vragen` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `taalid` int(3) NOT NULL,
  `gebruiker` int(4) NOT NULL,
  `vraag` varchar(255) NOT NULL,
  `aanvulling` varchar(255) DEFAULT NULL,
  `beantwoord` tinyint(1) NOT NULL DEFAULT '0',
  `posttijd` datetime NOT NULL,
  PRIMARY KEY (`id`,`taalid`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

insert  into `vragen`(`id`,`taalid`,`gebruiker`,`vraag`,`aanvulling`,`beantwoord`,`posttijd`) values (1,1,0,'Hoe lees ik een txt file?','Hey, ik ben bezig met een klein programmatje en vroeg me af of iemand me kan vertellen hoe ik een txt file met namen uitlees bijvoorbeeld. Alvast bedankt!',0,'2011-03-20 17:52:29'),(2,1,0,'Hoe maak ik een database verbinding?',NULL,0,'0000-00-00 00:00:00'),(3,2,0,'Hoe maak ik een gui?',NULL,0,'0000-00-00 00:00:00');
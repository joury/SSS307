/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `spreektalen`;

CREATE TABLE `spreektalen` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=latin1;

insert  into `spreektalen`(`id`,`name`) values (1,'Mandarin'),(2,'Spanish'),(3,'English'),(4,'Hindi-Urdu'),(5,'Arabic'),(6,'Bengali'),(7,'Portuguese'),(8,'Russian'),(9,'Japanese'),(10,'Punjabi'),(11,'German'),(12,'Javanese'),(13,'Wu'),(14,'Telugu'),(15,'Marathi'),(16,'French'),(17,'Vietnamese'),(18,'Turkish'),(19,'Korean'),(20,'Tamil'),(21,'Italian'),(22,'Yue'),(23,'Min Nan'),(24,'Gujarati'),(25,'Pashto'),(26,'Polish'),(27,'Persian'),(28,'Bhojpuri'),(29,'Awadhi'),(30,'Ukrainian'),(31,'Malay'),(32,'Xiang'),(33,'Malayalam'),(34,'Kannada'),(35,'Maithili'),(36,'Sundanese'),(37,'Burmese'),(38,'Oriya'),(39,'Marwari'),(40,'Hakka'),(41,'Thai'),(42,'Hausa'),(43,'Tagalog'),(44,'Romanian'),(45,'Dutch'),(46,'Gan'),(47,'Sindhi'),(48,'Azerbaijani'),(49,'Uzbek'),(50,'Lao-Isan'),(51,'Yoruba'),(52,'Igbo'),(53,'Northern Berber'),(54,'Amharic'),(55,'Oromo'),(56,'Assamese'),(57,'Kurdish'),(58,'Serbo-Croatian'),(59,'Cebuano'),(60,'Sinhalese'),(61,'Rangpuri'),(62,'Malagasy'),(63,'Khmer'),(64,'Zhuang'),(65,'Sotho-Tswana'),(66,'Nepali'),(67,'Rwanda-Rundi'),(68,'Somali'),(69,'Madurese'),(70,'Greek'),(71,'Fula'),(72,'Hungarian'),(73,'Catalan'),(74,'Bulgarian-Macedonian'),(75,'Shona'),(76,'Zulu'),(77,'Min Bei');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

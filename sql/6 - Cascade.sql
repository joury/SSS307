ALTER TABLE `antwoorden` ENGINE = InnoDB;
ALTER TABLE `gebruikers` ENGINE = InnoDB;

DROP TABLE IF EXISTS `votes`;

CREATE TABLE `votes` (
  `antwoordid` int(11) NOT NULL,
  `gebruikersid` int(11) NOT NULL,
  `positive` tinyint(1) DEFAULT NULL,
  `negative` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`antwoordid`,`gebruikersid`),
  KEY `FK_user` (`gebruikersid`),
  CONSTRAINT `FK_answer` FOREIGN KEY (`antwoordid`) REFERENCES `antwoorden` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_user` FOREIGN KEY (`gebruikersid`) REFERENCES `gebruikers` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
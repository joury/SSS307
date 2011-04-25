create table `codedump`.`Votes`( 
   `antwoordid` int NOT NULL , 
   `gebruikersid` int NOT NULL , 
   `positive` boolean , 
   `negative` boolean , 
   PRIMARY KEY (`antwoordid`, `gebruikersid`)
 );
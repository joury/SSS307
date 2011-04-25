alter table `gebruikers` 
   change `voornaam` `voornaam` varchar(50) character set latin1 collate latin1_swedish_ci NULL , 
   change `achternaam` `achternaam` varchar(50) character set latin1 collate latin1_swedish_ci NULL , 
   change `land` `land` varchar(50) character set latin1 collate latin1_swedish_ci NULL , 
   change `geslacht` `geslacht` varchar(5) character set latin1 collate latin1_swedish_ci NULL , 
   change `geboortedatum` `geboortedatum` varchar(50) character set latin1 collate latin1_swedish_ci NULL;
ALTER TABLE `gebruikers`
ADD UNIQUE KEY `SECONDARY` (`gebruikersnaam`),
ADD UNIQUE KEY `TERTIARY` (`email`);
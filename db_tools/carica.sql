/**
Ma come funzionano 'ste path relative ?
 */
LOAD DATA LOCAL INFILE '/home/dario/Documenti/DB_Tirocini/db_tools/ateco2007.csv'
  INTO TABLE CodiceAteco
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE '/home/dario/Documenti/DB_Tirocini/db_tools/classificazioni_predefinite.csv'
  INTO TABLE Classificazioni
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(descrizione);
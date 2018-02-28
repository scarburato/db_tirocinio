
LOAD DATA LOCAL INFILE 'C:\\Users\\Utente\\Documents\\db_tirocinio\\db_tools\\ateco2007.csv'
  INTO TABLE CodiceAteco
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE 'C:\\Users\\Utente\\Documents\\db_tirocinio\\db_tools\\classificazioni_predefinite.csv'
  INTO TABLE Classificazioni
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(descrizione);

INSERT INTO UnitaOrganizzativa(tipo, unita_organizzativa) VALUES
  ('studente', '/STUDENTI'),
  ('docente', '/DOCENTI ITI'),
  ('docente', '/Docenti IPSIA');
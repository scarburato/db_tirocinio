
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

INSERT INTO Privilegio(nome, descrizione) VALUES
  ('user.google.add', 'Consente di aggiungere utenti alla base dati'),
  ('user.factory.add', 'Consente di creare aziende'),
  ('user.factory.resetpasswd', 'Consente di cambiare la parola d\'ordine delle aziende'),
  ('train.add', 'Consente di creare tirocini'),
  ('train.import', 'Consente di importare tirocini dal CSV esoterico'),
  ('train.pubblish', 'Consente di pubblicare tirocini'),
  ('train.readall', 'Consente di leggere i tirocini non propri'),
  ('control.forgive', 'Consente di perdonare gli indirizzi che hanno tentato troppi accessi'),
  ('control.groups', 'COnsente di modificare i gruppi'),
  ('user.groups', 'Consente di assegnare gruppi agli utenti'),
  ('root', 'Accesso totale a tutto, nessuna domanda');

INSERT INTO Gruppo(nome, descrizione) VALUES ('root', 'Accesso totale e globale al programma');
INSERT INTO PermessiGruppo(gruppo, privilegio) VALUES ('root', 'root');
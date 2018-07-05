LOAD DATA LOCAL INFILE '/tmp/csvtmp/ateco2007.csv'
  INTO TABLE CodiceAteco
FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES;

LOAD DATA LOCAL INFILE '/tmp/csvtmp/classificazioni_predefinite.csv'
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
  ('user.google.add', 'Consente di aggiungere utenti alla base dati leggendoli dal dominio'),
  ('user.google.orgunits', 'Consente di modificare le associazioni tra tipo di utente e le unità organizzative del dominio'),
  ('user.factory.add', 'Consente di creare aziende'),
  ('user.factory.resetpasswd', 'Consente di cambiare la parola d\'ordine delle aziende'),
  ('train.add', 'Consente di creare tirocini'),
  ('train.import', 'Consente di importare tirocini dal CSV esoterico'),
  ('train.pubblish', 'Consente di pubblicare i resoconti degli studenti al pubblico, al momento non usato'),
  ('train.readall', 'Consente di leggere i tirocini non propri'),
  ('train.comments.delete', 'Consente di elimare i commenti'),
  ('control.forgive', 'Consente di perdonare gli indirizzi che hanno tentato troppi accessi'),
  ('control.groups', 'COnsente di modificare i gruppi'),
  ('factory.intouch', 'Consente di entrare in contatto con le aziende'),
  ('factory.contacts.create', 'Consente di creare contatti aziendali'),
  ('user.groups', 'Consente di assegnare gruppi agli utenti'),
  ('control.throw', 'Abilita a lanciare un eccezzione'),
  ('root', 'Accesso totale a tutto, nessuna domanda');

INSERT INTO Gruppo(nome, descrizione) VALUES ('root', 'Accesso totale e globale al programma');
INSERT INTO PermessiGruppo(gruppo, privilegio) VALUES ('root', 'root');

INSERT INTO Indirizzo(indirizzo) VALUES
  ('Informatica e Telecomunicazioni'),
  ('Chimica, Materiali e Biotecnologie'),
  ('Elettronica ed Elettrotecnica'),
  ('Meccanica, Meccatronica ed Energia'),
  ('Trasporti e Logistica');

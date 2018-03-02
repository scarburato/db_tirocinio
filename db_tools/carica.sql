
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
  ('control.google.users.readonly', 'Consente la lettura delle utenze che accedono al programma.'),
  ('control.google.users', 'Consente la gestione delle utenze che accedono al programma.'),
  ('control.business.users', 'Consente la gestione delle utenze aziendali.'),
  ('control.network.list', 'Consente di visualizzare gli indirizzi di rete dei tentativi d\'accesso.'),
  ('control.network.forgive', 'Consente di "perdonare" gli indirizzi di rete che hanno effettuato eccessi tentativi di autenticazione senza successo.'),
  ('control.google.permissions', 'Consente di modificare i permessi degli utenti Google.')
;
DROP DATABASE IF EXISTS Tirocini;

CREATE DATABASE IF NOT EXISTS Tirocini
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE Tirocini;

CREATE TABLE IF NOT EXISTS UtenteGoogle (
  id              SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  SUB_GOOGLE      VARCHAR(64) UNIQUE,
  nome            VARCHAR(32) NOT NULL,
  secondoNome     VARCHAR(128),
  cognome         VARCHAR(48) NOT NULL,
  indirizzo_posta VARCHAR(64) NOT NULL
);

CREATE TABLE IF NOT EXISTS Indirizzo (
  indirizzo VARCHAR(20) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS Studente (
  utente    SMALLINT UNSIGNED PRIMARY KEY,
  indirizzo VARCHAR(20)        NOT NULL,
  Matricola VARCHAR(10) UNIQUE NOT NULL,

  FOREIGN KEY (utente)
  REFERENCES UtenteGoogle (id),
  FOREIGN KEY (indirizzo)
  REFERENCES Indirizzo (indirizzo)
);

CREATE TABLE IF NOT EXISTS Docente (
  utente SMALLINT UNSIGNED PRIMARY KEY,
  FOREIGN KEY (utente)
  REFERENCES UtenteGoogle (id)
);

CREATE TABLE IF NOT EXISTS Privilegio (
  nome        VARCHAR(126) PRIMARY KEY,
  descrizione TINYTEXT NOT NULL
);

INSERT INTO Privilegio (nome, descrizione) VALUES
  ('control.google.users', 'Consente la gestione delle utenze che accedono al programma.'),
  ('control.business.users', 'Consente la gestione delle utenze aziendali.'),
  ('control.network.list', 'Consente di visualizzare gli indirizzi di rete dei tentativi d\'accesso.'),
  ('control.network.list.forgive',
   'Consente di "perdonare" gli indirizzi di rete che hanno effettuato eccessi tentativi di autenticazione senza successo.'),
  ('control.google.permissions', 'Consente di modificare i permessi degli utenti Google.'),
  ('user.root',
   'Non si applicano restrizioni di alcun tipo, può essere assegnata solo da un\'altro utente root ovvero da chi può accedere in maniera diretta alla base di dati.');

/* TODO Aggiungere altri permessi per i commenti*/

CREATE TABLE IF NOT EXISTS PrivilegiApplicati (
  utente     SMALLINT UNSIGNED,
  privilegio VARCHAR(10),

  PRIMARY KEY (utente, privilegio),
  FOREIGN KEY (utente)
  REFERENCES Docente (utente),
  FOREIGN KEY (privilegio)
  REFERENCES Privilegio (nome)
);

CREATE TABLE IF NOT EXISTS Classificazioni (
  id          SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  descrizione TINYTEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS CodiceAteco (
  id          SMALLINT UNSIGNED PRIMARY KEY,
  cod2007     CHAR(8) UNIQUE ,
  descrizione TEXT
);

CREATE TABLE IF NOT EXISTS Azienda (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  IVA             CHAR(11) UNIQUE   NOT NULL,
  codiceFiscale   CHAR(16) UNIQUE,
  nominativo      VARCHAR(100)      NOT NULL,
  parolaOrdine    CHAR(128)         NOT NULL,
  classificazione SMALLINT UNSIGNED NOT NULL,
  ateco           SMALLINT UNSIGNED NOT NULL,
  dimensione      ENUM ('0-9', '10-49', '50-99', '100-199', '200-499', '500+'),
  gestione        ENUM ('pubblica', 'privata', 'mista'),

  no_accessi      BOOLEAN           NOT NULL DEFAULT TRUE,

  FOREIGN KEY (Classificazione)
  REFERENCES Classificazioni (ID),
  FOREIGN KEY (Ateco)
  REFERENCES CodiceAteco (id)
);

CREATE TABLE IF NOT EXISTS Sede (
  id        TINYINT UNSIGNED AUTO_INCREMENT,
  azienda   INT UNSIGNED,
  nomeSede  VARCHAR(128) NOT NULL,
  indirizzo VARCHAR(128),
  numCivico VARCHAR(15),
  comune    VARCHAR(128),
  provincia VARCHAR(128),
  stato     VARCHAR(128),
  CAP       SMALLINT(5) UNSIGNED,

  PRIMARY KEY (ID, Azienda),
  FOREIGN KEY (Azienda)
  REFERENCES Azienda (ID)
);

CREATE TABLE IF NOT EXISTS IndirizziAzienda (
  indirizzo   VARCHAR(20),
  azienda     INT UNSIGNED,
  motivazioni TEXT NOT NULL,

  PRIMARY KEY (Indirizzo, Azienda),
  FOREIGN KEY (Indirizzo)
  REFERENCES Indirizzo (Indirizzo),
  FOREIGN KEY (Azienda)
  REFERENCES Azienda (ID)
);

CREATE TABLE IF NOT EXISTS Contatto (
  id             INT(8) UNSIGNED PRIMARY KEY,
  azienda        INT UNSIGNED,
  nome           VARCHAR(32) NOT NULL,
  secondoNome    VARCHAR(128),
  cognome        VARCHAR(48) NOT NULL,
  email          VARCHAR(64),
  telefono       CHAR(35), /* In conformità a ISO 20022 */
  FAX            CHAR(35),
  qualifica      VARCHAR(60),
  ruoloAziendale TINYTEXT    NOT NULL,

  FOREIGN KEY (Azienda)
  REFERENCES Azienda (ID)
);

CREATE TABLE IF NOT EXISTS EntratoInContatto (
  contatto INT(8) UNSIGNED,
  docente  SMALLINT UNSIGNED,
  inizio   DATE NOT NULL,
  fine     DATE,

  PRIMARY KEY (Contatto, Docente),
  FOREIGN KEY (Contatto)
  REFERENCES Contatto (ID),
  FOREIGN KEY (Docente)
  REFERENCES Docente (Utente)
);

CREATE TABLE IF NOT EXISTS Tirocinio (
  id              INT(8) UNSIGNED                                  AUTO_INCREMENT PRIMARY KEY,
  studente        SMALLINT UNSIGNED                       NOT NULL,
  azienda         INT UNSIGNED                            NOT NULL,
  docenteTutore   SMALLINT UNSIGNED                       NOT NULL,
  tutoreAziendale INT(8) UNSIGNED                         NOT NULL,
  dataInizio      DATE                                    NOT NULL,
  dataTermine     DATE,

  giudizio        TINYINT UNSIGNED,
  descrizione     TINYTEXT,
  visibilita      ENUM ('studente', 'docente', 'azienda') NOT NULL DEFAULT 'studente',

  UNIQUE (Studente, Azienda, DataInizio),
  FOREIGN KEY (Studente)
  REFERENCES Studente (utente),
  FOREIGN KEY (Azienda)
  REFERENCES Azienda (ID),
  FOREIGN KEY (DocenteTutore)
  REFERENCES Docente (Utente),
  FOREIGN KEY (TutoreAziendale)
  REFERENCES Contatto (ID)
);

CREATE TABLE IF NOT EXISTS Commento (
  tirocinio INT(8) UNSIGNED,
  autore    SMALLINT UNSIGNED,
  testo     TEXT      NOT NULL,
  quando    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),

  PRIMARY KEY (Tirocinio, Autore),
  FOREIGN KEY (Tirocinio)
  REFERENCES Tirocinio (id),
  FOREIGN KEY (Autore)
  REFERENCES UtenteGoogle (id)
);

CREATE TABLE IF NOT EXISTS AziendeTentativiAccesso (
  indirizzo_rete    VARBINARY(16)   NOT NULL PRIMARY KEY,
  ultimo_accesso    TIMESTAMP    NULL     DEFAULT NULL,
  tentativi_falliti INT UNSIGNED NOT NULL DEFAULT 0,
  ultimo_tentativo  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP()
);
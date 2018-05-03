/**
Alcune tabelle contengono CHECK, solo le versioni di MariaDB pari o superiori a 10.2.1 supportano i CHECK,
nelle altre versioni o in altri DMBS mysql il costrutto viene ignorato.

https://mariadb.com/kb/en/library/constraint/#check-constraints
 */

DROP DATABASE IF EXISTS Tirocini;

CREATE DATABASE Tirocini
  DEFAULT CHARACTER SET utf8
  DEFAULT COLLATE utf8_general_ci;

USE Tirocini;

CREATE TABLE UnitaOrganizzativa(
  tipo                ENUM ('docente', 'studente', 'ambedue') NOT NULL,
  unita_organizzativa VARCHAR(255) UNIQUE NOT NULL
);

CREATE TABLE UtenteGoogle (
  id              SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  SUB_GOOGLE      VARCHAR(64) UNIQUE,
  nome            VARCHAR(128) NOT NULL,
  cognome         VARCHAR(128) NOT NULL,
  indirizzo_posta VARCHAR(2083) NOT NULL,
  fotografia      VARCHAR(2083),

  INDEX (indirizzo_posta)
);

CREATE TABLE Indirizzo (
  id        INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  indirizzo VARCHAR(128) NOT NULL
);

CREATE TABLE Studente (
  utente    SMALLINT UNSIGNED PRIMARY KEY,
  indirizzo INT UNSIGNED,
  matricola VARCHAR(10) UNIQUE,

  FOREIGN KEY (utente)
  REFERENCES UtenteGoogle (id),
  FOREIGN KEY (indirizzo)
  REFERENCES Indirizzo (id)
);

CREATE TABLE Docente (
  utente SMALLINT UNSIGNED PRIMARY KEY,
  FOREIGN KEY (utente)
  REFERENCES UtenteGoogle (id)
);

CREATE TABLE Privilegio (
  nome        VARCHAR(126) PRIMARY KEY,
  descrizione TINYTEXT NOT NULL
);

CREATE TABLE Gruppo (
  nome        VARCHAR(126) PRIMARY KEY,
  descrizione TINYTEXT NOT NULL
);

CREATE TABLE PermessiGruppo (
  gruppo      VARCHAR(126),
  privilegio  VARCHAR(126),

  PRIMARY KEY (gruppo, privilegio),
  FOREIGN KEY (gruppo) REFERENCES Gruppo(nome),
  FOREIGN KEY (privilegio) REFERENCES Privilegio(nome)
);

CREATE TABLE GruppiApplicati (
  utente     SMALLINT UNSIGNED,
  gruppo     VARCHAR(126),

  PRIMARY KEY (utente, gruppo),
  FOREIGN KEY (utente)
  REFERENCES Docente (utente),
  FOREIGN KEY (gruppo)
  REFERENCES Gruppo (nome)
);

CREATE TABLE Classificazioni (
  id          SMALLINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  descrizione TINYTEXT NOT NULL
);

CREATE TABLE CodiceAteco (
  id          SMALLINT UNSIGNED PRIMARY KEY,
  cod2007     CHAR(8) UNIQUE,
  descrizione TEXT
);

CREATE TABLE Azienda (
  id              INT UNSIGNED               AUTO_INCREMENT PRIMARY KEY,
  IVA             CHAR(11) UNIQUE CHECK (CHAR_LENGTH(IVA) = 11),
  codiceFiscale   CHAR(16) UNIQUE CHECK (CHAR_LENGTH(codiceFiscale) BETWEEN 11 AND 16),
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

CREATE TABLE Sede (
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

/**
Gli indirizzi di una azienda sono quelli della scuola (Info, tlc, cma ecc)
non quelli di casa!
 */
CREATE TABLE IndirizziAzienda (
  indirizzo   INT UNSIGNED,
  azienda     INT UNSIGNED,
  motivazioni TEXT NOT NULL,

  PRIMARY KEY (Indirizzo, Azienda),
  FOREIGN KEY (Indirizzo)
  REFERENCES Indirizzo (id),
  FOREIGN KEY (Azienda)
  REFERENCES Azienda (ID)
);

CREATE TABLE Contatto (
  id             INT(8) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  azienda        INT UNSIGNED,
  nome           VARCHAR(128) NOT NULL,
  cognome        VARCHAR(128) NOT NULL,
  email          VARCHAR(2083),
  telefono       CHAR(35) CHECK (telefono REGEXP '\\+[0-9]{1,3}-[0-9()+\\-]{1,30}'), /* In conformità a ISO 20022 */
  FAX            CHAR(35) CHECK (FAX REGEXP '\\+[0-9]{1,3}-[0-9()+\\-]{1,30}'),
  qualifica      VARCHAR(128),
  ruoloAziendale TEXT    NOT NULL,

  FOREIGN KEY (Azienda)
  REFERENCES Azienda (ID)
);

CREATE TABLE EntratoInContatto (
  contatto INT(8) UNSIGNED,
  docente  SMALLINT UNSIGNED,
  inizio   DATE NOT NULL,
  fine     DATE               CHECK(fine IS NULL OR fine >= inizio),

  PRIMARY KEY (Contatto, Docente, inizio),
  FOREIGN KEY (Contatto)
  REFERENCES Contatto (ID),
  FOREIGN KEY (Docente)
  REFERENCES Docente (Utente)
);

/*
Questo evento viene chiamato prima dell'inserimento nella tabella EntratoInContatto,
se un contatto con una persone si sovrappone temporalmente un errore 70002 viene generato!
 */
CREATE TRIGGER ControlloSovrapposizioneTemporale
  BEFORE INSERT ON EntratoInContatto
  FOR EACH ROW
  BEGIN
    IF (EXISTS (
        SELECT inizio
        FROM EntratoInContatto E
        WHERE E.docente = NEW.docente
              AND E.contatto = NEW.contatto
              AND (E.fine IS NULL OR NEW.inizio <= E.fine)
              AND (NEW.fine IS NULL OR NEW.fine >= E.inizio)
    ))
    THEN
      SIGNAL SQLSTATE '70002'
      SET MESSAGE_TEXT = 'Already in contact each other!';
    END IF;
  END;

CREATE TABLE Tirocinio (
  id              INT(8) UNSIGNED                         AUTO_INCREMENT PRIMARY KEY,
  studente        SMALLINT UNSIGNED                       NOT NULL,
  azienda         INT UNSIGNED                            NOT NULL,
  docenteTutore   SMALLINT UNSIGNED                       NOT NULL,
  tutoreAziendale INT(8) UNSIGNED,
  dataInizio      DATE                                    NOT NULL,
  dataTermine     DATE,

  giudizio        TINYINT UNSIGNED,
  descrizione     LONGTEXT, /* È la recensione dello studente! */
  ultima_modifica TIMESTAMP                               NULL DEFAULT NULL,
  visibilita      ENUM ('studente', 'docente', 'azienda') NOT NULL DEFAULT 'studente',

  UNIQUE (Studente, Azienda, DataInizio),
  FOREIGN KEY (Studente)
  REFERENCES Studente (utente),
  FOREIGN KEY (Azienda)
  REFERENCES Azienda (ID),
  FOREIGN KEY (DocenteTutore)
  REFERENCES Docente (Utente),
  FOREIGN KEY (TutoreAziendale)
  REFERENCES Contatto (ID),

  CONSTRAINT CHK_data CHECK (dataTermine IS NULL OR dataInizio <= dataTermine)
);

CREATE TRIGGER AggiornaUltimaModifica
  BEFORE UPDATE ON Tirocinio
  FOR EACH ROW
  BEGIN
    IF MD5(NEW.descrizione) <> MD5(OLD.descrizione) THEN
      IF NEW.visibilita = 'azienda' THEN
        SIGNAL SQLSTATE '70003'
        SET MESSAGE_TEXT = 'It\'s impossible to update descrizione when visibilita = \'azienda\'!';
      ELSE
        SET NEW.ultima_modifica = CURRENT_TIMESTAMP();
      END IF;
    END IF;
  END;

CREATE TABLE Commento (
  id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  tirocinio INT(8) UNSIGNED NOT NULL,
  autore    SMALLINT UNSIGNED NOT NULL,
  testo     TEXT      NOT NULL,
  quando    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),

  UNIQUE (tirocinio, autore, quando),

  FOREIGN KEY (Tirocinio)
  REFERENCES Tirocinio (id),
  FOREIGN KEY (Autore)
  REFERENCES UtenteGoogle (id)
);

CREATE TABLE AziendeTentativiAccesso (
  indirizzo_rete    VARBINARY(16) NOT NULL PRIMARY KEY,
  ultimo_accesso    TIMESTAMP     NULL     DEFAULT NULL,
  tentativi_falliti INT UNSIGNED  NOT NULL DEFAULT 0,
  ultimo_tentativo  TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP()
);

CREATE FUNCTION aggiungiTentativoAccesso(indirizzo VARBINARY(16))
  RETURNS INT UNSIGNED
  BEGIN
    IF NOT EXISTS(SELECT *
                  FROM AziendeTentativiAccesso
                  WHERE indirizzo_rete = indirizzo)
    THEN
      INSERT INTO AziendeTentativiAccesso (indirizzo_rete, tentativi_falliti) VALUES (indirizzo, 1);
    ELSE
      UPDATE AziendeTentativiAccesso
      SET tentativi_falliti = tentativi_falliti + 1, ultimo_tentativo = CURRENT_TIMESTAMP()
      WHERE indirizzo_rete = indirizzo;
    END IF;

    RETURN (SELECT tentativi_falliti
            FROM AziendeTentativiAccesso
            WHERE indirizzo_rete = indirizzo);
  END;

CREATE FUNCTION successoAccesso(indirizzo VARBINARY(16))
  RETURNS INT UNSIGNED
  BEGIN
    IF NOT EXISTS(SELECT *
                  FROM AziendeTentativiAccesso
                  WHERE indirizzo_rete = indirizzo)
    THEN
      INSERT INTO AziendeTentativiAccesso (indirizzo_rete, ultimo_accesso) VALUES (indirizzo, CURRENT_TIMESTAMP());
    ELSE
      UPDATE AziendeTentativiAccesso
        SET tentativi_falliti = 0, ultimo_accesso = CURRENT_TIMESTAMP()
      WHERE indirizzo_rete = indirizzo;
    END IF;
    RETURN (SELECT tentativi_falliti
            FROM AziendeTentativiAccesso
            WHERE indirizzo_rete = indirizzo);
  END;

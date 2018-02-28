# Gestione tirocini ITIS Leonardo da Vinci

Progetto assegnatoci dalla squola per non pagare dei professionisti.
L'obbiettivo di questa applicazione WEB è di gestire i tirocini all'interno dell'ITIS Leonardo da Vinci di Pisa. Oltre a questo offre funzionalità per la gestione dei contatti tra i docenti ed i referenti delle aziende con le quali la scuola è in contatto.

# Installare
## Prerequisiti
- php 7.0 o superiore
- estensione mysqli per php
- estensione per utf8 per php
- estensione per curl per php
- mariaDB 1.2.1 o superiore (le versioni precedenti potrebbero funzionare)

## Preliminari
- Per rendere operativo il sito su un server WEB clonare la repo in una cartella a caso come `/opt/mysite`.
- Dopo di che copiare i file segreti nella radice del progetto, **attenzione i file segreti non devono essere caricati su Internet**. 
- Accertarsi che l'utente di servizio che fa girare il server WEB possa leggere sulla cartella del progetto e nel suo contenuto.
- A questo punto creare un collegamento simbolico alla cartella radice del vostro server che punti alla cartella htdocs nella cartella del progetto. Poniamo caso che la root web sia in `/var/www/html` e htdocs sia in `/opt/mysite/htdocs` allora si dovrà creare un collegamento simbolico come segue `ln -s /opt/mysite/htdocs /var/www/html/`.

## Librerie di Composer
Installare le librerire in `composer.json` come descritto qua https://getcomposer.org/doc/01-basic-usage.md#composer-json-project-setup . Alternativamente lasciate fare a PhpStorm. NB Clonando questa repo sono le librerire sono già installate ma potrebbero non essere aggiornate.

## Creazione della base di dati
spostarsi nella cartella `db_tools` ed avviare una sessione interattiva della console mysql con `mariadb -u root` ovvero `mysql -u root` ed eseguire
```source build.sql```
Questo dovrebbe aver creato la struttura base della base di dati, ora inseriamo i dati
```source carica.sql```

## Ultimi passi
Ora modificare le impostazioni di Google ed in `const.hphp` inserendo il nuovo URL di login.

# Domande frequenti che potrebbero porgere i futuri ~~schiavi~~ studenti di alternanza
Perché i nomi dei file sono metà in italiano e metà in albionico? 
> Non so dare una spiegazione razionale

Perché i nomi delle funzioni sono metaInCamelCase e meta nello_altro_modo?
> Le funzioni native di PHP sanno fare molto peggio

Che framewok CSS è stato usato?
> bulma.io

Perché mancano commenti e documentazione?
> Perché dovete paty

È presente un miner di criptmonete offuscato nel JavaScript?
> Assolutamente no®. 

Non riesco a far partire Apache2 e/o MariaDB
> Controllare bobina, spinterogeno e candele

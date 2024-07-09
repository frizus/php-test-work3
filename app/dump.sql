/* Converted via https://www.sqlines.com/online */

/* SQLINES DEMO *** ME_ZONE=@@TIME_ZONE */;
/* SQLINES DEMO *** NE='+00:00' */;
/* SQLINES DEMO *** IQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/* SQLINES DEMO *** REIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/* SQLINES DEMO *** L_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/* SQLINES DEMO *** L_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS agency CASCADE;
/* SQLINES DEMO *** cs_client     = @@character_set_client */;
/* SQLINES DEMO *** er_set_client = utf8mb4 */;
-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE agency (
  id bigint check (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  name varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ;
/* SQLINES DEMO *** er_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS contacts CASCADE;
/* SQLINES DEMO *** cs_client     = @@character_set_client */;
/* SQLINES DEMO *** er_set_client = utf8mb4 */;
-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE contacts (
  id bigint check (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  name varchar(255) NOT NULL,
  phones varchar(255) NOT NULL,
  PRIMARY KEY (id)
) ;
/* SQLINES DEMO *** er_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS estate CASCADE;
/* SQLINES DEMO *** cs_client     = @@character_set_client */;
/* SQLINES DEMO *** er_set_client = utf8mb4 */;
-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE estate (
  id bigint check (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  external_id varchar(255) NULL,
  address varchar(255) NOT NULL,
  price int NOT NULL,
  rooms int NOT NULL,
  floor int NOT NULL,
  house_floors int NOT NULL,
  description text NOT NULL,
  contact_id bigint check (contact_id > 0) NOT NULL,
  manager_id bigint check (manager_id > 0) NOT NULL,
  PRIMARY KEY (id)
,
  CONSTRAINT estate_contact_id_foreign FOREIGN KEY (contact_id) REFERENCES contacts (id)/*,
  CONSTRAINT estate_manager_id_foreign FOREIGN KEY (manager_id) REFERENCES manager (id)*/
) ;

CREATE INDEX estate_contact_id_foreign ON estate (contact_id);
CREATE INDEX estate_manager_id_foreign ON estate (manager_id);
/* SQLINES DEMO *** er_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS manager CASCADE;
/* SQLINES DEMO *** cs_client     = @@character_set_client */;
/* SQLINES DEMO *** er_set_client = utf8mb4 */;
-- SQLINES LICENSE FOR EVALUATION USE ONLY
CREATE TABLE manager (
  id bigint check (id > 0) NOT NULL GENERATED ALWAYS AS IDENTITY,
  name varchar(255) NOT NULL,
  agency_id bigint check (agency_id > 0) NOT NULL,
  PRIMARY KEY (id)
,
  CONSTRAINT manager_agency_id_foreign FOREIGN KEY (agency_id) REFERENCES agency (id)
) ;

CREATE INDEX manager_agency_id_foreign ON manager (agency_id);

ALTER TABLE estate ADD CONSTRAINT estate_manager_id_foreign FOREIGN KEY (manager_id) REFERENCES manager (id);
/* SQLINES DEMO *** er_set_client = @saved_cs_client */;

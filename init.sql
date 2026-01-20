/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.6.24-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: infong7e_crm
-- ------------------------------------------------------
-- Server version	10.6.24-MariaDB-ubu2204

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `Organiz`
--

DROP TABLE IF EXISTS `Organiz`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `Organiz` (
  `id_organiz` int(11) NOT NULL AUTO_INCREMENT,
  `Name_organiz` varchar(100) NOT NULL,
  `INN_organiz` varchar(16) NOT NULL,
  PRIMARY KEY (`id_organiz`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Organiz`
--

LOCK TABLES `Organiz` WRITE;
/*!40000 ALTER TABLE `Organiz` DISABLE KEYS */;
/*!40000 ALTER TABLE `Organiz` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `clients_info`
--

DROP TABLE IF EXISTS `clients_info`;
/*!50001 DROP VIEW IF EXISTS `clients_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `clients_info` AS SELECT
 1 AS `id_cli`,
  1 AS `Familiya`,
  1 AS `Imya`,
  1 AS `Otchestv`,
  1 AS `telegramid`,
  1 AS `organizs`,
  1 AS `emailsc`,
  1 AS `phone` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `clientsc`
--

DROP TABLE IF EXISTS `clientsc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientsc` (
  `id_cli` int(11) NOT NULL AUTO_INCREMENT,
  `Familiya` longtext NOT NULL,
  `Imya` longtext NOT NULL,
  `Otchestv` longtext NOT NULL,
  `telegramid` longtext NOT NULL,
  `organizs` longtext NOT NULL,
  `emailsc` longtext NOT NULL,
  `phone` longtext NOT NULL,
  PRIMARY KEY (`id_cli`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientsc`
--

LOCK TABLES `clientsc` WRITE;
/*!40000 ALTER TABLE `clientsc` DISABLE KEYS */;
/*!40000 ALTER TABLE `clientsc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id_com` int(11) NOT NULL AUTO_INCREMENT,
  `name_com` longtext NOT NULL,
  `sot_id` int(11) NOT NULL,
  `zayav_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `docx` longblob DEFAULT NULL,
  PRIMARY KEY (`id_com`),
  KEY `id_sot` (`sot_id`),
  KEY `comments_ibfk_2` (`zayav_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`sot_id`) REFERENCES `userscr` (`id_use`),
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`zayav_id`) REFERENCES `zayavki` (`id_zay`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments_zadach`
--

DROP TABLE IF EXISTS `comments_zadach`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments_zadach` (
  `id_com` int(11) NOT NULL AUTO_INCREMENT,
  `name_com` longtext NOT NULL,
  `sot_id` int(11) NOT NULL,
  `docx` longblob DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `id_zad` int(11) NOT NULL,
  PRIMARY KEY (`id_com`),
  KEY `sot_id` (`sot_id`,`id_zad`),
  KEY `comments_zadach_ibfk_2` (`id_zad`),
  CONSTRAINT `comments_zadach_ibfk_1` FOREIGN KEY (`sot_id`) REFERENCES `userscr` (`id_use`),
  CONSTRAINT `comments_zadach_ibfk_2` FOREIGN KEY (`id_zad`) REFERENCES `zadachi` (`id_zadch`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments_zadach`
--

LOCK TABLES `comments_zadach` WRITE;
/*!40000 ALTER TABLE `comments_zadach` DISABLE KEYS */;
/*!40000 ALTER TABLE `comments_zadach` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dolzh`
--

DROP TABLE IF EXISTS `dolzh`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `dolzh` (
  `id_dolzh` int(11) NOT NULL AUTO_INCREMENT,
  `namedolzh` longtext NOT NULL,
  PRIMARY KEY (`id_dolzh`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dolzh`
--

LOCK TABLES `dolzh` WRITE;
/*!40000 ALTER TABLE `dolzh` DISABLE KEYS */;
INSERT INTO `dolzh` VALUES (1,'Менеджер'),(2,'Администратор'),(3,'Менеджер'),(4,'Администратор'),(5,'Администратор');
/*!40000 ALTER TABLE `dolzh` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `full_zayavki_view`
--

DROP TABLE IF EXISTS `full_zayavki_view`;
/*!50001 DROP VIEW IF EXISTS `full_zayavki_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `full_zayavki_view` AS SELECT
 1 AS `id_zay`,
  1 AS `date_reg`,
  1 AS `status`,
  1 AS `id_stat`,
  1 AS `id_cli`,
  1 AS `client_familiya`,
  1 AS `client_imya`,
  1 AS `client_otchestvo`,
  1 AS `client_telegram`,
  1 AS `client_organization`,
  1 AS `client_email`,
  1 AS `client_phone`,
  1 AS `id_use`,
  1 AS `sotrudnik_familiya`,
  1 AS `sotrudnik_imya`,
  1 AS `sotrudnik_otchestvo`,
  1 AS `sotrudnik_phone`,
  1 AS `sotrudnik_otdel`,
  1 AS `dolzhnost`,
  1 AS `sotrudnik_telegram`,
  1 AS `sotrudnik_email`,
  1 AS `istoch`,
  1 AS `file`,
  1 AS `comment`,
  1 AS `comment_document`,
  1 AS `dedlain`,
  1 AS `soderzh`,
  1 AS `otdel_zayav`,
  1 AS `otdel_zayav_name` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `otdeli`
--

DROP TABLE IF EXISTS `otdeli`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `otdeli` (
  `id_otd` int(11) NOT NULL AUTO_INCREMENT,
  `name_otd` longtext NOT NULL,
  PRIMARY KEY (`id_otd`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otdeli`
--

LOCK TABLES `otdeli` WRITE;
/*!40000 ALTER TABLE `otdeli` DISABLE KEYS */;
INSERT INTO `otdeli` VALUES (1,'Отдел Продаж'),(2,'Администрация'),(3,'Отдел Продаж'),(4,'Администрация');
/*!40000 ALTER TABLE `otdeli` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `status` (
  `id_stat` int(11) NOT NULL AUTO_INCREMENT,
  `name_stat` longtext NOT NULL,
  PRIMARY KEY (`id_stat`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'Новая'),(2,'В работе'),(3,'Завершена'),(4,'Отменена'),(5,'Новая'),(6,'В работе'),(7,'Завершена'),(8,'Отменена');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!50001 DROP VIEW IF EXISTS `user_info`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `user_info` AS SELECT
 1 AS `id_use`,
  1 AS `Familiya`,
  1 AS `Imya`,
  1 AS `Otchestvo`,
  1 AS `phone`,
  1 AS `name_otd`,
  1 AS `id_otd`,
  1 AS `id_dolzh`,
  1 AS `namedolzh`,
  1 AS `telegramID`,
  1 AS `mailc`,
  1 AS `admin` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `userscr`
--

DROP TABLE IF EXISTS `userscr`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `userscr` (
  `id_use` int(11) NOT NULL AUTO_INCREMENT,
  `Familiya` longtext NOT NULL,
  `Imya` longtext NOT NULL,
  `Otchestvo` longtext NOT NULL,
  `phone` longtext NOT NULL,
  `otdel_id` int(11) NOT NULL,
  `dolzh_id` int(11) NOT NULL,
  `telegramID` longtext NOT NULL,
  `mailc` longtext NOT NULL,
  `admin` int(11) NOT NULL,
  `passwd` longtext NOT NULL,
  PRIMARY KEY (`id_use`),
  KEY `otdel_id` (`otdel_id`),
  KEY `dolzh_id` (`dolzh_id`),
  CONSTRAINT `userscr_ibfk_1` FOREIGN KEY (`otdel_id`) REFERENCES `otdeli` (`id_otd`),
  CONSTRAINT `userscr_ibfk_2` FOREIGN KEY (`dolzh_id`) REFERENCES `dolzh` (`id_dolzh`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `userscr`
--

LOCK TABLES `userscr` WRITE;
/*!40000 ALTER TABLE `userscr` DISABLE KEYS */;
INSERT INTO `userscr` VALUES (1,'Петров','Петр','Петрович','+79990000000',1,1,'11223344','petr@test.com',1,'$2y$10$rlDlGePdtdhbLRGnDhugwu2JGVGGPwUvEzqLqMNSB20029Nd5cIDS'),(2,'Admin','System','User','+70000000000',2,2,'0','admin@crm.local',1,'admin123'),(3,'Петров','Петр','Петрович','+79990000000',3,3,'11223344','petr@test.com',1,'$2y$10$rlDlGePdtdhbLRGnDhugwu2JGVGGPwUvEzqLqMNSB20029Nd5cIDS'),(4,'Admin','System','User','+70000000000',4,5,'0','admin@crm.local',1,'admin@crm.local');
/*!40000 ALTER TABLE `userscr` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vosstanovlenie`
--

DROP TABLE IF EXISTS `vosstanovlenie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `vosstanovlenie` (
  `id_vost` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` longtext NOT NULL,
  `session` longtext NOT NULL,
  `try` longtext NOT NULL,
  PRIMARY KEY (`id_vost`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `vosstanovlenie_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `userscr` (`id_use`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vosstanovlenie`
--

LOCK TABLES `vosstanovlenie` WRITE;
/*!40000 ALTER TABLE `vosstanovlenie` DISABLE KEYS */;
/*!40000 ALTER TABLE `vosstanovlenie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `zadachi`
--

DROP TABLE IF EXISTS `zadachi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `zadachi` (
  `id_zadch` int(11) NOT NULL AUTO_INCREMENT,
  `nazv` longtext NOT NULL,
  `status_id` int(11) NOT NULL,
  `opisanie` longtext NOT NULL,
  `iniciat_id` int(11) NOT NULL,
  `ispolnit_id` int(11) NOT NULL,
  `file` longblob DEFAULT NULL,
  `data_naz` longtext NOT NULL,
  `data_dedl` longtext NOT NULL,
  `comm_id` int(11) DEFAULT NULL,
  `zayav_id` int(11) DEFAULT NULL,
  `client_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_zadch`),
  KEY `iniciat_id` (`iniciat_id`,`ispolnit_id`,`zayav_id`),
  KEY `zayav_id` (`zayav_id`),
  KEY `ispolnit_id` (`ispolnit_id`),
  KEY `client_id` (`client_id`),
  KEY `status_id` (`status_id`),
  KEY `comm_id` (`comm_id`),
  CONSTRAINT `zadachi_ibfk_2` FOREIGN KEY (`zayav_id`) REFERENCES `zayavki` (`id_zay`),
  CONSTRAINT `zadachi_ibfk_3` FOREIGN KEY (`iniciat_id`) REFERENCES `userscr` (`id_use`),
  CONSTRAINT `zadachi_ibfk_4` FOREIGN KEY (`ispolnit_id`) REFERENCES `userscr` (`id_use`),
  CONSTRAINT `zadachi_ibfk_5` FOREIGN KEY (`client_id`) REFERENCES `clientsc` (`id_cli`),
  CONSTRAINT `zadachi_ibfk_6` FOREIGN KEY (`status_id`) REFERENCES `status` (`id_stat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zadachi`
--

LOCK TABLES `zadachi` WRITE;
/*!40000 ALTER TABLE `zadachi` DISABLE KEYS */;
/*!40000 ALTER TABLE `zadachi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `zadachi_detailed_view`
--

DROP TABLE IF EXISTS `zadachi_detailed_view`;
/*!50001 DROP VIEW IF EXISTS `zadachi_detailed_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `zadachi_detailed_view` AS SELECT
 1 AS `id_zadch`,
  1 AS `nazv`,
  1 AS `id_stat`,
  1 AS `status_name`,
  1 AS `opisanie`,
  1 AS `init_id`,
  1 AS `init_familiya`,
  1 AS `init_imya`,
  1 AS `init_otchestvo`,
  1 AS `init_phone`,
  1 AS `init_telegram`,
  1 AS `init_email`,
  1 AS `init_otdel`,
  1 AS `init_dolzhnost`,
  1 AS `isp_id`,
  1 AS `isp_familiya`,
  1 AS `isp_imya`,
  1 AS `isp_otchestvo`,
  1 AS `isp_phone`,
  1 AS `isp_telegram`,
  1 AS `isp_email`,
  1 AS `isp_otdel`,
  1 AS `isp_dolzhnost`,
  1 AS `data_naz`,
  1 AS `data_dedl`,
  1 AS `task_file`,
  1 AS `comment_text`,
  1 AS `comment_file`,
  1 AS `zayavka_id`,
  1 AS `zayavka_status`,
  1 AS `zayavka_soderzh`,
  1 AS `zayavka_date`,
  1 AS `client_id`,
  1 AS `client_familiya`,
  1 AS `client_imya`,
  1 AS `client_otchestvo`,
  1 AS `client_telegram`,
  1 AS `client_organiz`,
  1 AS `client_email`,
  1 AS `client_phone` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `zadachi_full_view`
--

DROP TABLE IF EXISTS `zadachi_full_view`;
/*!50001 DROP VIEW IF EXISTS `zadachi_full_view`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8mb4;
/*!50001 CREATE VIEW `zadachi_full_view` AS SELECT
 1 AS `id_zadch`,
  1 AS `nazv`,
  1 AS `id_stat`,
  1 AS `status_name`,
  1 AS `opisanie`,
  1 AS `init_id_use`,
  1 AS `init_Familiya`,
  1 AS `init_Imya`,
  1 AS `init_Otchestvo`,
  1 AS `init_phone`,
  1 AS `init_telegramID`,
  1 AS `init_email`,
  1 AS `init_otdel_id`,
  1 AS `init_otdel_name`,
  1 AS `isp_id_use`,
  1 AS `isp_Familiya`,
  1 AS `isp_Imya`,
  1 AS `isp_Otchestvo`,
  1 AS `isp_phone`,
  1 AS `isp_telegramID`,
  1 AS `isp_email`,
  1 AS `isp_otdel_id`,
  1 AS `isp_otdel_name`,
  1 AS `data_naz`,
  1 AS `data_dedl`,
  1 AS `zayavka_id`,
  1 AS `client_Familiya`,
  1 AS `client_Imya`,
  1 AS `client_Otchestvo`,
  1 AS `client_telegramid`,
  1 AS `client_organizs`,
  1 AS `client_email`,
  1 AS `client_phone` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `zayavki`
--

DROP TABLE IF EXISTS `zayavki`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `zayavki` (
  `id_zay` int(11) NOT NULL AUTO_INCREMENT,
  `date_reg` longtext NOT NULL,
  `status_id` int(11) NOT NULL,
  `cli_id` int(11) NOT NULL,
  `sot_id` int(11) DEFAULT NULL,
  `istoch` longtext NOT NULL,
  `soderzh` longtext NOT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `dedlain` longtext NOT NULL,
  `file` longblob DEFAULT NULL,
  PRIMARY KEY (`id_zay`),
  KEY `status_id` (`status_id`,`cli_id`,`sot_id`),
  KEY `sot_id` (`sot_id`),
  KEY `cli_id` (`cli_id`),
  KEY `comment_id` (`comment_id`),
  CONSTRAINT `zayavki_ibfk_1` FOREIGN KEY (`status_id`) REFERENCES `status` (`id_stat`),
  CONSTRAINT `zayavki_ibfk_2` FOREIGN KEY (`sot_id`) REFERENCES `userscr` (`id_use`),
  CONSTRAINT `zayavki_ibfk_3` FOREIGN KEY (`cli_id`) REFERENCES `clientsc` (`id_cli`),
  CONSTRAINT `zayavki_ibfk_4` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id_com`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `zayavki`
--

LOCK TABLES `zayavki` WRITE;
/*!40000 ALTER TABLE `zayavki` DISABLE KEYS */;
/*!40000 ALTER TABLE `zayavki` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'infong7e_crm'
--

--
-- Final view structure for view `clients_info`
--

/*!50001 DROP VIEW IF EXISTS `clients_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `clients_info` AS select `c`.`id_cli` AS `id_cli`,`c`.`Familiya` AS `Familiya`,`c`.`Imya` AS `Imya`,`c`.`Otchestv` AS `Otchestv`,`c`.`telegramid` AS `telegramid`,`c`.`organizs` AS `organizs`,`c`.`emailsc` AS `emailsc`,`c`.`phone` AS `phone` from `clientsc` `c` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `full_zayavki_view`
--

/*!50001 DROP VIEW IF EXISTS `full_zayavki_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `full_zayavki_view` AS select `z`.`id_zay` AS `id_zay`,`z`.`date_reg` AS `date_reg`,`s`.`name_stat` AS `status`,`s`.`id_stat` AS `id_stat`,`c`.`id_cli` AS `id_cli`,`c`.`Familiya` AS `client_familiya`,`c`.`Imya` AS `client_imya`,`c`.`Otchestv` AS `client_otchestvo`,`c`.`telegramid` AS `client_telegram`,`c`.`organizs` AS `client_organization`,`c`.`emailsc` AS `client_email`,`c`.`phone` AS `client_phone`,`u`.`id_use` AS `id_use`,`u`.`Familiya` AS `sotrudnik_familiya`,`u`.`Imya` AS `sotrudnik_imya`,`u`.`Otchestvo` AS `sotrudnik_otchestvo`,`u`.`phone` AS `sotrudnik_phone`,`o1`.`name_otd` AS `sotrudnik_otdel`,`d`.`namedolzh` AS `dolzhnost`,`u`.`telegramID` AS `sotrudnik_telegram`,`u`.`mailc` AS `sotrudnik_email`,`z`.`istoch` AS `istoch`,`z`.`file` AS `file`,`cm`.`name_com` AS `comment`,`cm`.`docx` AS `comment_document`,`z`.`dedlain` AS `dedlain`,`z`.`soderzh` AS `soderzh`,`u`.`otdel_id` AS `otdel_zayav`,`o1`.`name_otd` AS `otdel_zayav_name` from ((((((`zayavki` `z` left join `userscr` `u` on(`z`.`sot_id` = `u`.`id_use`)) left join `status` `s` on(`z`.`status_id` = `s`.`id_stat`)) left join `otdeli` `o1` on(`u`.`otdel_id` = `o1`.`id_otd`)) left join `dolzh` `d` on(`u`.`dolzh_id` = `d`.`id_dolzh`)) left join `comments` `cm` on(`z`.`comment_id` = `cm`.`id_com`)) left join `clientsc` `c` on(`z`.`cli_id` = `c`.`id_cli`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `user_info`
--

/*!50001 DROP VIEW IF EXISTS `user_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `user_info` AS select `u`.`id_use` AS `id_use`,`u`.`Familiya` AS `Familiya`,`u`.`Imya` AS `Imya`,`u`.`Otchestvo` AS `Otchestvo`,`u`.`phone` AS `phone`,`o`.`name_otd` AS `name_otd`,`o`.`id_otd` AS `id_otd`,`d`.`id_dolzh` AS `id_dolzh`,`d`.`namedolzh` AS `namedolzh`,`u`.`telegramID` AS `telegramID`,`u`.`mailc` AS `mailc`,`u`.`admin` AS `admin` from ((`userscr` `u` left join `otdeli` `o` on(`u`.`otdel_id` = `o`.`id_otd`)) left join `dolzh` `d` on(`u`.`dolzh_id` = `d`.`id_dolzh`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `zadachi_detailed_view`
--

/*!50001 DROP VIEW IF EXISTS `zadachi_detailed_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `zadachi_detailed_view` AS select `z`.`id_zadch` AS `id_zadch`,`z`.`nazv` AS `nazv`,`s`.`id_stat` AS `id_stat`,`s`.`name_stat` AS `status_name`,`z`.`opisanie` AS `opisanie`,`u_init`.`id_use` AS `init_id`,`u_init`.`Familiya` AS `init_familiya`,`u_init`.`Imya` AS `init_imya`,`u_init`.`Otchestvo` AS `init_otchestvo`,`u_init`.`phone` AS `init_phone`,`u_init`.`telegramID` AS `init_telegram`,`u_init`.`mailc` AS `init_email`,`o_init`.`name_otd` AS `init_otdel`,`d_init`.`namedolzh` AS `init_dolzhnost`,`u_isp`.`id_use` AS `isp_id`,`u_isp`.`Familiya` AS `isp_familiya`,`u_isp`.`Imya` AS `isp_imya`,`u_isp`.`Otchestvo` AS `isp_otchestvo`,`u_isp`.`phone` AS `isp_phone`,`u_isp`.`telegramID` AS `isp_telegram`,`u_isp`.`mailc` AS `isp_email`,`o_isp`.`name_otd` AS `isp_otdel`,`d_isp`.`namedolzh` AS `isp_dolzhnost`,`z`.`data_naz` AS `data_naz`,`z`.`data_dedl` AS `data_dedl`,`z`.`file` AS `task_file`,`cm`.`name_com` AS `comment_text`,`cm`.`docx` AS `comment_file`,`za`.`id_zay` AS `zayavka_id`,`fz`.`status` AS `zayavka_status`,`fz`.`soderzh` AS `zayavka_soderzh`,`fz`.`date_reg` AS `zayavka_date`,`c`.`id_cli` AS `client_id`,`c`.`Familiya` AS `client_familiya`,`c`.`Imya` AS `client_imya`,`c`.`Otchestv` AS `client_otchestvo`,`c`.`telegramid` AS `client_telegram`,`c`.`organizs` AS `client_organiz`,`c`.`emailsc` AS `client_email`,`c`.`phone` AS `client_phone` from (((((((((((`zadachi` `z` left join `status` `s` on(`z`.`status_id` = `s`.`id_stat`)) left join `userscr` `u_init` on(`z`.`iniciat_id` = `u_init`.`id_use`)) left join `otdeli` `o_init` on(`u_init`.`otdel_id` = `o_init`.`id_otd`)) left join `dolzh` `d_init` on(`u_init`.`dolzh_id` = `d_init`.`id_dolzh`)) left join `userscr` `u_isp` on(`z`.`ispolnit_id` = `u_isp`.`id_use`)) left join `otdeli` `o_isp` on(`u_isp`.`otdel_id` = `o_isp`.`id_otd`)) left join `dolzh` `d_isp` on(`u_isp`.`dolzh_id` = `d_isp`.`id_dolzh`)) left join `comments_zadach` `cm` on(`z`.`comm_id` = `cm`.`id_com`)) left join `zayavki` `za` on(`z`.`zayav_id` = `za`.`id_zay`)) left join `full_zayavki_view` `fz` on(`za`.`id_zay` = `fz`.`id_zay`)) left join `clientsc` `c` on(`z`.`client_id` = `c`.`id_cli`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `zadachi_full_view`
--

/*!50001 DROP VIEW IF EXISTS `zadachi_full_view`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `zadachi_full_view` AS select `z`.`id_zadch` AS `id_zadch`,`z`.`nazv` AS `nazv`,`s`.`id_stat` AS `id_stat`,`s`.`name_stat` AS `status_name`,`z`.`opisanie` AS `opisanie`,`u_init`.`id_use` AS `init_id_use`,`u_init`.`Familiya` AS `init_Familiya`,`u_init`.`Imya` AS `init_Imya`,`u_init`.`Otchestvo` AS `init_Otchestvo`,`u_init`.`phone` AS `init_phone`,`u_init`.`telegramID` AS `init_telegramID`,`u_init`.`mailc` AS `init_email`,`o_init`.`id_otd` AS `init_otdel_id`,`o_init`.`name_otd` AS `init_otdel_name`,`u_isp`.`id_use` AS `isp_id_use`,`u_isp`.`Familiya` AS `isp_Familiya`,`u_isp`.`Imya` AS `isp_Imya`,`u_isp`.`Otchestvo` AS `isp_Otchestvo`,`u_isp`.`phone` AS `isp_phone`,`u_isp`.`telegramID` AS `isp_telegramID`,`u_isp`.`mailc` AS `isp_email`,`o_isp`.`id_otd` AS `isp_otdel_id`,`o_isp`.`name_otd` AS `isp_otdel_name`,`z`.`data_naz` AS `data_naz`,`z`.`data_dedl` AS `data_dedl`,`za`.`id_zay` AS `zayavka_id`,`c`.`Familiya` AS `client_Familiya`,`c`.`Imya` AS `client_Imya`,`c`.`Otchestv` AS `client_Otchestvo`,`c`.`telegramid` AS `client_telegramid`,`c`.`organizs` AS `client_organizs`,`c`.`emailsc` AS `client_email`,`c`.`phone` AS `client_phone` from (((((((`zadachi` `z` left join `status` `s` on(`z`.`status_id` = `s`.`id_stat`)) left join `userscr` `u_init` on(`z`.`iniciat_id` = `u_init`.`id_use`)) left join `otdeli` `o_init` on(`u_init`.`otdel_id` = `o_init`.`id_otd`)) left join `userscr` `u_isp` on(`z`.`ispolnit_id` = `u_isp`.`id_use`)) left join `otdeli` `o_isp` on(`u_isp`.`otdel_id` = `o_isp`.`id_otd`)) left join `clientsc` `c` on(`z`.`client_id` = `c`.`id_cli`)) left join `zayavki` `za` on(`z`.`zayav_id` = `za`.`id_zay`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-20 12:00:31

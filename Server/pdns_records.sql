CREATE DATABASE  IF NOT EXISTS `pdns` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `pdns`;
-- MySQL dump 10.13  Distrib 5.6.19, for osx10.7 (i386)
--
-- Host: eticaretal.cyvxrrucekbo.eu-central-1.rds.amazonaws.com    Database: pdns
-- ------------------------------------------------------
-- Server version	5.6.23-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `cryptokeys`
--

LOCK TABLES `cryptokeys` WRITE;
/*!40000 ALTER TABLE `cryptokeys` DISABLE KEYS */;
/*!40000 ALTER TABLE `cryptokeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `domainmetadata`
--

LOCK TABLES `domainmetadata` WRITE;
/*!40000 ALTER TABLE `domainmetadata` DISABLE KEYS */;
/*!40000 ALTER TABLE `domainmetadata` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `domains`
--

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
REPLACE INTO `domains` VALUES (2,'in-addr.arpa','',NULL,'MASTER',NULL,NULL),(3,'test.net',NULL,NULL,'MASTER',NULL,NULL);
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `perm_items`
--

LOCK TABLES `perm_items` WRITE;
/*!40000 ALTER TABLE `perm_items` DISABLE KEYS */;
REPLACE INTO `perm_items` VALUES (41,'zone_master_add','User is allowed to add new master zones.'),(42,'zone_slave_add','User is allowed to add new slave zones.'),(43,'zone_content_view_own','User is allowed to see the content and meta data of zones he owns.'),(44,'zone_content_edit_own','User is allowed to edit the content of zones he owns.'),(45,'zone_meta_edit_own','User is allowed to edit the meta data of zones he owns.'),(46,'zone_content_view_others','User is allowed to see the content and meta data of zones he does not own.'),(47,'zone_content_edit_others','User is allowed to edit the content of zones he does not own.'),(48,'zone_meta_edit_others','User is allowed to edit the meta data of zones he does not own.'),(49,'search','User is allowed to perform searches.'),(50,'supermaster_view','User is allowed to view supermasters.'),(51,'supermaster_add','User is allowed to add new supermasters.'),(52,'supermaster_edit','User is allowed to edit supermasters.'),(53,'user_is_ueberuser','User has full access. God-like. Redeemer.'),(54,'user_view_others','User is allowed to see other users and their details.'),(55,'user_add_new','User is allowed to add new users.'),(56,'user_edit_own','User is allowed to edit their own details.'),(57,'user_edit_others','User is allowed to edit other users.'),(58,'user_passwd_edit_others','User is allowed to edit the password of other users.'),(59,'user_edit_templ_perm','User is allowed to change the permission template that is assigned to a user.'),(60,'templ_perm_add','User is allowed to add new permission templates.'),(61,'templ_perm_edit','User is allowed to edit existing permission templates.');
/*!40000 ALTER TABLE `perm_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `perm_templ`
--

LOCK TABLES `perm_templ` WRITE;
/*!40000 ALTER TABLE `perm_templ` DISABLE KEYS */;
REPLACE INTO `perm_templ` VALUES (1,'Administrator','Administrator template with full rights.');
/*!40000 ALTER TABLE `perm_templ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `perm_templ_items`
--

LOCK TABLES `perm_templ_items` WRITE;
/*!40000 ALTER TABLE `perm_templ_items` DISABLE KEYS */;
REPLACE INTO `perm_templ_items` VALUES (1,1,53);
/*!40000 ALTER TABLE `perm_templ_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `records`
--

LOCK TABLES `records` WRITE;
/*!40000 ALTER TABLE `records` DISABLE KEYS */;
REPLACE INTO `records`
VALUES (9,2,'4.3.2.1.in-addr.arpa','PTR','test.net',86400,0,1437320594,NULL,NULL),
(10,3,'mail.test.net','A','1.2.3.4',86400,0,1437549715,'mail',1),
(11,3,'www.test.net','CNAME','test.net',86400,0,1437549715,'www',1),
(12,3,'test.net','SOA','dns1.test.net server_name.com 2015072205 10800 3600 604800 600',86400,0,1437492462,'',1),
(13,3,'test.net','NS','dns1.test.net',86400,0,1437549715,'',1),
(14,3,'test.net','NS','dns2.test.net',86400,0,1437549715,'',1),
(15,3,'test.net','A','1.2.3.4',86400,0,1437549715,'',1),
(16,3,'test.net','MX','server_name.com',86400,10,1437549715,'',1),
(17,3,'test.net','SPF','\"v=spf1 a mx ptr ip4:1.2.3.4 mx:server_name.com include:server_name.com -all\"',86400,0,1437549715,'',1),
(18,3,'test.net','TXT','\"v=spf1 a mx ptr ip4:1.2.3.4 mx:server_name.com include:server_name.com -all\"',86400,0,1437549715,'',1),
(22,3,'server1.test.net','A','1.2.3.4',86400,0,1437549715,'server1',1),
(24,3,'dkim._domainkey.e-ticaretal.net','TXT','\"v=DKIM1; k=rsa; p=privateKeyStr"',86400,0,1437549715,'_domainkey test',1);
/*!40000 ALTER TABLE `records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `records_zone_templ`
--

LOCK TABLES `records_zone_templ` WRITE;
/*!40000 ALTER TABLE `records_zone_templ` DISABLE KEYS */;
REPLACE INTO `records_zone_templ` VALUES (3,10,1),(3,11,1),(3,12,1),(3,13,1),(3,14,1),(3,15,1),(3,16,1),(3,17,1),(3,18,1);
/*!40000 ALTER TABLE `records_zone_templ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `supermasters`
--

LOCK TABLES `supermasters` WRITE;
/*!40000 ALTER TABLE `supermasters` DISABLE KEYS */;
REPLACE INTO `supermasters` VALUES ('1.2.3.4','dns1.test.net','admin'),
('1.2.3.4','dns2.test.net','admin');
/*!40000 ALTER TABLE `supermasters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `tsigkeys`
--

LOCK TABLES `tsigkeys` WRITE;
/*!40000 ALTER TABLE `tsigkeys` DISABLE KEYS */;
/*!40000 ALTER TABLE `tsigkeys` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
REPLACE INTO `users` VALUES (1,'admin','','Administrator','admin@example.net','Administrator with full rights.',1,1,0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `zone_templ`
--

LOCK TABLES `zone_templ` WRITE;
/*!40000 ALTER TABLE `zone_templ` DISABLE KEYS */;
REPLACE INTO `zone_templ` VALUES (1,'server1','server1',1);
/*!40000 ALTER TABLE `zone_templ` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `zone_templ_records`
--

LOCK TABLES `zone_templ_records` WRITE;
/*!40000 ALTER TABLE `zone_templ_records` DISABLE KEYS */;
REPLACE INTO `zone_templ_records` VALUES (1,1,'[ZONE]','SOA','dns1.test.net server_name.com [SERIAL] 10800 3600 604800 600',86400,0),
(2,1,'[ZONE]','NS','dns1.test.net',86400,0),
(3,1,'[ZONE]','NS','dns2.test.net',86400,0),
(4,1,'[ZONE]','A','1.2.3.4',86400,0),
(6,1,'www.[ZONE]','CNAME','[ZONE]',86400,0),
(7,1,'[ZONE]','MX','server_name.com',86400,10),
(8,1,'[ZONE]','SPF','\"v=spf1 a mx ptr ip4:1.2.3.4 mx:server_name.com include:server_name.com -all\"',86400,0),
(8,1,'[ZONE]','TXT','\"v=spf1 a mx ptr ip4:1.2.3.4 mx:server_name.com include:server_name.com -all\"',86400,0),
(10,1,'dkim._domainkey.[ZONE]','TXT','v=DKIM1; k=rsa; t=y; p=someKey',86400,0);
/*!40000 ALTER TABLE `zone_templ_records` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `zones`
--

LOCK TABLES `zones` WRITE;
/*!40000 ALTER TABLE `zones` DISABLE KEYS */;
REPLACE INTO `zones` VALUES (2,2,1,'',0),(3,3,1,'test.net server 1.',1);
/*!40000 ALTER TABLE `zones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'pdns'
--

--
-- Dumping routines for database 'pdns'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-07-22 10:26:24

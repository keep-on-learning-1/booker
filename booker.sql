-- MySQL dump 10.13  Distrib 5.5.38, for Win32 (x86)
--
-- Host: localhost    Database: nbb
-- ------------------------------------------------------
-- Server version	5.5.38-log

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
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `employees` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'employee 0','mail-0@domain.any'),(2,'employee 1','mail-1@domain.any'),(3,'employee 2','mail-2@domain.any'),(4,'employee 3','mail-3@domain.any'),(5,'employee 4','mail-4@domain.any'),(6,'employee 5','mail-5@domain.any'),(7,'employee 6','mail-6@domain.any'),(8,'employee 7','mail-7@domain.any'),(9,'employee 8','mail-8@domain.any'),(10,'employee 9','mail-9@domain.any'),(11,'employee 10','mail-10@domain.any'),(12,'employee 11','mail-11@domain.any'),(13,'employee 12','mail-12@domain.any'),(14,'employee 13','mail-13@domain.any'),(15,'employee 14','mail-14@domain.any'),(16,'employee 15','mail-15@domain.any'),(17,'employee 16','mail-16@domain.any'),(18,'employee 17','mail-17@domain.any'),(19,'employee 18','mail-18@domain.any'),(20,'employee 19','mail-19@domain.any');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recurring` tinyint(4) NOT NULL DEFAULT '0',
  `employee_id` int(11) NOT NULL DEFAULT '0',
  `specifics` varchar(50) DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES (1,0,4,'test','2016-07-18 05:21:31'),(2,0,15,'test','2016-07-18 05:21:31'),(3,0,1,'test','2016-07-18 05:21:31'),(4,0,4,'test','2016-07-18 05:21:31'),(5,0,8,'test','2016-07-18 05:21:31'),(6,0,15,'test','2016-07-18 05:21:31'),(7,0,9,'test','2016-07-18 05:21:31'),(8,0,4,'test','2016-07-18 05:21:31'),(9,0,20,'test','2016-07-18 05:21:31'),(10,0,2,'test','2016-07-18 05:21:31'),(11,0,7,'test','2016-07-18 05:21:31'),(12,0,9,'test','2016-07-18 05:21:31'),(13,0,14,'test','2016-07-18 05:21:31'),(14,0,9,'test','2016-07-18 05:21:31'),(15,0,3,'test','2016-07-18 05:21:31'),(16,0,8,'test','2016-07-18 05:21:31'),(17,0,10,'test','2016-07-18 05:21:31'),(18,0,2,'test','2016-07-18 05:21:31'),(19,0,14,'test','2016-07-18 05:21:31'),(20,0,20,'test','2016-07-18 05:21:31'),(21,0,20,'test','2016-07-18 05:21:31'),(22,0,14,'test','2016-07-18 05:21:31'),(23,1,7,'test','2016-07-18 05:21:31'),(24,1,16,'test','2016-07-18 05:21:31'),(25,1,7,'test','2016-07-18 05:21:31'),(26,1,11,'test','2016-07-18 05:21:31');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `times`
--

DROP TABLE IF EXISTS `times`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `times` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `event_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `times`
--

LOCK TABLES `times` WRITE;
/*!40000 ALTER TABLE `times` DISABLE KEYS */;
INSERT INTO `times` VALUES (1,'2016-07-01 03:00:00','2016-07-01 04:00:00',1),(2,'2016-07-01 05:00:00','2016-07-01 06:00:00',2),(3,'2016-07-01 08:00:00','2016-07-01 09:00:00',3),(4,'2016-07-02 03:00:00','2016-07-02 05:00:00',4),(5,'2016-07-03 07:00:00','2016-07-03 08:00:00',5),(6,'2016-07-04 05:00:00','2016-07-04 06:00:00',6),(7,'2016-07-05 07:00:00','2016-07-05 08:00:00',7),(8,'2016-07-06 08:30:00','2016-07-06 09:45:00',8),(9,'2016-07-07 08:00:00','2016-07-07 08:30:00',9),(10,'2016-07-08 10:00:00','2016-07-08 10:30:00',10),(11,'2016-07-10 07:00:00','2016-07-10 08:00:00',11),(12,'2016-07-11 05:00:00','2016-07-11 06:00:00',12),(13,'2016-07-13 03:00:00','2016-07-13 04:00:00',13),(14,'2016-07-14 03:00:00','2016-07-14 04:00:00',14),(15,'2016-07-16 03:00:00','2016-07-16 05:00:00',15),(16,'2016-07-17 05:00:00','2016-07-17 06:00:00',16),(17,'2016-07-19 09:00:00','2016-07-19 11:00:00',17),(18,'2016-07-20 08:00:00','2016-07-20 09:00:00',18),(19,'2016-07-20 10:00:00','2016-07-20 10:30:00',19),(20,'2016-07-20 11:00:00','2016-07-20 12:00:00',20),(21,'2016-07-23 13:00:00','2016-07-23 14:00:00',21),(22,'2016-07-25 04:00:00','2016-07-25 05:00:00',22),(23,'2016-07-12 04:00:00','2016-07-12 05:00:00',23),(24,'2016-08-12 04:00:00','2016-08-12 05:00:00',23),(25,'2016-09-12 04:00:00','2016-09-12 05:00:00',23),(26,'2016-07-18 04:00:00','2016-07-18 05:00:00',24),(27,'2016-08-18 04:00:00','2016-08-18 05:00:00',24),(28,'2016-09-18 04:00:00','2016-09-18 05:00:00',24),(29,'2016-07-21 04:00:00','2016-07-21 05:00:00',25),(30,'2016-07-28 04:00:00','2016-07-28 05:00:00',25),(31,'2016-08-04 04:00:00','2016-08-04 05:00:00',25),(32,'2016-08-11 04:00:00','2016-08-11 05:00:00',25),(33,'2016-08-18 04:00:00','2016-08-18 05:00:00',25),(34,'2016-07-26 04:00:00','2016-07-26 05:00:00',26),(35,'2016-08-02 04:00:00','2016-08-02 05:00:00',26),(36,'2016-08-09 04:00:00','2016-08-09 05:00:00',26),(37,'2016-08-16 04:00:00','2016-08-16 05:00:00',26),(38,'2016-08-23 04:00:00','2016-08-23 05:00:00',26);
/*!40000 ALTER TABLE `times` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'admin','c4ca4238a0b923820dcc509a6f75849b');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-07-18  8:24:04

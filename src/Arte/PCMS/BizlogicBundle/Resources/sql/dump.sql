-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: pcms
-- ------------------------------------------------------
-- Server version	5.5.16-log

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
-- Table structure for table `TBCustomer`
--

DROP TABLE IF EXISTS `TBCustomer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBCustomer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBCustomer`
--

LOCK TABLES `TBCustomer` WRITE;
/*!40000 ALTER TABLE `TBCustomer` DISABLE KEYS */;
INSERT INTO `TBCustomer` VALUES (1,'顧客1',0),(2,'顧客2',1),(3,'顧客3',0);
/*!40000 ALTER TABLE `TBCustomer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TBDepartment`
--

DROP TABLE IF EXISTS `TBDepartment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBDepartment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SortNo` int(11) NOT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBDepartment`
--

LOCK TABLES `TBDepartment` WRITE;
/*!40000 ALTER TABLE `TBDepartment` DISABLE KEYS */;
INSERT INTO `TBDepartment` VALUES (1,'部署01',1,0),(2,'部署02',2,1),(3,'部署03',3,0),(4,'部署04',4,0),(5,'部署05',5,0);
/*!40000 ALTER TABLE `TBDepartment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TBProductionCost`
--

DROP TABLE IF EXISTS `TBProductionCost`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBProductionCost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectCostMasterId` int(11) NOT NULL,
  `SystemUserId` int(11) NOT NULL,
  `WorkDate` date NOT NULL,
  `Cost` int(11) NOT NULL,
  `Note` longtext COLLATE utf8_unicode_ci,
  `DeleteFlag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_D08CE457D1971DCD` (`ProjectCostMasterId`),
  KEY `IDX_D08CE457A5DCE404` (`SystemUserId`),
  CONSTRAINT `FK_D08CE457A5DCE404` FOREIGN KEY (`SystemUserId`) REFERENCES `TBSystemUser` (`id`),
  CONSTRAINT `FK_D08CE457D1971DCD` FOREIGN KEY (`ProjectCostMasterId`) REFERENCES `TBProjectCostMaster` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBProductionCost`
--

LOCK TABLES `TBProductionCost` WRITE;
/*!40000 ALTER TABLE `TBProductionCost` DISABLE KEYS */;
INSERT INTO `TBProductionCost` VALUES (1,1,1,'2013-08-01',480,'備考1',0),(2,1,1,'2013-08-02',120,'備考2',0),(3,3,1,'2013-08-02',300,'備考3',0),(4,3,2,'2013-08-02',480,'備考4',0);
/*!40000 ALTER TABLE `TBProductionCost` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TBProjectCostHierarchyMaster`
--

DROP TABLE IF EXISTS `TBProjectCostHierarchyMaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBProjectCostHierarchyMaster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `TBProjectMasterId` int(11) NOT NULL,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Path` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SortNo` int(11) NOT NULL,
  `DeleteFlag` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_BBCFA335AF18B565` (`TBProjectMasterId`),
  CONSTRAINT `FK_BBCFA335AF18B565` FOREIGN KEY (`TBProjectMasterId`) REFERENCES `TBProjectMaster` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBProjectCostHierarchyMaster`
--

LOCK TABLES `TBProjectCostHierarchyMaster` WRITE;
/*!40000 ALTER TABLE `TBProjectCostHierarchyMaster` DISABLE KEYS */;
INSERT INTO `TBProjectCostHierarchyMaster` VALUES (1,1,'root','\\',0,0),(2,1,'製造','\\1\\',2,0),(3,1,'管理側','\\1\\2\\',2,0),(4,1,'一般側','\\1\\2\\',1,0);
/*!40000 ALTER TABLE `TBProjectCostHierarchyMaster` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TBProjectCostMaster`
--

DROP TABLE IF EXISTS `TBProjectCostMaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBProjectCostMaster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ProjectMasterId` int(11) NOT NULL,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Cost` int(11) DEFAULT NULL,
  `SortNo` int(11) DEFAULT NULL,
  `DeleteFlag` tinyint(1) NOT NULL,
  `HierarchyPath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TBProjectCostHierarchyMasterId` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_DDDDD06921E54FE0` (`ProjectMasterId`),
  KEY `IDX_DDDDD069C753C8D9` (`TBProjectCostHierarchyMasterId`),
  CONSTRAINT `FK_DDDDD069C753C8D9` FOREIGN KEY (`TBProjectCostHierarchyMasterId`) REFERENCES `TBProjectCostHierarchyMaster` (`id`),
  CONSTRAINT `FK_DDDDD06921E54FE0` FOREIGN KEY (`ProjectMasterId`) REFERENCES `TBProjectMaster` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBProjectCostMaster`
--

LOCK TABLES `TBProjectCostMaster` WRITE;
/*!40000 ALTER TABLE `TBProjectCostMaster` DISABLE KEYS */;
INSERT INTO `TBProjectCostMaster` VALUES (1,1,'要件定義',1440,4,0,'',1),(2,1,'設計',2400,3,0,'',1),(3,1,'ユーザー管理',2880,1,0,'',3),(4,1,'顧客管理',1920,2,0,'',3),(5,1,'ログイン',960,2,0,'',4),(6,1,'申請管理',4320,1,0,'',4),(7,1,'納品',480,1,0,'',1);
/*!40000 ALTER TABLE `TBProjectCostMaster` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TBProjectMaster`
--

DROP TABLE IF EXISTS `TBProjectMaster`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBProjectMaster` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Status` int(11) NOT NULL,
  `Explanation` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CustomerId` int(11) NOT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  `PeriodStart` date DEFAULT NULL,
  `PeriodEnd` date DEFAULT NULL,
  `ManagerId` int(11) DEFAULT NULL,
  `EstimateFilePath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ScheduleFilePath` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_7C23FF7FBE22D475` (`CustomerId`),
  KEY `IDX_7C23FF7FD64FFAC3` (`ManagerId`),
  CONSTRAINT `FK_7C23FF7FD64FFAC3` FOREIGN KEY (`ManagerId`) REFERENCES `TBSystemUser` (`id`),
  CONSTRAINT `FK_7C23FF7FBE22D475` FOREIGN KEY (`CustomerId`) REFERENCES `TBCustomer` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBProjectMaster`
--

LOCK TABLES `TBProjectMaster` WRITE;
/*!40000 ALTER TABLE `TBProjectMaster` DISABLE KEYS */;
INSERT INTO `TBProjectMaster` VALUES (1,'案件3',1,'案件3説明',1,0,'2013-08-01','2013-08-05',1,'estimate3','schedule3');
/*!40000 ALTER TABLE `TBProjectMaster` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TBProjectUser`
--

DROP TABLE IF EXISTS `TBProjectUser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBProjectUser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SystemUserId` int(11) NOT NULL,
  `ProjectMasterId` int(11) NOT NULL,
  `RoleNo` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_5B5B65FA5DCE404` (`SystemUserId`),
  KEY `IDX_5B5B65F21E54FE0` (`ProjectMasterId`),
  CONSTRAINT `FK_5B5B65F21E54FE0` FOREIGN KEY (`ProjectMasterId`) REFERENCES `TBProjectMaster` (`id`),
  CONSTRAINT `FK_5B5B65FA5DCE404` FOREIGN KEY (`SystemUserId`) REFERENCES `TBSystemUser` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBProjectUser`
--

LOCK TABLES `TBProjectUser` WRITE;
/*!40000 ALTER TABLE `TBProjectUser` DISABLE KEYS */;
/*!40000 ALTER TABLE `TBProjectUser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TBSystemUser`
--

DROP TABLE IF EXISTS `TBSystemUser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TBSystemUser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Salt` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Password` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `Active` tinyint(1) NOT NULL,
  `SystemRoleId` int(11) DEFAULT NULL,
  `DisplayName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `DisplayNameKana` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `NickName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `MailAddress` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `DepartmentId` int(11) DEFAULT NULL,
  `LastLoginDatetime` datetime DEFAULT NULL,
  `DeleteFlag` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_CB29A6C224EDF738` (`DepartmentId`),
  KEY `IX_TBSystemUser` (`Username`),
  CONSTRAINT `FK_CB29A6C224EDF738` FOREIGN KEY (`DepartmentId`) REFERENCES `TBDepartment` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TBSystemUser`
--

LOCK TABLES `TBSystemUser` WRITE;
/*!40000 ALTER TABLE `TBSystemUser` DISABLE KEYS */;
INSERT INTO `TBSystemUser` VALUES (1,'test001',NULL,'a',1,1,'てすと001','テスト001','t001','test001@test.com',1,'2012-07-01 00:00:00',0),(2,'test002',NULL,'a',1,1,'てすと002','テスト002','t002','test002@test.com',1,'2012-07-02 00:00:00',1),(3,'test003',NULL,'a',0,1,'てすと003','テスト003','t003','test003@test.com',1,'2012-07-03 00:00:00',0),(4,'test004',NULL,'a',1,1,'てすと004','テスト004','t004','test004@test.com',1,'2013-07-04 00:00:00',0),(5,'test005',NULL,'a',1,1,'てすと005','テスト005','t005','test005@test.com',1,'2013-07-05 00:00:00',0),(6,'test006',NULL,'a',1,1,'てすと006','テスト006','t006','test006@test.com',1,'2013-07-06 00:00:00',0),(7,'test007',NULL,'a',1,1,'てすと007','テスト007','t007','test007@test.com',1,'2013-07-07 00:00:00',0),(8,'test008',NULL,'a',1,1,'てすと008','テスト008','t008','test008@test.com',1,'2013-07-08 00:00:00',0),(9,'test009',NULL,'a',1,1,'てすと009','テスト009','t009','test009@test.com',1,'2013-07-09 00:00:00',0),(10,'test010',NULL,'a',1,1,'てすと010','テスト010','t010','test010@test.com',1,'2013-07-10 00:00:00',0);
/*!40000 ALTER TABLE `TBSystemUser` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-09-02 14:09:14

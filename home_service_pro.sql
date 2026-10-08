-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: home_service_pro
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `Created_At` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Email_UNIQUE` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `applianceissue`
--

DROP TABLE IF EXISTS `applianceissue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applianceissue` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Issue` varchar(255) NOT NULL,
  `Details` text DEFAULT NULL,
  `Detailed_Report` text DEFAULT NULL,
  `Ticket_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_issue_ticket` (`Ticket_ID`),
  CONSTRAINT `fk_issue_ticket` FOREIGN KEY (`Ticket_ID`) REFERENCES `repairticket` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `booking_media`
--

DROP TABLE IF EXISTS `booking_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_media` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Ticket_ID` int(11) NOT NULL,
  `Media_Type` enum('image','video') NOT NULL,
  `File_Path` varchar(500) NOT NULL,
  `File_Name` varchar(255) NOT NULL,
  `Created_At` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `idx_ticket_media` (`Ticket_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calls`
--

DROP TABLE IF EXISTS `calls`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calls` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Caller_ID` int(11) NOT NULL,
  `Caller_Role` varchar(20) NOT NULL,
  `Caller_Name` varchar(100) DEFAULT '',
  `Callee_ID` int(11) NOT NULL,
  `Callee_Role` varchar(20) NOT NULL,
  `Callee_Name` varchar(100) DEFAULT '',
  `Room_Name` varchar(128) NOT NULL,
  `Room_URL` varchar(255) NOT NULL,
  `Status` enum('ringing','active','ended','declined','missed') DEFAULT 'ringing',
  `Created_At` datetime DEFAULT current_timestamp(),
  `Started_At` datetime DEFAULT NULL,
  `Ended_At` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `idx_ringing` (`Callee_ID`,`Callee_Role`,`Status`),
  KEY `idx_room` (`Room_Name`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `chat_messages`
--

DROP TABLE IF EXISTS `chat_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat_messages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Sender_ID` int(11) NOT NULL,
  `Sender_Role` enum('customer','repairman','admin') NOT NULL,
  `Receiver_ID` int(11) NOT NULL,
  `Receiver_Role` enum('customer','repairman','admin') NOT NULL,
  `Message` text NOT NULL,
  `Is_Read` tinyint(1) NOT NULL DEFAULT 0,
  `Created_At` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `idx_chat_sender` (`Sender_ID`,`Sender_Role`),
  KEY `idx_chat_receiver` (`Receiver_ID`,`Receiver_Role`),
  KEY `idx_chat_created` (`Created_At`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `clientappliances`
--

DROP TABLE IF EXISTS `clientappliances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `clientappliances` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Client_ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Type` varchar(100) NOT NULL,
  `Make` varchar(100) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL,
  `Details` text DEFAULT NULL,
  `Issue_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_appliance_issue` (`Issue_ID`),
  KEY `fk_appliance_client` (`Client_ID`),
  CONSTRAINT `fk_appliance_client` FOREIGN KEY (`Client_ID`) REFERENCES `user_clientprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_appliance_issue` FOREIGN KEY (`Issue_ID`) REFERENCES `applianceissue` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customer_reviews`
--

DROP TABLE IF EXISTS `customer_reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_reviews` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Ticket_ID` int(11) NOT NULL,
  `Customer_ID` int(11) NOT NULL,
  `Repairman_ID` int(11) NOT NULL,
  `Rating` tinyint(1) NOT NULL,
  `Comments` text DEFAULT NULL,
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Ticket_ID` (`Ticket_ID`),
  KEY `Repairman_ID` (`Repairman_ID`),
  KEY `Customer_ID` (`Customer_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `payment_qr_codes`
--

DROP TABLE IF EXISTS `payment_qr_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_qr_codes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Provider` enum('gcash','maya') NOT NULL,
  `File_Path` varchar(255) NOT NULL,
  `Updated_At` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Provider_UNIQUE` (`Provider`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `repairhistory`
--

DROP TABLE IF EXISTS `repairhistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repairhistory` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Repairman_ID` int(11) NOT NULL,
  `Repairer_ID` int(11) DEFAULT NULL,
  `Schedule_ID` int(11) NOT NULL,
  `Ticket_ID` int(11) NOT NULL,
  `ClientAppliances_ID` int(11) NOT NULL,
  `Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_history_repairman` (`Repairman_ID`),
  KEY `fk_history_schedule` (`Schedule_ID`),
  KEY `fk_history_ticket` (`Ticket_ID`),
  KEY `fk_history_appliance` (`ClientAppliances_ID`),
  KEY `idx_repair_history_date` (`Date`),
  CONSTRAINT `fk_history_appliance` FOREIGN KEY (`ClientAppliances_ID`) REFERENCES `clientappliances` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_history_repairman` FOREIGN KEY (`Repairman_ID`) REFERENCES `user_repairmanprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_history_schedule` FOREIGN KEY (`Schedule_ID`) REFERENCES `repairschedule` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_history_ticket` FOREIGN KEY (`Ticket_ID`) REFERENCES `repairticket` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `repairman_subscription_payments`
--

DROP TABLE IF EXISTS `repairman_subscription_payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repairman_subscription_payments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Repairman_ID` int(11) NOT NULL,
  `Plan_ID` int(11) NOT NULL,
  `Plan_Name` varchar(100) NOT NULL,
  `Duration_Months` tinyint(3) unsigned NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `Payment_Method` enum('gcash','maya') NOT NULL,
  `Reference_No` varchar(100) DEFAULT NULL,
  `Proof_Path` varchar(255) NOT NULL,
  `Status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `Admin_Note` varchar(255) DEFAULT NULL,
  `Submitted_At` datetime NOT NULL DEFAULT current_timestamp(),
  `Reviewed_At` datetime DEFAULT NULL,
  `Reviewed_By` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Repairman_ID_IDX` (`Repairman_ID`),
  KEY `Status_IDX` (`Status`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `repairschedule`
--

DROP TABLE IF EXISTS `repairschedule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repairschedule` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Client_ID` int(11) NOT NULL,
  `Date_Time` datetime NOT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Scheduled',
  `Repairman_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_schedule_client` (`Client_ID`),
  KEY `fk_schedule_repairman` (`Repairman_ID`),
  KEY `idx_repair_schedule_status` (`Status`),
  KEY `idx_repair_schedule_datetime` (`Date_Time`),
  CONSTRAINT `fk_schedule_client` FOREIGN KEY (`Client_ID`) REFERENCES `user_clientprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_schedule_repairman` FOREIGN KEY (`Repairman_ID`) REFERENCES `user_repairmanprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `repairticket`
--

DROP TABLE IF EXISTS `repairticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repairticket` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Client_ID` int(11) NOT NULL,
  `Appliance_ID` int(11) DEFAULT NULL,
  `Client_Lat` decimal(10,7) DEFAULT NULL,
  `Client_Lng` decimal(10,7) DEFAULT NULL,
  `Route_Distance_Km` decimal(6,2) DEFAULT NULL,
  `Route_Duration_Min` int(11) DEFAULT NULL,
  `Repairman_ID` int(11) DEFAULT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Open',
  `Details` text DEFAULT NULL,
  `Schedule_ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `fk_ticket_client` (`Client_ID`),
  KEY `fk_ticket_repairman` (`Repairman_ID`),
  KEY `fk_ticket_schedule` (`Schedule_ID`),
  KEY `idx_repair_ticket_status` (`Status`),
  CONSTRAINT `fk_ticket_client` FOREIGN KEY (`Client_ID`) REFERENCES `user_clientprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ticket_repairman` FOREIGN KEY (`Repairman_ID`) REFERENCES `user_repairmanprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_ticket_schedule` FOREIGN KEY (`Schedule_ID`) REFERENCES `repairschedule` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `subscription_plans`
--

DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subscription_plans` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Code` varchar(20) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Duration_Months` tinyint(3) unsigned NOT NULL,
  `Price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Updated_At` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Code_UNIQUE` (`Code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_clientprofile`
--

DROP TABLE IF EXISTS `user_clientprofile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_clientprofile` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Address_Line1` varchar(255) DEFAULT NULL,
  `Address_Line2` varchar(255) DEFAULT NULL,
  `Brgy` varchar(100) DEFAULT NULL,
  `City_Min` varchar(100) DEFAULT NULL,
  `Province` varchar(100) DEFAULT NULL,
  `Region` varchar(100) DEFAULT NULL,
  `Zip_Code` varchar(10) DEFAULT NULL,
  `Latitude` decimal(10,7) DEFAULT NULL,
  `Longitude` decimal(10,7) DEFAULT NULL,
  `Formatted_Address` varchar(500) DEFAULT NULL,
  `Prl_MobileNo` varchar(20) DEFAULT NULL,
  `Sec_MobileNo` varchar(20) DEFAULT NULL,
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Email_UNIQUE` (`Email`),
  KEY `fk_client_profile_user` (`User_ID`),
  KEY `idx_client_profile_email` (`Email`),
  CONSTRAINT `fk_client_profile_user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `user_repairmanprofile`
--

DROP TABLE IF EXISTS `user_repairmanprofile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_repairmanprofile` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Address` text DEFAULT NULL,
  `Latitude` decimal(10,7) DEFAULT NULL,
  `Longitude` decimal(10,7) DEFAULT NULL,
  `Formatted_Address` varchar(500) DEFAULT NULL,
  `MobileNo` varchar(20) DEFAULT NULL,
  `FacebookPage` varchar(255) DEFAULT NULL,
  `Availability` varchar(50) DEFAULT NULL,
  `Details` text DEFAULT NULL,
  `Skills` text DEFAULT NULL,
  `Education` text DEFAULT NULL,
  `Certifications` text DEFAULT NULL,
  `Assessment` text DEFAULT NULL,
  `Ratings` decimal(3,2) DEFAULT 0.00,
  `Reviews` text DEFAULT NULL,
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `Subscription_Status` enum('trial','subscribed','unsubscribed') NOT NULL DEFAULT 'unsubscribed',
  `Trial_Ends_At` datetime DEFAULT NULL,
  `Subscription_Ends_At` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Email_UNIQUE` (`Email`),
  KEY `fk_repairman_profile_user` (`User_ID`),
  CONSTRAINT `fk_repairman_profile_user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('customer','repairman') NOT NULL,
  `Status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `Created_At` datetime NOT NULL DEFAULT current_timestamp(),
  `Terms_Accepted_At` datetime DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Email_UNIQUE` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: home_service_pro
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
INSERT INTO `admin` VALUES (1,'Admin','admin@home-service-pro.shop','$2y$10$0qSJZq8bTJpCFqZb9bhJFejKU2E/0chSo1dBs.HilhdnceIfkDfFO','active','2026-09-01 15:31:11');
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'John Doe','john@gmail.com','$2y$10$H/IR5RASyhbVIDqeLC3KZOFjGOCrlQN/DHlBvqN08t1aOMzjllRmC','repairman','active','2026-09-07 22:31:21',NULL),(2,'Jane Smith','jane@gmail.com','$2y$10$fwdAj4z6PuvsXcNTbS4LwehqFlreRH4n77YTcezdne24pO2trb72q','repairman','active','2026-09-07 22:37:10',NULL),(3,'Ben Dover','ben@gmail.com','$2y$10$xSbNw2NCmeufAssMqAxE/uWxCQPvlqYdAjf4Ecz.ChoQFJvuWDtL2','customer','active','2026-09-07 22:39:02',NULL),(8,'Juan Dela Cruz','dragecave@gmail.com','$2y$10$jOZgRpC.ixBSpA3ib..i7.IzkhFZVNfP2Od8XRa8KDTkaXRQ1r1RS','repairman','active','2026-09-09 15:53:27',NULL),(25,'Bat Man','batman@gmail.com','$2y$10$V.PvJuONd3r.c/YsHUNsXuA2sucyRmZMW5TwK/DIHeaRZVzCM2PSy','customer','active','2026-09-22 10:00:38',NULL),(26,'Rhandon Dave','daverhandon@gmail.com','$2y$10$5nyQBH.ezqFVWhnUZ1XgyeKOMWxCQ7Qbzz.afGHYbQCszrqeNtFnS','customer','active','2026-09-29 21:30:11',NULL),(27,'Rhandon San Jose','rhandond@gmail.com','$2y$10$bMGiU8pNM5MycvhkbDwXzOP0dVzpYr6pCIebDQAQuc28QjFDwzWDG','customer','active','2026-09-29 21:41:45',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `user_clientprofile`
--

LOCK TABLES `user_clientprofile` WRITE;
/*!40000 ALTER TABLE `user_clientprofile` DISABLE KEYS */;
INSERT INTO `user_clientprofile` VALUES (1,3,'Ben Dover','ben@gmail.com','Updated','','','Manila','Metro Manila','NCR','1000',15.4866934,120.5966154,'J573+WC8 Cruz, Bongabon, Nueva Ecija, Philippines','09179998888','','active'),(6,25,'Bat Man','batman@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,15.6696576,120.7566336,'MQ94+VM Guimba, Nueva Ecija, Philippines',NULL,NULL,'active'),(7,26,'Rhandon Dave','daverhandon@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,15.6729344,120.8254464,'MRCG+M27, Bunol, Guimba, Nueva Ecija, Philippines',NULL,NULL,'active'),(8,27,'Rhandon San Jose','rhandond@gmail.com',NULL,NULL,NULL,NULL,NULL,NULL,NULL,15.6100000,120.9000000,'JV5X+XX Santo Domingo, Nueva Ecija, Philippines','',NULL,'active');
/*!40000 ALTER TABLE `user_clientprofile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `user_repairmanprofile`
--

LOCK TABLES `user_repairmanprofile` WRITE;
/*!40000 ALTER TABLE `user_repairmanprofile` DISABLE KEYS */;
INSERT INTO `user_repairmanprofile` VALUES (1,1,'John Doe','john@gmail.com','',15.4866934,120.5966154,'Lolita Bldg., Lolita Bldg. (in front of Silayan) R-9, Tarlac City, 2300 Tarlac, Philippines','09171234567','','1',NULL,'refrigerator,washing-machine,air-conditioner,dryer','Updated Education','[{\"name\":\"Test Certification\",\"issued\":\"2024-01-15\",\"valid_until\":\"2025-01-15\"}]',NULL,4.50,NULL,'active','trial','2026-10-11 13:43:20',NULL),(2,2,'Jane Smith','jane@gmail.com',NULL,14.5547000,121.0244000,'Makati, Metro Manila',NULL,NULL,'1',NULL,NULL,NULL,NULL,NULL,0.00,NULL,'active','trial','2026-10-11 13:43:20',NULL),(7,8,'Juan Dela Cruz','dragecave@gmail.com','',15.7089792,120.9171968,'PW58+HV Muñoz, Nueva Ecija, Philippines','09123456789','','1',NULL,NULL,'','[{\"name\":\"Tesda\",\"file\":null},{\"name\":\"Tesda 2\",\"file\":null}]',NULL,4.00,NULL,'active','trial','2026-10-11 13:43:20',NULL);
/*!40000 ALTER TABLE `user_repairmanprofile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping data for table `subscription_plans`
--

LOCK TABLES `subscription_plans` WRITE;
/*!40000 ALTER TABLE `subscription_plans` DISABLE KEYS */;
INSERT INTO `subscription_plans` VALUES (1,'monthly','1 Month',1,299.00,'2026-10-08 13:43:20'),(2,'quarterly','3 Months',3,799.00,'2026-10-08 13:29:24'),(3,'yearly','1 Year',12,2999.00,'2026-10-08 13:29:24');
/*!40000 ALTER TABLE `subscription_plans` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed

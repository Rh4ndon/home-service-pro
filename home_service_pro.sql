-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 19, 2026 at 08:33 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `home_service_pro`
--

-- --------------------------------------------------------

--
-- Table structure for table `applianceissue`
--

CREATE TABLE `applianceissue` (
  `ID` int(11) NOT NULL,
  `Issue` varchar(255) NOT NULL,
  `Details` text DEFAULT NULL,
  `Detailed_Report` text DEFAULT NULL,
  `Ticket_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clientaddress`
--

CREATE TABLE `clientaddress` (
  `ID` int(11) NOT NULL,
  `Address_Line1` varchar(255) NOT NULL,
  `Address_Line2` varchar(255) DEFAULT NULL,
  `Brgy` varchar(100) DEFAULT NULL,
  `City_Min` varchar(100) NOT NULL,
  `Province` varchar(100) NOT NULL,
  `Region` varchar(100) DEFAULT NULL,
  `Zip_Code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clientappliances`
--

CREATE TABLE `clientappliances` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Type` varchar(100) NOT NULL,
  `Make` varchar(100) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL,
  `Details` text DEFAULT NULL,
  `Issue_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clientcontact`
--

CREATE TABLE `clientcontact` (
  `ID` int(11) NOT NULL,
  `Prl_MobileNo` varchar(20) DEFAULT NULL,
  `Prl_TelNo` varchar(20) DEFAULT NULL,
  `Sec_MobileNo` varchar(20) DEFAULT NULL,
  `Sec_TelNo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairhistory`
--

CREATE TABLE `repairhistory` (
  `ID` int(11) NOT NULL,
  `Repairman_ID` int(11) NOT NULL,
  `Repairer_ID` int(11) DEFAULT NULL,
  `Schedule_ID` int(11) NOT NULL,
  `Ticket_ID` int(11) NOT NULL,
  `ClientAppliances_ID` int(11) NOT NULL,
  `Date` datetime NOT NULL DEFAULT current_timestamp(),
  `Status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairmanagerbackground`
--

CREATE TABLE `repairmanagerbackground` (
  `ID` int(11) NOT NULL,
  `Skills` text DEFAULT NULL,
  `Education` text DEFAULT NULL,
  `Certifications` text DEFAULT NULL,
  `Assessment` text DEFAULT NULL,
  `Ratings` decimal(3,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairschedule`
--

CREATE TABLE `repairschedule` (
  `ID` int(11) NOT NULL,
  `Client_ID` int(11) NOT NULL,
  `Date_Time` datetime NOT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Scheduled',
  `Repairman_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `repairticket`
--

CREATE TABLE `repairticket` (
  `ID` int(11) NOT NULL,
  `Client_ID` int(11) NOT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Open',
  `Details` text DEFAULT NULL,
  `Schedule_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_clientprofile`
--

CREATE TABLE `user_clientprofile` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `ClientAppliances_ID` int(11) DEFAULT NULL,
  `ClientAdd_ID` int(11) NOT NULL,
  `ClientContact_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_repairmanprofile`
--

CREATE TABLE `user_repairmanprofile` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Address` text DEFAULT NULL,
  `MobileNo` varchar(20) DEFAULT NULL,
  `TelNo` varchar(20) DEFAULT NULL,
  `Availability` varchar(50) DEFAULT NULL,
  `Details` text DEFAULT NULL,
  `Reviews` text DEFAULT NULL,
  `RepairmanBG_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applianceissue`
--
ALTER TABLE `applianceissue`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_issue_ticket` (`Ticket_ID`);

--
-- Indexes for table `clientaddress`
--
ALTER TABLE `clientaddress`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `clientappliances`
--
ALTER TABLE `clientappliances`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_appliance_issue` (`Issue_ID`);

--
-- Indexes for table `clientcontact`
--
ALTER TABLE `clientcontact`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `repairhistory`
--
ALTER TABLE `repairhistory`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_history_repairman` (`Repairman_ID`),
  ADD KEY `fk_history_schedule` (`Schedule_ID`),
  ADD KEY `fk_history_ticket` (`Ticket_ID`),
  ADD KEY `fk_history_appliance` (`ClientAppliances_ID`),
  ADD KEY `idx_repair_history_date` (`Date`);

--
-- Indexes for table `repairmanagerbackground`
--
ALTER TABLE `repairmanagerbackground`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `repairschedule`
--
ALTER TABLE `repairschedule`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_schedule_client` (`Client_ID`),
  ADD KEY `fk_schedule_repairman` (`Repairman_ID`),
  ADD KEY `idx_repair_schedule_status` (`Status`),
  ADD KEY `idx_repair_schedule_datetime` (`Date_Time`);

--
-- Indexes for table `repairticket`
--
ALTER TABLE `repairticket`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_ticket_client` (`Client_ID`),
  ADD KEY `fk_ticket_schedule` (`Schedule_ID`),
  ADD KEY `idx_repair_ticket_status` (`Status`);

--
-- Indexes for table `user_clientprofile`
--
ALTER TABLE `user_clientprofile`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email_UNIQUE` (`Email`),
  ADD KEY `fk_client_profile_address` (`ClientAdd_ID`),
  ADD KEY `fk_client_profile_contact` (`ClientContact_ID`),
  ADD KEY `fk_client_profile_appliances` (`ClientAppliances_ID`),
  ADD KEY `idx_client_profile_email` (`Email`);

--
-- Indexes for table `user_repairmanprofile`
--
ALTER TABLE `user_repairmanprofile`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email_UNIQUE` (`Email`),
  ADD KEY `fk_repairman_profile_background` (`RepairmanBG_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applianceissue`
--
ALTER TABLE `applianceissue`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientaddress`
--
ALTER TABLE `clientaddress`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientappliances`
--
ALTER TABLE `clientappliances`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientcontact`
--
ALTER TABLE `clientcontact`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairhistory`
--
ALTER TABLE `repairhistory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairmanagerbackground`
--
ALTER TABLE `repairmanagerbackground`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairschedule`
--
ALTER TABLE `repairschedule`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `repairticket`
--
ALTER TABLE `repairticket`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_clientprofile`
--
ALTER TABLE `user_clientprofile`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_repairmanprofile`
--
ALTER TABLE `user_repairmanprofile`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applianceissue`
--
ALTER TABLE `applianceissue`
  ADD CONSTRAINT `fk_issue_ticket` FOREIGN KEY (`Ticket_ID`) REFERENCES `repairticket` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `clientappliances`
--
ALTER TABLE `clientappliances`
  ADD CONSTRAINT `fk_appliance_issue` FOREIGN KEY (`Issue_ID`) REFERENCES `applianceissue` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `repairhistory`
--
ALTER TABLE `repairhistory`
  ADD CONSTRAINT `fk_history_appliance` FOREIGN KEY (`ClientAppliances_ID`) REFERENCES `clientappliances` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_history_repairman` FOREIGN KEY (`Repairman_ID`) REFERENCES `user_repairmanprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_history_schedule` FOREIGN KEY (`Schedule_ID`) REFERENCES `repairschedule` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_history_ticket` FOREIGN KEY (`Ticket_ID`) REFERENCES `repairticket` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `repairschedule`
--
ALTER TABLE `repairschedule`
  ADD CONSTRAINT `fk_schedule_client` FOREIGN KEY (`Client_ID`) REFERENCES `user_clientprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_schedule_repairman` FOREIGN KEY (`Repairman_ID`) REFERENCES `user_repairmanprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `repairticket`
--
ALTER TABLE `repairticket`
  ADD CONSTRAINT `fk_ticket_client` FOREIGN KEY (`Client_ID`) REFERENCES `user_clientprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_schedule` FOREIGN KEY (`Schedule_ID`) REFERENCES `repairschedule` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_clientprofile`
--
ALTER TABLE `user_clientprofile`
  ADD CONSTRAINT `fk_client_profile_address` FOREIGN KEY (`ClientAdd_ID`) REFERENCES `clientaddress` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_client_profile_appliances` FOREIGN KEY (`ClientAppliances_ID`) REFERENCES `clientappliances` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_client_profile_contact` FOREIGN KEY (`ClientContact_ID`) REFERENCES `clientcontact` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_repairmanprofile`
--
ALTER TABLE `user_repairmanprofile`
  ADD CONSTRAINT `fk_repairman_profile_background` FOREIGN KEY (`RepairmanBG_ID`) REFERENCES `repairmanagerbackground` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

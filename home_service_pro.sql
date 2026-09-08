-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 08, 2026 at 10:31 PM
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
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `Created_At` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID`, `Name`, `Email`, `Password`, `Status`, `Created_At`) VALUES
(1, 'Admin', 'admin@admin.com', '$2y$10$0qSJZq8bTJpCFqZb9bhJFejKU2E/0chSo1dBs.HilhdnceIfkDfFO', 'active', '2026-09-01 15:31:11');

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

--
-- Dumping data for table `applianceissue`
--

INSERT INTO `applianceissue` (`ID`, `Issue`, `Details`, `Detailed_Report`, `Ticket_ID`) VALUES
(1, 'Not cooling', 'Not cooling', NULL, 1),
(2, 'Not cooling', 'Not cooling', NULL, 2),
(5, 'Its now rotating', 'Its now rotating', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `ID` int(11) NOT NULL,
  `Sender_ID` int(11) NOT NULL,
  `Sender_Role` enum('customer','repairman','admin') NOT NULL,
  `Receiver_ID` int(11) NOT NULL,
  `Receiver_Role` enum('customer','repairman','admin') NOT NULL,
  `Message` text NOT NULL,
  `Is_Read` tinyint(1) NOT NULL DEFAULT 0,
  `Created_At` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`ID`, `Sender_ID`, `Sender_Role`, `Receiver_ID`, `Receiver_Role`, `Message`, `Is_Read`, `Created_At`) VALUES
(1, 3, 'customer', 1, 'repairman', 'Hello repairman!', 1, '2026-09-08 01:05:22'),
(2, 3, 'customer', 1, 'repairman', 'Hey there', 1, '2026-09-09 03:27:38');

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

--
-- Dumping data for table `clientaddress`
--

INSERT INTO `clientaddress` (`ID`, `Address_Line1`, `Address_Line2`, `Brgy`, `City_Min`, `Province`, `Region`, `Zip_Code`) VALUES
(1, '', NULL, NULL, '', '', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `clientappliances`
--

CREATE TABLE `clientappliances` (
  `ID` int(11) NOT NULL,
  `Client_ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Type` varchar(100) NOT NULL,
  `Make` varchar(100) DEFAULT NULL,
  `Year` int(11) DEFAULT NULL,
  `Details` text DEFAULT NULL,
  `Issue_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clientappliances`
--

INSERT INTO `clientappliances` (`ID`, `Client_ID`, `Name`, `Type`, `Make`, `Year`, `Details`, `Issue_ID`) VALUES
(2, 1, 'Samsung Washing', 'washing-machine', 'Samsung', 2023, 'Its now rotating', 5),
(3, 1, 'Samsung RT45', 'refrigerator', 'Samsung', 2024, 'Frost-free-inverter', 2),
(6, 1, 'Everest Aircon', 'air-conditioner', 'Everest', 2024, 'Everest Aircon', NULL);

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

--
-- Dumping data for table `clientcontact`
--

INSERT INTO `clientcontact` (`ID`, `Prl_MobileNo`, `Prl_TelNo`, `Sec_MobileNo`, `Sec_TelNo`) VALUES
(1, NULL, NULL, NULL, NULL);

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

--
-- Dumping data for table `repairhistory`
--

INSERT INTO `repairhistory` (`ID`, `Repairman_ID`, `Repairer_ID`, `Schedule_ID`, `Ticket_ID`, `ClientAppliances_ID`, `Date`, `Status`) VALUES
(2, 1, NULL, 1, 1, 2, '2026-09-08 00:59:11', 'Completed');

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

--
-- Dumping data for table `repairmanagerbackground`
--

INSERT INTO `repairmanagerbackground` (`ID`, `Skills`, `Education`, `Certifications`, `Assessment`, `Ratings`) VALUES
(1, 'Dishwasher Repair', NULL, '[{\"name\":\"Test Certification\",\"issued\":\"2024-01-15\",\"valid_until\":\"2025-01-15\"}]', NULL, 0.00),
(2, NULL, NULL, NULL, NULL, 0.00);

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

--
-- Dumping data for table `repairschedule`
--

INSERT INTO `repairschedule` (`ID`, `Client_ID`, `Date_Time`, `Status`, `Repairman_ID`) VALUES
(1, 1, '2026-09-10 10:00:00', 'In Progress', 1),
(2, 1, '2026-09-10 10:00:00', 'Scheduled', NULL),
(5, 1, '2026-09-11 13:00:00', 'Scheduled', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `repairticket`
--

CREATE TABLE `repairticket` (
  `ID` int(11) NOT NULL,
  `Client_ID` int(11) NOT NULL,
  `Appliance_ID` int(11) DEFAULT NULL,
  `Repairman_ID` int(11) DEFAULT NULL,
  `Status` varchar(50) NOT NULL DEFAULT 'Open',
  `Details` text DEFAULT NULL,
  `Schedule_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `repairticket`
--

INSERT INTO `repairticket` (`ID`, `Client_ID`, `Appliance_ID`, `Repairman_ID`, `Status`, `Details`, `Schedule_ID`) VALUES
(1, 1, 2, 1, 'Completed', 'Not cooling', 1),
(2, 1, 3, 1, 'Completed', 'Not cooling', 2),
(5, 1, 2, NULL, 'Open', 'Its now rotating', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('customer','repairman') NOT NULL,
  `Status` enum('active','inactive','banned') NOT NULL DEFAULT 'active',
  `Created_At` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Name`, `Email`, `Password`, `Role`, `Status`, `Created_At`) VALUES
(1, 'John Doe', 'john@gmail.com', '$2y$10$H/IR5RASyhbVIDqeLC3KZOFjGOCrlQN/DHlBvqN08t1aOMzjllRmC', 'repairman', 'active', '2026-09-07 22:31:21'),
(2, 'Jane Smith', 'jane@gmail.com', '$2y$10$fwdAj4z6PuvsXcNTbS4LwehqFlreRH4n77YTcezdne24pO2trb72q', 'repairman', 'active', '2026-09-07 22:37:10'),
(3, 'Ben Dover', 'ben@gmail.com', '$2y$10$xSbNw2NCmeufAssMqAxE/uWxCQPvlqYdAjf4Ecz.ChoQFJvuWDtL2', 'customer', 'active', '2026-09-07 22:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `user_clientprofile`
--

CREATE TABLE `user_clientprofile` (
  `ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `ClientAdd_ID` int(11) NOT NULL,
  `ClientContact_ID` int(11) NOT NULL,
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_clientprofile`
--

INSERT INTO `user_clientprofile` (`ID`, `User_ID`, `Name`, `Email`, `ClientAdd_ID`, `ClientContact_ID`, `Status`) VALUES
(1, 3, 'Ben Dover', 'ben@gmail.com', 1, 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `user_repairmanprofile`
--

CREATE TABLE `user_repairmanprofile` (
  `ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Name` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Address` text DEFAULT NULL,
  `MobileNo` varchar(20) DEFAULT NULL,
  `TelNo` varchar(20) DEFAULT NULL,
  `Availability` varchar(50) DEFAULT NULL,
  `Details` text DEFAULT NULL,
  `Reviews` text DEFAULT NULL,
  `RepairmanBG_ID` int(11) NOT NULL,
  `Status` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_repairmanprofile`
--

INSERT INTO `user_repairmanprofile` (`ID`, `User_ID`, `Name`, `Email`, `Address`, `MobileNo`, `TelNo`, `Availability`, `Details`, `Reviews`, `RepairmanBG_ID`, `Status`) VALUES
(1, 1, 'John Doe', 'john@gmail.com', '123 Test St', '09171234567', '02-1234567', NULL, NULL, NULL, 1, 'active'),
(2, NULL, 'Jane Smith', 'jane@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 2, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email_UNIQUE` (`Email`);

--
-- Indexes for table `applianceissue`
--
ALTER TABLE `applianceissue`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_issue_ticket` (`Ticket_ID`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_chat_sender` (`Sender_ID`,`Sender_Role`),
  ADD KEY `idx_chat_receiver` (`Receiver_ID`,`Receiver_Role`),
  ADD KEY `idx_chat_created` (`Created_At`);

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
  ADD KEY `fk_appliance_issue` (`Issue_ID`),
  ADD KEY `fk_appliance_client` (`Client_ID`);

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
  ADD KEY `fk_ticket_repairman` (`Repairman_ID`),
  ADD KEY `fk_ticket_schedule` (`Schedule_ID`),
  ADD KEY `idx_repair_ticket_status` (`Status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email_UNIQUE` (`Email`);

--
-- Indexes for table `user_clientprofile`
--
ALTER TABLE `user_clientprofile`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email_UNIQUE` (`Email`),
  ADD KEY `fk_client_profile_user` (`User_ID`),
  ADD KEY `fk_client_profile_address` (`ClientAdd_ID`),
  ADD KEY `fk_client_profile_contact` (`ClientContact_ID`),
  ADD KEY `idx_client_profile_email` (`Email`);

--
-- Indexes for table `user_repairmanprofile`
--
ALTER TABLE `user_repairmanprofile`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email_UNIQUE` (`Email`),
  ADD KEY `fk_repairman_profile_user` (`User_ID`),
  ADD KEY `fk_repairman_profile_background` (`RepairmanBG_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `applianceissue`
--
ALTER TABLE `applianceissue`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clientaddress`
--
ALTER TABLE `clientaddress`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clientappliances`
--
ALTER TABLE `clientappliances`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `clientcontact`
--
ALTER TABLE `clientcontact`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `repairhistory`
--
ALTER TABLE `repairhistory`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `repairmanagerbackground`
--
ALTER TABLE `repairmanagerbackground`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `repairschedule`
--
ALTER TABLE `repairschedule`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `repairticket`
--
ALTER TABLE `repairticket`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_clientprofile`
--
ALTER TABLE `user_clientprofile`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_repairmanprofile`
--
ALTER TABLE `user_repairmanprofile`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  ADD CONSTRAINT `fk_appliance_client` FOREIGN KEY (`Client_ID`) REFERENCES `user_clientprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
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
  ADD CONSTRAINT `fk_ticket_repairman` FOREIGN KEY (`Repairman_ID`) REFERENCES `user_repairmanprofile` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_ticket_schedule` FOREIGN KEY (`Schedule_ID`) REFERENCES `repairschedule` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_clientprofile`
--
ALTER TABLE `user_clientprofile`
  ADD CONSTRAINT `fk_client_profile_address` FOREIGN KEY (`ClientAdd_ID`) REFERENCES `clientaddress` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_client_profile_contact` FOREIGN KEY (`ClientContact_ID`) REFERENCES `clientcontact` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_client_profile_user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user_repairmanprofile`
--
ALTER TABLE `user_repairmanprofile`
  ADD CONSTRAINT `fk_repairman_profile_background` FOREIGN KEY (`RepairmanBG_ID`) REFERENCES `repairmanagerbackground` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_repairman_profile_user` FOREIGN KEY (`User_ID`) REFERENCES `users` (`ID`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

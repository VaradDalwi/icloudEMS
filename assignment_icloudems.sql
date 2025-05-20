-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 20, 2025 at 09:43 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `assignment_icloudems`
--

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
CREATE TABLE IF NOT EXISTS `branches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `branch_name` (`branch_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `common_fee_collection`
--

DROP TABLE IF EXISTS `common_fee_collection`;
CREATE TABLE IF NOT EXISTS `common_fee_collection` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_id` int NOT NULL DEFAULT '1',
  `trans_id` varchar(255) NOT NULL,
  `adm_no` varchar(255) NOT NULL,
  `roll_no` varchar(255) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `br_id` int DEFAULT NULL,
  `academic_year` varchar(255) NOT NULL,
  `financial_year` varchar(255) NOT NULL,
  `display_receipt_no` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `entry_mode` int DEFAULT NULL,
  `paid_date` date NOT NULL,
  `inactive` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `display_receipt_no` (`display_receipt_no`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `common_fee_collection_headwise`
--

DROP TABLE IF EXISTS `common_fee_collection_headwise`;
CREATE TABLE IF NOT EXISTS `common_fee_collection_headwise` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_id` int NOT NULL DEFAULT '1',
  `receipt_id` int DEFAULT NULL,
  `head_id` int DEFAULT NULL,
  `head_name` varchar(255) NOT NULL,
  `br_id` int DEFAULT NULL,
  `amount` decimal(20,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entry_mode`
--

DROP TABLE IF EXISTS `entry_mode`;
CREATE TABLE IF NOT EXISTS `entry_mode` (
  `id` int NOT NULL AUTO_INCREMENT,
  `entry_modename` varchar(255) NOT NULL,
  `crdr` varchar(255) NOT NULL,
  `entry_mode_no` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feecategory`
--

DROP TABLE IF EXISTS `feecategory`;
CREATE TABLE IF NOT EXISTS `feecategory` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fee_category` varchar(255) NOT NULL,
  `br_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feecollectiontype`
--

DROP TABLE IF EXISTS `feecollectiontype`;
CREATE TABLE IF NOT EXISTS `feecollectiontype` (
  `id` int NOT NULL AUTO_INCREMENT,
  `collection_head` varchar(255) NOT NULL,
  `collection_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `br_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feetypes`
--

DROP TABLE IF EXISTS `feetypes`;
CREATE TABLE IF NOT EXISTS `feetypes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fee_category` int NOT NULL DEFAULT '1',
  `f_name` varchar(255) NOT NULL,
  `collection_id` int NOT NULL DEFAULT '1',
  `br_id` int NOT NULL,
  `seq_id` int NOT NULL,
  `fee_type_ledger` varchar(255) NOT NULL,
  `fee_head_type` int DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fee_head` (`f_name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_trans`
--

DROP TABLE IF EXISTS `financial_trans`;
CREATE TABLE IF NOT EXISTS `financial_trans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_id` int NOT NULL DEFAULT '1',
  `trans_id` varchar(255) NOT NULL,
  `adm_no` varchar(255) NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `crdr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `tran_date` date NOT NULL,
  `acad_year` varchar(255) NOT NULL,
  `entry_mode_no` int DEFAULT NULL,
  `voucher_number` varchar(255) NOT NULL,
  `br_id` int DEFAULT NULL,
  `type_of_concession` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `financial_transdetail`
--

DROP TABLE IF EXISTS `financial_transdetail`;
CREATE TABLE IF NOT EXISTS `financial_transdetail` (
  `id` int NOT NULL AUTO_INCREMENT,
  `financial_trans_id` int NOT NULL,
  `module_id` int DEFAULT '1',
  `amount` decimal(20,2) NOT NULL,
  `head_id` int NOT NULL,
  `crdr` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `br_id` int DEFAULT NULL,
  `head_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

DROP TABLE IF EXISTS `module`;
CREATE TABLE IF NOT EXISTS `module` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_name` varchar(255) NOT NULL,
  `module_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `temporary_completedata`
--

DROP TABLE IF EXISTS `temporary_completedata`;
CREATE TABLE IF NOT EXISTS `temporary_completedata` (
  `id` int NOT NULL AUTO_INCREMENT,
  `s_no` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `academic_year` varchar(255) NOT NULL,
  `session` varchar(255) NOT NULL,
  `alloted_category` varchar(255) NOT NULL,
  `voucher_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `voucher_number` varchar(255) NOT NULL,
  `roll_no` varchar(255) NOT NULL,
  `adm_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` varchar(255) NOT NULL,
  `fee_category` varchar(255) NOT NULL,
  `faculty` varchar(255) NOT NULL,
  `program` varchar(255) NOT NULL,
  `department` varchar(255) NOT NULL,
  `branch` varchar(255) NOT NULL,
  `receipt_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `fee_head` varchar(255) NOT NULL,
  `due_amount` decimal(20,2) NOT NULL,
  `paid_amount` decimal(20,2) NOT NULL,
  `concession_amount` decimal(20,2) NOT NULL,
  `scholarship_amount` decimal(20,2) NOT NULL,
  `reverse_concession_amount` decimal(20,2) NOT NULL,
  `writoff_amount` decimal(20,2) NOT NULL,
  `adjusted_amount` decimal(20,2) NOT NULL,
  `refund_amount` decimal(20,2) NOT NULL,
  `fund_transfer_amount` decimal(20,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `faculty` (`faculty`),
  KEY `fee_head` (`fee_head`),
  KEY `receipt_number` (`receipt_number`) USING BTREE,
  KEY `idx_temp_voucher_type` (`voucher_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

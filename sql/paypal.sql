-- phpMyAdmin SQL Dump
-- version 4.0.6deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 06, 2014 at 06:40 AM
-- Server version: 5.5.37-0ubuntu0.13.10.1
-- PHP Version: 5.5.3-1ubuntu2.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `paypal`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `street1` varchar(255) DEFAULT NULL,
  `street2` varchar(255) DEFAULT NULL,
  `city_name` varchar(255) DEFAULT NULL,
  `state_or_province` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `phone` varchar(45) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `creation_date` datetime DEFAULT NULL,
  `modification_date` datetime DEFAULT NULL,
  `shpping` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`shpping`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `guid` varchar(255) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `billing_id` int(11) DEFAULT NULL,
  `shipping_id` int(11) DEFAULT NULL,
  `token` varchar(20) DEFAULT NULL,
  `order_total` double DEFAULT NULL,
  `order_description` varchar(127) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `paypal_error_codes` text,
  `cardtype` varchar(20) NOT NULL,
  `creation_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `guid` (`guid`),
  KEY `order_billing` (`billing_id`),
  KEY `order_shipping` (`shipping_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_billing` FOREIGN KEY (`billing_id`) REFERENCES `address` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_shipping` FOREIGN KEY (`shipping_id`) REFERENCES `address` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 3.5.0
-- http://www.phpmyadmin.net
--
-- Host: 10.1.6.202
-- Generation Time: Sep 24, 2014 at 10:28 AM
-- Server version: 5.0.95-log
-- PHP Version: 5.4.21

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `editionstgi`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL auto_increment,
  `guid` varchar(255) default NULL,
  `transaction_id` varchar(255) default NULL,
  `token` varchar(20) default NULL,
  `order_total` double default NULL,
  `order_description` varchar(127) default NULL,
  `status` varchar(255) default NULL,
  `paypal_error_codes` text,
  `download` tinyint(1) NOT NULL default '0',
  `creation_date` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `guid` (`guid`),
  KEY `guid_UNIQUE` (`guid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=177 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 10.1.22-MariaDB : Database - bowling
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `bowlers` */

DROP TABLE IF EXISTS `bowlers`;

CREATE TABLE `bowlers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `bowler_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `final_score` smallint(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `bowlers` */

/*Table structure for table `game` */

DROP TABLE IF EXISTS `game`;

CREATE TABLE `game` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `game_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `game` */

/*Table structure for table `scores` */

DROP TABLE IF EXISTS `scores`;

CREATE TABLE `scores` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `bowler_id` int(11) DEFAULT NULL,
  `frame_no` tinyint(2) DEFAULT NULL,
  `first_score` tinyint(2) DEFAULT NULL,
  `second_score` tinyint(2) DEFAULT NULL,
  `bonus_score` tinyint(2) DEFAULT NULL,
  `frame_score` smallint(3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `scores` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

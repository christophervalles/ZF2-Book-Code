# ************************************************************
# Sequel Pro SQL dump
# Version 4004
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 192.168.56.2 (MySQL 5.5.29-0ubuntu0.12.04.2-log)
# Database: sn
# Generation Time: 2013-05-01 05:02:30 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table user_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_comments`;

CREATE TABLE `user_comments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `type` tinyint(1) unsigned DEFAULT NULL,
  `entry_id` int(11) unsigned DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_entry_id` (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_comments` WRITE;
/*!40000 ALTER TABLE `user_comments` DISABLE KEYS */;

INSERT INTO `user_comments` (`id`, `user_id`, `type`, `entry_id`, `comment`, `created_at`, `updated_at`)
VALUES
	(1,1,3,1,'Comment 1','2013-04-27 20:54:02',NULL);

/*!40000 ALTER TABLE `user_comments` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_feed_articles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_feed_articles`;

CREATE TABLE `user_feed_articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(11) unsigned DEFAULT NULL,
  `title` varchar(512) DEFAULT NULL,
  `content` text,
  `url` varchar(2048) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_feed_id` (`feed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_feeds
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_feeds`;

CREATE TABLE `user_feeds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `title` varchar(512) DEFAULT NULL,
  `icon` varchar(2048) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table user_images
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_images`;

CREATE TABLE `user_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `filename` varchar(44) DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_images` WRITE;
/*!40000 ALTER TABLE `user_images` DISABLE KEYS */;

INSERT INTO `user_images` (`id`, `user_id`, `filename`, `created_at`, `updated_at`)
VALUES
	(1,1,'727f0c18ca77f17bc07bd717bafee686d3f361ad.png','2013-04-27 20:53:15',NULL);

/*!40000 ALTER TABLE `user_images` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_links
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_links`;

CREATE TABLE `user_links` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `url` varchar(2048) DEFAULT NULL,
  `title` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_links` WRITE;
/*!40000 ALTER TABLE `user_links` DISABLE KEYS */;

INSERT INTO `user_links` (`id`, `user_id`, `url`, `title`, `created_at`, `updated_at`)
VALUES
	(1,1,'http://www.google.es','Google','2013-04-27 20:53:44',NULL);

/*!40000 ALTER TABLE `user_links` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table user_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_statuses`;

CREATE TABLE `user_statuses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `status` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `user_statuses` WRITE;
/*!40000 ALTER TABLE `user_statuses` DISABLE KEYS */;

INSERT INTO `user_statuses` (`id`, `user_id`, `status`, `created_at`, `updated_at`)
VALUES
	(1,1,'Test','2013-04-27 20:52:59',NULL);

/*!40000 ALTER TABLE `user_statuses` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(254) DEFAULT NULL,
  `password` binary(60) DEFAULT NULL,
  `avatar_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(25) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `bio` tinytext,
  `location` tinytext,
  `gender` tinyint(1) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `avatar_id`, `name`, `surname`, `bio`, `location`, `gender`, `created_at`, `updated_at`)
VALUES
	(1,'tbhot3ww','hello@christophervalles.com',X'243279243134247078466B61344E4F584B6275363057324445376F754F434E657349796F6F462E49534D694B6D526475796F51315A77494874484E32',1,'Christopher','Valles','Game backend engineer. Entrepreneur. Airsofter. Apple fanboy. ACTC. Cooker. Sysadmin. ACSP. If you want to know more just ask!','London, United Kingdom',1,NULL,NULL);

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

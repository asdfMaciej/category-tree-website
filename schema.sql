-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Wersja serwera:               10.1.35-MariaDB - mariadb.org binary distribution
-- Serwer OS:                    Win32
-- HeidiSQL Wersja:              10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Zrzut struktury bazy danych strona
CREATE DATABASE IF NOT EXISTS `strona` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `strona`;

-- Zrzut struktury tabela strona.categories
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `parent_id_name` (`parent_id`,`name`),
  CONSTRAINT `FK_categories_categories` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Zrzucanie danych dla tabeli strona.categories: ~7 rows (około)
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` (`id`, `parent_id`, `name`) VALUES
	(0, 0, ''),
	(12, 0, 'Bażanty 1232'),
	(18, 0, 'kategoria inna'),
	(2, 0, 'Koty'),
	(1, 0, 'Psy'),
	(11, 0, 'Ptaki'),
	(3, 2, 'Dachowce'),
	(7, 3, 'Martwe'),
	(19, 18, 'podkategoria');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;

-- Zrzut struktury tabela strona.items
CREATE TABLE IF NOT EXISTS `items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_id_name` (`category_id`,`name`),
  CONSTRAINT `FK_items_categories` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Zrzucanie danych dla tabeli strona.items: ~3 rows (około)
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` (`id`, `category_id`, `name`) VALUES
	(1, 3, 'Lonia'),
	(2, 7, 'Emilia'),
	(3, 7, 'Kubuś'),
	(20, 11, 'Ptaszynka 66'),
	(22, 19, 'przedmiot'),
	(24, 19, 'przedmiot 3');
/*!40000 ALTER TABLE `items` ENABLE KEYS */;

-- Zrzut struktury tabela strona.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Zrzucanie danych dla tabeli strona.users: ~1 rows (około)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `login`, `password`, `created`, `admin`) VALUES
	(1, 'admin', '$2y$10$FLQeyX7QyKQShA4jEiTk0OJ4GLNBHl.yfnsnq7HGQ/E0ND4Z0VaSW', '2020-02-25 10:24:47', 1),
	(4, 'maciej', '$2y$10$SeCyBptBHm2ayvr82GzXHOK00oLzBDzyU2a6bNRHooDqxGb2DwrMy', '2020-02-25 10:55:42', 0),
	(5, 'uzytkownik', '$2y$10$ANXVu2/PCgeyFTpxIrRBoe4vKgKKW6L0x9DsIBOizleVzMRuIDPXO', '2020-02-25 11:14:31', 0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

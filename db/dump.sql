-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: db:3306
-- Generation Time: Paź 30, 2024 at 03:03 PM
-- Wersja serwera: 9.1.0
-- Wersja PHP: 8.2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Usunięcie istniejących tabel
DROP TABLE IF EXISTS `SimulationData`;
DROP TABLE IF EXISTS `DeviceParameter`;
DROP TABLE IF EXISTS `DeviceTypeParameter`;
DROP TABLE IF EXISTS `Device`;
DROP TABLE IF EXISTS `DeviceType`;
DROP TABLE IF EXISTS `Parameter`;

--
-- Database: `smarthaven`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Parameter`
--

CREATE TABLE `Parameter` (
  `ParameterID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Unit` varchar(50) DEFAULT NULL,
  `Description` text,
  PRIMARY KEY (`ParameterID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `DeviceType`
--

CREATE TABLE `DeviceType` (
  `DeviceTypeID` int NOT NULL AUTO_INCREMENT,
  `TypeName` varchar(100) NOT NULL,
  `Description` text,
  PRIMARY KEY (`DeviceTypeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Device`
--

CREATE TABLE `Device` (
  `DeviceID` int NOT NULL AUTO_INCREMENT,
  `DeviceTypeID` int DEFAULT NULL,
  `DeviceName` varchar(100) NOT NULL,
  `Location` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`DeviceID`),
  KEY `DeviceTypeID` (`DeviceTypeID`),
  CONSTRAINT `Device_ibfk_1` FOREIGN KEY (`DeviceTypeID`) REFERENCES `DeviceType` (`DeviceTypeID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `DeviceTypeParameter`
--

CREATE TABLE `DeviceTypeParameter` (
  `DeviceTypeID` int NOT NULL,
  `ParameterID` int NOT NULL,
  `DefaultValue` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`DeviceTypeID`,`ParameterID`),
  KEY `ParameterID` (`ParameterID`),
  CONSTRAINT `DeviceTypeParameter_ibfk_1` FOREIGN KEY (`DeviceTypeID`) REFERENCES `DeviceType` (`DeviceTypeID`) ON DELETE CASCADE,
  CONSTRAINT `DeviceTypeParameter_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `DeviceParameter`
--

CREATE TABLE `DeviceParameter` (
  `DeviceID` int NOT NULL,
  `ParameterID` int NOT NULL,
  `Value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`DeviceID`,`ParameterID`),
  KEY `ParameterID` (`ParameterID`),
  CONSTRAINT `DeviceParameter_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
  CONSTRAINT `DeviceParameter_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `SimulationData`
--

CREATE TABLE `SimulationData` (
  `SimulationID` int NOT NULL AUTO_INCREMENT,
  `DeviceID` int DEFAULT NULL,
  `ParameterID` int DEFAULT NULL,
  `SimulatedValue` varchar(100) DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`SimulationID`),
  KEY `DeviceID` (`DeviceID`),
  KEY `ParameterID` (`ParameterID`),
  CONSTRAINT `SimulationData_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
  CONSTRAINT `SimulationData_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dodanie przykładowych danych
--

-- Wstawienie typów urządzeń
INSERT INTO `DeviceType` (`DeviceTypeID`, `TypeName`, `Description`) VALUES
(1, 'Klimatyzacja', 'Urządzenie klimatyzacyjne'),
(2, 'Lampa', 'Urządzenie oświetleniowe');

-- Wstawienie parametrów
INSERT INTO `Parameter` (`ParameterID`, `Name`, `Unit`, `Description`) VALUES
(1, 'Status', NULL, '0 - wyłączone, 1 - włączone'),
(2, 'Temperatura', '°C', 'Temperatura zadana lub aktualna'),
(3, 'Jasność', '%', 'Poziom jasności od 0% do 100%'),
(4, 'Tryb', NULL, 'Tryb pracy urządzenia klimatyzacyjnego'),
(5, 'Kolor', NULL, 'Kolor światła');

-- Powiązanie parametrów z typami urządzeń
INSERT INTO `DeviceTypeParameter` (`DeviceTypeID`, `ParameterID`, `DefaultValue`) VALUES
(1, 1, '0'),    -- Klimatyzacja, Status
(1, 2, '22'),   -- Klimatyzacja, Temperatura
(1, 4, 'Cool'), -- Klimatyzacja, Tryb
(2, 1, '0'),    -- Lampa, Status
(2, 3, '50'),   -- Lampa, Jasność
(2, 5, 'Biały');-- Lampa, Kolor

-- Wstawienie urządzeń
INSERT INTO `Device` (`DeviceID`, `DeviceTypeID`, `DeviceName`, `Location`) VALUES
(1, 1, 'klimatyzacja1', 'Salon'),
(2, 2, 'lampa1', 'Sypialnia');

-- Wstawienie parametrów urządzeń
INSERT INTO `DeviceParameter` (`DeviceID`, `ParameterID`, `Value`) VALUES
(1, 1, '0'),      -- klimatyzacja1, Status
(1, 2, '22'),     -- klimatyzacja1, Temperatura
(1, 4, 'Heat'),   -- klimatyzacja1, Tryb
(2, 1, '1'),      -- lampa1, Status
(2, 3, '75'),     -- lampa1, Jasność
(2, 5, 'Czerwony'); -- lampa1, Kolor

-- Wstawienie danych symulacyjnych
INSERT INTO `SimulationData` (`SimulationID`, `DeviceID`, `ParameterID`, `SimulatedValue`, `Timestamp`) VALUES
(1, 1, 1, '0', NOW()),
(2, 1, 2, '22', NOW()),
(3, 1, 4, 'Heat', NOW()),
(4, 2, 1, '1', NOW()),
(5, 2, 3, '75', NOW()),
(6, 2, 5, 'Czerwony', NOW());

COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

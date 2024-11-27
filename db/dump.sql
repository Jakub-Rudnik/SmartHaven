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
DROP TABLE IF EXISTS `Schedule`;
DROP TABLE IF EXISTS `Device`;
DROP TABLE IF EXISTS `DeviceType`;
DROP TABLE IF EXISTS `Parameter`;
DROP TABLE IF EXISTS `Users`;

--
-- Database: `smarthaven`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Parameter`
--

CREATE TABLE `Users` (
  `UserID` int NOT NULL AUTO_INCREMENT,
  `Username` varchar(100) NOT NULL,
  `Email` varchar(255) NOT NULL UNIQUE,
  `PasswordHash` varchar(255) NOT NULL,
  `CreatedAt` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Role` varchar(50) DEFAULT 'user',
  `IsActive` boolean DEFAULT 1,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Schedule`
--

CREATE TABLE `Schedule` (
  `ScheduleID` int NOT NULL AUTO_INCREMENT,
  `DeviceID` int NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime DEFAULT NULL,
  `ParameterID` int DEFAULT NULL,
  `ParameterValue` varchar(100) DEFAULT NULL,
  `RepeatPattern` varchar(50) DEFAULT NULL, -- Informacja o powtarzaniu, np. codziennie, tygodniowo
  `ScheduleState` INT(1) DEFAULT 0,
  PRIMARY KEY (`ScheduleID`),
  KEY `DeviceID` (`DeviceID`),
  KEY `ParameterID` (`ParameterID`),
  CONSTRAINT `Schedule_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
  CONSTRAINT `Schedule_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dodanie przykładowych danych
--

-- Wstawienie typów urządzeń
INSERT INTO `DeviceType` (`TypeName`, `Description`) VALUES
('Klimatyzacja', 'Urządzenie klimatyzacyjne'),
('Lampa', 'Urządzenie oświetleniowe'),
('Brama Wjazdowa', 'Brama do posesji, sterowana automatycznie'),
('Brama Garażowa', 'Automatyczna brama garażowa'),
('Kamera', 'Kamera monitoringu'),
('Czujnik Ruchu', 'Urządzenie do wykrywania ruchu');

-- Wstawienie parametrów
INSERT INTO `Parameter` (`Name`, `Unit`, `Description`) VALUES
('Status', NULL, '0 - wyłączone, 1 - włączone'),
('Temperatura', '°C', 'Temperatura zadana lub aktualna'),
('Jasność', '%', 'Poziom jasności od 0% do 100%'),
('Tryb', NULL, 'Tryb pracy urządzenia klimatyzacyjnego'),
('Kolor', NULL, 'Kolor światła'),
('Stan', NULL, 'Stan bramy: otwarta/zamknięta'),
('Czułość', '%', 'Czułość czujnika ruchu od 0% do 100%');

-- Powiązanie parametrów z typami urządzeń
INSERT INTO `DeviceTypeParameter` (`DeviceTypeID`, `ParameterID`, `DefaultValue`) VALUES
(1, 1, '0'),    -- Klimatyzacja, Status
(1, 2, '22'),   -- Klimatyzacja, Temperatura
(1, 4, 'Cool'), -- Klimatyzacja, Tryb
(2, 1, '0'),    -- Lampa, Status
(2, 3, '50'),   -- Lampa, Jasność
(2, 5, 'Biały'),-- Lampa, Kolor
(3, 6, 'zamknięta'), -- Brama Wjazdowa, Stan
(4, 6, 'zamknięta'), -- Brama Garażowa, Stan
(5, 1, '0'),    -- Kamera, Status
(6, 1, '0'),    -- Czujnik Ruchu, Status
(6, 7, '50');   -- Czujnik Ruchu, Czułość

-- Wstawienie urządzeń
INSERT INTO `Device` (`DeviceTypeID`, `DeviceName`, `Location`) VALUES
(1, 'klimatyzacja1', 'Salon'),
(2, 'lampa1', 'Sypialnia'),
(3, 'brama_wjazdowa1', 'Wjazd'),
(4, 'brama_garazowa1', NULL),      -- Brama garażowa bez przypisanego pokoju
(2, 'lampa2', 'Kuchnia'),
(5, 'kamera1', NULL),              -- Kamera bez przypisanego pokoju
(6, 'czujnik_ruchu1', 'Korytarz'),
(2, 'lampa3', NULL);               -- Lampa bez przypisanego pokoju

-- Wstawienie parametrów urządzeń
INSERT INTO `DeviceParameter` (`DeviceID`, `ParameterID`, `Value`) VALUES
(1, 1, '0'),      -- klimatyzacja1, Status
(1, 2, '22'),     -- klimatyzacja1, Temperatura
(1, 4, 'Heat'),   -- klimatyzacja1, Tryb
(2, 1, '1'),      -- lampa1, Status
(2, 3, '75'),     -- lampa1, Jasność
(2, 5, 'Czerwony'), -- lampa1, Kolor
(3, 6, 'zamknięta'), -- brama_wjazdowa1, Stan
(4, 6, 'otwarta'),  -- brama_garazowa1, Stan
(5, 1, '1'),      -- kamera1, Status
(5, 3, '50'),     -- lampa2, Jasność
(5, 5, 'Zielony'),-- lampa2, Kolor
(6, 1, '1'),      -- kamera1, Status
(7, 1, '1'),      -- czujnik_ruchu1, Status
(7, 7, '80'),     -- czujnik_ruchu1, Czułość
(8, 1, '1'),      -- lampa3, Status
(8, 3, '100'),    -- lampa3, Jasność
(8, 5, 'Biały');  -- lampa3, Kolor

-- Wstawienie harmonogramów
INSERT INTO `Schedule` (`DeviceID`, `StartTime`, `EndTime`, `ParameterID`, `ParameterValue`, `RepeatPattern`) VALUES
(1, '2024-10-31 08:00:00', NULL, 1, '1', 'codziennie'); -- Klimatyzacja włączana codziennie o 8:00

-- Wstawienie danych symulacyjnych
INSERT INTO `SimulationData` (`DeviceID`, `ParameterID`, `SimulatedValue`, `Timestamp`) VALUES
(1, 1, '0', NOW()),
(1, 2, '22', NOW()),
(1, 4, 'Heat', NOW()),
(2, 1, '1', NOW()),
(2, 3, '75', NOW()),
(2, 5, 'Czerwony', NOW()),
(3, 6, 'zamknięta', NOW()),
(4, 6, 'otwarta', NOW()),
(5, 1, '1', NOW()),
(5, 3, '50', NOW()),
(5, 5, 'Zielony', NOW()),
(6, 1, '1', NOW()),
(7, 1, '1', NOW()),
(7, 7, '80', NOW()),
(8, 1, '1', NOW()),
(8, 3, '100', NOW()),
(8, 5, 'Biały', NOW());

COMMIT;

SET FOREIGN_KEY_CHECKS = 1;

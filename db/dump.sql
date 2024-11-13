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
DROP TABLE IF EXISTS `Notifications`; -- Nowa tabela Notifications

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
  `State` TINYINT(1) DEFAULT 0,  -- Zmieniono nazwę kolumny na State
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
-- Struktura tabeli dla tabeli `Notifications`
--

CREATE TABLE Notifications (
    NotificationID INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    DeviceID INT NOT NULL,
    NewState TINYINT(1) NOT NULL,
    Timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- --------------------------------------------------------


-- Wstawienie typów urządzeń
INSERT INTO `DeviceType` (`TypeName`, `Description`) VALUES
('Klimatyzacja', 'Urządzenie klimatyzacyjne'),
('Lampa', 'Urządzenie oświetleniowe'),
('Brama Wjazdowa', 'Brama do posesji, sterowana automatycznie'),
('Brama Garażowa', 'Automatyczna brama garażowa'),
('Kamera', 'Kamera monitoringu'),
('Czujnik Ruchu', 'Urządzenie do wykrywania ruchu');

-- Wstawienie urządzeń
INSERT INTO `Device` (`DeviceTypeID`, `DeviceName`, `Location`, `State`) VALUES
(1, 'klimatyzacja1', 'Salon', 1),
(2, 'lampa1', 'Sypialnia', 1),
(3, 'brama_wjazdowa1', 'Wjazd', 0),
(4, 'brama_garazowa1', NULL, 0),
(2, 'lampa2', 'Kuchnia', 1),
(5, 'kamera1', NULL, 1),
(6, 'czujnik_ruchu1', 'Korytarz', 1),
(2, 'lampa3', NULL, 0);

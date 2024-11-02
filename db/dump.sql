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


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smarthaven`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Device`
--

CREATE TABLE `Device` (
  `DeviceID` int NOT NULL,
  `DeviceTypeID` int DEFAULT NULL,
  `DeviceName` varchar(100) NOT NULL,
  `Location` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `DeviceParameter`
--

CREATE TABLE `DeviceParameter` (
  `DeviceID` int NOT NULL,
  `ParameterID` int NOT NULL,
  `Value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `DeviceType`
--

CREATE TABLE `DeviceType` (
  `DeviceTypeID` int NOT NULL,
  `TypeName` varchar(100) NOT NULL,
  `Description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `DeviceTypeParameter`
--

CREATE TABLE `DeviceTypeParameter` (
  `DeviceTypeID` int NOT NULL,
  `ParameterID` int NOT NULL,
  `DefaultValue` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `Parameter`
--

CREATE TABLE `Parameter` (
  `ParameterID` int NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Unit` varchar(50) DEFAULT NULL,
  `Description` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `SimulationData`
--

CREATE TABLE `SimulationData` (
  `SimulationID` int NOT NULL,
  `DeviceID` int DEFAULT NULL,
  `ParameterID` int DEFAULT NULL,
  `SimulatedValue` varchar(100) DEFAULT NULL,
  `Timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `Device`
--
ALTER TABLE `Device`
  ADD PRIMARY KEY (`DeviceID`),
  ADD KEY `DeviceTypeID` (`DeviceTypeID`);

--
-- Indeksy dla tabeli `DeviceParameter`
--
ALTER TABLE `DeviceParameter`
  ADD PRIMARY KEY (`DeviceID`,`ParameterID`),
  ADD KEY `ParameterID` (`ParameterID`);

--
-- Indeksy dla tabeli `DeviceType`
--
ALTER TABLE `DeviceType`
  ADD PRIMARY KEY (`DeviceTypeID`);

--
-- Indeksy dla tabeli `DeviceTypeParameter`
--
ALTER TABLE `DeviceTypeParameter`
  ADD PRIMARY KEY (`DeviceTypeID`,`ParameterID`),
  ADD KEY `ParameterID` (`ParameterID`);

--
-- Indeksy dla tabeli `Parameter`
--
ALTER TABLE `Parameter`
  ADD PRIMARY KEY (`ParameterID`);

--
-- Indeksy dla tabeli `SimulationData`
--
ALTER TABLE `SimulationData`
  ADD PRIMARY KEY (`SimulationID`),
  ADD KEY `DeviceID` (`DeviceID`),
  ADD KEY `ParameterID` (`ParameterID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Device`
--
ALTER TABLE `Device`
  MODIFY `DeviceID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DeviceType`
--
ALTER TABLE `DeviceType`
  MODIFY `DeviceTypeID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Parameter`
--
ALTER TABLE `Parameter`
  MODIFY `ParameterID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SimulationData`
--
ALTER TABLE `SimulationData`
  MODIFY `SimulationID` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Device`
--
ALTER TABLE `Device`
  ADD CONSTRAINT `Device_ibfk_1` FOREIGN KEY (`DeviceTypeID`) REFERENCES `DeviceType` (`DeviceTypeID`) ON DELETE SET NULL;

--
-- Constraints for table `DeviceParameter`
--
ALTER TABLE `DeviceParameter`
  ADD CONSTRAINT `DeviceParameter_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
  ADD CONSTRAINT `DeviceParameter_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE;

--
-- Constraints for table `DeviceTypeParameter`
--
ALTER TABLE `DeviceTypeParameter`
  ADD CONSTRAINT `DeviceTypeParameter_ibfk_1` FOREIGN KEY (`DeviceTypeID`) REFERENCES `DeviceType` (`DeviceTypeID`) ON DELETE CASCADE,
  ADD CONSTRAINT `DeviceTypeParameter_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE;

--
-- Constraints for table `SimulationData`
--
ALTER TABLE `SimulationData`
  ADD CONSTRAINT `SimulationData_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
  ADD CONSTRAINT `SimulationData_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
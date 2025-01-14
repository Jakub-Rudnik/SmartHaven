SET NAMES 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

DROP DATABASE IF EXISTS smarthaven;
CREATE DATABASE smarthaven CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci;
USE smarthaven;

DROP TABLE IF EXISTS `UserDevice`;
DROP TABLE IF EXISTS `SimulationData`;
DROP TABLE IF EXISTS `DeviceParameter`;
DROP TABLE IF EXISTS `DeviceTypeParameter`;
DROP TABLE IF EXISTS `Schedule`;
DROP TABLE IF EXISTS `Device`;
DROP TABLE IF EXISTS `DeviceType`;
DROP TABLE IF EXISTS `Parameter`;
DROP TABLE IF EXISTS `Groups`;
DROP TABLE IF EXISTS `Users`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `Users`
(
    `UserID`       int          NOT NULL AUTO_INCREMENT,
    `Username`     varchar(100) NOT NULL,
    `Email`        varchar(255) NOT NULL UNIQUE,
    `PasswordHash` varchar(255) NOT NULL,
    `CreatedAt`    datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `UpdatedAt`    datetime              DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `Role`         varchar(50)           DEFAULT 'user',
    `IsActive`     boolean               DEFAULT 1,
    PRIMARY KEY (`UserID`)
);

INSERT INTO `Users` (`Username`, `Email`, `PasswordHash`, `Role`, `IsActive`)
VALUES ('admin', 'admin@example.com', 'hashedpassword1', 'admin', 1),
       ('user1', 'user1@example.com', 'hashedpassword2', 'user', 1),
       ('user2', 'user2@example.com', 'hashedpassword3', 'user', 1);

CREATE TABLE `Groups`
(
    `GroupID`   int          NOT NULL AUTO_INCREMENT,
    `UserID`    int          NOT NULL,
    `GroupName` varchar(100) NOT NULL,
    PRIMARY KEY (`GroupID`),
    KEY `UserID` (`UserID`),
    CONSTRAINT `Groups_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE
);

INSERT INTO `Groups` (`UserID`, `GroupName`)
VALUES (2, 'Kitchen'),
       (2, 'Living Room'),
       (3, 'Garage');

CREATE TABLE `Parameter`
(
    `ParameterID` int          NOT NULL AUTO_INCREMENT,
    `Name`        varchar(100) NOT NULL,
    `Unit`        varchar(50) DEFAULT NULL,
    `Description` text,
    PRIMARY KEY (`ParameterID`)
);

INSERT INTO `Parameter` (`Name`, `Unit`, `Description`)
VALUES ('Status', NULL, '0 - wyłączone, 1 - włączone'),
       ('Temperatura', '°C', 'Temperatura zadana lub aktualna'),
       ('Jasność', '%', 'Poziom jasności od 0% do 100%'),
       ('Tryb', NULL, 'Tryb pracy urządzenia klimatyzacyjnego'),
       ('Kolor', NULL, 'Kolor światła'),
       ('Stan', NULL, 'Stan bramy: otwarta/zamknięta'),
       ('Czułość', '%', 'Czułość czujnika ruchu od 0% do 100%');

CREATE TABLE `DeviceType`
(
    `DeviceTypeID` int          NOT NULL AUTO_INCREMENT,
    `TypeName`     varchar(100) NOT NULL,
    `Description`  text,
    PRIMARY KEY (`DeviceTypeID`)
);

INSERT INTO `DeviceType` (`TypeName`, `Description`)
VALUES ('Klimatyzacja', 'Urządzenie klimatyzacyjne'),
       ('Lampa', 'Urządzenie oświetleniowe'),
       ('Brama Wjazdowa', 'Brama do posesji, sterowana automatycznie'),
       ('Brama Garażowa', 'Automatyczna brama garażowa');

CREATE TABLE `Device`
(
    `DeviceID`     int          NOT NULL AUTO_INCREMENT,
    `DeviceTypeID` int          DEFAULT NULL,
    `GroupID`      int          DEFAULT NULL,
    `DeviceName`   varchar(100) NOT NULL UNIQUE,
    `DeviceUrl`    varchar(255) DEFAULT NULL,
    PRIMARY KEY (`DeviceID`),
    KEY `DeviceTypeID` (`DeviceTypeID`),
    KEY `GroupID` (`GroupID`),
    CONSTRAINT `Device_ibfk_1` FOREIGN KEY (`DeviceTypeID`) REFERENCES `DeviceType` (`DeviceTypeID`) ON DELETE SET NULL,
    CONSTRAINT `Device_ibfk_2` FOREIGN KEY (`GroupID`) REFERENCES `Groups` (`GroupID`) ON DELETE SET NULL
);

INSERT INTO `Device` (`DeviceTypeID`, `GroupID`, `DeviceName`)
VALUES (1, 1, 'klimatyzacja1'),
       (2, 1, 'lampa1'),
       (3, 3, 'brama_wjazdowa1'),
       (4, 3, 'brama_garazowa1'),
       (2, NULL, 'lampa2'),
       (5, 3, 'kamera1'),
       (6, 2, 'czujnik_ruchu1'),
       (2, 2, 'lampa3');

CREATE TABLE `DeviceTypeParameter`
(
    `DeviceTypeID` int NOT NULL,
    `ParameterID`  int NOT NULL,
    `DefaultValue` varchar(100) DEFAULT NULL,
    PRIMARY KEY (`DeviceTypeID`, `ParameterID`),
    KEY `ParameterID` (`ParameterID`),
    CONSTRAINT `DeviceTypeParameter_ibfk_1` FOREIGN KEY (`DeviceTypeID`) REFERENCES `DeviceType` (`DeviceTypeID`) ON DELETE CASCADE,
    CONSTRAINT `DeviceTypeParameter_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE
);

INSERT INTO `DeviceTypeParameter` (`DeviceTypeID`, `ParameterID`, `DefaultValue`)
VALUES (1, 1, '0'),         -- Klimatyzacja, Status
       (1, 2, '22'),        -- Klimatyzacja, Temperatura
       (1, 4, 'Cool'),      -- Klimatyzacja, Tryb
       (2, 1, '0'),         -- Lampa, Status
       (2, 3, '50'),        -- Lampa, Jasność
       (2, 5, 'Biały'),-- Lampa, Kolor
       (3, 6, 'zamknięta'), -- Brama Wjazdowa, Stan
       (4, 6, 'zamknięta'); -- Brama Garażowa, Stan

CREATE TABLE `DeviceParameter`
(
    `DeviceID`    int NOT NULL,
    `ParameterID` int NOT NULL,
    `Value`       varchar(100) DEFAULT NULL,
    PRIMARY KEY (`DeviceID`, `ParameterID`),
    KEY `ParameterID` (`ParameterID`),
    CONSTRAINT `DeviceParameter_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
    CONSTRAINT `DeviceParameter_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE
);

INSERT INTO `DeviceParameter` (`DeviceID`, `ParameterID`, `Value`)
VALUES (1, 1, '0'),        -- klimatyzacja1, Status
       (1, 2, '22'),       -- klimatyzacja1, Temperatura
       (1, 4, 'Heat'),     -- klimatyzacja1, Tryb
       (2, 1, '1'),        -- lampa1, Status
       (2, 3, '75'),       -- lampa1, Jasność
       (2, 5, 'Czerwony'), -- lampa1, Kolor
       (3, 6, 'zamknięta'),-- brama_wjazdowa1, Stan
       (4, 6, 'otwarta'),  -- brama_garazowa1, Stan

-- Urządzenie 5 - lampa2 (bez groupID)
       (5, 1, '0'),        -- lampa2, Status
       (5, 3, '50'),       -- lampa2, Jasność
       (5, 5, 'Zielony'),  -- lampa2, Kolor

-- Urządzenie 6 - kamera1
       (6, 1, '1'),        -- kamera1, Status

-- Urządzenie 7 - czujnik_ruchu1
       (7, 1, '1'),        -- czujnik_ruchu1, Status
       (7, 7, '80'),       -- czujnik_ruchu1, Czułość

-- Urządzenie 8 - lampa3
       (8, 1, '1'),        -- lampa3, Status
       (8, 3, '100'),      -- lampa3, Jasność
       (8, 5, 'Biały'); -- lampa3, Kolor

CREATE TABLE `SimulationData`
(
    `SimulationID`   int       NOT NULL AUTO_INCREMENT,
    `DeviceID`       int            DEFAULT NULL,
    `ParameterID`    int            DEFAULT NULL,
    `SimulatedValue` varchar(100)   DEFAULT NULL,
    `Timestamp`      timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`SimulationID`),
    KEY `DeviceID` (`DeviceID`),
    KEY `ParameterID` (`ParameterID`),
    CONSTRAINT `SimulationData_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
    CONSTRAINT `SimulationData_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE CASCADE
);

INSERT INTO `SimulationData` (`DeviceID`, `ParameterID`, `SimulatedValue`, `Timestamp`)
VALUES (1, 1, '0', NOW()),
       (1, 2, '22', NOW()),
       (1, 4, 'Heat', NOW()),
       (2, 1, '1', NOW()),
       (2, 3, '75', NOW()),
       (2, 5, 'Czerwony', NOW()),
       (3, 6, 'zamknięta', NOW()),
       (4, 6, 'otwarta', NOW());

CREATE TABLE `Schedule`
(
    `ScheduleID`     int  NOT NULL AUTO_INCREMENT,
    `DeviceID`       int  NOT NULL,
    `StartTime`      TIME NOT NULL,
    `EndTime`        TIME         DEFAULT NULL,
    `ParameterID`    int          DEFAULT NULL,
    `ParameterValue` varchar(100) DEFAULT NULL,
    `RepeatPattern`  varchar(50)  DEFAULT NULL,
    `ScheduleState`  INT(1)       DEFAULT 0,
    PRIMARY KEY (`ScheduleID`),
    KEY `DeviceID` (`DeviceID`),
    KEY `ParameterID` (`ParameterID`),
    CONSTRAINT `Schedule_ibfk_1` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE,
    CONSTRAINT `Schedule_ibfk_2` FOREIGN KEY (`ParameterID`) REFERENCES `Parameter` (`ParameterID`) ON DELETE SET NULL
);

INSERT INTO `Schedule` (`DeviceID`, `StartTime`, `EndTime`, `ParameterID`, `ParameterValue`, `RepeatPattern`)
VALUES (1, '08:00:00', NULL, 1, '1', 'codziennie');
-- Klimatyzacja nr 1 włączana codziennie o 8:00

CREATE TABLE `UserDevice`
(
    `UserID`   int NOT NULL,
    `DeviceID` int NOT NULL,
    PRIMARY KEY (`UserID`, `DeviceID`),
    KEY `DeviceID` (`DeviceID`),
    CONSTRAINT `UserDevice_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE,
    CONSTRAINT `UserDevice_ibfk_2` FOREIGN KEY (`DeviceID`) REFERENCES `Device` (`DeviceID`) ON DELETE CASCADE
);

INSERT INTO `UserDevice` (`UserID`, `DeviceID`)
VALUES (2, 1), -- user1 ma urządzenie 1 (klimatyzacja1)  -> i jest w grupie "Kitchen" user1
       (2, 2), -- user1 ma urządzenie 2 (lampa1)         -> i jest w grupie "Kitchen" user1
       (3, 3), -- user2 ma urządzenie 3 (brama_wjazdowa1)-> w grupie "Garage" user2
       (3, 4), -- user2 ma urządzenie 4 (brama_garazowa1)-> w grupie "Garage" user2
       (2, 5), -- user1 ma urządzenie 5 (lampa2)         -> brak grupy (NULL)
       (2, 7), -- user1 ma urządzenie 7 (czujnik_ruchu1) -> w grupie "Living Room" user1
       (3, 6), -- user2 ma urządzenie 6 (kamera1)        -> w grupie "Garage" user2
       (2, 8); -- user1 ma urządzenie 8 (lampa3)         -> w grupie "Living Room" user1

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;
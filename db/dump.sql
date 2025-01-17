SET NAMES 'utf8mb4' COLLATE 'utf8mb4_polish_ci';

DROP DATABASE IF EXISTS smarthaven;
CREATE DATABASE smarthaven CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci;
USE smarthaven;

DROP TABLE IF EXISTS `UserDevice`;
DROP TABLE IF EXISTS `DeviceTypeParameter`;
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

CREATE TABLE `Groups`
(
    `GroupID`   int          NOT NULL AUTO_INCREMENT,
    `UserID`    int          NOT NULL,
    `GroupName` varchar(100) NOT NULL,
    PRIMARY KEY (`GroupID`),
    KEY `UserID` (`UserID`),
    CONSTRAINT `Groups_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE
);

CREATE TABLE `Parameter`
(
    `ParameterID` int          NOT NULL AUTO_INCREMENT,
    `Name`        varchar(100) NOT NULL,
    `Unit`        varchar(50) DEFAULT NULL,
    `Description` text,
    PRIMARY KEY (`ParameterID`)
);

INSERT INTO `Parameter` (`Name`, `Unit`, `Description`)
VALUES ('Status', NULL, 'Status'),
       ('Temperature', '°C', 'Temperatura'),
       ('Brightness', '%', 'Poziom jasności'),
       ('State', NULL, 'Stan');

CREATE TABLE `DeviceType`
(
    `DeviceTypeID` int          NOT NULL AUTO_INCREMENT,
    `TypeName`     varchar(100) NOT NULL,
    `Description`  text,
    PRIMARY KEY (`DeviceTypeID`)
);

INSERT INTO `DeviceType` (`TypeName`, `Description`)
VALUES ('AC', 'Klimatyzacja'),
       ('Light', 'Lampa'),
       ('Gate', 'Brama');

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
    CONSTRAINT `Device_ibfk_1` FOREIGN KEY (`DeviceTypeID`) REFERENCES `DeviceType` (`DeviceTypeID`) ON DELETE SET NULL
);

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
VALUES (1, 1, '0'),  -- Klimatyzacja, Status
       (1, 2, '22'), -- Klimatyzacja, Temperatura
       (2, 1, '0'),  -- Lampa, Status
       (2, 3, '50'), -- Lampa, Jasność
       (3, 4, 'zamknięta'); -- Brama Wjazdowa, Stan


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
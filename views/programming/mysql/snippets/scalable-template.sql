/* This is a super-powerful database template I made, and have used it for many things. It is extremely scalable and in 5th normal form. :) */

DROP DATABASE IF EXISTS `objects`;
CREATE DATABASE `objects`;
USE `objects`;

/* Table of object types. */
CREATE TABLE IF NOT EXISTS `object_type`(
	`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`Description` VARCHAR(255) NOT NULL
);

/* Table of objects. */
CREATE TABLE IF NOT EXISTS `object`(
	`ObjectTypeID` INT NOT NULL,
	`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`Description` VARCHAR(255),
	FOREIGN KEY (`ObjectTypeID`) REFERENCES `object_type`(`ID`)
);

/* Table of property types. */
CREATE TABLE IF NOT EXISTS `property_type`(
	`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`Description` VARCHAR(255) NOT NULL
);

/* Table of properties. */
CREATE TABLE IF NOT EXISTS `property`(
	`ObjectID` INT NOT NULL,
	`PropertyTypeID` INT NOT NULL,
	`ID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`Value` LONGTEXT NOT NULL,
	`Description` VARCHAR(255),
	UNIQUE (`ObjectID`, `PropertyTypeID`, `Value`(255)),
	FOREIGN KEY (`ObjectID`) REFERENCES `object`(`ID`),
	FOREIGN KEY (`PropertyTypeID`) REFERENCES `property_type`(`ID`)
);

/* Example of an object and its properties. */
INSERT INTO `object_type` SET `ID` = 1, `Description` = 'Employee';

INSERT INTO `object` SET `ObjectTypeID` = 1, `ID` = 1, `Description` = 'Jon Hawks';

INSERT INTO `property_type` SET `ID` = 1, `Description` = 'Eye Color';
INSERT INTO `property_type` SET `ID` = 2, `Description` = 'Hair Color';

INSERT INTO `property` SET `ObjectID` = 1, `PropertyTypeID` = 1, `ID` = 1, `Value` = 'Blue';
INSERT INTO `property` SET `ObjectID` = 1, `PropertyTypeID` = 2, `ID` = 2, `Value` = 'Blonde';
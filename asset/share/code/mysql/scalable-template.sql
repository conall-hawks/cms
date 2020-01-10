/*-----------------------------------------------------------------------------\
| A super-awesome SQL boilerplate. Scales well; 5th normal form.               |
\-----------------------------------------------------------------------------*/

/* The database itself. */
DROP DATABASE IF EXISTS `boilerplate`;
CREATE DATABASE `boilerplate`;
USE `boilerplate`;

/* Table of object types. */
CREATE TABLE IF NOT EXISTS `object_type`(
    `id`          INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `description` VARCHAR(255)
);

/* Table of objects. */
CREATE TABLE IF NOT EXISTS `object`(
    `object_type_id` INT NOT NULL,
    `id`             INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `description`    VARCHAR(255),
    FOREIGN KEY (`object_type_id`) REFERENCES `object_type`(`id`)
);

/* Table of property types. */
CREATE TABLE IF NOT EXISTS `property_type`(
    `id`          INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `description` VARCHAR(255)
);

/* Table of properties. */
CREATE TABLE IF NOT EXISTS `property`(
    `object_id`        INT      NOT NULL,
    `property_type_id` INT      NOT NULL,
    `value`            LONGTEXT,
    `id`               INT      NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `description`      VARCHAR(255),
    FOREIGN KEY (`object_id`)        REFERENCES `object`(`id`),
    FOREIGN KEY (`property_type_id`) REFERENCES `property_type`(`id`),
    CONSTRAINT UNIQUE(`object_id`, `property_type_id`, `value`(255))
);

/* Example of an object and its properties. */
INSERT INTO `object_type` (`id`, `description`) VALUES(1, 'Employee');

INSERT INTO `object` (`object_type_id`, `id`, `description`) VALUES(1, 1, 'Jon Hawks');

INSERT INTO `property_type` (`id`, `description`) VALUES(1, 'Job Title');
INSERT INTO `property_type` (`id`, `description`) VALUES(2, 'Salary');

INSERT INTO `property` (`object_id`, `property_type_id`, `value`) VALUES(1, 1, 'CEO');
INSERT INTO `property` (`object_id`, `property_type_id`, `value`) VALUES(1, 2, 999999999999);

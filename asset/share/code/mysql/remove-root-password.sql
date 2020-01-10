USE `mysql`;
UPDATE `user` SET `password` = PASSWORD('') WHERE `User`='root';
FLUSH PRIVILEGES;

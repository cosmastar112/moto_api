# create databases
CREATE DATABASE IF NOT EXISTS `moto_test`;

USE moto_test;
CREATE USER 'moto'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON moto_test.* TO 'moto'@'%';
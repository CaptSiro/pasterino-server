-- MySQL Script generated by MySQL Workbench
-- Fri Aug  4 20:03:46 2023
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema pasterino
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `pasterino` ;

-- -----------------------------------------------------
-- Schema pasterino
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `pasterino` DEFAULT CHARACTER SET utf8 ;
USE `pasterino` ;

-- -----------------------------------------------------
-- Table `pasterino`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pasterino`.`users` ;

CREATE TABLE IF NOT EXISTS `pasterino`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(25) NOT NULL,
  `profile_picture` VARCHAR(256) NULL,
  `access_token` CHAR(30) NOT NULL,
  `state` CHAR(32) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pasterino`.`sessions`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pasterino`.`sessions` ;

CREATE TABLE IF NOT EXISTS `pasterino`.`sessions` (
  `id` CHAR(32) NOT NULL,
  `expires` DATETIME NOT NULL,
  `users_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_sessions_users_idx` (`users_id` ASC) VISIBLE,
  CONSTRAINT `fk_sessions_users`
    FOREIGN KEY (`users_id`)
    REFERENCES `pasterino`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pasterino`.`copypastas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pasterino`.`copypastas` ;

CREATE TABLE IF NOT EXISTS `pasterino`.`copypastas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `content` VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pasterino`.`tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pasterino`.`tags` ;

CREATE TABLE IF NOT EXISTS `pasterino`.`tags` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(16) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pasterino`.`copypastas_tags`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pasterino`.`copypastas_tags` ;

CREATE TABLE IF NOT EXISTS `pasterino`.`copypastas_tags` (
  `copypastas_id` INT UNSIGNED NOT NULL,
  `tags_id` INT UNSIGNED NOT NULL,
  INDEX `fk_copypastas_tags_copypastas1_idx` (`copypastas_id` ASC) VISIBLE,
  INDEX `fk_copypastas_tags_tags1_idx` (`tags_id` ASC) VISIBLE,
  CONSTRAINT `fk_copypastas_tags_copypastas1`
    FOREIGN KEY (`copypastas_id`)
    REFERENCES `pasterino`.`copypastas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_copypastas_tags_tags1`
    FOREIGN KEY (`tags_id`)
    REFERENCES `pasterino`.`tags` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pasterino`.`users_copypastas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pasterino`.`users_copypastas` ;

CREATE TABLE IF NOT EXISTS `pasterino`.`users_copypastas` (
  `users_id` INT UNSIGNED NOT NULL,
  `copypastas_id` INT UNSIGNED NOT NULL,
  `channel` VARCHAR(25) NULL,
  INDEX `fk_users_copypastas_users1_idx` (`users_id` ASC) VISIBLE,
  INDEX `fk_users_copypastas_copypastas1_idx` (`copypastas_id` ASC) VISIBLE,
  CONSTRAINT `fk_users_copypastas_users1`
    FOREIGN KEY (`users_id`)
    REFERENCES `pasterino`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_copypastas_copypastas1`
    FOREIGN KEY (`copypastas_id`)
    REFERENCES `pasterino`.`copypastas` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pasterino`.`states`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pasterino`.`states` ;

CREATE TABLE IF NOT EXISTS `pasterino`.`states` (
  `state` CHAR(32) NOT NULL,
  PRIMARY KEY (`state`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

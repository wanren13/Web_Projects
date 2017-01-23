CREATE DATABASE coffee;
CREATE TABLE coffees (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT,
	name VARCHAR(30) NOT NULL,
	PRIMARY KEY (id)
) ENGINE = InnoDB;

CREATE TABLE `coffee`.`ratings` ( 
	`coffeeId` INT UNSIGNED NOT NULL , 
	`user` VARCHAR(50) NOT NULL , 
	`rating` DECIMAL(1,1) NOT NULL , 
	`comment` TINYTEXT NOT NULL,
	FOREIGN key (coffeeId) REFERENCES coffees (id)
) ENGINE = InnoDB;
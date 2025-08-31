START TRANSACTION;
CREATE TABLE IF NOT EXISTS `admin_list` (
  `admin_id` INT NOT NULL AUTO_INCREMENT,
  `fullname` VARCHAR(255) NOT NULL,
  `username` VARCHAR(255) NOT NULL,
  `password` TEXT NOT NULL,
  `type` INT NOT NULL DEFAULT 1,
  `status` INT NOT NULL DEFAULT 1,
  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`admin_id`)
);
CREATE TABLE IF NOT EXISTS `election_list` (
	`election_id` INT NOT NULL AUTO_INCREMENT,
	`title` TEXT NOT NULL,
	`status` INT NOT NULL DEFAULT 0,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`election_id`)
);

CREATE TABLE IF NOT EXISTS `position_list` (
	`position_id` INT NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL,
	`max` INT NOT NULL DEFAULT 1,
	`status` INT NOT NULL DEFAULT 0,
	`order_by` INT NOT NULL DEFAULT 0,
	`type` INT NOT NULL DEFAULT 1,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`position_id`)
);

-- Now you can safely create candidate_list, as the referenced tables exist
CREATE TABLE IF NOT EXISTS `candidate_list` (
	`candidate_id` INT NOT NULL AUTO_INCREMENT,
	`position_id` INT NOT NULL,
	`firstname` TEXT NOT NULL,
	`middlename` TEXT,
	`lastname` TEXT NOT NULL,
	`suffix` TEXT,
	`scope_id` INT,
	`election_id` INT NOT NULL,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`candidate_id`),
	FOREIGN KEY (`election_id`) REFERENCES `election_list`(`election_id`) ON DELETE CASCADE,
	FOREIGN KEY (`position_id`) REFERENCES `position_list`(`position_id`) ON DELETE CASCADE
);




CREATE TABLE IF NOT EXISTS `region_list` (
	`region_id` INT NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`region_id`)
);

CREATE TABLE IF NOT EXISTS `province_list` (
	`province_id` INT NOT NULL AUTO_INCREMENT,
	`region_id` INT NOT NULL,
	`name` TEXT NOT NULL,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`province_id`),
	FOREIGN KEY (`region_id`) REFERENCES `region_list`(`region_id`) ON DELETE CASCADE
);

-- Create district_list table first (since it is referenced by city_list)
CREATE TABLE IF NOT EXISTS `district_list` (
	`district_id` INT NOT NULL AUTO_INCREMENT,
	`province_id` INT NOT NULL,
	`name` TEXT NOT NULL,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`district_id`),
	FOREIGN KEY (`province_id`) REFERENCES `province_list`(`province_id`) ON DELETE CASCADE
);

-- Now create city_list table, which references district_list
CREATE TABLE IF NOT EXISTS `city_list` (
	`city_id` INT NOT NULL AUTO_INCREMENT,
	`district_id` INT NOT NULL,
	`name` TEXT NOT NULL,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`city_id`),
	FOREIGN KEY (`district_id`) REFERENCES `district_list`(`district_id`) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS `election_list` (
	`election_id` INT NOT NULL AUTO_INCREMENT,
	`title` TEXT NOT NULL,
	`status` INT NOT NULL DEFAULT 0,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`election_id`)
);

CREATE TABLE IF NOT EXISTS `position_list` (
	`position_id` INT NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL,
	`max` INT NOT NULL DEFAULT 1,
	`status` INT NOT NULL DEFAULT 0,
	`order_by` INT NOT NULL DEFAULT 0,
	`type` INT NOT NULL DEFAULT 1,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`position_id`)
);


CREATE TABLE IF NOT EXISTS `voter_list` (
	`voter_id` INT NOT NULL AUTO_INCREMENT,
	`election_id` INT NOT NULL,
	`firstname` TEXT NOT NULL,
	`middlename` TEXT,
	`lastname` TEXT NOT NULL,
	`username` TEXT NOT NULL,
	`password` TEXT NOT NULL,
	`gender` TEXT NOT NULL,
	`dob` TEXT NOT NULL,
	`contact` TEXT NOT NULL,
	`city_id` INT NOT NULL,
	`status` INT NOT NULL DEFAULT 0,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`voter_id`),
	FOREIGN KEY (`city_id`) REFERENCES `city_list`(`city_id`) ON DELETE CASCADE,
	FOREIGN KEY (`election_id`) REFERENCES `election_list`(`election_id`) ON DELETE CASCADE
);



CREATE TABLE IF NOT EXISTS `vote_list` (
	`vote_id` INT NOT NULL AUTO_INCREMENT,
	`election_id` INT NOT NULL,
	`voter_id` INT NOT NULL,
	`position_id` INT NOT NULL,
	`candidate_id` INT NOT NULL,
	`date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`vote_id`),
	FOREIGN KEY (`candidate_id`) REFERENCES `candidate_list`(`candidate_id`) ON DELETE CASCADE,
	FOREIGN KEY (`election_id`) REFERENCES `election_list`(`election_id`) ON DELETE CASCADE,
	FOREIGN KEY (`position_id`) REFERENCES `position_list`(`position_id`) ON DELETE CASCADE,
	FOREIGN KEY (`voter_id`) REFERENCES `voter_list`(`voter_id`) ON DELETE CASCADE
);



INSERT INTO `admin_list` 
VALUES (1, 'Administrator', 'admin', '0192023a7bbd73250516f069df18b500', 1, 1, '2024-12-19 19:51:46');

COMMIT;

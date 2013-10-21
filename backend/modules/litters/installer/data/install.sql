CREATE TABLE IF NOT EXISTS `litters` (
	`id`          INT(11)                                                                   NOT NULL AUTO_INCREMENT,
	`meta_id`     INT(11)                                                                   NOT NULL,
	`category_id` INT(11)                                                                   NOT NULL,
	`language`    VARCHAR(5)                                                                NOT NULL,
	`name`        VARCHAR(255)                                                              NOT NULL,
	`father`      ENUM('S*Roseberry Leontes', 'ICH Evening in Kyoto de la Rosdollane')      NOT NULL,
	`mother`      ENUM('Frosty Snowbelle de la Rosdollane', 'Zion Hawaiian Ultra Violette') NOT NULL,
	`birth_date`  DATETIME                                                                  NOT NULL,
	`created_on`  DATETIME                                                                  NOT NULL,
	`edited_on`   DATETIME                                                                  NOT NULL,
	`sequence`    INT(11)                                                                   NOT NULL,
	PRIMARY KEY (`id`)
)
	ENGINE =MyISAM
	DEFAULT CHARSET =utf8
	COLLATE =utf8_unicode_ci;
CREATE TABLE `virtual_domains` (
`id`  INT NOT NULL AUTO_INCREMENT,
`name` VARCHAR(50) NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `virtual_users` (
`id` INT NOT NULL AUTO_INCREMENT,
`domain_id` INT NOT NULL,
`password` VARCHAR(106) NOT NULL,
`email` VARCHAR(120) NOT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `email` (`email`),
FOREIGN KEY (domain_id) REFERENCES virtual_domains(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `virtual_aliases` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `domain_id` INT NOT NULL,
  `source` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (domain_id) REFERENCES virtual_domains(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


INSERT INTO `servermail`.`virtual_domains`
(`id` ,`name`)
VALUES
  ('1', 'test.net'),
  ('2', 'server1.test.net');

INSERT INTO `servermail`.`virtual_users`
(`id`, `domain_id`, `password` , `email`)
VALUES
  ('1', '1', ENCRYPT('some_plain_password', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), 'hostmaster@test.net'),
  ('2', '1', ENCRYPT('some_plain_password', CONCAT('$6$', SUBSTRING(SHA(RAND()), -16))), 'info@test.net');


INSERT INTO `servermail`.`virtual_aliases`
(`id`, `domain_id`, `source`, `destination`)
VALUES
  ('1', '1', 'hostmaster@test.net', 'info@test.net');
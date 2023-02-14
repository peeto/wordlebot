DROP DATABASE IF EXISTS `wordle`;
CREATE DATABASE `wordle`;
USE `wordle`;

-- Tables

DROP TABLE IF EXISTS `word_letters`;
DROP TABLE IF EXISTS `words`;
DROP TABLE IF EXISTS `letters`;

CREATE TABLE `letters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `letter` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `letter` (`letter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

INSERT INTO `letters` VALUES (1,'a'),(2,'b'),(3,'c'),(4,'d'),(5,'e'),(6,'f'),(7,'g'),(8,'h'),(9,'i'),(10,'j'),(11,'k'),(12,'l'),(13,'m'),(14,'n'),(15,'o'),(16,'p'),(17,'q'),(18,'r'),(19,'s'),(20,'t'),(21,'u'),(22,'v'),(23,'w'),(24,'x'),(25,'y'),(26,'z');

CREATE TABLE `words` (
  `id` int NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `word_length` int GENERATED ALWAYS AS (length(`word`)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `word_letters` (
  `word_id` int NOT NULL,
  `letter_id` int NOT NULL,
  `position` int NOT NULL,
  PRIMARY KEY (`word_id`,`letter_id`,`position`),
  KEY `word_id` (`word_id`),
  KEY `letter_id` (`letter_id`),
  KEY `position` (`position`),
  CONSTRAINT `letter_id` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `word_id` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Triggers

DELIMITER //

CREATE TRIGGER `words_AFTER_INSERT` AFTER INSERT ON `words` FOR EACH ROW BEGIN
	DELETE wl.* FROM word_letters AS wl
    INNER JOIN words AS w ON w.id = wl.word_id
    WHERE w.word = new.word;
    
    insert into word_letters( word_id, letter_id, position)
    with recursive `cte` as 
        (select new.`id` AS `id`,new.word, left(new.`word`,1) AS `letter`, 1 AS `pos`
        union all
        select `cte`.`id` AS `id`,cte.word, substr(`cte`.`word`,(`cte`.`pos` + 1),1) as letter,(`cte`.`pos` + 1) AS `pos` from `cte`
        where (`cte`.`pos` < char_length(`cte`.`word`)))
    select `cte`.`id` AS `word_id`, l.id AS `letter_id`, `cte`.`pos` AS `position`
    from `cte`
    join letters as l on l.letter=cte.letter;
END //

CREATE TRIGGER `words_AFTER_UPDATE` AFTER UPDATE ON `words` FOR EACH ROW BEGIN
	DELETE wl.* FROM word_letters AS wl
    INNER JOIN words AS w ON w.id = wl.word_id
    WHERE w.word = new.word;
    
    insert into word_letters( word_id, letter_id, position)
    with recursive `cte` as 
        (select new.`id` AS `id`,new.word, left(new.`word`,1) AS `letter`, 1 AS `pos`
        union all
        select `cte`.`id` AS `id`,cte.word, substr(`cte`.`word`,(`cte`.`pos` + 1),1) as letter,(`cte`.`pos` + 1) AS `pos` from `cte`
        where (`cte`.`pos` < char_length(`cte`.`word`)))
    select `cte`.`id` AS `word_id`, l.id AS `letter_id`, `cte`.`pos` AS `position`
    from `cte`
    join letters as l on l.letter=cte.letter;
END //

CREATE TRIGGER `words_BEFORE_DELETE` BEFORE DELETE ON `words` FOR EACH ROW BEGIN
	DELETE wl.* FROM word_letters AS wl
    INNER JOIN words AS w ON w.id = wl.word_id
    WHERE w.word = old.word;
END //

DELIMITER ;

-- Views

DROP VIEW IF EXISTS `view_wordle_word_stats`;
DROP VIEW IF EXISTS `view_wordle_letter_stats`;
DROP VIEW IF EXISTS `view_wordle_words`;

CREATE VIEW `view_wordle_words` AS 
select `w`.`id` AS `id`,`w`.`word` AS `word`,`l1`.`letter` AS `l1`,`l2`.`letter` AS `l2`,`l3`.`letter` AS `l3`,`l4`.`letter` AS `l4`,`l5`.`letter` AS `l5`,`l1`.`id` AS `l1id`,`l2`.`id` AS `l2id`,`l3`.`id` AS `l3id`,`l4`.`id` AS `l4id`,`l5`.`id` AS `l5id` 
from ((((((((((`words` `w` 
left join `word_letters` `wl1` on(((`wl1`.`word_id` = `w`.`id`) and (`wl1`.`position` = 1)))) 
left join `letters` `l1` on((`l1`.`id` = `wl1`.`letter_id`))) 
left join `word_letters` `wl2` on(((`wl2`.`word_id` = `w`.`id`) and (`wl2`.`position` = 2)))) 
left join `letters` `l2` on((`l2`.`id` = `wl2`.`letter_id`))) 
left join `word_letters` `wl3` on(((`wl3`.`word_id` = `w`.`id`) and (`wl3`.`position` = 3)))) 
left join `letters` `l3` on((`l3`.`id` = `wl3`.`letter_id`))) 
left join `word_letters` `wl4` on(((`wl4`.`word_id` = `w`.`id`) and (`wl4`.`position` = 4)))) 
left join `letters` `l4` on((`l4`.`id` = `wl4`.`letter_id`))) 
left join `word_letters` `wl5` on(((`wl5`.`word_id` = `w`.`id`) and (`wl5`.`position` = 5)))) 
left join `letters` `l5` on((`l5`.`id` = `wl5`.`letter_id`))) 
where (`w`.`word_length` = 5);

CREATE VIEW `view_wordle_letter_stats` AS 
select `wl`.`letter_id` AS `letter_id`,`l`.`letter` AS `letter`,count(1) AS `usage` 
from ((`word_letters` `wl` 
join `letters` `l` on((`l`.`id` = `wl`.`letter_id`))) 
join `words` `w` on((`w`.`id` = `wl`.`word_id`))) 
where (`w`.`word_length` = 5) 
group by `l`.`id`,`l`.`letter` 
order by count(1) desc;

CREATE VIEW `view_wordle_word_stats` AS 
with `dl` as (select `wl`.`word_id` AS `word_id`,`wl`.`letter_id` AS `letter_id` 
from (`word_letters` `wl` 
join `words` `w` on((`w`.`id` = `wl`.`word_id`))) 
where (`w`.`word_length` = 5) 
group by `wl`.`word_id`,`wl`.`letter_id`),
`lusage` as (select `dl`.`word_id` AS `word_id`,sum(`vwls`.`usage`) AS `wusage`
from (`dl` 
join `view_wordle_letter_stats` `vwls` on((`vwls`.`letter_id` = `dl`.`letter_id`))) 
group by `dl`.`word_id` 
order by sum(`vwls`.`usage`) desc) 
select `vww`.`id` AS `id`,`vww`.`word` AS `word`,`vww`.`l1` AS `l1`,`vww`.`l2` AS `l2`,`vww`.`l3` AS `l3`,`vww`.`l4` AS `l4`,`vww`.`l5` AS `l5`,`vww`.`l1id` AS `l1id`,`vww`.`l2id` AS `l2id`,`vww`.`l3id` AS `l3id`,`vww`.`l4id` AS `l4id`,`vww`.`l5id` AS `l5id`,`lusage`.`wusage` AS `usage`
from (`view_wordle_words` `vww`
join `lusage` on((`lusage`.`word_id` = `vww`.`id`)))
order by `lusage`.`wusage` desc;


-- Tables

DROP TABLE IF EXISTS `wordle_words`;
DROP TABLE IF EXISTS `word_letters`;
DROP TABLE IF EXISTS `words`;
DROP TABLE IF EXISTS `letters`;

CREATE TABLE `letters` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `letter` varchar(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `letter` (`letter`)
) ENGINE=InnoDB DEFAULT CHARSET=ASCII;

INSERT INTO `letters` VALUES (1,'a'),(2,'b'),(3,'c'),(4,'d'),(5,'e'),(6,'f'),(7,'g'),(8,'h'),(9,'i'),(10,'j'),(11,'k'),(12,'l'),(13,'m'),(14,'n'),(15,'o'),(16,'p'),(17,'q'),(18,'r'),(19,'s'),(20,'t'),(21,'u'),(22,'v'),(23,'w'),(24,'x'),(25,'y'),(26,'z');

CREATE TABLE `words` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `word` varchar(255) NOT NULL,
  `word_length` int UNSIGNED GENERATED ALWAYS AS (length(`word`)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `word` (`word`),
  KEY `word_length` (`word_length`)
) ENGINE=InnoDB DEFAULT CHARSET=ASCII;

CREATE TABLE `word_letters` (
  `word_id` int UNSIGNED NOT NULL,
  `letter_id` int UNSIGNED NOT NULL,
  `position` int UNSIGNED NOT NULL,
  PRIMARY KEY (`word_id`,`letter_id`,`position`),
  KEY `word_id` (`word_id`),
  KEY `letter_id` (`letter_id`),
  KEY `position` (`position`),
  CONSTRAINT `letter_id` FOREIGN KEY (`letter_id`) REFERENCES `letters` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `word_id` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ASCII;

CREATE TABLE `wordle_words` (
  `word_id` int UNSIGNED  NOT NULL,
  `word` varchar(255) NOT NULL,
  `l1` varchar(1),
  `l1id` int UNSIGNED,
  `l1count` int UNSIGNED,
  `l2` varchar(1),
  `l2id` int UNSIGNED,
  `l2count` int UNSIGNED,
  `l3` varchar(1),
  `l3id` int UNSIGNED,
  `l3count` int UNSIGNED,
  `l4` varchar(1),
  `l4id` int UNSIGNED,
  `l4count` int UNSIGNED,
  `l5` varchar(1),
  `l5id` int UNSIGNED,
  `l5count` int UNSIGNED,
  `usage` int UNSIGNED NOT NULL,
  KEY `word_id` (`word_id`),
  KEY `word` (`word`),
  KEY `l1` (`l1`),
  KEY `l2` (`l2`),
  KEY `l3` (`l3`),
  KEY `l4` (`l4`),
  KEY `l5` (`l5`),
  KEY `usage` (`usage`),
  CONSTRAINT `ww_word_id` FOREIGN KEY (`word_id`) REFERENCES `words` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=ASCII;

-- Triggers

DELIMITER //

CREATE TRIGGER `words_AFTER_INSERT` AFTER INSERT ON `words` FOR EACH ROW BEGIN
    delete
        wl.*
    from
        word_letters as wl
    inner join
        words as w on w.id = wl.word_id
    where
        w.word = new.word;
    
    insert into word_letters( word_id, letter_id, position)
    with recursive `cte` as (
        select
            new.`id` AS `id`,
            new.word,
            left(new.`word`,1) AS `letter`,
            1 AS `pos`
        union all
        select
            `cte`.`id` AS `id`,
            cte.word,
            substr(`cte`.`word`,(`cte`.`pos` + 1),1) as letter,
            (`cte`.`pos` + 1) AS `pos` from `cte`
        where
            `cte`.`pos` < char_length(`cte`.`word`))
    select
        `cte`.`id` AS `word_id`,
        l.id AS `letter_id`,
        `cte`.`pos` AS `position`
    from
        `cte`
    join
        letters as l on l.letter = cte.letter;
END //

CREATE TRIGGER `words_AFTER_UPDATE` AFTER UPDATE ON `words` FOR EACH ROW BEGIN
    delete
        wl.*
    from
        word_letters as wl
    inner join
        words as w on w.id = wl.word_id
    where
        w.word = new.word;
    
    insert into word_letters( word_id, letter_id, position)
    with recursive `cte` as (
        select
            new.`id` AS `id`,
            new.word,
            left(new.`word`,1) AS `letter`,
            1 AS `pos`
        union all
        select
            `cte`.`id` AS `id`,
            cte.word,
            substr(`cte`.`word`,(`cte`.`pos` + 1),1) as letter,
            (`cte`.`pos` + 1) AS `pos` from `cte`
        where
            `cte`.`pos` < char_length(`cte`.`word`))
    select
        `cte`.`id` AS `word_id`,
        l.id AS `letter_id`,
        `cte`.`pos` AS `position`
    from
        `cte`
    join
        letters as l on l.letter = cte.letter;
END //

CREATE TRIGGER `words_BEFORE_DELETE` BEFORE DELETE ON `words` FOR EACH ROW BEGIN
    delete
        wl.*
    from
        word_letters as wl
    inner join
        words as w on w.id = wl.word_id
    where
        w.word = old.word;
END //

DELIMITER ;

-- Views

DROP VIEW IF EXISTS `view_wordle_word_stats`;
DROP VIEW IF EXISTS `view_wordle_letter_stats`;
DROP VIEW IF EXISTS `view_wordle_words`;

CREATE OR REPLACE VIEW `view_wordle_letter_stats` AS
select
    `wl`.`letter_id` AS `letter_id`,
    `l`.`letter` AS `letter`,
    count(1) AS `usage`
from
    `word_letters` as `wl`
join
    `letters` as `l` on `l`.`id` = `wl`.`letter_id`
join
    `words` as `w` on `w`.`id` = `wl`.`word_id`
where
    `w`.`word_length` = 5
group by
    `l`.`id`,`l`.`letter`
order by
    count(1) desc;

CREATE OR REPLACE VIEW `view_wordle_words` AS
select
    `w`.`id` AS `id`,
    `w`.`word` AS `word`,
    `l1`.`letter` AS `l1`,
    `l2`.`letter` AS `l2`,
    `l3`.`letter` AS `l3`,
    `l4`.`letter` AS `l4`,
    `l5`.`letter` AS `l5`,
    `l1`.`id` AS `l1id`,
    `l2`.`id` AS `l2id`,
    `l3`.`id` AS `l3id`,
    `l4`.`id` AS `l4id`,
    `l5`.`id` AS `l5id`,
    `l1c`.`num` AS `l1count`,
    `l2c`.`num` AS `l2count`,
    `l3c`.`num` AS `l3count`,
    `l4c`.`num` AS `l4count`,
    `l5c`.`num` AS `l5count`
from
    `words` as `w`
left join
    `word_letters` as `wl1` on `wl1`.`word_id` = `w`.`id` and `wl1`.`position` = 1
left join
    `letters` as `l1` on `l1`.`id` = `wl1`.`letter_id`
left join
    (
        select
            word_id, letter_id, count(1) as num
        from
            word_letters
        group by
            word_id, letter_id
    ) as l1c on l1c.word_id = w.id and l1c.letter_id = wl1.letter_id
left join
    `word_letters` as `wl2` on `wl2`.`word_id` = `w`.`id` and `wl2`.`position` = 2
left join
    `letters` as `l2` on `l2`.`id` = `wl2`.`letter_id`
left join
    (
        select
            word_id, letter_id, count(1) as num
        from
            word_letters
        group by
            word_id, letter_id
    ) as l2c on l2c.word_id = w.id and l2c.letter_id = wl2.letter_id
left join
    `word_letters` as `wl3` on `wl3`.`word_id` = `w`.`id` and `wl3`.`position` = 3
left join
    `letters` as `l3` on `l3`.`id` = `wl3`.`letter_id`
left join
    (
        select
            word_id, letter_id, count(1) as num
        from
            word_letters
        group by
            word_id, letter_id
    ) as l3c on l3c.word_id = w.id and l3c.letter_id = wl3.letter_id
left join
    `word_letters` as `wl4` on `wl4`.`word_id` = `w`.`id` and `wl4`.`position` = 4
left join
    `letters` as `l4` on `l4`.`id` = `wl4`.`letter_id`
left join
    (
        select
            word_id, letter_id, count(1) as num
        from
            word_letters
        group by
            word_id, letter_id
    ) as l4c on l4c.word_id = w.id and l4c.letter_id = wl4.letter_id
left join
    `word_letters` as `wl5` on `wl5`.`word_id` = `w`.`id` and `wl5`.`position` = 5
left join
    `letters` as `l5` on `l5`.`id` = `wl5`.`letter_id`
left join
    (
        select
            word_id, letter_id, count(1) as num
        from
            word_letters
        group by
            word_id, letter_id
    ) as l5c on l5c.word_id = w.id and l5c.letter_id = wl5.letter_id
where
    `w`.`word_length` = 5;

CREATE VIEW `view_wordle_word_stats` AS
with `dl` as (
    select
        `wl`.`word_id` AS `word_id`,
        `wl`.`letter_id` AS `letter_id`
    from
        `word_letters` as `wl`
    join
        `words` as `w` on `w`.`id` = `wl`.`word_id`
    where
        `w`.`word_length` = 5
    group by
        `wl`.`word_id`,`wl`.`letter_id`
),
`lusage` as (
    select
        `dl`.`word_id` AS `word_id`,
        sum(`vwls`.`usage`) AS `wusage`
    from
        `dl`
    join
        `view_wordle_letter_stats` as `vwls` on `vwls`.`letter_id` = `dl`.`letter_id`
    group by
        `dl`.`word_id`
    order by
        sum(`vwls`.`usage`) desc
)
select
    `vww`.*,
    `lusage`.`wusage` as `usage`
from
    `view_wordle_words` as `vww`
join
    `lusage` on `lusage`.`word_id` = `vww`.`id`
order by
    `lusage`.`wusage` desc;

-- Procedures

DROP PROCEDURE IF EXISTS `truncate_words`;
DELIMITER $$
CREATE PROCEDURE `truncate_words` ()
BEGIN
	DELETE FROM wordle_words;
	DELETE FROM word_letters;
	DELETE FROM words;
	TRUNCATE wordle_words;
	TRUNCATE word_letters;
	TRUNCATE words;
END$$
DELIMITER ;

DROP PROCEDURE IF EXISTS `rebuild_wordle_words`;
DELIMITER $$
CREATE PROCEDURE `rebuild_wordle_words` ()
BEGIN
	truncate wordle_words;
	insert into wordle_words (
	  `word_id`, `word`, 
	  `l1`, `l1id`, `l1count`,
	  `l2`, `l2id`, `l2count`,
	  `l3`, `l3id`, `l3count`,
	  `l4`, `l4id`, `l4count`,
	  `l5`, `l5id`, `l5count`,
	  `usage`
	  )
	select 
	  `id`, ucase(`word`), 
	  ucase(`l1`), `l1id`, `l1count`,
	  ucase(`l2`), `l2id`, `l2count`,
	  ucase(`l3`), `l3id`, `l3count`,
	  ucase(`l4`), `l4id`, `l4count`,
	  ucase(`l5`), `l5id`, `l5count`,
	  `usage`
	from view_wordle_word_stats;
END$$
DELIMITER ;


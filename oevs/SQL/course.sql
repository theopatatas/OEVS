ALTER TABLE `voters`
  ADD COLUMN `Course` varchar(255) NULL AFTER `Year`;


ALTER TABLE `candidate`
  ADD COLUMN `Department` VARCHAR(10) NULL AFTER `Year`,
  ADD COLUMN `Course` VARCHAR(255) NULL AFTER `Department`;

ALTER TABLE `candidate`
  ADD COLUMN `Campus` VARCHAR(128) DEFAULT 'Au Main' NULL;

-- (optional) set a campus value for existing rows if you prefer a specific default
UPDATE `candidate`
  SET `Campus` = 'Au Main'
  WHERE `Campus` IS NULL OR `Campus` = '';
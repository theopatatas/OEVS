ALTER TABLE `voters`
  ADD COLUMN `Course` varchar(255) NULL AFTER `Year`;


ALTER TABLE `candidate`
  ADD COLUMN `Department` VARCHAR(10) NULL AFTER `Year`,
  ADD COLUMN `Course` VARCHAR(255) NULL AFTER `Department`;

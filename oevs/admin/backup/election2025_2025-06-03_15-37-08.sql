-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: oevs
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `candidate`
--

DROP TABLE IF EXISTS `candidate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `candidate` (
  `CandidateID` int(11) NOT NULL AUTO_INCREMENT,
  `abc` varchar(1) NOT NULL,
  `Position` varchar(200) NOT NULL,
  `Party` varchar(100) NOT NULL,
  `FirstName` varchar(200) NOT NULL,
  `LastName` varchar(200) NOT NULL,
  `MiddleName` varchar(100) NOT NULL,
  `Gender` varchar(6) NOT NULL,
  `Year` varchar(100) NOT NULL,
  `Photo` varchar(200) NOT NULL,
  `Qualification` text NOT NULL,
  PRIMARY KEY (`CandidateID`)
) ENGINE=MyISAM AUTO_INCREMENT=238 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `candidate`
--

LOCK TABLES `candidate` WRITE;
/*!40000 ALTER TABLE `candidate` DISABLE KEYS */;
INSERT INTO `candidate` VALUES (212,'a','Governor','Team 1','Joseph','Santos','C','Male','4th year','upload/1748141762_SANTOS,MARK JOSEPH C (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(205,'v','Vice-President','Team 2','Eljohn','Miranda','G','Male','4th year','upload/1748141520_MIRANDA,EL JOHN G (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(204,'v','Vice-President','Team 1','John Arnie ','Mariano','A','Male','4th year','upload/1748141490_MARIANO,JOHN ARNIE  (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(203,'p','President','Team 2','Cristian','Fernandez','','Male','4th year','upload/1748141446_FERNANDEZ,CRISTIAN  (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(202,'p','President','Team 1','Kristopher Glenn','Martinez','','Male','4th year','upload/1748141388_MARTINEZ,KRISTOPHER GLENN (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(207,'a','Governor','Team 2','Don Emmanuel','Aluquin','','Male','4th year','upload/1748141580_ALUQUIN,DON EMMANUEL (19).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(208,'b','Vice-Governor','Team 1','Margarette','Roque','E','FeMale','4th year','upload/1748141612_ROQUE,JEIZEE MARGARETTE E (12).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(213,'b','Vice-Governor','Team 1','Kurt Angelo','Aragon','S','Male','4th year','upload/1748141806_ARAGON,KURT ANGELO A (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(210,'s','Secretary','Team 1','Aeron Paul','Salipsip','','Male','4th year','upload/1748141686_SALIPSIP,AERON PAUL  (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(211,'s','Secretary','Team 2','Justine','Retiro','D','Male','4th year','upload/1748141716_RETIRO,JUSTINE D (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(214,'t','Treasurer','Team 1','Vincent','Francisco','','Male','4th year','upload/1748141837_FRANCISCO,VINCENT C (11).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(215,'t','Treasurer','Team 2','Jeff','Ladignon','','Male','4th year','upload/1748141863_LADIGNON,JEFF M (10).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(216,'s','Social-Media Officer','Team 1','Cyrell','Domingo','','Male','4th year','upload/1748141886_DOMINGO,CYRELL T (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(217,'s','Social-Media Officer','Team 2','Vincent','Unarce','','Male','4th year','upload/1748141909_UNARCE,VINCENT E  (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(218,'r','Representative','Team 1','Amiel Angelo','Villanueva','','Male','4th year','upload/1748141937_VILLANIUEVA,AMIEL ANGELO (10).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(219,'r','Representative','Team 2','John Michael','Parungao','','Male','4th year','upload/1748141994_Screenshot 2025-05-25 105943.png','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(221,'p','President','Team 1','Aleacel','Postor','R','FeMale','4th year','upload/1748169328_Screenshot 2025-05-25 183516.png','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(222,'p','President','Team 2','John Cedrick','Melegrito','A','Male','4th year','upload/1748169391_MELEGRITO,JOHN CEDRICK A (10).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(223,'p','President','Team 1','Adrianne Aebes','Maligaya','Q','Male','4th year','upload/1748169432_MALIGAYA, ADRIANNE AEBES Q (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(224,'v','Vice-President','Team 2','John Riel','Parcasio','N','Male','4th year','upload/1748169581_PARCASION,JOHN RIEL (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(225,'b','Vice-Governor','Team 1','Sharmaine','Blanca','','FeMale','4th year','upload/1748169628_BLANCA,SHARMAINE (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(226,'s','Secretary','Team 1','David Tristan','Bernal','M','Male','4th year','upload/1748169665_BERNAL,DAVID TRISTAN M (11).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(227,'b','Vice-Governor','Team 2','Ma.Alyssa','Sevilla','','FeMale','4th year','upload/1748169711_MA.ALYSSA SEVILLA  (10).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(228,'b','Vice-Governor','Team 2','Erika Lorraine','Frliciano','','FeMale','4th year','upload/1748169748_FELICIANO,ERIKA LORRAINE CLAMONTE (9).jpg',''),(229,'t','Treasurer','Team 1','Lanz Andrei','Molina','T','Male','4th year','upload/1748169810_MOLINA,LANZ ANDREI (9).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(230,'v','Vice-President','Team 2','Jerald','Torrs','','Male','4th year','upload/1748169838_TORRES,JERALD T (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(231,'s','Social-Media Officer','Team 1','Jessica','Villabriga','','FeMale','4th year','upload/1748169885_VILLABRIGA,JESSICA T (8).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.'),(232,'p','President','Team 1','Franz Andrei ','Villasquez','','Male','4th year','upload/1748169913_VILLASQUEZ,FRANZ ANDREI A (12).jpg','I am a currently enrolled student with a strong academic record, maintaining a general weighted average of 85% or higher. I have no disciplinary issues and am an active participant in various school activities and organizations. I have demonstrated leadership skills through previous roles and possess relevant experience related to the position I am running for. I am committed to serving my peers with dedication, responsibility, and integrity.');
/*!40000 ALTER TABLE `candidate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `complaint`
--

DROP TABLE IF EXISTS `complaint`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `complaint` (
  `complaint_id` int(11) NOT NULL AUTO_INCREMENT,
  `voterID` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Username` varchar(100) NOT NULL,
  `SchoolID` varchar(50) NOT NULL,
  `Year` varchar(100) NOT NULL,
  PRIMARY KEY (`complaint_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `complaint`
--

LOCK TABLES `complaint` WRITE;
/*!40000 ALTER TABLE `complaint` DISABLE KEYS */;
INSERT INTO `complaint` VALUES (10,100,'1','Voting machines malfunctioned, causing long delays and confusion at polls.','pending','2025-06-03 11:03:55','2025-06-03 11:03:55','joan.mariano.au@phinmaed.com','01-1234-12345','4th year'),(11,96,'2','Some voters were turned away due to missing names on lists.','pending','2025-06-03 11:05:27','2025-06-03 11:05:27','jimz.aluquin.au@phinmaed.com','01-1234-12345','4th year'),(12,97,'3','Ballots were not properly secured, risking tampering during the counting.','pending','2025-06-03 11:06:10','2025-06-03 11:06:10','elgo.miranda.au@phinmaed.com','01-1234-12345','4th year'),(13,98,'4','Campaign materials were removed unfairly, limiting candidates’ chances to communicate.','resolved','2025-06-03 11:07:03','2025-06-03 11:25:32','don.aluquin.au@phinmaed.com','01-1234-12345','4th year'),(14,101,'5','Polling stations opened late, resulting in voters missing their chance.','in_progress','2025-06-03 11:09:13','2025-06-03 11:25:31','aeron.salipsip.au@phinmaed.com	','01-1234-12345','4th year');
/*!40000 ALTER TABLE `complaint` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `history`
--

DROP TABLE IF EXISTS `history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `history` (
  `history_id` int(11) NOT NULL AUTO_INCREMENT,
  `data` varchar(30) NOT NULL,
  `action` varchar(50) NOT NULL,
  `date` varchar(20) NOT NULL,
  `user` varchar(20) NOT NULL,
  PRIMARY KEY (`history_id`)
) ENGINE=MyISAM AUTO_INCREMENT=874 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `history`
--

LOCK TABLES `history` WRITE;
/*!40000 ALTER TABLE `history` DISABLE KEYS */;
INSERT INTO `history` VALUES (793,'Laydee Champagne','Login','2025-05-26 16:41:40','admin'),(792,'Vincent Unarce','Added Voter','5/26/2025 16:39:31','admin'),(791,'Jeff Ladignon','Added Voter','5/26/2025 16:38:43','admin'),(790,'Aeron Paul Salipsip','Added Voter','5/26/2025 16:38:6','admin'),(789,'Laydee Champagne','Login','2025-05-26 16:38:03','admin'),(788,'Kat CSDL','Logout','2025-05-26 16:37:48','admin'),(787,'Kat CSDL','Login','2025-05-26 16:36:55','admin'),(786,'Kat CSDL','Logout','2025-05-26 16:35:07','admin'),(785,'Kat CSDL','Login','2025-05-26 16:34:51','admin'),(784,'Laydee Champagne','Logout','2025-05-26 16:34:37','admin'),(783,'Amiel Villanueva','Added Voter','5/26/2025 16:32:55','admin'),(782,'Jimmuel Aluquin','Added Voter','5/26/2025 16:32:16','admin'),(781,'Don Aluquin','Added Voter','5/26/2025 16:31:28','admin'),(780,'Eljohn Miranda','Added Voter','5/26/2025 16:31:3','admin'),(779,'Jay Mariano','Added Voter','5/26/2025 16:30:37','admin'),(778,'Joseph Santos','Added Voter','5/26/2025 16:29:46','admin'),(777,'Laydee Champagne','Login','2025-05-26 16:28:51','admin'),(776,'Kat CSDL','Logout','2025-05-26 16:25:14','admin'),(775,'Kat CSDL','Login','2025-05-26 16:24:59','admin'),(794,'Laydee Champagne','Added Voter','5/27/2025 8:37:16','admin'),(795,'Laydee Champagne','Added Voter','5/27/2025 8:37:56','admin'),(796,'Laydee Champagne','Login','2025-06-01 18:10:44','admin'),(797,'Laydee Champagne','Logout','2025-06-01 19:13:40','admin'),(798,'Laydee Champagne','Login','2025-06-01 19:13:56','admin'),(799,'Laydee Champagne','Login','2025-06-01 19:15:34','admin'),(800,'Laydee Champagne','Logout','2025-06-01 19:20:35','admin'),(801,'Laydee Champagne','Login','2025-06-03 17:09:31','admin'),(802,'Joseph FAFA','Added Candidate','2025-06-03 17:15:27','admin'),(803,'ASDFA FAFA','Added Candidate','2025-06-03 17:17:54','admin'),(804,'Joseph FAFA','Added Candidate','2025-06-03 17:23:07','admin'),(805,'Joseph FAFA','Deleted Candidate','6/3/2025 17:23:37','Admin'),(806,'','Deleted Candidate','6/3/2025 17:23:46','Admin'),(807,'Joseph FAFA','Edit Candidate','2025-06-03 17:26:47','admin'),(808,'Joseph FAFA','Edit Candidate','2025-06-03 17:27:22','admin'),(809,'Joseph FAFA','Edit Candidate','2025-06-03 17:29:23','admin'),(810,'Joseph FAFA','Edit Candidate','2025-06-03 17:29:26','admin'),(811,'Joseph FAFA','Edit Candidate','2025-06-03 17:30:34','admin'),(812,'Joseph FAFA','Edit Candidate','2025-06-03 17:30:44','admin'),(813,'Laydee Champagne','Login','2025-06-03 17:36:51','admin'),(814,'Joseph FAFA','Deleted Candidate','6/3/2025 18:8:50','Admin'),(815,'test test','Added Voter','6/3/2025 18:9:6','admin'),(816,'test1 test1','Added Voter','6/3/2025 18:9:24','admin'),(817,'test2 test2','Added Voter','6/3/2025 18:9:42','admin'),(818,' ','Logout','2025-06-03 18:20:05',''),(819,'Laydee Champagne','Login','2025-06-03 18:20:10','admin'),(820,'Laydee Champagne','Login','2025-06-03 18:20:59','admin'),(821,'Laydee Champagne','Login','2025-06-03 18:22:07','admin'),(822,'Laydee Champagne','Login','2025-06-03 18:23:42','admin'),(823,'Jimmuel Aluquin','Added Voter','6/3/2025 18:40:40','admin'),(824,'Eljohn Miranda','Added Voter','6/3/2025 18:41:15','admin'),(825,'Don Aluquin','Added Voter','6/3/2025 18:41:38','admin'),(826,'Joseph Santos','Added Voter','6/3/2025 18:41:54','admin'),(827,'John Arnie Mariano','Added Voter','6/3/2025 18:42:15','admin'),(828,'Aeron Paul Salipsip','Added Voter','6/3/2025 18:42:42','admin'),(829,'Amiel Villanueva','Added Voter','6/3/2025 18:42:57','admin'),(830,'Jeff Ladignon','Added Voter','6/3/2025 18:43:17','admin'),(831,'Vincent Unarce','Added Voter','6/3/2025 18:43:37','admin'),(832,'Laydee Champagne','Added Voter','6/3/2025 18:43:56','admin'),(833,'Laydee Champagne','Added Voter','6/3/2025 18:44:17','admin'),(834,'Joseph Santos','Edit Candidate','2025-06-03 18:47:23','admin'),(835,'Don Emmanuel Aluquin','Edit Candidate','2025-06-03 18:48:02','admin'),(836,'Margarette Roque','Edit Candidate','2025-06-03 18:48:06','admin'),(837,'Kurt Angelo Aragon','Edit Candidate','2025-06-03 18:48:11','admin'),(838,'Sharmaine Blanca','Edit Candidate','2025-06-03 18:48:18','admin'),(839,'Ma.Alyssa Sevilla','Edit Candidate','2025-06-03 18:48:22','admin'),(840,'Cristian Fernandez','Edit Candidate','2025-06-03 18:48:34','admin'),(841,'Cristian Fernandez','Edit Candidate','2025-06-03 18:48:38','admin'),(842,'Kristopher Glenn Martinez','Edit Candidate','2025-06-03 18:48:42','admin'),(843,'Aleacel Postor','Edit Candidate','2025-06-03 18:48:48','admin'),(844,'John Cedrick Melegrito','Edit Candidate','2025-06-03 18:48:54','admin'),(845,'Adrianne Aebes Maligaya','Edit Candidate','2025-06-03 18:48:58','admin'),(846,'Franz Andrei  Villasquez','Edit Candidate','2025-06-03 18:49:02','admin'),(847,'Amiel Angelo Villanueva','Edit Candidate','2025-06-03 18:49:06','admin'),(848,'John Michael Parungao','Edit Candidate','2025-06-03 18:49:10','admin'),(849,'Aeron Paul Salipsip','Edit Candidate','2025-06-03 18:49:17','admin'),(850,'Justine Retiro','Edit Candidate','2025-06-03 18:49:22','admin'),(851,'Cyrell Domingo','Edit Candidate','2025-06-03 18:49:28','admin'),(852,'Vincent Unarce','Edit Candidate','2025-06-03 18:49:44','admin'),(853,'David Tristan Bernal','Edit Candidate','2025-06-03 18:49:54','admin'),(854,'Jessica Villabriga','Edit Candidate','2025-06-03 18:50:00','admin'),(855,'Vincent Francisco','Edit Candidate','2025-06-03 18:50:06','admin'),(856,' ','Logout','2025-06-03 18:50:08',''),(857,'Laydee Champagne','Login','2025-06-03 18:50:13','admin'),(858,'Jeff Ladignon','Edit Candidate','2025-06-03 18:50:26','admin'),(859,'Lanz Andrei Molina','Edit Candidate','2025-06-03 18:50:31','admin'),(860,'Eljohn Miranda','Edit Candidate','2025-06-03 18:50:40','admin'),(861,'John Arnie  Mariano','Edit Candidate','2025-06-03 18:50:48','admin'),(862,'John Riel Parcasio','Edit Candidate','2025-06-03 18:50:55','admin'),(863,'Jerald Torrs','Edit Candidate','2025-06-03 18:51:21','admin'),(864,'Laydee Champagne','Logout','2025-06-03 18:51:44','admin'),(865,'Laydee Champagne','Login','2025-06-03 18:51:48','admin'),(866,'Laydee Champagne','Logout','2025-06-03 18:58:57','admin'),(867,'Laydee Champagne','Login','2025-06-03 18:59:26','admin'),(868,'Laydee Champagne','Login','2025-06-03 19:00:11','admin'),(869,'Laydee Champagne','Login','2025-06-03 19:01:15','admin'),(870,'Laydee Champagne','Login','2025-06-03 19:07:32','admin'),(871,'Laydee Champagne','Login','2025-06-03 19:12:01','admin'),(872,'Laydee Champagne','Logout','2025-06-03 19:26:01','admin'),(873,'Laydee Champagne','Login','2025-06-03 21:29:51','admin');
/*!40000 ALTER TABLE `history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voters`
--

DROP TABLE IF EXISTS `voters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voters` (
  `VoterID` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(150) NOT NULL,
  `LastName` varchar(150) NOT NULL,
  `MiddleName` varchar(100) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Year` varchar(100) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `SchoolID` varchar(100) NOT NULL,
  `Verified` enum('Verified','Not Verified') NOT NULL DEFAULT 'Not Verified',
  `DateVoted` varchar(50) NOT NULL,
  `TimeVoted` varchar(50) NOT NULL,
  `Room` varchar(100) NOT NULL,
  PRIMARY KEY (`VoterID`)
) ENGINE=MyISAM AUTO_INCREMENT=107 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voters`
--

LOCK TABLES `voters` WRITE;
/*!40000 ALTER TABLE `voters` DISABLE KEYS */;
INSERT INTO `voters` VALUES (106,'Laydee','Champagne','','laydee.champagne1.au@phinmaed.com','01-1234-12345','','4th year','Unvoted','01-1234-12345','Not Verified','','',''),(105,'Laydee','Champagne','','laydee.champagne.au@phinmaed.com','01-1234-12345','','4th year','Unvoted','01-1234-12345','Verified','','',''),(104,'Vincent','Unarce','','Vincent.Unarce.au@phinmaed.com	','01-1234-12345','','4th year','Unvoted','01-1234-12345','Verified','','',''),(103,'Jeff','Ladignon','','jeff.ladignon.au@phinmaed.com','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:10:34','Comlab 4'),(102,'Amiel','Villanueva','','amiel.villanueva.au@phinmaed.com','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:10:10','Comlab 3'),(101,'Aeron Paul','Salipsip','','aeron.salipsip.au@phinmaed.com	','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:09:31','Comlab 2'),(100,'John Arnie','Mariano','','joan.mariano.au@phinmaed.com','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:04:38','Comlab 1'),(99,'Joseph','Santos','','maca.santos.au@phinmaed.com	','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:08:36','Comlab 1'),(98,'Don','Aluquin','','don.aluquin.au@phinmaed.com','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:07:23','Comlab 4'),(97,'Eljohn','Miranda','','elgo.miranda.au@phinmaed.com','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:06:29','Comlab 3'),(96,'Jimmuel','Aluquin','','jimz.aluquin.au@phinmaed.com','01-1234-12345','','4th year','Voted','01-1234-12345','Verified','2025-06-03','19:05:51','Comlab 2');
/*!40000 ALTER TABLE `voters` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `votes`
--

DROP TABLE IF EXISTS `votes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `CandidateID` int(11) NOT NULL,
  `votes` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=462 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `votes`
--

LOCK TABLES `votes` WRITE;
/*!40000 ALTER TABLE `votes` DISABLE KEYS */;
INSERT INTO `votes` VALUES (461,218,0),(460,217,0),(459,215,0),(458,210,0),(457,213,0),(456,212,0),(455,204,0),(454,202,0),(453,218,0),(452,217,0),(451,215,0),(450,210,0),(449,213,0),(448,212,0),(447,204,0),(446,203,0),(445,218,0),(444,217,0),(443,215,0),(442,210,0),(441,213,0),(440,212,0),(439,204,0),(438,203,0),(437,218,0),(436,217,0),(435,215,0),(434,210,0),(433,213,0),(432,212,0),(431,204,0),(430,202,0),(429,218,0),(428,217,0),(427,215,0),(426,210,0),(425,213,0),(424,212,0),(423,204,0),(422,202,0),(421,218,0),(420,217,0),(419,215,0),(418,210,0),(417,213,0),(416,212,0),(415,204,0),(414,203,0),(413,218,0),(412,216,0),(411,214,0),(410,210,0),(409,208,0),(408,212,0),(407,204,0),(406,202,0),(405,218,0),(404,216,0),(403,214,0),(402,210,0),(401,208,0),(400,212,0),(399,205,0),(398,203,0);
/*!40000 ALTER TABLE `votes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `User_id` int(11) NOT NULL AUTO_INCREMENT,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `User_Type` varchar(50) NOT NULL,
  `Position` varchar(100) NOT NULL,
  PRIMARY KEY (`User_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (6,'Laydee','Champagne','admin','12345','admin','Admin'),(13,'John','Leabres','John','12345','admin','Secretary Officer'),(12,'Evelyn ','Juliano','evelyn','12345','admin','Faculty Officer'),(14,'Isaiah','Mizona','Isaiah','12345','admin','Election Officer 1'),(15,'Kat','CSDL','Kat','12345','admin','CSDL Officer'),(16,'Laydee','Champagne','Laydee','12345','admin','CSDL Officer');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-06-03 21:37:08

-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 01, 2011 at 09:59 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sweetgame`
--

-- --------------------------------------------------------

--
-- Table structure for table `characters`
--

DROP TABLE IF EXISTS `characters`;
CREATE TABLE IF NOT EXISTS `characters` (
  `characterid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `hp` int(11) NOT NULL,
  `maxhp` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `strength` int(11) NOT NULL,
  `defense` int(11) NOT NULL,
  `speed` int(11) NOT NULL,
  `agility` int(11) NOT NULL,
  `accuracy` int(11) NOT NULL,
  `weapon1` int(11) NOT NULL DEFAULT '1',
  `weapon2` int(11) DEFAULT NULL,
  `weapon3` int(11) DEFAULT NULL,
  `shield1` int(11) NOT NULL DEFAULT '1',
  `shield2` int(11) DEFAULT NULL,
  `shield3` int(11) DEFAULT NULL,
  `dead` varchar(3) NOT NULL,
  `exp` int(11) NOT NULL,
  `maxexp` int(11) NOT NULL,
  `potion1` int(11) DEFAULT NULL,
  `potion2` int(11) DEFAULT NULL,
  `potion3` int(11) DEFAULT NULL,
  `potion4` int(11) DEFAULT NULL,
  `potion5` int(11) DEFAULT NULL,
  `item1` int(11) DEFAULT NULL,
  `item2` int(11) DEFAULT NULL,
  `item3` int(11) DEFAULT NULL,
  `item4` int(11) DEFAULT NULL,
  `item5` int(11) DEFAULT NULL,
  `weaponequipped` int(11) NOT NULL DEFAULT '1',
  `shieldequipped` int(11) NOT NULL DEFAULT '1',
  `gold` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `questID` int(11) NOT NULL COMMENT 'Quest character is currently on',
  `queststep` int(11) NOT NULL COMMENT 'Step of the quest a character is on',
  `gender` varchar(6) NOT NULL,
  `fisttype` varchar(11) NOT NULL,
  `barearmstype` varchar(11) NOT NULL,
  PRIMARY KEY (`characterid`),
  KEY `weapon1` (`weapon1`),
  KEY `weapon2` (`weapon2`),
  KEY `weapon3` (`weapon3`),
  KEY `shield1` (`shield1`),
  KEY `shield2` (`shield2`),
  KEY `shield3` (`shield3`),
  KEY `potion1` (`potion1`),
  KEY `potion2` (`potion2`),
  KEY `potion3` (`potion3`),
  KEY `potion4` (`potion4`),
  KEY `potion5` (`potion5`),
  KEY `item1` (`item1`),
  KEY `item2` (`item2`),
  KEY `item3` (`item3`),
  KEY `item4` (`item4`),
  KEY `item5` (`item5`),
  KEY `weaponequipped` (`weaponequipped`),
  KEY `shieldequipped` (`shieldequipped`),
  KEY `userID` (`userID`),
  KEY `questID` (`questID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `inbox`
--

DROP TABLE IF EXISTS `inbox`;
CREATE TABLE IF NOT EXISTS `inbox` (
  `inboxmessageid` int(11) NOT NULL AUTO_INCREMENT,
  `mailnumber` int(11) NOT NULL,
  `recipient` int(11) NOT NULL,
  `read` varchar(5) NOT NULL DEFAULT 'false',
  `deleted` varchar(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`inboxmessageid`),
  KEY `mailnumber` (`mailnumber`),
  KEY `recipient` (`recipient`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
CREATE TABLE IF NOT EXISTS `items` (
  `itemID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY (`itemID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`itemID`, `name`, `type`) VALUES
(1, 'rat tail', 'flesh'),
(2, 'slug slime', 'nature'),
(3, 'snout', 'poison'),
(4, 'petal', 'poison'),
(5, 'wolf fang', 'nature'),
(6, 'tiny death sickle', 'death'),
(7, 'baby cog', 'technology'),
(8, 'boogey', 'flesh'),
(9, 'catnip', 'nature'),
(10, 'boney key', 'death'),
(11, 'kremican flag', 'valor'),
(12, 'pendulum', 'time'),
(13, 'religious symbol', 'life'),
(14, 'baby bullet', 'metal'),
(15, 'ghoulish finger', 'flesh'),
(16, 'poisonous petal', 'poison');

-- --------------------------------------------------------

--
-- Table structure for table `merchants`
--

DROP TABLE IF EXISTS `merchants`;
CREATE TABLE IF NOT EXISTS `merchants` (
  `merchantID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `welcome` varchar(100) NOT NULL,
  `type` varchar(15) NOT NULL,
  PRIMARY KEY (`merchantID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `merchants`
--

INSERT INTO `merchants` (`merchantID`, `name`, `welcome`, `type`) VALUES
(1, 'Potion Pete', 'Well HOWDY! Welcome to Potion Pete''s! BUY SOMETHING. HICKA DICKA DING DONG!', 'potion'),
(2, 'Weapon Wally', 'Um... hi...', 'weapon'),
(3, 'Shieldy Sammy', 'Welcome to Shieldy Sammy''s. Buy a shield you jerk.', 'shield');

-- --------------------------------------------------------

--
-- Table structure for table `monsters`
--

DROP TABLE IF EXISTS `monsters`;
CREATE TABLE IF NOT EXISTS `monsters` (
  `number` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `weapontype` varchar(15) NOT NULL,
  `shieldtype` varchar(15) NOT NULL,
  `hp` int(5) NOT NULL,
  `maxhp` int(5) NOT NULL,
  `level` int(3) NOT NULL,
  `strength` int(3) NOT NULL,
  `defense` int(3) NOT NULL,
  `speed` int(11) NOT NULL,
  `weaponlow` int(3) NOT NULL,
  `weaponhigh` int(3) NOT NULL,
  `itemID` int(11) DEFAULT NULL,
  PRIMARY KEY (`number`),
  KEY `itemID` (`itemID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

--
-- Dumping data for table `monsters`
--

INSERT INTO `monsters` (`number`, `name`, `weapontype`, `shieldtype`, `hp`, `maxhp`, `level`, `strength`, `defense`, `speed`, `weaponlow`, `weaponhigh`, `itemID`) VALUES
(1, 'Rat', 'flesh', 'flesh', 3, 3, 1, 1, 1, 1, 1, 1, 1),
(2, 'Overgrown Slug', 'nature', 'nature', 4, 4, 1, 1, 1, 0, 1, 1, 2),
(3, 'Piggy', 'poison', 'flesh', 7, 7, 2, 2, 2, 2, 1, 1, 3),
(4, 'Little Flower', 'poison', 'nature', 1, 1, 0, 0, 0, 0, 0, 0, 4),
(5, 'Crazed Wolf', 'nature', 'nature', 13, 13, 3, 3, 1, 2, 2, 2, 5),
(6, 'Shiny Wolf', 'metal', 'nature', 15, 15, 2, 1, 1, 2, 1, 2, 5),
(7, 'Lil Death', 'death', 'death', 3, 3, 2, 1, 1, 7, 1, 1, 6),
(8, 'Robot Tot', 'technology', 'technology', 5, 5, 3, 1, 1, 3, 2, 3, 7),
(9, 'Boogey Baby', 'flesh', 'flesh', 3, 3, 1, 1, 1, 3, 1, 1, 8),
(13, 'Cuddle Cat', 'nature', 'nature', 3, 3, 1, 1, 1, 5, 1, 1, 9),
(19, 'Son of a Gun', 'metal', 'metal', 12, 12, 3, 3, 2, 4, 2, 4, 14),
(20, 'Choir Boy', 'life', 'flesh', 13, 13, 3, 3, 3, 1, 3, 3, 13),
(21, 'Obnoxious Clock', 'time', 'time', 12, 12, 3, 4, 2, 1, 3, 4, 12),
(22, 'Kremican', 'metal', 'flesh', 15, 15, 3, 2, 2, 3, 4, 5, 11),
(23, 'Skeleton Key', 'death', 'death', 22, 22, 4, 6, 3, 1, 5, 5, 10),
(24, 'Unscary Ghoul', 'flesh', 'flesh', 24, 24, 4, 3, 6, 4, 5, 6, 15),
(25, 'Mean Flower', 'poison', 'nature', 25, 25, 4, 4, 4, 0, 5, 6, 16);

-- --------------------------------------------------------

--
-- Table structure for table `potions`
--

DROP TABLE IF EXISTS `potions`;
CREATE TABLE IF NOT EXISTS `potions` (
  `potionID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(15) NOT NULL,
  `power` int(5) NOT NULL,
  `merchantID` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  PRIMARY KEY (`potionID`),
  KEY `merchantID` (`merchantID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `potions`
--

INSERT INTO `potions` (`potionID`, `name`, `power`, `merchantID`, `cost`) VALUES
(1, 'Weak Potion', 10, 1, 5),
(2, 'Decent Potion', 20, 1, 10),
(3, 'Great Potion', 35, 1, 15);

-- --------------------------------------------------------

--
-- Table structure for table `quests`
--

DROP TABLE IF EXISTS `quests`;
CREATE TABLE IF NOT EXISTS `quests` (
  `questID` int(11) NOT NULL AUTO_INCREMENT,
  `questName` varchar(30) NOT NULL,
  `badgeName` varchar(30) NOT NULL,
  `questfile` varchar(30) NOT NULL,
  PRIMARY KEY (`questID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `quests`
--

INSERT INTO `quests` (`questID`, `questName`, `badgeName`, `questfile`) VALUES
(1, 'A strange piece of paper...', 'Drunken Pirate Boxing', 'inc_quest1.php'),
(2, 'That awful witch!', 'Swan Dive', 'inc_quest2.php'),
(3, 'A strange amulet', 'Combustable Amulet', 'inc_quest3.php');

-- --------------------------------------------------------

--
-- Table structure for table `questscompleted`
--

DROP TABLE IF EXISTS `questscompleted`;
CREATE TABLE IF NOT EXISTS `questscompleted` (
  `questscompletedID` int(11) NOT NULL AUTO_INCREMENT,
  `questID` int(11) NOT NULL,
  `characterID` int(11) NOT NULL,
  PRIMARY KEY (`questscompletedID`),
  KEY `questID` (`questID`,`characterID`),
  KEY `userID` (`characterID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `relationship`
--

DROP TABLE IF EXISTS `relationship`;
CREATE TABLE IF NOT EXISTS `relationship` (
  `relationshipID` int(11) NOT NULL AUTO_INCREMENT,
  `initiatorID` int(11) NOT NULL,
  `receiverID` int(11) NOT NULL,
  `status` int(1) NOT NULL COMMENT '0 Requested, 1 Friends, 2 Blocked',
  PRIMARY KEY (`relationshipID`),
  KEY `initiatorID` (`initiatorID`,`receiverID`),
  KEY `receiverID` (`receiverID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `sentbox`
--

DROP TABLE IF EXISTS `sentbox`;
CREATE TABLE IF NOT EXISTS `sentbox` (
  `mailnumber` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) DEFAULT NULL,
  `subject` varchar(45) NOT NULL,
  `body` varchar(500) NOT NULL,
  `datesent` int(11) NOT NULL,
  `deleted` varchar(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`mailnumber`),
  KEY `sender` (`sender`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `shields`
--

DROP TABLE IF EXISTS `shields`;
CREATE TABLE IF NOT EXISTS `shields` (
  `shieldID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `power` int(3) NOT NULL,
  `type` varchar(15) NOT NULL,
  `prefix` varchar(30) NOT NULL,
  `merchantID` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  PRIMARY KEY (`shieldID`),
  KEY `merchantID` (`merchantID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `shields`
--

INSERT INTO `shields` (`shieldID`, `name`, `power`, `type`, `prefix`, `merchantID`, `cost`) VALUES
(1, 'Bare Arms', 0, 'flesh', 'default', NULL, NULL),
(2, 'Beginner Shield of Metal', 5, 'metal', 'Beginner', 3, 10),
(3, 'Beginner Shield of Technology', 5, 'technology', 'Beginner', NULL, NULL),
(4, 'Beginner Shield of Nature', 5, 'nature', 'Beginner', NULL, NULL),
(5, 'Beginner Shield of Life', 5, 'life', 'Beginner', NULL, NULL),
(6, 'Beginner Shield of Death', 5, 'death', 'Beginner', NULL, NULL),
(7, 'Beginner Shield of Time', 5, 'time', 'Beginner', NULL, NULL),
(8, 'Beginner Shield of Valor', 5, 'valor', 'Beginner', NULL, NULL),
(9, 'Beginner Shield of Poison', 5, 'poison', 'Beginner', NULL, NULL),
(10, 'Beginner Shield of Flesh', 5, 'flesh', 'Beginner', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(15) NOT NULL,
  `userpassword` varchar(32) NOT NULL,
  `datejoined` int(11) NOT NULL,
  `sessionID` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `weapons`
--

DROP TABLE IF EXISTS `weapons`;
CREATE TABLE IF NOT EXISTS `weapons` (
  `weaponID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `low` int(3) NOT NULL,
  `high` int(15) NOT NULL,
  `type` varchar(11) NOT NULL,
  `prefix` varchar(30) NOT NULL,
  `merchantID` int(11) DEFAULT NULL,
  `cost` int(11) DEFAULT NULL,
  PRIMARY KEY (`weaponID`),
  KEY `merchantID` (`merchantID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `weapons`
--

INSERT INTO `weapons` (`weaponID`, `name`, `low`, `high`, `type`, `prefix`, `merchantID`, `cost`) VALUES
(1, 'Fists', 1, 3, 'flesh', 'default', NULL, NULL),
(2, 'Beginner Sword of Metal', 3, 7, 'metal', 'Beginner', 2, 10),
(3, 'Beginner Sword of Technology', 3, 7, 'technology', 'Beginner', NULL, NULL),
(4, 'Beginner Sword of Nature', 3, 7, 'nature', 'Beginner', NULL, NULL),
(5, 'Beginner Sword of Life', 3, 7, 'life', 'Beginner', NULL, NULL),
(6, 'Beginner Sword of Death', 3, 7, 'death', 'Beginner', NULL, NULL),
(7, 'Beginner Sword of Time', 3, 7, 'time', 'Beginner', NULL, NULL),
(8, 'Beginner Sword of Valor', 3, 7, 'valor', 'Beginner', NULL, NULL),
(9, 'Beginner Sword of Poison', 3, 7, 'poison', 'Beginner', NULL, NULL),
(10, 'Beginner Sword of Flesh', 3, 7, 'flesh', 'Beginner', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `characters`
--
ALTER TABLE `characters`
  ADD CONSTRAINT `characters_ibfk_1` FOREIGN KEY (`weapon2`) REFERENCES `weapons` (`weaponID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_10` FOREIGN KEY (`potion5`) REFERENCES `potions` (`potionID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_11` FOREIGN KEY (`item1`) REFERENCES `items` (`itemID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_12` FOREIGN KEY (`item2`) REFERENCES `items` (`itemID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_13` FOREIGN KEY (`item3`) REFERENCES `items` (`itemID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_14` FOREIGN KEY (`item4`) REFERENCES `items` (`itemID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_15` FOREIGN KEY (`item5`) REFERENCES `items` (`itemID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_16` FOREIGN KEY (`weapon1`) REFERENCES `weapons` (`weaponID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_17` FOREIGN KEY (`shield1`) REFERENCES `shields` (`shieldID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_18` FOREIGN KEY (`weaponequipped`) REFERENCES `weapons` (`weaponID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_19` FOREIGN KEY (`shieldequipped`) REFERENCES `shields` (`shieldID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_2` FOREIGN KEY (`weapon3`) REFERENCES `weapons` (`weaponID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_20` FOREIGN KEY (`userID`) REFERENCES `users` (`userid`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_3` FOREIGN KEY (`shield2`) REFERENCES `shields` (`shieldID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_4` FOREIGN KEY (`shield3`) REFERENCES `shields` (`shieldID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_6` FOREIGN KEY (`potion1`) REFERENCES `potions` (`potionID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_7` FOREIGN KEY (`potion2`) REFERENCES `potions` (`potionID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_8` FOREIGN KEY (`potion3`) REFERENCES `potions` (`potionID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `characters_ibfk_9` FOREIGN KEY (`potion4`) REFERENCES `potions` (`potionID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inbox`
--
ALTER TABLE `inbox`
  ADD CONSTRAINT `inbox_ibfk_1` FOREIGN KEY (`mailnumber`) REFERENCES `sentbox` (`mailnumber`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `inbox_ibfk_2` FOREIGN KEY (`recipient`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `monsters`
--
ALTER TABLE `monsters`
  ADD CONSTRAINT `monsters_ibfk_1` FOREIGN KEY (`itemID`) REFERENCES `items` (`itemID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `potions`
--
ALTER TABLE `potions`
  ADD CONSTRAINT `potions_ibfk_1` FOREIGN KEY (`merchantID`) REFERENCES `merchants` (`merchantID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `questscompleted`
--
ALTER TABLE `questscompleted`
  ADD CONSTRAINT `questscompleted_ibfk_1` FOREIGN KEY (`questID`) REFERENCES `quests` (`questID`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `questscompleted_ibfk_2` FOREIGN KEY (`characterID`) REFERENCES `characters` (`characterid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `relationship`
--
ALTER TABLE `relationship`
  ADD CONSTRAINT `relationship_ibfk_1` FOREIGN KEY (`initiatorID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `relationship_ibfk_2` FOREIGN KEY (`receiverID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sentbox`
--
ALTER TABLE `sentbox`
  ADD CONSTRAINT `sentbox_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `users` (`userid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shields`
--
ALTER TABLE `shields`
  ADD CONSTRAINT `shields_ibfk_1` FOREIGN KEY (`merchantID`) REFERENCES `merchants` (`merchantID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `weapons`
--
ALTER TABLE `weapons`
  ADD CONSTRAINT `weapons_ibfk_1` FOREIGN KEY (`merchantID`) REFERENCES `merchants` (`merchantID`) ON DELETE CASCADE ON UPDATE CASCADE;

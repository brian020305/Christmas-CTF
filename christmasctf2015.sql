-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- 호스트: localhost
-- 처리한 시간: 16-02-25 17:48
-- 서버 버전: 10.0.21-MariaDB-1~trusty-log
-- PHP 버전: 5.5.9-1ubuntu4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 데이터베이스: `christmasctf2015`
--
CREATE DATABASE IF NOT EXISTS `christmasctf2015` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `christmasctf2015`;

-- --------------------------------------------------------

--
-- 테이블 구조 `ctf_auth_log`
--

DROP TABLE IF EXISTS `ctf_auth_log`;
CREATE TABLE IF NOT EXISTS `ctf_auth_log` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `user_no` int(11) NOT NULL,
  `team_no` int(11) NOT NULL,
  `challenges_no` int(11) NOT NULL,
  `input_key` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `result` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `reg_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 테이블 구조 `ctf_challenges`
--

DROP TABLE IF EXISTS `ctf_challenges`;
CREATE TABLE IF NOT EXISTS `ctf_challenges` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `content` varchar(1024) COLLATE utf8_unicode_ci NOT NULL,
  `authKey` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `points` int(11) NOT NULL,
  `solved_count` int(11) NOT NULL,
  `reg_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `udt_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `opend` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 테이블 구조 `ctf_notice`
--

DROP TABLE IF EXISTS `ctf_notice`;
CREATE TABLE IF NOT EXISTS `ctf_notice` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `reg_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 테이블 구조 `ctf_team`
--

DROP TABLE IF EXISTS `ctf_team`;
CREATE TABLE IF NOT EXISTS `ctf_team` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `team_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `team_leader_no` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `reg_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_auth` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 테이블 구조 `ctf_user`
--

DROP TABLE IF EXISTS `ctf_user`;
CREATE TABLE IF NOT EXISTS `ctf_user` (
  `no` int(11) NOT NULL AUTO_INCREMENT,
  `team_no` int(11) NOT NULL,
  `user_id` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_pw` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `user_email` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `score` int(11) NOT NULL,
  `pKey` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `last_auth` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_ip` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `reg_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`no`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

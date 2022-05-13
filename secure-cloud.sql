-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Mag 14, 2022 alle 00:41
-- Versione del server: 10.4.22-MariaDB
-- Versione PHP: 8.0.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE `secure-cloud`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `secure-cloud`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `account_recovery`
--

CREATE TABLE `secure-cloud`.`account_recovery` (
  `id_user` int(11) NOT NULL,
  `htkn` varchar(80) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `account_verify`
--

CREATE TABLE `secure-cloud`.`account_verify` (
  `id_user` int(11) NOT NULL,
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `downloads`
--

CREATE TABLE `secure-cloud`.`downloads` (
  `download_datet` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_session` varchar(20) NOT NULL,
  `id_file` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `downloads`
--

INSERT INTO `secure-cloud`.`downloads` (`download_datet`, `id_user`, `id_session`, `id_file`) VALUES
('2022-05-14 00:04:12', 34, '8b9dan16jycpfjjw', 'pYqI40Yl'),
('2022-05-14 00:24:27', 34, '8b9dan16jycpfjjw', 'pYqI40Yl');

-- --------------------------------------------------------

--
-- Struttura della tabella `env`
--

CREATE TABLE `secure-cloud`.`env` (
  `id` varchar(10) NOT NULL,
  `key` varchar(30) NOT NULL,
  `value` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struttura della tabella `files`
--

CREATE TABLE `secure-cloud`.`files` (
  `idf` varchar(8) NOT NULL,
  `fname` varchar(500) NOT NULL,
  `ref` varchar(1000) NOT NULL,
  `size` int(11) NOT NULL,
  `mime` varchar(30) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `files`
--

INSERT INTO `secure-cloud`.`files` (`idf`, `fname`, `ref`, `size`, `mime`, `id_user`) VALUES
('pYqI40Yl', 'U2FsdGVkX19ZlXFS+TSHxtTbjkoAnOwiZSKBjHAJRPM=', '../users/c0baeb7eadb69ccbfb43eca284fe9717/U2FsdGVkX19ZlXFS+TSHxtTbjkoAnOwiZSKBjHAJRPM=', 408, 'mimetype', 34);

-- --------------------------------------------------------

--
-- Struttura della tabella `remember`
--

CREATE TABLE `secure-cloud`.`remember` (
  `htkn` varchar(64) NOT NULL,
  `expires` datetime NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `remember`
--

INSERT INTO `secure-cloud`.`remember` (`htkn`, `expires`, `id_user`) VALUES
('f7decb918d44a14b111afebde41d5d6256f04fedd90b47eebabb52b69eee0288', '2022-05-23 10:29:56', 6);

-- --------------------------------------------------------

--
-- Struttura della tabella `sessions`
--

CREATE TABLE `secure-cloud`.`sessions` (
  `id` varchar(20) NOT NULL,
  `ip` varchar(30) NOT NULL,
  `client` varchar(30) NOT NULL,
  `os` varchar(20) NOT NULL,
  `device` varchar(10) NOT NULL,
  `last_time` datetime NOT NULL,
  `session_status` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `rem_htkn` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `sessions`
--

INSERT INTO `secure-cloud`.`sessions` (`id`, `ip`, `client`, `os`, `device`, `last_time`, `session_status`, `id_user`, `rem_htkn`) VALUES
('1g4m3c64l91f008x', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-14 00:32:50', 0, 34, NULL),
('64ngoxaotmjjv674', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-08 22:00:39', 0, 34, NULL),
('8b9dan16jycpfjjw', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-14 00:24:42', 0, 34, NULL),
('aqdajf0hrst51wwp', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-14 00:32:54', 0, 34, NULL),
('dbl4aomismfk3wkt', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-14 00:35:30', 0, 34, 'a7419h27cr4qmtde2w55drc3625q419vjwu1'),
('hmw91ncl3osen997', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-14 00:25:11', 0, 34, NULL),
('t01zvwjxuocwjsav', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-09 00:00:12', 0, 34, NULL),
('ycaef7zg90mh8ggy', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-14 00:32:31', 0, 34, NULL),
('zb126jelle8sxwhq', '127.0.0.1', 'Chrome', 'Windows', 'Desktop', '2022-05-09 00:04:45', 0, 34, NULL);

-- --------------------------------------------------------

--
-- Struttura della tabella `uploads`
--

CREATE TABLE `secure-cloud`.`uploads` (
  `upload_datet` datetime NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_session` varchar(20) NOT NULL,
  `id_file` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `uploads`
--

INSERT INTO `secure-cloud`.`uploads` (`upload_datet`, `id_user`, `id_session`, `id_file`) VALUES
('2022-05-14 00:03:48', 34, '8b9dan16jycpfjjw', 'pYqI40Yl');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE `secure-cloud`.`users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass` varchar(100) NOT NULL,
  `logged_with` varchar(20) NOT NULL,
  `2FA` int(11) NOT NULL,
  `verified` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `secure-cloud`.`users` (`id`, `email`, `pass`, `logged_with`, `2FA`, `verified`) VALUES
(34, 'gabrieledevs@gmail.com', '$2y$10$/VyUb5nT6h1Ppl4HUfB9wujeeLm8SU61PnmtVwPkm2ulURDuq8OxC', 'EMAIL', 0, 1);

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `files`
--
ALTER TABLE `secure-cloud`.`files`
  ADD PRIMARY KEY (`idf`);

--
-- Indici per le tabelle `sessions`
--
ALTER TABLE `secure-cloud`.`sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `users`
--
ALTER TABLE `secure-cloud`.`users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `users`
--
ALTER TABLE `secure-cloud`.`users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
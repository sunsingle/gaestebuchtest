-- phpMyAdmin SQL Dump
-- version 4.4.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 25. Sep 2015 um 11:05
-- Server-Version: 5.6.26
-- PHP-Version: 5.5.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `gaestebuchtest`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gaestebuchtest`
--

CREATE TABLE IF NOT EXISTS `gaestebuchtest` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `email` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `entry` longtext COLLATE utf8_unicode_ci NOT NULL,
  `ref` int(11) DEFAULT '-1'
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `gaestebuchtest`
--

INSERT INTO `gaestebuchtest` (`id`, `name`, `date`, `email`, `entry`, `ref`) VALUES
(1, 'Hans Wurst', '2015-11-01 15:22:32', 'Hans@Wurst.wa', 'Das ist ein sehr schönes Gästebuch', -1),
(4, 'Jack O''Neill', '2015-11-01 15:30:18', 'oneill@stargate.co', 'Läuft... und jetzt aus der Datenbank', -1),
(7, 'icke', '2015-09-23 12:48:30', 'ich@web.de', 'läuft', -1),
(8, 'Gordon Shamway', '2015-09-23 14:43:42', 'alf@melmac.cc', 'HAHA ich lach mich tot', -1),
(9, 'Al Borland', '2015-09-23 14:44:31', 'borland@flanell.cc', 'Das glaub ich nicht Tim', -1),
(10, 'T''acl', '2015-09-23 14:46:18', 'talc@shayennemountain.com', 'In der tat', -1),
(11, 'useless', '2015-09-23 15:15:42', 'useless@box.sk', '[youtube]NiI6kRyscaA[/youtube]', -1),
(12, 'BBCode', '2015-09-23 15:58:36', 'bb@co.de', '[b]Fette Sache[/b]\\r\\n[u]siehe unten[/u]\\r\\n[i]voll schräg[/i]\\r\\n[url=http://sunsingle.de]Sunny[/url]', -1),
(13, 'Grün', '2015-09-23 16:02:39', 'gr@ue.n', '[b][color=green]GRÜÜÜN[/color][/b]', -1),
(14, 'Jean Luc Picard', '2015-09-24 09:26:59', 'picard@lcars.net', 'Energie', -1),
(15, 'Kaffee', '2015-09-24 09:29:23', 'kaffee@jetzt.los', 'Kaffee [b]Kaffee[/b] [size=30px]Kaffee!![/size]\\r\\n[b][size=20px][color=red]Jetzt[/color][/size][/b]', -1),
(16, 'icke', '2015-09-24 09:31:14', 'ich@web.de', 'Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee Kaffee ach und Kaffee....', -1),
(21, 'Jean Luc Picard', '2015-09-24 09:58:59', 'picard@starfleet.sol', 'Machen Sie es so!', -1),
(22, 'test', '2015-09-24 10:04:22', 'test@test.test', 'Suc = link -> lastpage', -1),
(24, 'Kaffee', '2015-09-24 15:07:12', 'kaffee@jetzt.los', '[img]http://1.bp.blogspot.com/-UKZXrj5PnHE/T0_2CuHaLGI/AAAAAAAAMsc/uNZhMWlftdQ/s1600/funny%2Bcoffee%2Bcat.jpg[/img]', -1),
(26, 'toChange', '2015-09-25 10:14:53', NULL, 'Mach Mal Leerzeichen Dazwischen Wa :-P', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `gbuser`
--

CREATE TABLE IF NOT EXISTS `gbuser` (
  `uid` int(11) NOT NULL,
  `nick` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `pass` varchar(32) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Daten für Tabelle `gbuser`
--

INSERT INTO `gbuser` (`uid`, `nick`, `pass`) VALUES
(1, 'test', 'e10adc3949ba59abbe56e057f20f883e');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `gaestebuchtest`
--
ALTER TABLE `gaestebuchtest`
  ADD PRIMARY KEY (`id`);

--
-- Indizes für die Tabelle `gbuser`
--
ALTER TABLE `gbuser`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `gaestebuchtest`
--
ALTER TABLE `gaestebuchtest`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT für Tabelle `gbuser`
--
ALTER TABLE `gbuser`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

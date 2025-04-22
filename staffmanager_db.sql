-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sty 31, 2025 at 11:44 PM
-- Wersja serwera: 10.4.32-MariaDB
-- Wersja PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `staffmanager_db`
--
CREATE DATABASE IF NOT EXISTS `staffmanager_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `staffmanager_db`;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `dodatkowe_dni_wolne`
--

DROP TABLE IF EXISTS `dodatkowe_dni_wolne`;
CREATE TABLE `dodatkowe_dni_wolne` (
  `id` int(11) NOT NULL,
  `pracownik_id` int(11) NOT NULL,
  `ilosc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownicy`
--

DROP TABLE IF EXISTS `pracownicy`;
CREATE TABLE `pracownicy` (
  `id` int(11) NOT NULL,
  `imie` varchar(30) NOT NULL,
  `nazwisko` varchar(30) NOT NULL,
  `pesel` varchar(11) NOT NULL,
  `rola` enum('pracodawca','pracownik') NOT NULL,
  `login` varchar(30) NOT NULL,
  `haslo` varchar(255) NOT NULL,
  `data_zatrudnienia` date NOT NULL,
  `punkty` int(255) NOT NULL,
  `dostepne_dni_wolne` int(11) DEFAULT 26
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `pracownik_zadanie`
--

DROP TABLE IF EXISTS `pracownik_zadanie`;
CREATE TABLE `pracownik_zadanie` (
  `id` int(11) NOT NULL,
  `pracownik_id` int(11) NOT NULL,
  `zadanie_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `premie`
--

DROP TABLE IF EXISTS `premie`;
CREATE TABLE `premie` (
  `id` int(11) NOT NULL,
  `pracownik_id` int(11) NOT NULL,
  `wartosc` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `urlopy`
--

DROP TABLE IF EXISTS `urlopy`;
CREATE TABLE `urlopy` (
  `id` int(11) NOT NULL,
  `pracownik_id` int(11) NOT NULL,
  `daty_urlopu` text DEFAULT NULL,
  `data_zlozenia` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `zadania`
--

DROP TABLE IF EXISTS `zadania`;
CREATE TABLE `zadania` (
  `id` int(11) NOT NULL,
  `opis` varchar(250) NOT NULL,
  `status` enum('do wykonania','ukończone','w realizacji') NOT NULL,
  `deadline` date NOT NULL,
  `data_zakonczenia` date DEFAULT NULL,
  `ilosc_punkty` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `dodatkowe_dni_wolne`
--
ALTER TABLE `dodatkowe_dni_wolne`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pracownik_id` (`pracownik_id`);

--
-- Indeksy dla tabeli `pracownicy`
--
ALTER TABLE `pracownicy`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `pracownik_zadanie`
--
ALTER TABLE `pracownik_zadanie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pracownik_id` (`pracownik_id`),
  ADD KEY `zadanie_id` (`zadanie_id`);

--
-- Indeksy dla tabeli `premie`
--
ALTER TABLE `premie`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pracownik_id` (`pracownik_id`);

--
-- Indeksy dla tabeli `urlopy`
--
ALTER TABLE `urlopy`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pracownik_id` (`pracownik_id`);

--
-- Indeksy dla tabeli `zadania`
--
ALTER TABLE `zadania`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dodatkowe_dni_wolne`
--
ALTER TABLE `dodatkowe_dni_wolne`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pracownicy`
--
ALTER TABLE `pracownicy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pracownik_zadanie`
--
ALTER TABLE `pracownik_zadanie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `premie`
--
ALTER TABLE `premie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `urlopy`
--
ALTER TABLE `urlopy`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `zadania`
--
ALTER TABLE `zadania`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dodatkowe_dni_wolne`
--
ALTER TABLE `dodatkowe_dni_wolne`
  ADD CONSTRAINT `dodatkowe_dni_wolne_ibfk_1` FOREIGN KEY (`pracownik_id`) REFERENCES `pracownicy` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pracownik_zadanie`
--
ALTER TABLE `pracownik_zadanie`
  ADD CONSTRAINT `pracownik_zadanie_ibfk_1` FOREIGN KEY (`pracownik_id`) REFERENCES `pracownicy` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pracownik_zadanie_ibfk_2` FOREIGN KEY (`zadanie_id`) REFERENCES `zadania` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `premie`
--
ALTER TABLE `premie`
  ADD CONSTRAINT `premie_ibfk_1` FOREIGN KEY (`pracownik_id`) REFERENCES `pracownicy` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `urlopy`
--
ALTER TABLE `urlopy`
  ADD CONSTRAINT `urlopy_ibfk_1` FOREIGN KEY (`pracownik_id`) REFERENCES `pracownicy` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
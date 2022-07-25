-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июл 25 2022 г., 18:07
-- Версия сервера: 5.7.27-30
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `u0904790_winnftbot`
--

-- --------------------------------------------------------

--
-- Структура таблицы `nft`
--

CREATE TABLE `nft` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `nft_balance` float NOT NULL,
  `anime` float NOT NULL,
  `custom3d` float NOT NULL,
  `nude` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `paylinks`
--

CREATE TABLE `paylinks` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `times` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `sum` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `raffles_history`
--

CREATE TABLE `raffles_history` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `nft` varchar(64) NOT NULL,
  `sum` int(3) NOT NULL,
  `date_time` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `sums`
--

CREATE TABLE `sums` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `sum` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `temp_coin`
--

CREATE TABLE `temp_coin` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `ccase` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `temp_sess`
--

CREATE TABLE `temp_sess` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `action` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `transactions`
--

CREATE TABLE `transactions` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `sender` varchar(128) NOT NULL,
  `date_time` varchar(64) NOT NULL,
  `nude` float NOT NULL,
  `anime` float NOT NULL,
  `custom3d` float NOT NULL,
  `ton` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `rowid` int(11) NOT NULL,
  `chatid` bigint(20) NOT NULL,
  `fname` varchar(64) NOT NULL,
  `lname` varchar(64) NOT NULL,
  `username` varchar(64) NOT NULL,
  `nftcode` varchar(50) NOT NULL,
  `refbalance` float NOT NULL,
  `ref` bigint(20) NOT NULL,
  `verified` int(1) NOT NULL,
  `lang` int(1) NOT NULL,
  `wallet` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `nft`
--
ALTER TABLE `nft`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `paylinks`
--
ALTER TABLE `paylinks`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `raffles_history`
--
ALTER TABLE `raffles_history`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `sums`
--
ALTER TABLE `sums`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `temp_coin`
--
ALTER TABLE `temp_coin`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `temp_sess`
--
ALTER TABLE `temp_sess`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`rowid`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`rowid`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `nft`
--
ALTER TABLE `nft`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `paylinks`
--
ALTER TABLE `paylinks`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `raffles_history`
--
ALTER TABLE `raffles_history`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `sums`
--
ALTER TABLE `sums`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `temp_coin`
--
ALTER TABLE `temp_coin`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `temp_sess`
--
ALTER TABLE `temp_sess`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `transactions`
--
ALTER TABLE `transactions`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `rowid` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 23-12-2016 a las 19:24:09
-- Versión del servidor: 5.7.16-0ubuntu0.16.04.1
-- Versión de PHP: 7.0.8-0ubuntu0.16.04.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `dbsnail`
--
CREATE DATABASE IF NOT EXISTS `dbsnail` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `dbsnail`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `SNAIL_LOG`
--

CREATE TABLE `SNAIL_LOG` (
  `ID` int(11) NOT NULL,
  `DATE` datetime NOT NULL,
  `H` int(11) NOT NULL,
  `U` int(11) NOT NULL,
  `D` int(11) NOT NULL,
  `F` int(11) NOT NULL,
  `result` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `SNAIL_LOG`
--
ALTER TABLE `SNAIL_LOG`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `SNAIL_LOG`
--
ALTER TABLE `SNAIL_LOG`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

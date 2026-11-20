-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-09-2026 a las 18:07:37
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cnlab`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conf_modulos`
--

CREATE TABLE `conf_modulos` (
  `Id_modulo` int(11) NOT NULL,
  `Id_proyecto` int(11) NOT NULL,
  `Data_mediciones` int(11) NOT NULL,
  `Data_guardado` int(11) NOT NULL,
  `Nombre` varchar(150) NOT NULL,
  `Descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mediciones`
--

CREATE TABLE `mediciones` (
  `Id_medicion` bigint(20) NOT NULL,
  `Id_proyecto` int(11) NOT NULL,
  `Id_modulo` int(11) NOT NULL,
  `Valor` decimal(15,4) NOT NULL,
  `Tiempo` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mediciones_proyecto`
--

CREATE TABLE `mediciones_proyecto` (
  `id` bigint(20) NOT NULL,
  `id_proyecto` int(11) NOT NULL,
  `fecha_hora` datetime NOT NULL DEFAULT current_timestamp(),
  `mq5` decimal(15,4) DEFAULT NULL,
  `dht11_temperatura` decimal(10,4) DEFAULT NULL,
  `dht11_humedad` decimal(10,4) DEFAULT NULL,
  `ds18b20` decimal(10,4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mediciones_proyecto`
--

INSERT INTO `mediciones_proyecto` (`id`, `id_proyecto`, `fecha_hora`, `mq5`, `dht11_temperatura`, `dht11_humedad`, `ds18b20`) VALUES
(2, 7, '2026-09-07 03:47:30', 326.0000, 20.5000, 67.6000, 25.4000),
(3, 8, '2026-09-07 03:50:12', NULL, NULL, NULL, NULL),
(4, 9, '2026-09-07 03:51:41', NULL, NULL, NULL, NULL),
(5, 10, '2026-09-07 03:53:15', NULL, NULL, NULL, NULL),
(6, 11, '2026-09-07 03:54:27', NULL, NULL, NULL, NULL),
(7, 12, '2026-09-07 03:56:02', 730.0000, 20.1000, 71.2000, 20.9000),
(8, 12, '2026-09-07 04:00:08', 467.0000, 20.2000, 52.9000, 22.6000),
(9, 13, '2026-09-07 04:00:20', 408.0000, 24.1000, 55.8000, 23.2000),
(10, 15, '2026-09-07 04:33:48', 485.0000, 20.5000, 60.7000, 27.0000),
(11, 15, '2026-09-09 01:40:36', 440.0000, 26.2000, 58.1000, 20.1000);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyectos`
--

CREATE TABLE `proyectos` (
  `Id_proyecto` int(11) NOT NULL,
  `Usuario` varchar(100) NOT NULL,
  `Descripcion` text DEFAULT NULL,
  `Titulo` varchar(200) NOT NULL,
  `Fecha_inicio` datetime DEFAULT NULL,
  `Fecha_fin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proyectos`
--

INSERT INTO `proyectos` (`Id_proyecto`, `Usuario`, `Descripcion`, `Titulo`, `Fecha_inicio`, `Fecha_fin`) VALUES
(15, 'Emiboca123', 'dasdsad', 'adsdas', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` varchar(100) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido` varchar(100) NOT NULL,
  `Rol` enum('estudiante','docente','invitado','administrador') NOT NULL DEFAULT 'estudiante',
  `Password` varchar(255) NOT NULL DEFAULT 'estudiantes_2026',
  `Institucion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `Nombre`, `Apellido`, `Rol`, `Password`, `Institucion`) VALUES
('Emiboca123', 'Emiliano', 'Legendre', 'administrador', '$2y$10$4hsyFdYDrNdPaPaXu34WT.wXjn5q7GVFlms4gdAsSLiDKz/fVXOlW', 'EEST4'),
('USR001', 'Juan', 'Pérez', 'estudiante', 'estudiantes_2026', 'Instituto Demo');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `conf_modulos`
--
ALTER TABLE `conf_modulos`
  ADD PRIMARY KEY (`Id_modulo`),
  ADD KEY `fk_modulos_proyecto` (`Id_proyecto`);

--
-- Indices de la tabla `mediciones`
--
ALTER TABLE `mediciones`
  ADD PRIMARY KEY (`Id_medicion`),
  ADD KEY `fk_mediciones_proyecto` (`Id_proyecto`),
  ADD KEY `fk_mediciones_modulo` (`Id_modulo`);

--
-- Indices de la tabla `mediciones_proyecto`
--
ALTER TABLE `mediciones_proyecto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_proyecto_fecha` (`id_proyecto`,`fecha_hora`);

--
-- Indices de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD PRIMARY KEY (`Id_proyecto`),
  ADD KEY `fk_proyectos_usuario` (`Usuario`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `conf_modulos`
--
ALTER TABLE `conf_modulos`
  MODIFY `Id_modulo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `mediciones`
--
ALTER TABLE `mediciones`
  MODIFY `Id_medicion` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `mediciones_proyecto`
--
ALTER TABLE `mediciones_proyecto`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `proyectos`
--
ALTER TABLE `proyectos`
  MODIFY `Id_proyecto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `conf_modulos`
--
ALTER TABLE `conf_modulos`
  ADD CONSTRAINT `fk_modulos_proyecto` FOREIGN KEY (`Id_proyecto`) REFERENCES `proyectos` (`Id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `mediciones`
--
ALTER TABLE `mediciones`
  ADD CONSTRAINT `fk_mediciones_modulo` FOREIGN KEY (`Id_modulo`) REFERENCES `conf_modulos` (`Id_modulo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mediciones_proyecto` FOREIGN KEY (`Id_proyecto`) REFERENCES `proyectos` (`Id_proyecto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proyectos`
--
ALTER TABLE `proyectos`
  ADD CONSTRAINT `fk_proyectos_usuario` FOREIGN KEY (`Usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

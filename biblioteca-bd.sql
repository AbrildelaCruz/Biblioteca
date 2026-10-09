-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-10-2026 a las 19:10:26
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
-- Base de datos: `biblioteca-bd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id_Alumnos` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Madre_encargada` varchar(100) DEFAULT NULL,
  `Familia` varchar(100) DEFAULT NULL,
  `Piso` varchar(20) DEFAULT NULL,
  `Grupo` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_Alumnos`, `Nombre`, `Madre_encargada`, `Familia`, `Piso`, `Grupo`) VALUES
(1, 'Abril', 'Hna. Griseida', 'sta bernardiata', '2', '501'),
(2, 'Heidy', 'Hna. Griseida', 'sta bernardita', '2', '501'),
(6, 'Diana', 'Hna. Rosario', 'Sta. Adela', '3', '502');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `Estado_conservación` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `libros`
--

CREATE TABLE `libros` (
  `id_libro` int(11) NOT NULL,
  `Titulo` varchar(255) NOT NULL,
  `Autor` varchar(150) DEFAULT NULL,
  `Categoría` varchar(100) NOT NULL,
  `Editorial` varchar(100) DEFAULT NULL,
  `Edición` varchar(50) DEFAULT NULL,
  `Idioma` varchar(50) DEFAULT NULL,
  `Ejemplares_totales` int(11) DEFAULT 0,
  `Ejemplares_disponibles` int(11) DEFAULT 0,
  `Ejemplares_prestados` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL,
  `id_alumnos` int(11) DEFAULT NULL,
  `id_profesores` int(11) DEFAULT NULL,
  `id_libros` int(11) NOT NULL,
  `fecha_salida` date NOT NULL,
  `fecha_devolucion_esperada` date NOT NULL,
  `fecha_devolucion_real` date DEFAULT NULL,
  `estado_libro` varchar(100) DEFAULT NULL,
  `Renovacion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (  
  `id_Profesores` INT(11) NOT NULL AUTO_INCREMENT,  
  `Nombre` VARCHAR(100) NOT NULL,  
  `Materia` VARCHAR(100) DEFAULT NULL,  
  `Libro` VARCHAR(100) NOT NULL,  
  `Cantidad` INT(11) NOT NULL,  
  `Fecha_de_prestamo` DATE NOT NULL,  
  `Fecha_de_entrega` DATE NOT NULL,
  PRIMARY KEY (`id_Profesores`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id_Alumnos`);

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id_inventario`),
  ADD KEY `id_libro` (`id_libro`);

--
-- Indices de la tabla `libros`
--
ALTER TABLE `libros`
  ADD PRIMARY KEY (`id_libro`);

--
-- Indices de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD PRIMARY KEY (`id_prestamo`),
  ADD KEY `id_alumnos` (`id_alumnos`),
  ADD KEY `id_profesores` (`id_profesores`),
  ADD KEY `id_libros` (`id_libros`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id_Profesores`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id_Alumnos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id_inventario` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_Profesores` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD CONSTRAINT `inventario_ibfk_1` FOREIGN KEY (`id_libro`) REFERENCES `libros` (`id_libro`) ON DELETE CASCADE;

--
-- Filtros para la tabla `prestamos`
--
ALTER TABLE `prestamos`
  ADD CONSTRAINT `prestamos_ibfk_1` FOREIGN KEY (`id_alumnos`) REFERENCES `alumnos` (`id_Alumnos`) ON DELETE SET NULL,
  ADD CONSTRAINT `prestamos_ibfk_2` FOREIGN KEY (`id_profesores`) REFERENCES `profesores` (`id_Profesores`) ON DELETE SET NULL,
  ADD CONSTRAINT `prestamos_ibfk_3` FOREIGN KEY (`id_libros`) REFERENCES `libros` (`id_libro`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

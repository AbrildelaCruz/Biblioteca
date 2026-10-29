-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-10-2026 a las 16:24:08
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
CREATE DATABASE IF NOT EXISTS `biblioteca-bd` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `biblioteca-bd`;

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
  `Grupo` varchar(20) DEFAULT NULL,
  `Renovación` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id_Alumnos`, `Nombre`, `Madre_encargada`, `Familia`, `Piso`, `Grupo`, `Renovación`) VALUES
(0, 'LuzMaria', 'Hna. Rosario', 'Sta. Adela', '3', '508', NULL),
(1, 'Abril', 'Hna. Griseida', 'sta bernardiata', '2', '501', NULL),
(2, 'Heidy', 'Hna. Griseida', 'sta bernardita', '2', '501', NULL),
(6, 'Diana', 'Hna. Rosario', 'Sta. Antonia', '3', '502', NULL);

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
  `Editorial` varchar(100) DEFAULT NULL,
  `ISBN` varchar(100) DEFAULT NULL,
  `País` varchar(100) DEFAULT NULL,
  `Idioma` varchar(50) DEFAULT NULL,
  `Descripción` varchar(100) DEFAULT NULL,
  `Encabezamiento de Materia` varchar(100) DEFAULT NULL,
  `Ejemplares_totales` int(11) DEFAULT 0,
  `Ejemplares_disponibles` int(11) DEFAULT 0,
  `Ejemplares_prestados` int(11) DEFAULT 0,
  `Clasificación` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `libros`
--

INSERT INTO `libros` (`id_libro`, `Titulo`, `Autor`, `Editorial`, `ISBN`, `País`, `Idioma`, `Descripción`, `Encabezamiento de Materia`, `Ejemplares_totales`, `Ejemplares_disponibles`, `Ejemplares_prestados`, `Clasificación`) VALUES
(1, 'A ninguna de las tres', 'Fernando calderon', '\'Porrúa', '970-07-0221-9', 'México', 'Español', '22cm,b/n pág.159', 'Romanticismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(2, 'Al faro', 'Virginia Woolf', 'Porrúa', '970-07-1400-0', 'Reino Unido', 'Español', '22cm,b/n pág.228', 'Modernismo.-siglo XX.-literatura modernista', NULL, NULL, NULL, NULL),
(3, 'Al polo norte', 'Emilio Salgari', 'Porrúa', '968-432-469-4', 'Italia', 'Español', '22cm,b/n pág.151', 'Romanticismo.-siglo XIX.-literatura de aventuras', NULL, NULL, NULL, NULL),
(4, 'Alemania', 'Enrique Heine', 'Porrúa', '968-452-469-2', 'Alemania', 'Español', '22cm,b/n pág.181', 'Romanticismo.-siglo XIX.-literatura alemana', NULL, NULL, NULL, NULL),
(5, 'Alicia en el pais de las maravillas', 'Lewis Carroll', 'Porrúa', '970-07-6752-3', 'Reino Unido', 'Español', '22cm,b/n pág.230', 'Romanticismo.-siglo XIX.-literatura inglesa', NULL, NULL, NULL, NULL),
(6, 'Almacén de antigüedades', 'Carlos Dickens', 'Porrúa', '968-452-754-3', 'Reino Unido', 'Español', '22cm,b/n pág.351', 'Realismo.-Siglo XIX.-literatura inglesa', NULL, NULL, NULL, NULL),
(7, 'Amadis de Gaula', 'Arturo Souto', 'Porrúa', '970-07-1563-9', 'España', 'Español', '22cm,b/n pág.307', 'Edad Media.-siglos XIV y XV.-literatura española', NULL, NULL, NULL, NULL),
(8, 'Amalia', 'José Mármol', 'Porrúa', '970-07-1834-4', 'Buenos Aires', 'Español', '22cm,b/n pág.584', 'Romanticismo.-siglo XIX.-literatura argentina', NULL, NULL, NULL, NULL),
(9, 'Amaya o los vascos en el siglo VIII', 'Francisco Navarro Villoslada', 'Porrúa', '968-452-223-1', 'España', 'Español', '22cm,b/n pág.588', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(10, 'Análisis de la expresión literaria', 'Arqueles Vela', 'Porrúa', '970-07-1597-3', 'México', 'Español', '22cm,b/n pág.225', 'Contemporánea.-siglo XX.-literatura mexicana', NULL, NULL, NULL, NULL),
(11, 'Antología', 'Alfonso el Sabio', 'Porrúa', '970-07-2114-0', 'España', 'Español', '22cm,b/n pág.238', 'Edad Media.-siglo XIII.-literatura española', NULL, NULL, NULL, NULL),
(12, 'Arcipreste de talavera', 'Alfonso Martínez De Toledo', 'Porrúa', '970-07-2143-4', 'España', 'Español', '22cm,b/n pág.397', 'Edad Media.-siglos XIV y XV.-literatura española', NULL, NULL, NULL, NULL),
(13, 'Ariel', 'José Enrique Rodó', 'Porrúa', '968-432-225-9', 'Uruguay', 'Español', '22cm,b/n pág.245', 'Modernismo.-siglo XX.-literatura modernista', NULL, NULL, NULL, NULL),
(14, 'Así se templo el acero', 'Nikolai Ostrovski', 'Porrúa', '968-432-937-7', 'Rusia', 'Español', '22cm,b/n pág.261', 'Realismo.-siglo XX.-literatura rusa', NULL, NULL, NULL, NULL),
(15, 'Atala-Rene', 'Rene Chateaubriand', 'Porrúa', '968-452-204-5', 'Francia', 'Español', '22cm,b/n pág.198', 'Romanticismo.-siglo XIX.-literatura francesa y grecorromana', NULL, NULL, NULL, NULL),
(16, 'Autobiografía', 'Benvenuto Cellini', 'Porrúa', '968-452-755-1', 'Francia', 'Español', '22cm,b/n pág.357', 'Renacimiento.-siglos XVI y XVII.-literatura francesa y grecorromana', NULL, NULL, NULL, NULL),
(17, 'Aventuras de Robinson Crusoe', 'Daniel Defoe', 'Porrúa', '970-07-2637-1', 'Inglaterra', 'Español', '22cm,b/n pág.158', 'Neoclasicismo.-siglo XVIII.-literatura inglesa', NULL, NULL, NULL, NULL),
(18, 'Aventuras de telemaco', 'Fenelon', 'Porrúa', '968-432-808-7', 'Francia', 'Español', '22cm,b/n pág.231', 'Romance.-siglo XVII.-literatura francesa', NULL, NULL, NULL, NULL),
(19, 'Aventuras del capitan hatteras', 'Julio Verne', 'Porrúa', '968-452-265-7', 'Francia', 'Español', '22cm,b/n pág.351', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(20, 'Azorín', 'M. Del Pozo', 'Porrúa', '970-07-1566-3', 'España', 'Español', '22cm,b/n pág.275', 'Modernismo.-siglo XX.-literatura contemporánea', NULL, NULL, NULL, NULL),
(21, 'Bajo la rueda', 'Hermann Hesse', 'Porrúa', '970-07-1301-6', 'Alemania', 'Español', '22cm,b/n pág.257', 'Romanticismo.-siglo XIX.-Novela alemana', NULL, NULL, NULL, NULL),
(22, 'Beatriz', 'Gaston Boissier', 'Porrúa', '968-452-186-3', 'Francia', 'Español', '22cm,b/n pág.250', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(23, 'Ben-hur', 'Lewis Wallace', 'Porrúa', '968-432-306-0', 'EE.UU.', 'Español', '22cm,b/n pág.335', 'Romanticismo.-siglo XIX.-literatura estadounidense', NULL, NULL, NULL, NULL),
(24, 'Caballería roja', 'Isaac Babel', 'Porrúa', '968-452-652-3', 'Alemania', 'Español', '22cm,b/n pág.235', 'Realismo.-siglo XX.-literatura rusa', NULL, NULL, NULL, NULL),
(25, 'Canción de navidad', 'Carlos Dickens', 'Porrúa', '970-07-1584-1', 'Reino Unido', 'Español', '22cm,b/n pág.379', 'Realismo.-siglo XIX.-literatura inglesa', NULL, NULL, NULL, NULL),
(26, 'Cancionero', 'Petrarca', 'Porrúa', '968-452-117-0', 'Italia', 'Español', '22cm,b/n pág.325', 'Renacimiento.-siglo XIV.-literatura italiana', NULL, NULL, NULL, NULL),
(27, 'Cándido', 'Voltaire', 'Porrúa', '970-07-1487-6', 'Francia', 'Español', '22cm,b/n pág.193', 'Ilustración.-siglo XVIII.-literatura francesa', NULL, NULL, NULL, NULL),
(28, 'Cándido o el optimismo', 'Voltaire', 'Porrúa', '970-07-1487-6', 'Francia', 'Español', '22cm,b/n pág.193', 'Ilustración.-siglo XVIII.-literatura francesa', NULL, NULL, NULL, NULL),
(29, 'Cantares de Gesta Medievales', 'Manuel Alvar', 'Porrúa', '968-432-720-X', 'España', 'Español', '22cm,b/n pág.200', 'Medieval.-siglo XII.-literatura europea', NULL, NULL, NULL, NULL),
(30, 'Cantos de vida y esperanza', 'Rubén Darío', 'Porrúa', '970-07-1783-6', 'Nicaragua', 'Español', '22cm,b/n pág.183', 'Modernismo.-siglo XIX.-literatura nicaragüense', NULL, NULL, NULL, NULL),
(31, 'Cárcel de amor', 'Diego de San Pedro', 'Porrúa', NULL, 'España', 'Español', '22cm,b/n pág.195', 'Prerrenacimiento.-siglo XV.-literatura española', NULL, NULL, NULL, NULL),
(32, 'Cecilia Valdés', 'Cirilo Villaverde', 'Porrúa', '968-432-326-3', 'Cuba', 'Español', '22cm,b/n pág.301', 'Romanticismo.-siglo XIX.-literatura cubana', NULL, NULL, NULL, NULL),
(33, 'César Cascabel', 'Julio Verne', 'Porrúa', '968-452-326-3', 'Francia', 'Español', '22cm,b/n pág.265', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(34, 'Chapaev', 'Dimitri Furmanov', 'Porrúa', '968-452-650-7', 'Rusia', 'Español', '22cm,b/n pág.209', 'Realismo.-siglo XX.-literatura rusa', NULL, NULL, NULL, NULL),
(35, 'Cicerón y sus amigos', 'Gaston Boissier', 'Porrúa', '968-452-186-3', 'Francia', 'Español', '22cm,b/n pág.250', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(36, 'Clemencia', 'Ignacio M. Altamirano', 'Porrúa', '968-432-027-4', 'México', 'Español', '22cm,b/n pág.208', 'Romanticismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(37, 'Comentarios reales', 'Garcilaso de la Vega', 'Porrúa', '968-432-656-3', 'Perú', 'Español', '22cm,b/n pág.438', 'Renacimiento.-siglo XVII.-literatura peruana', NULL, NULL, NULL, NULL),
(38, 'Cosmopolitas', 'W. Somerset Maugham', 'Porrúa', '968-452-284-0', 'Inglaterra', 'Español', '22cm,b/n pág.221', 'Modernismo.-siglo XX.-literatura británica', NULL, NULL, NULL, NULL),
(39, 'Cuentos de la selva', 'Horacio Quiroga', 'Porrúa', '970-07-6727-2', 'Argentina', 'Español', '22cm,b/n pág.215', 'Cuentos y relatos cortos de América', NULL, NULL, NULL, NULL),
(40, 'Cuentos judíos', 'Ilan Stavans', 'Porrúa', '968-452-762-6', 'México', 'Español', '22cm,b/n pág.447', 'Contemporánea.-siglo XX.-literatura mexicana', NULL, NULL, NULL, NULL),
(41, 'Cuentos y poemas del antiguo', 'Angel Ma. Garibay K.', 'Porrúa', '970-07-2032-2', 'Israel', 'Español', '22cm,b/n pág.232', 'Antigüedad.-siglo X d.C.-literatura hebrea', NULL, NULL, NULL, NULL),
(42, 'Cuentos escogidos', 'Antón Chéjov', 'Porrúa', '968-432-163-9', 'Rusia', 'Español', '22cm,b/n pág.312', 'Realismo.-siglo XIX.-literatura rusa', NULL, NULL, NULL, NULL),
(43, 'Cuentos de la cruz', 'Alejandro Pérez Lugín', 'Porrúa', '970-07-098-0', 'Madrid', 'Español', '22cm,b/n pág.287', 'Modernismo.-siglo XX.-literatura española', NULL, NULL, NULL, NULL),
(44, 'Cyrano de Bergerac', 'Edmond Rostand', 'Porrúa', '968-432-798-6', 'Francia', 'Español', '22cm,b/n pág.131', 'Romanticismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(45, 'Dafnis y Cloe', 'Longo', 'Porrúa', '968-432-256-9', 'Mitilene', 'Español', '22cm,b/n pág.181', 'Romántico.-siglo II a.C.-novela griega', NULL, NULL, NULL, NULL),
(46, 'David Copperfield', 'Carlos Dickens', 'Porrúa', '968-452-928-7', 'Inglaterra', 'Español', '22cm,b/n pág.542', 'Realismo.-siglo XIX.-literatura inglesa', NULL, NULL, NULL, NULL),
(47, 'De mi vida poesía y verdad', 'J.W. Goethe', 'Porrúa', '968-432-997-4', 'Alemania', 'Español', '22cm,b/n pág.501', 'Romanticismo.-siglos XVIII y XIX.-literatura alemana', NULL, NULL, NULL, NULL),
(48, 'Diálogo de la lengua', 'Juan De Valdés', 'Porrúa', '968-432-941-5', 'España', 'Español', '22cm,b/n pág.135', 'Renacimiento.-siglo XVI.-literatura española', NULL, NULL, NULL, NULL),
(49, 'Diálogos', 'Platón', 'Porrúa', '970-07-1481-7', 'Grecia', 'Español', '22cm,b/n pág.407', 'Antigüedad.-siglo IV d.C.-literatura griega', NULL, NULL, NULL, NULL),
(50, 'Diálogos de amor', 'León Hebreo', 'Porrúa', '968-432-084-4', 'España', 'Español', '22cm,b/n pág.278', 'Renacimiento.-siglos XVI-XVII.-literatura italiana y española', NULL, NULL, NULL, NULL),
(51, 'Diario íntimo', 'Enrique Federico Amiel', 'Porrúa', '968-432-160-x', 'Suiza', 'Español', '22cm,b/n pág.439', 'Realismo.-siglo XIX.-literatura europea', NULL, NULL, NULL, NULL),
(52, 'Don Álvaro o La fuerza del sino', 'Duque de Rivas', 'Porrúa', '970-07-2222-9', 'Finlandia', 'Español', '22cm,b/n pág.407', 'Romanticismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(53, 'Don Gil de las calzas verdes', 'Tirso de Molina', 'Porrúa', '978-84-7039-105-7', 'España', 'Español', '22cm,b/n pág.265', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(54, 'Don Carlos', 'Federico Schiller', 'Porrúa', '968-432-996-2', 'Alemania', 'Español', '22cm,b/n pág.234', 'Romántico.-siglo XVIII.-literatura alemana', NULL, NULL, NULL, NULL),
(55, 'Don Juan Tenorio', 'José Zorrilla', 'Porrúa', '970-07-2665-7', 'España', 'Español', '22cm,b/n pág.143', 'Romanticismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(56, 'Don segundo sombra', 'Ricardo Güiraldes', 'Porrúa', '968-432-365-5', 'Argentina', 'Español', '22cm,b/n pág.129', 'Regionalismo.-siglo XX.-literatura argentina', NULL, NULL, NULL, NULL),
(57, 'Doña Bárbara', 'Rómulo Gallegos', 'Porrúa', '978-84-376-0154-7', 'Venezuela', 'Español', '22cm,b/n pág.349', 'Realismo.-siglo XX.-literatura venezolana', NULL, NULL, NULL, NULL),
(58, 'Doña Perfecta', 'Benito Pérez Galdós', 'Porrúa', '968-432-257-7', 'España', 'Español', '22cm,b/n pág.199', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(59, 'El archipiélago de fuego', 'Julio Verne', 'Porrúa', '968-452-330-0', 'Francia', 'Español', '22cm,b/n pág.204', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(60, 'El Cantar de Roldán', 'Anónimo', 'Porrúa', '968-432-514-2', 'Francia', 'Español', '22cm,b/n pág.146', 'Edad Media.-siglo XII.-literatura francesa', NULL, NULL, NULL, NULL),
(61, 'El capitán de la \"D Jumna\"', 'Emilio Salgari', 'Porrúa', '968-452-252-2', 'España', 'Español', '22cm,b/n pág.167', 'Realismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(62, 'El carretero de la muerte', 'Selma Lagerlöf', 'Porrúa', '968-452-271-1', 'Suecia', 'Español', '22cm,b/n pág.210', 'Narrativa.-siglo XX.-literatura sueca', NULL, NULL, NULL, NULL),
(63, 'El castillo de los carpatos', 'Julio Verne', 'Porrúa', '968-452-727-7', 'Francia', 'Español', '22cm,b/n pág.257', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(64, 'El conde lucanor', 'Don Juan Manuel', 'Porrúa', '970-07-1695-6', 'España', 'Español', '22cm,b/n pág.195', 'Edad Media.-siglo XIV.-literatura española', NULL, NULL, NULL, NULL),
(65, 'El cantar de los nibelungos', 'Anónimo', 'Porrúa', '970-07-2177-1', 'Austria y Aleman', 'Español', '22cm,b/n pág.255', 'Edad Media.-siglo XIII.-literatura germánica', NULL, NULL, NULL, NULL),
(66, 'El contrato social', 'Juan Jacobo Rousseau', 'Porrúa', '970-07-1481-6', 'Amsterdam', 'Español', '22cm,b/n pág.234', 'Ilustración.-siglo XVIII.-literatura francesa', NULL, NULL, NULL, NULL),
(67, 'El corsario negro', 'Emilio Salgari', 'Porrúa', '968-432-639-4', 'Italia', 'Español', '22cm,b/n pág.170', 'Romanticismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(68, 'El decamerón', 'Boccaccio', 'Porrúa', '970-07-1688-0', 'Italia', 'Español', '22cm,b/n pág.475', 'Renacimiento.-siglo XIV.-literatura italiana', NULL, NULL, NULL, NULL),
(69, 'El desafío', 'Julio Verne', 'Porrúa', '968-452-452-6', 'Rusia', 'Español', '22cm,b/n pág.205', 'Realismo.-siglo XIX.-literatura peruana', NULL, NULL, NULL, NULL),
(70, 'El jinete de la carreta de c.', 'Eça de Queiroz', 'Porrúa', '968-452-191-X', 'Portugal', 'Español', '22cm,b/n pág.308', 'Realismo.-siglo XIX.-literatura portuguesa', NULL, NULL, NULL, NULL),
(71, 'El diablo cojuelo', 'Luis Vélez de Guevara', 'Porrúa', '968-432-344-1', 'España', 'Español', '22cm,b/n pág.110', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(72, 'El discreto', 'Baltasar Gracián', 'Porrúa', '968-432-163-4', 'España', 'Español', '22cm,b/n pág.397', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(73, 'El doncel de don Enrique, el doliente', 'Mariano José de Larra', 'Porrúa', '968-452-002-6', 'España', 'Español', '22cm,b/n pág.259', 'Romanticismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(74, 'El examen de maridos', 'Juan Ruiz de Alarcón', 'Porrúa', '968-432-973-3', 'España', 'Español', '22cm,b/n pág.229', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(75, 'El guarany', 'José Martiniano de Alencar', 'Porrúa', '970-07-2261-9', 'Brasil', 'Español', '22cm,b/n pág.305', 'Romanticismo.-siglo XIX.-literatura brasileña', NULL, NULL, NULL, NULL),
(76, 'El hombre de la situación', 'Manuel Payno', 'Porrúa', '968-452-514-1', 'México', 'Español', '22cm,b/n pág.288', 'Costumbrismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(77, 'El hombrecillo de los ganzos', 'Jakob Wassermann', 'Porrúa', '970-07-0933-7', 'Alemania', 'Español', '22cm,b/n pág.405', 'Realismo.-siglo XX.-literatura alemana', NULL, NULL, NULL, NULL),
(78, 'El indio', 'Gregorio López y Fuentes', 'Porrúa', '968-432-460-X', 'México', 'Español', '22cm,b/n pág.125', 'Narrativa.-siglo XX.-literatura mexicana', NULL, NULL, NULL, NULL),
(79, 'El ingenioso hidalgo', 'Alonso Fernández de Avellaneda', 'Porrúa', '968-452-153-7', 'España', 'Español', '22cm,b/n pág.275', 'Renacimiento.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(80, 'El jorobado', 'Paul Féval', 'Porrúa', '968-452-170-7', 'Francia', 'Español', '22cm,b/n pág.391', 'Romanticismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(81, 'El judío errante', 'Eugenio Sue', 'Porrúa', '968-452-610-5', 'Francia', 'Español', '22cm,b/n pág.693', 'Romanticismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(82, 'El libro de las tierras vírgenes', 'Rudyard Kipling', 'Porrúa', '970-07-1969-2', 'Reino Unido', 'Español', '22cm,b/n pág.229', 'Realismo.-siglo XIX.-literatura británica', NULL, NULL, NULL, NULL),
(83, 'El lirio en el valle', 'Honorato de Balzac', 'Porrúa', '970-07-2027-6', 'Francia', 'Español', '22cm,b/n pág.173', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(84, 'El llamado de la selva', 'Jack London', 'Porrúa', '970-07-1873-5', 'Estados Unidos', 'Español', '22cm,b/n pág.199', 'Realismo.-siglo XX.-literatura estadounidense', NULL, NULL, NULL, NULL),
(85, 'El mar', 'Jack London', 'Porrúa', '968-432-421-9', 'Estados Unidos', 'Español', '22cm,b/n pág.200', 'Realismo.-siglo XX.-literatura estadounidense', NULL, NULL, NULL, NULL),
(86, 'El mito de Sísifo', 'Albert Camus', 'Porrúa', '970-07-1688-0', 'Francia', 'Español', '22cm,b/n pág.195', 'Existencialismo.-siglo XX.-literatura francesa', NULL, NULL, NULL, NULL),
(87, 'El mártir del gólgota', 'Enrique Pérez Escrich', 'Porrúa', '970-07-1622-8', 'España', 'Español', '22cm,b/n pág.422', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(88, 'El negro del narciso', 'Joseph Conrad', 'Porrúa', '968-452-257-5', 'Inglaterra', 'Español', '22cm,b/n pág.187', 'Neoclasicismo.-siglo XIX.-literatura británica', NULL, NULL, NULL, NULL),
(89, 'El origen de las especies', 'Carlos Darwin', 'Porrúa', '970-07-2102', 'Reino Unido', 'Español', '22cm,b/n pág.232', 'Positivismo.-siglo XIX.-literatura británica', NULL, NULL, NULL, NULL),
(90, 'El pájaro azul', 'Maurice Maeterlinck', 'Porrúa', '970-07-2147-7', 'Bélgica', 'Español', '22cm,b/n pág.83', 'Modernismo.-siglo XX.-literatura teatral', NULL, NULL, NULL, NULL),
(91, 'El paje del duque de Saboya', 'Alejandro Dumas', 'Porrúa', '968-432-659-7', 'Francia', 'Español', '22cm,b/n pág.375', 'Romanticismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(92, 'El periquillo sarniento', 'José Joaquín Fernández de Lizardi', 'Porrúa', '970-07-1606-6', 'México', 'Español', '22cm,b/n pág.374', 'Barroco.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(93, 'El poder y la gloria', 'Graham Greene', 'Porrúa', '970-07-1636-8', 'Reino Unido', 'Español', '22cm,b/n pág.174', 'Realismo.-siglo XX.-literatura británica', NULL, NULL, NULL, NULL),
(94, 'El príncipe idiota', 'Fedor M. Dostoievski', 'Porrúa', '968-432-679-0', 'Rusia', 'Español', '22cm,b/n pág.193', 'Realismo.-siglo XIX.-literatura rusa', NULL, NULL, NULL, NULL),
(95, 'El príncipe idiota', 'Paul Féval', 'Porrúa', '968-452-170-7', 'Francia', 'Español', '22cm,b/n pág.290', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(96, 'El principito', 'Antoine de Saint-Exupéry', 'Porrúa', '970-07-2742-4', 'Francia', 'Español', '22cm,b/n pág.88', 'Modernismo.-siglo XX.-literatura francesa', NULL, NULL, NULL, NULL),
(97, 'El ramayana', 'Valmiki', 'Porrúa', '970-07-2033-x', 'India', 'Español', '22cm,b/n pág.107', 'Antigüedad.-siglo III.-literatura hindú', NULL, NULL, NULL, NULL),
(98, 'El rey del mar', 'Emilio Salgari', 'Porrúa', '970-07-1625-2', 'Italia', 'Español', '22cm,b/n pág.239', 'Realismo.-siglo XIX.-literatura de aventuras', NULL, NULL, NULL, NULL),
(99, 'El romanticismo', 'Raimundo Lazo', 'Porrúa', '968-432-377-0', 'Imperio Romano', 'Español', '22cm,b/n pág.235', 'Romántico.-siglo III d.C.-literatura latina', NULL, NULL, NULL, NULL),
(100, 'El señor de bembibre', 'Enrique Gil y Carrasco', 'Porrúa', '968-432-935-0', 'España', 'Español', '22cm,b/n pág.266', 'Novela histórica.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(101, 'El sol de mayo', 'Juan A. Mateos', 'Porrúa', '970-07-0935', 'México', 'Español', '22cm,b/n pág.235', 'Romanticismo.-siglo XIX.-Intervención europea', NULL, NULL, NULL, NULL),
(102, 'El sueño de makar', 'Vladimir G. Korolenko', 'Porrúa', '968-452-270-2', 'Rusia', 'Español', '22cm,b/n pág.279', 'Realismo.-siglo XIX.-literatura rusa', NULL, NULL, NULL, NULL),
(103, 'El tejedor de segovia', 'Juan Ruiz de Alarcón', 'Porrúa', '968-432-973-3', 'España', 'Español', '22cm,b/n pág.229', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(104, 'El trabajo intelectual', 'Jean Guitton', 'Porrúa', '978-84-371-2155-7', 'Francia', 'Español', '22cm,b/n pág.225', 'siglo XX.-literatura francesa', NULL, NULL, NULL, NULL),
(105, 'El varón de rabinal', 'Rabinal-achí', 'Porrúa', '968-432-866-9', 'Guatemala', 'Español', '22cm,b/n pág.120', 'siglos XIII.-literatura prehispánica', NULL, NULL, NULL, NULL),
(106, 'El vergonzoso en el palacio', 'Tirso de Molina', 'Porrúa', '978-84-7039-105-7', 'España', 'Español', '22cm,b/n pág.265', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(107, 'El viejo y el mar', 'Ernest Hemingway', 'Porrúa', '970-07-1051-3', 'Estados Unidos', 'Español', '22cm,b/n pág.120', 'Vanguardismo.-siglo XX.-literatura estadounidense', NULL, NULL, NULL, NULL),
(108, 'El vizconde de bragelonne', 'Alejandro Dumas', 'Porrúa', '970-07-2155-1', NULL, 'Español', '22cm,b/n pág.610', 'Romanticismo.-siglo XVII.-literatura francesa', NULL, NULL, NULL, NULL),
(109, 'Elogio de la locura', 'Erasmo de Rotterdam', 'Porrúa', '970-07-2166-3', 'Europa', 'Español', '22cm,b/n pág.255', 'Renacimiento.-siglo XVI.-literatura neolatina', NULL, NULL, NULL, NULL),
(110, 'En el mar de las perlas', 'Emilio Salgari', 'Porrúa', '968-452-277-0', 'Italia', 'Español', '22cm,b/n pág.255', 'Romanticismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(111, 'En las fronteras del Far-west', 'Emilio Salgari', 'Porrúa', '968-452-757-6', 'Italia', 'Español', '22cm,b/n pág.285', 'Romanticismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(112, 'En los suburbios de la india', 'Emilio Salgari', 'Porrúa', '968-452-252-2', 'Italia', 'Español', '22cm,b/n pág.267', 'Romanticismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(113, 'Ensayos Completos', 'Montaigne', 'Porrúa', '970-07-1627-4', 'Francia', 'Español', '22cm,b/n pág.958', 'Renacimiento.-siglos XVI y XVII.-literatura francesa', NULL, NULL, NULL, NULL),
(114, 'Enéida', 'Virgilio', 'Porrúa', '968-432-131-3', 'Grecia', 'Español', '22cm,b/n pág.299', 'Antigüedad.-siglo I.-literatura romana', NULL, NULL, NULL, NULL),
(115, 'Episodios Nacionales', 'Victoriano Salado Álvarez', 'Porrúa', '968-432-892-X', 'España', 'Español', '22cm,b/n pág.202', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(116, 'Escritos literarios', 'Francisco Zarco', 'Porrúa', '968-452-455-0', 'México', 'Español', '22cm,b/n pág.247', 'Ensayos.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(117, 'Estudios y ensayos', 'Marcelino Menéndez Pelayo', 'Porrúa', '970-07-2011-4', 'España', 'Español', '22cm,b/n pág.429', 'Ensayos y críticas literarias contemporáneas', NULL, NULL, NULL, NULL),
(118, 'Fábulas', 'Anónimo', 'Porrúa', '970-07-1563-1', 'Grecia', 'Español', '22cm,b/n pág.293', 'Ilustración.-siglo VI.-literatura griega', NULL, NULL, NULL, NULL),
(119, 'Fausto y Werther', 'J.W. Goethe', 'Porrúa', '968-432-997-4', 'Alemania', 'Español', '22cm,b/n pág.501', 'Romanticismo.-siglos XVIII-XIX.-literatura alemana', NULL, NULL, NULL, NULL),
(120, 'Fera', 'Stefan Zweig', 'Porrúa', '970-07-1380-6', 'Austria', 'Español', '22cm,b/n pág.149', 'Histórica.-siglo XX.-literatura narrativa', NULL, NULL, NULL, NULL),
(121, 'Fuenteovejuna', 'Lope de Vega', 'Porrúa', '970-07-2291-0', 'España', 'Español', '22cm,b/n pág.229', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(122, 'Guzmán de Alfarache', 'Mateo Alemán', 'Porrúa', '968-452-059-1', 'España', 'Español', '22cm,b/n pág.427', 'Renacimiento.-siglo XVI.-literatura española', NULL, NULL, NULL, NULL),
(123, 'Gertrudis Gómez', 'Raimundo Lazo', 'Porrúa', '968-432-448-X', 'Cuba', 'Español', '22cm,b/n pág.155', 'Romanticismo.-siglo XIX.-literatura hispanoamericana', NULL, NULL, NULL, NULL),
(124, 'Gil Blas de Santillán', 'Le Sage', 'Porrúa', '968-452-422-4', 'Francia', 'Español', '22cm,b/n pág.534', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(125, 'Gil Gómez', 'Juan Díaz Covarrubias', 'Porrúa', '968-452-496-X', 'México', 'Español', '22cm,b/n pág.224', 'Romanticismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(126, 'Graziella Rafael', 'Alfonso de Lamartine', 'Porrúa', '968-432-610-6', 'Francia', 'Español', '22cm,b/n pág.184', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(127, 'Historia de la literatura hispanoamericana', 'Raimundo Lazo', 'Porrúa', '968-432-413-8', 'México', 'Español', '22cm,b/n pág.270', 'Histórica.-siglos XVI al XX.-literatura hispanoamericana', NULL, NULL, NULL, NULL),
(128, 'Hambre', 'Knut Hamsun', 'Porrúa', '968-432-887-9', 'Noruega', 'Español', '22cm,b/n pág.177', 'Modernismo.-siglo XIX.-literatura noruega', NULL, NULL, NULL, NULL),
(129, 'Héctor Servadac', 'Julio Verne', 'Porrúa', '968-452-265-7', 'Francia', 'Español', '22cm,b/n pág.351', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(130, 'Hogar y patria', 'Juan de Dios Peza', 'Porrúa', '968-432-133-3', 'México', 'Español', '22cm,b/n pág.225', 'Romanticismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(131, 'Ilusiones Perdidas', 'Honorato de Balzac', 'Porrúa', '970-07-2265-0', 'Francia', 'Español', '22cm,b/n pág.515', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(132, 'Iliada', 'Homero', 'Porrúa', '970-07-1572-8', 'Grecia', 'Español', '22cm,b/n pág.445', 'Antigüedad.-siglo VIII.-literatura griega', NULL, NULL, NULL, NULL),
(133, 'Infancia adolescencia juventud', 'León Tolstoi', 'Porrúa', '968-432-817-5', 'Rusia', 'Español', '22cm,b/n pág.380', 'Realismo.-siglo XIX.-literatura rusa', NULL, NULL, NULL, NULL),
(134, 'Indagación de la literatura española', 'Arqueles Vela', 'Porrúa', '970-07-1597-3', 'México', 'Español', '22cm,b/n pág.225', 'Contemporánea.-siglo XX.-literatura mexicana', NULL, NULL, NULL, NULL),
(135, 'Ivanhoe o el cruzado', 'Walter Scott', 'Porrúa', '970-07-0137-4', 'Escocia', 'Español', '22cm,b/n pág.275', 'Modernismo.-siglo XIX.-literatura cubana', NULL, NULL, NULL, NULL),
(136, 'Joyas de la amistad', 'Anónimo', 'Porrúa', '970-07-077-4', 'Francia', 'Español', '22cm,b/n pág.376', 'Romanticismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(137, 'Juanita la larga', 'Juan Valera', 'Porrúa', '968-432-104-X', 'España', 'Español', '22cm,b/n pág.220', 'Romanticismo.-siglo XIX.-Novela española', NULL, NULL, NULL, NULL),
(138, 'Karaban', 'Julio Verne', 'Porrúa', '968-452-272-X', 'Francia', 'Español', '22cm,b/n pág.251', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(139, 'Libro del buen amor', 'Arcipreste De Hita', 'Porrúa', '970-07-2143-4', 'España', 'Español', '22cm,b/n pág.397', 'Edad Media.-siglo XIV.-literatura española', NULL, NULL, NULL, NULL),
(140, 'La araucana', 'Alonso de Ercilla', 'Porrúa', '968-432-457-X', 'España', 'Español', '22cm,b/n pág.521', 'Renacimiento.-siglo XVI.-literatura española', NULL, NULL, NULL, NULL),
(141, 'La barraca', 'Blasco Ibáñez', 'Porrúa', '970-07-1580-9', 'España', 'Español', '22cm,b/n pág.321', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(142, 'La batalla de los arapiles', 'Benito Pérez Galdós', 'Porrúa', '968-432-199-X', 'España', 'Español', '22cm,b/n pág.199', 'Histórica.-siglo XIX.-literatura realista', NULL, NULL, NULL, NULL),
(143, 'La bacante', 'Guy de Maupassant', 'Porrúa', '970-07-2250-3', 'Francia', 'Español', '22cm,b/n pág.311', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(144, 'La cabaña del tío tom', 'Harriet Beecher Stowe', 'Porrúa', '970-07-1608-2', 'Estados Unidos', 'Español', '22cm,b/n pág.279', 'Realismo.-siglo XIX.-literatura estadounidense', NULL, NULL, NULL, NULL),
(145, 'La caída de un empire', 'Emilio Salgari', 'Porrúa', '968-452-467-7', 'Italia', 'Español', '22cm,b/n pág.223', 'Aventuras.-siglo XX.-literatura italiana', NULL, NULL, NULL, NULL),
(146, 'La ciudad de los milagros', 'Emilio Salgari', 'Porrúa', '968-452-255-0', 'Italia', 'Español', '22cm,b/n pág.232', 'Aventuras.-siglo XX.-literatura italiana', NULL, NULL, NULL, NULL),
(147, 'La campana de yuta', 'Emilio Salgari', 'Porrúa', '968-452-255-0', 'Italia', 'Español', '22cm,b/n pág.228', 'Aventuras.-siglo XX.-literatura italiana', NULL, NULL, NULL, NULL),
(148, 'La carreta', 'Enrique Amorim', 'Porrúa', '968-432-027-4', 'Uruguay', 'Español', '22cm,b/n pág.332', 'Realismo.-siglo XX.-literatura uruguaya', NULL, NULL, NULL, NULL),
(149, 'La cazadora de cabelleras', 'Emilio Salgari', 'Porrúa', '968-452-277-0', 'Italia', 'Español', '22cm,b/n pág.255', 'Romanticismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(150, 'La celestina', 'Fernando de Rojas', 'Porrúa', '970-07-2059-3', 'España', 'Español', '22cm,b/n pág.235', 'Novela dialogada.-siglo XVI.-literatura española', NULL, NULL, NULL, NULL),
(151, 'La ciudad antigua', 'Fustel de Coulanges', 'Porrúa', '970-07-2831-X', 'Francia', 'Español', '22cm,b/n pág.298', 'siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(152, 'La ciudad de las de oro verde y vert.', 'A. J. Cronin', 'Porrúa', '968-452-467-1', 'Escocia', 'Español', '22cm,b/n pág.192', 'Contemporánea.-siglo XX.-literatura británica', NULL, NULL, NULL, NULL),
(153, 'La conquista de las rutas oceánicas', 'Carlos Pereyra', 'Porrúa', '970-07-1540-X', 'Austria', 'Español', '22cm,b/n pág.313', 'Historiografía.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(154, 'La de Bringas', 'Benito Pérez Galdós', 'Porrúa', '968-452-103-0', 'España', 'Español', '22cm,b/n pág.365', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(155, 'La Desheredada', 'Benito Pérez Galdós', 'Porrúa', '968-452-514-1', 'Madrid', 'Español', '22cm,b/n pág.302', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(156, 'La favorita del mahdi', 'Emilio Salgari', 'Porrúa', '968-452-252-2', 'Italia', 'Español', '22cm,b/n pág.167', 'Romanticismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(157, 'La fuerza del sino', 'Duque de Rivas', 'Porrúa', '970-07-2222-9', 'Finlandia', 'Español', '22cm,b/n pág.113', 'Romanticismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(158, 'La gloria de don Ramiro', 'Enrique Larreta', 'Porrúa', '970-07-1834-3', 'Argentina', 'Español', '22cm,b/n pág.235', 'Modernismo.-siglo XX.-literatura argentina', NULL, NULL, NULL, NULL),
(159, 'La gitanilla', 'Anónimo', 'Porrúa', '970-07-1563-1', 'España', 'Español', '22cm,b/n pág.293', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(160, 'La intuición y el estilo', 'Pío Baroja', 'Porrúa', '968-452-380-7', 'España', 'Español', '22cm,b/n pág.417', 'Realismo.-siglo XX.-literatura rusa', NULL, NULL, NULL, NULL),
(161, 'La jangada', 'Julio Verne', 'Porrúa', '968-452-161-8', 'Francia', 'Español', '22cm,b/n pág.235', 'Novela de aventuras.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(162, 'La luna nueva', 'Rabindranath Tagore', 'Porrúa', NULL, 'India', 'Español', '22cm,b/n pág.259', 'Poesía lírica.-siglo XX.-literatura bengalí moderna', NULL, NULL, NULL, NULL),
(163, 'La luna y seis peniques', 'William Somerset Maugham', 'Porrúa', '970-07-1400-0', 'Reino Unido', 'Español', '22cm,b/n pág.228', 'Modernismo.-siglo XX.-literatura inglesa', NULL, NULL, NULL, NULL),
(164, 'La madre', 'Máximo Gorki', 'Porrúa', '970-07-1646-5', 'Rusia', 'Español', '22cm,b/n pág.325', 'Realismo.-siglo XX.-literatura rusa', NULL, NULL, NULL, NULL),
(165, 'La madre naturaleza', 'Emilia Pardo Bazán', 'Porrúa', '968-452-125-1', 'España', 'Español', '22cm,b/n pág.182', 'Naturalismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(166, 'La mala hora', 'Gabriel García Márquez', 'Porrúa', '978-84-376-0154-7', 'Colombia', 'Español', '22cm,b/n pág.349', 'Realismo mágico.-siglo XX.-literatura colombiana', NULL, NULL, NULL, NULL),
(167, 'La montaña de los siete Círculos', 'Thomas Merton', 'Porrúa', '970-07-1732-1', 'Estados Unidos', 'Español', '22cm,b/n pág.429', 'Espiritual.-siglo XX.-literatura religiosa', NULL, NULL, NULL, NULL),
(168, 'La occisa', 'G.E. Lessing', 'Porrúa', '968-452-662-8', 'Alemania', 'Español', '22cm,b/n pág.181', 'Neoclasicismo.-siglo XVIII.-literatura alemana', NULL, NULL, NULL, NULL),
(169, 'La perfecta casada', 'Fray Luis de León', 'Porrúa', '970-07-2222-9', 'España', 'Español', '22cm,b/n pág.113', 'Renacimiento.-siglo XVI.-literatura española', NULL, NULL, NULL, NULL),
(170, 'La perla roja', 'Emilio Salgari', 'Porrúa', '968-452-269-X', 'Italia', 'Español', '22cm,b/n pág.219', 'Romanticismo.-siglo XIX.-literatura de aventuras', NULL, NULL, NULL, NULL),
(171, 'La prueba de las promesas', 'Juan Ruiz de Alarcón', 'Porrúa', '968-432-973-3', 'México', 'Español', '22cm,b/n pág.229', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(172, 'La reina de los caribes', 'Emilio Salgari', 'Porrúa', '968-432-884-2', 'Italia', 'Español', '22cm,b/n pág.173', 'Romanticismo.-siglo XVII.-Novela de aventuras, Piratas', NULL, NULL, NULL, NULL),
(173, 'La resurrección de rocambole', 'Ponson du Terrail', 'Porrúa', '968-452-186-0', 'Francia', 'Español', '22cm,b/n pág.711', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(174, 'La vida y hechos de Estebanillo González', 'Anónimo', 'Porrúa', '968-452-143-X', 'España', 'Español', '22cm,b/n pág.151', 'Realismo.-siglo XVII.-literatura española o novela', NULL, NULL, NULL, NULL),
(175, 'La soberana del campo de oro', 'Emilio Salgari', 'Porrúa', '968-432-808-0', 'Francia', 'Español', '22cm,b/n pág.231', 'Aventuras.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(176, 'La tía Tula', 'Miguel de Unamuno', 'Porrúa', '968-432-884-2', 'España', 'Español', '22cm,b/n pág.103', 'Contemporánea.-siglo XX.-literatura española', NULL, NULL, NULL, NULL),
(177, 'La vorágine', 'José Eustasio Rivera', 'Porrúa', '968-452-131-7', 'Colombia', 'Español', '22cm,b/n pág.157', 'Realismo.-siglo XX.-literatura colombiana', NULL, NULL, NULL, NULL),
(178, 'La vida es sueño y El alcalde de Zalamea', 'Calderón de la Barca', 'Porrúa', '970-07-2143-7', 'España', 'Español', '22cm,b/n pág.160', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(179, 'La vida intelectual', 'A. D. Sertillanges', 'Porrúa', '968-432-944-X', 'Francia', 'Español', '22cm,b/n pág.225', 'siglo XX.-literatura europea/francesa', NULL, NULL, NULL, NULL),
(180, 'La vuelta al mundo en ochenta días', 'Julio Verne', 'Porrúa', '970-07-1889-1', 'Alemania', 'Español', '22cm,b/n pág.257', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(181, 'Laocoonte', 'G.E. Lessing', 'Porrúa', '968-452-662-8', 'Alemania', 'Español', '22cm,b/n pág.181', 'Ilustración.-siglo XVIII.-literatura comparada', NULL, NULL, NULL, NULL),
(182, 'Las minas del rey Salomón', 'Henry Rider Haggard', 'Porrúa', '968-452-165-0', 'Reino Unido', 'Español', '22cm,b/n pág.213', 'Histórica.-siglo XIX.-literatura inglesa', NULL, NULL, NULL, NULL),
(183, 'Las aventuras de Tom Sawyer', 'Mark Twain', 'Porrúa', '968-452-929-5', 'Estados Unidos', 'Español', '22cm,b/n pág.126', 'Realismo.-siglo XIX.-literatura estadounidense', NULL, NULL, NULL, NULL),
(184, 'Las cien mejores poesías', 'Marcelino Menéndez y Pelayo', 'Porrúa', '968-432-511-8', 'España', 'Español', '22cm,b/n pág.250', 'Poesía.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(185, 'Las dos emparedadas', 'Vicente Riva Palacio', 'Porrúa', '968-452-045-X', 'México', 'Español', '22cm,b/n pág.275', 'Romanticismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(186, 'Las flores del mal', 'Carlos Baudelaire', 'Porrúa', '968-452-913-X', 'Francia', 'Español', '22cm,b/n pág.194', 'Modernismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(187, 'Las historias de la literatura hispanoamericana', 'Raimundo Lazo', 'Porrúa', '968-432-413-8', 'México', 'Español', '22cm,b/n pág.270', 'Histórica.-siglos XVI al XX.-literatura hispanoamericana', NULL, NULL, NULL, NULL),
(188, 'Las llaves del reino', 'A. J. Cronin', 'Porrúa', '968-452-467-1', 'Escocia', 'Español', '22cm,b/n pág.192', 'Contemporánea.-siglo XX.-literatura británica', NULL, NULL, NULL, NULL),
(189, 'Las mocedades del cid', 'Guillén de Castro', 'Porrúa', '968-452-057-3', 'España', 'Español', '22cm,b/n pág.161', 'Barroco.-siglo XVII.-literatura española', NULL, NULL, NULL, NULL),
(190, 'Las once comedias', 'Aristófanes', 'Porrúa', '970-07-1644-9', 'Grecia', 'Español', '22cm,b/n pág.292', 'Antigüedad.-siglo IV.-literatura griega', NULL, NULL, NULL, NULL),
(191, 'Las siete tragedias', 'Sófocles', 'Porrúa', '970-07-1780-3', 'España/México', 'Español', '22cm,b/n pág.222', 'Antigüedad.-siglo V d.C.-literatura griega', NULL, NULL, NULL, NULL),
(192, 'Lázaro de Tormes', 'Anónimo', 'Porrúa', '970-07-0033-X', 'España', 'Español', '22cm,b/n pág.190', 'Renacimiento.-siglo XVI.-literatura española', NULL, NULL, NULL, NULL),
(193, 'Lecciones Preliminares de filosofía', 'Manuel García Morente', 'Porrúa', '968-432-241-0', 'España', 'Español', '22cm,b/n pág.302', 'Neoclasicismo.-siglo XX.-Filosofía', NULL, NULL, NULL, NULL),
(194, 'Lectura para mujeres', 'Gabriela Mistral', 'Porrúa', '968-432-537-1', 'México', 'Español', '22cm,b/n pág.255', 'Modernismo.-siglo XX.-literatura mexicana', NULL, NULL, NULL, NULL),
(195, 'Lecturas Españolas', 'Azorín', 'Porrúa', '970-07-1568-X', 'España', 'Español', '22cm,b/n pág.297', 'siglo XX.-literatura española', NULL, NULL, NULL, NULL),
(196, 'Leyendas Históricas', 'Juan de Dios Peza', 'Porrúa', '970-07-1032-4', 'México', 'Español', '22cm,b/n pág.242', 'Modernismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(197, 'Los amotinados de la bounty', 'Julio Verne', 'Porrúa', '968-452-252-2', 'Francia', 'Español', '22cm,b/n pág.267', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(198, 'Los caracteres', 'La Bruyère', 'Porrúa', '968-452-277-7', 'Francia', 'Español', '22cm,b/n pág.276', 'Clasicismo.-siglo XVII.-literatura francesa', NULL, NULL, NULL, NULL),
(199, 'Los cazadores de microbios', 'Paul de Kruif', 'Porrúa', '970-07-1688-2', 'Francia', 'Español', '22cm,b/n pág.355', 'Ciencia moderna', NULL, NULL, NULL, NULL),
(200, 'Los cosacos', 'León Tolstoi', 'Porrúa', '968-432-816-8', 'Rusia', 'Español', '22cm,b/n pág.283', 'Realismo.-siglo XIX.-literatura rusa', NULL, NULL, NULL, NULL),
(201, 'Los dioses tienen sed', 'Anatole France', 'Porrúa', '968-452-984-8', 'Francia', 'Español', '22cm,b/n pág.253', 'Novela histórica.-siglo XX.-literatura francesa moderna', NULL, NULL, NULL, NULL),
(202, 'Los misterios de la india', 'Emilio Salgari', 'Porrúa', '968-452-252-2', 'Italia', 'Español', '22cm,b/n pág.167', 'Romanticismo.-siglo XIX.-literatura italiana', NULL, NULL, NULL, NULL),
(203, 'Los misterios de París', 'Eugenio Sue', 'Porrúa', '968-452-212-6', 'Francia', 'Español', '22cm,b/n pág.505', 'Romanticismo.-siglo XIX.-literatura romana', NULL, NULL, NULL, NULL),
(204, 'Los naufragos de liguria', 'Emilio Salgari', 'Porrúa', '968-452-175-8', 'Italia', 'Español', '22cm,B/N,pag. 181', 'Realismo.-siglo XX .-literatura italiana', NULL, NULL, NULL, NULL),
(205, 'Los operarios de la viña', 'Giovanni Papini', 'Porrua', '968-452-750-0', 'Italia', 'Español', '22cm,b/n pág.206', 'Religiosa.-siglo XX .-literatura italiana.', NULL, NULL, NULL, NULL),
(206, 'Los Operarios de la viña', 'Giovanni Papini', 'Porrúa', '968-452-750-0', 'Italia', 'Español', '22cm,B/N,pag. 386', NULL, NULL, NULL, NULL, NULL),
(207, 'Los pardaillan', 'Miguel Zevaco', 'Porrúa', '973-07-2344-5', 'Francia', 'Español', '22cm,B/N,Pag.289', 'Romanticismo.-siglo XX.-literatura francesa.', NULL, NULL, NULL, NULL),
(208, 'Los piratas de las Bermudas', 'Emilio Salgari', 'Porrúa', '968-452-290-8', 'Italia', 'Español', '22cm,B/N,Pag.356', 'Romanticismo.-siglo XIX.-literatura mexicana, ', NULL, NULL, NULL, NULL),
(209, 'Los Pueblos', 'Azorín', 'Porrúa', '970-07-1566-3', 'España', 'Español', '22cm,B/N,Pag.275', 'Modernismo.-siglo XX.-literatura española', NULL, NULL, NULL, NULL),
(210, 'Los tres mosqueteros', 'Alexandre Dumas', 'Porrúa', '970-07-2670-3', 'Francia', 'Español', '22cm,B/N,pag. 484', 'Romanticismo.-siglo XIX.- literatura francesa', NULL, NULL, NULL, NULL),
(211, 'Los tres mosqueteros', 'Alejandro Dumas', 'Porrua', '970-07-2670-3', 'Francia', 'Español', '22cm, b/n, pág.486', 'Romanticismo.-siglo XIX.-Literatura francesa.', NULL, NULL, NULL, NULL),
(212, 'Los últimos filibusteros', 'Emilio Salgari', 'Porrúa', '968-452-256-8', 'Italia', 'Español', '22cm,B/N,pag. 221', 'Novela.-siglo XX .-', NULL, NULL, NULL, NULL),
(213, 'Macías', 'Mariano José De Larra', 'Porrúa', '968-452-002-6', 'España.', 'Español', '22cm,B/N,pag. 256', 'Romanticismo.-siglo XX .-Literatura española', NULL, NULL, NULL, NULL),
(214, 'Malvaloca', 'Serafin y Joaquin Alvarez Quintero', 'Porrua', '968-432-474-X', 'España', 'Español', '22cm,b/n pág.215', 'Realismo.- siglo XX.-literatura española.', NULL, NULL, NULL, NULL),
(215, 'Mandame Bovary', 'Gustavo Flaubert', 'Porrúa', '970-07-2852-8', 'Francia', 'Español', '22cm,B/N,pag. 195', 'Romanticismo.-siglo XlX .-Novela realista', NULL, NULL, NULL, NULL),
(216, 'Maria Estuardo', 'Federico Schiller', 'Porrúa', '970-07-1781-x', 'Alemania', 'Español', '22cm,B/N,Pag.207', 'Clasicismo.-siglos XVIII.- literatura alemana', NULL, NULL, NULL, NULL),
(217, 'Marianela', 'Benito Pérez Galdós', 'Porrua', '978-970-07-7328-0', 'España', 'Español', '22cm,b/n, pág.135', 'Realismo.-siglo XIX.-Literatura española', NULL, NULL, NULL, NULL),
(218, 'Martín Fierro', 'José Hernández', 'Porrúa', '968-432-424-3', 'argentina', 'español', '22cm,B/N,pag. 104', NULL, NULL, NULL, NULL, NULL),
(219, 'Mas cuentos', 'Horacio Quiroga', 'Porrúa', '970-07-6727-2', 'Argentina', 'Español', '22cm,B/N,Pag.215', NULL, NULL, NULL, NULL, NULL),
(220, 'Memorias de mis tiempos', 'Guillermo Prieto', 'Porrúa', '968-452-099-9', 'México', 'Español', '22cm,B/N,pag. 355', 'Romanticismo.-siglo XIX.- literatura mexicana', NULL, NULL, NULL, NULL),
(221, 'Memorias, reliquias y retratos', 'Juan de Dios Peza', 'Porrua', '968-452-427-7', 'Mexico', 'Español', '22cm,b/n pág.271', 'Romanticismo.- siglo XIX.-Literatura mexicana.', NULL, NULL, NULL, NULL),
(222, 'Miau, Marianela', 'Benito Pérez Galdós', 'Porrúa', '970-07-2181-7', 'España', 'Español', '22cm,B/N,pag. 104', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(223, 'Miguel strogoff', 'Julio Verne', 'Porrua', '968-432-510-x', 'Francia', 'Español', '22cm,b/n, pág.205', 'Realismo.-siglo XIX.-Literatura francesa.', NULL, NULL, NULL, NULL),
(224, 'Moby dick', 'Herman Melville', 'Porrúa', '968-452-152-9', 'Estados unidos', 'Español', '22cm,B/N,Pag.426', 'Renacimiento.-siglo XIX.-literatura estadounidense', NULL, NULL, NULL, NULL),
(225, 'Momentos estelares de la huminadad', 'Stefan Zweig', 'Porrua', '970-07-1380-6', 'Austria', 'Español', '22cm,b/n, pág.149', 'Histórica.-siglo XX.-Literatura narrativa', NULL, NULL, NULL, NULL),
(226, 'Motivos de Proteo', 'Jose Enrique Rodo', 'Porrúa', '968-432-225-9', 'Uruguay', 'Español', '22cm,B/N,pag.245', 'Modernismo.-siglo XX.-', NULL, NULL, NULL, NULL),
(227, 'Mudarse por mejorarse', 'Juan Ruiz De Alarcón', 'Porrúa', '968-432-973-3', 'México', 'Español', '22cm,B/N,pag. 229', 'Barroco.-siglo XVII .-literatura española', NULL, NULL, NULL, NULL),
(228, 'Musa Callejera', 'Guillermo Prieto', 'Porrúa', '968-452-064-6', 'México', 'Español', '22cm,B/N,pag. 275', 'Romanticismo.-siglo XIX .- Poesía mexicana', NULL, NULL, NULL, NULL),
(229, 'Napoleón en chamarrin', 'Benito Pérez Galdós', 'Porrúa', '9678-452-302-5', 'España', 'Español', '22cm,B/N,pag. 239', 'Realismo.-Siglo XIX.-Literatura española', NULL, NULL, NULL, NULL),
(230, 'Nayar', 'Miguel Ángel Menéndez', 'Porrúa', '968-432-187-2', 'México', 'Español', '22cm,B/N,pag. 278', NULL, NULL, NULL, NULL, NULL),
(231, 'Nido de Hidalgos', 'Ivan Turguenev', 'Porrúa', '970-07-2374-7', 'España', 'Español', '22cm,B/N,Pag.329', 'Realismo.-siglo XIX.-literatura rusa', NULL, NULL, NULL, NULL),
(232, 'Niebla', 'Miguel de Unamuno', 'Porrúa', '970-07-1692-9', 'España', 'Español', '22cm,B/N,Pag.235', 'Realismo.-siglo XX.-literatura española', NULL, NULL, NULL, NULL),
(233, 'Novelas cortas', 'Anton Chejov', 'Porrúa', '968-452-659-8', 'Rusia', 'Español', '22cm,B/N,pag. 277', 'Romanticismo.-siglo XlX .-Novela rusa', NULL, NULL, NULL, NULL),
(234, 'Novelas ejemplares', 'Miguel de Cervantes Saavedra', 'Porrúa', '970-07-2021-7', 'España', 'Español', '22cm,B/N,Pag.335', 'Renacimiento.-siglo XVI.-Literatura española.', NULL, NULL, NULL, NULL),
(235, 'Nuevas aventuras', 'Emilio salgari', 'Porrua', '968-452-291-6', 'Italia', 'Español', '22cm,b/n, pág.197', 'Narrativa.-siglo XX.-Literatura de aventuras', NULL, NULL, NULL, NULL),
(236, 'Nuevas Aventuras de cabeza de piedra', 'Emilio Salgari', 'Porrúa', '968-452-291-6', 'Italia', 'Español', '22cm,B/N,pag. 197', 'Romanticismo.-siglo XIX .-', NULL, NULL, NULL, NULL),
(237, 'Obras completas', 'Sor Juana Ines De La Cruz', 'Porrúa', '970-07-2769-6', 'Mexico', 'Español', '22cm,B/N,pag.941', NULL, NULL, NULL, NULL, NULL),
(238, 'Ocho siglos de poesia', 'Francisco Montes de Oca', 'Porrúa', '968-452-953-8', 'México', 'Español', '22cm,B/N,pag. 385', NULL, NULL, NULL, NULL, NULL),
(239, 'Oliver Twist', 'Carlos Dickens', 'Porrúa', '968-432-731-5', 'Reino Unido', 'Español', '22cm,B/N,pag. 292', 'Realismo.-siglo XIX.-literatura inglesa', NULL, NULL, NULL, NULL),
(240, 'Panorama literario de los pueblos Nahuas', 'Angel Ma. Garibay K.', 'Porrúa', '968-432-355-7', 'México', 'Español', '22cm,B/N,pag. 202', 'siglo XII al XVI.- literatura de México', NULL, NULL, NULL, NULL),
(241, 'Papa Goriot', 'Horato De Balzac', 'Porrúa', '970-07--1966-9', 'Francia', 'Español', '22cm,B/N,Pag.168', 'Realismo.-siglo XIX.- literatura francesa', NULL, NULL, NULL, NULL),
(242, 'Pensamientos y otros escritos', 'Blas pascal', 'Porrúa', '968-452-353-X', 'Francia', 'Francés', '22cm,B/N,pag. 46', NULL, NULL, NULL, NULL, NULL),
(243, 'Peñas arriba', 'Jose M. De pereda', 'Porrúa', '968-432-840', 'España', 'Español', '22cm,B/N,pag. 394', NULL, NULL, NULL, NULL, NULL),
(244, 'Pepita Jiménez y Juanita L a Larga', 'Juan Valera', 'Porrúa', '968-432-104-x', 'España', 'Español', '22cm,B/N,pag. 220', 'Romanticismo.-siglo XlX .- Novela española', NULL, NULL, NULL, NULL),
(245, 'Pequeñeses', 'P.Luis Coloma', 'Porrúa', '970-07-1623-6', 'España', 'Español', '22cm,B/N,Pag.502', 'Realismo.-siglo XIX.-literatura española.', NULL, NULL, NULL, NULL),
(246, 'Platero y Yo', 'Juan Ramón Jiménez', 'Porrúa', '970-07-2126-4', 'España', 'Español', '22cm,B/N,pag. 220', 'Romanticismo.-siglo XX .-Literatura española', NULL, NULL, NULL, NULL),
(247, 'Pobreza y riqueza', 'Carlos Ignacio Gonzalez', 'Porrúa', '968-452-289-4', 'México', 'Español', '22cm,B/N,pag. 179', NULL, NULL, NULL, NULL, NULL),
(248, 'Poema de mio cid', 'Anónimo', 'Porrúa', '970-07-1608-2', 'España', 'Español', '22cm,B/N,pag. 279', 'Edad Media.-siglo XI.-literatura española', NULL, NULL, NULL, NULL),
(249, 'Poesia lirica', 'Lope de Vega', 'Porrúa', '968-452-883-3', 'Grecia', 'Español', '22cm,B/N,Pag.427', 'Edad Media.-Siglos VII.-literatura griega', NULL, NULL, NULL, NULL),
(250, 'Poesia mexicana', 'Francisco Montes de Oca', 'Porrúa', '968-432-658-0', 'México', 'Español', '22cm,B/N,pag. 419', NULL, NULL, NULL, NULL, NULL),
(251, 'Poesías', 'Luis de Góngora', 'Porrúa', '970-07-1522-1', 'España', 'Español', '22cm,B/N,pag. 339', NULL, NULL, NULL, NULL, NULL),
(252, 'Poesias completas', 'Gaspar Nuñez de Arce', 'Porrúa', '968-432-749-8', 'España', 'Español', '22cm,B/N,pag. 250', 'Poesía española .-siglo XIX .-lieteratura española', NULL, NULL, NULL, NULL),
(253, 'Poesias completas', 'Julio Herrera y Reissig', 'Porrúa', '968-452-308-4', 'Uruguay', 'Español', '22cm,B/N,pag. 160', 'Poesía.-siglo XIX .-', NULL, NULL, NULL, NULL),
(254, 'Popol Wuj', 'Anonimo', 'Porrúa', '970-07-1952-9', 'Guatemala', 'Español', '22cm,B/N,pag. 166', NULL, NULL, NULL, NULL, NULL),
(255, 'Proverbios de Salomón', 'Angel Ma. Garibay K.', 'Porrúa', '970-07-2032-2', 'Antiguo israel', 'Español', '22cm,B/N,Pag.232', 'Antigüedad.-siglo X.- literatura hebrea', NULL, NULL, NULL, NULL),
(256, 'Rebelión en la granja', 'George orwell', 'Porrua', '970-07-1694-5', 'Reino Unido', 'Español', '22cm,b/n, pág.349', 'Contemporánea.-siglo.-XVI.-Literatura británica', NULL, NULL, NULL, NULL),
(257, 'Recuerdo de Vasco de Quiroga', 'Silvio Zavala', 'Porrúa', '968-452-244-4', 'Mexico', 'Español', '22cm,B/N,pag. 331', NULL, NULL, NULL, NULL, NULL),
(258, 'Recuerdos del tiempo viejo', 'Jose Zorrilla', 'Porrúa', '970-07-1056-4', 'España', 'Español', '22cm,B/N,pag. 502', NULL, NULL, NULL, NULL, NULL),
(259, 'Refranes populares de mexico', 'Guadalupe Appendini', 'Porrúa', '970-07-1032-7', 'Mexico', 'Español', '22cm,B/N,Pag.185', 'Costumbrismo.-siglos XIX.-Literatura mexicana', NULL, NULL, NULL, NULL),
(260, 'Retrato del arista adolescente', 'James Joyce', 'Porrúa', '968-432-816-8', 'Irlanda', 'Español', '22cm,B/N,pag. 283', 'Modernismo.-siglo XX.-literatura irlandesa', NULL, NULL, NULL, NULL),
(261, 'Rima, leyendas y narraciones', 'Becquer', 'Porrúa', '968-432-281-X', 'España', 'Español', '22cm,B/N,Pag.217', 'Romanticismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(262, 'Robin Hood', 'Anonimo', 'Porrúa', '970-07-2165-5', 'Inglaterra', 'Español', '22cm,B/N,pag.133', 'Edad Media.-siglos XIV y XV.-literatura inglesa', NULL, NULL, NULL, NULL),
(263, 'Rojo y negro', 'Stendhal', 'Porrúa', '968-432-708-0', 'Francia', 'Español', '22cm,B/N,pag. 311', 'Romanticismo.-Siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL),
(264, 'Sandokan', 'Emilio Salgari', 'Porrúa', '968-432-466-9', 'Italia', 'Español', '22cm,B/N,pag. 174', 'Romanticismo.-siglo XlX .-Literatura italiana', NULL, NULL, NULL, NULL),
(265, 'Sin novedad en el frente', 'Erich Maria Remarque', 'Porrúa', '970-07-0937-x', 'Alemania', 'Español', '22cm,B/N,Pag.174', 'Realismo.-Siglo XX.-Literatura alemana', NULL, NULL, NULL, NULL),
(266, 'Sinuhé, el egipcio', 'Mika Waltari', 'Porrúa', '970-07-1298-2', 'España', 'Español', '22cm,B/N,Pag.195', 'Histórica.-siglo XX.-literatura finlandesa', NULL, NULL, NULL, NULL),
(267, 'Su único hijo', 'Leopoldo Alas <<Clarín>>', 'Porrua', '968-452-322-x', 'España', 'Español', '22cm,b/n, pág.269', 'Realismo.-siglo XIX.-Literatura francesa.', NULL, NULL, NULL, NULL),
(268, 'Sueños', 'Francisco de Quevedo y Villegas', 'Porrúa', '968-452-062-X', 'España', 'Español', '22cm,B/N,pag. 243', 'Barroco.-Siglo XVII.-Literatura española', NULL, NULL, NULL, NULL),
(269, 'Sus mejores páginas', 'José Martí', 'Porrua', '968-432-197-x', 'Cuba', 'Español', '22cm, b/n, pág.251', 'Modernismo.- siglo XIX.- Literatura española.', NULL, NULL, NULL, NULL),
(270, 'Tabaré', 'Juan Zorrilla de San Martín', 'Porrua', '968-432-218-6', 'Uruguay', 'Español', '22cm,b/n, pág.213', 'Romanticismo.- siglo XIX.-Literatura uruguaya', NULL, NULL, NULL, NULL),
(271, 'Taras bulba', 'Nikolai V. Gogol', 'Porrúa', '970-07-2113-2', 'Ucrania', 'Español', '22cm,B/N,Pag.163', 'Nacionalismo.-siglo XIX.- literatura rusa', NULL, NULL, NULL, NULL);
INSERT INTO `libros` (`id_libro`, `Titulo`, `Autor`, `Editorial`, `ISBN`, `País`, `Idioma`, `Descripción`, `Encabezamiento de Materia`, `Ejemplares_totales`, `Ejemplares_disponibles`, `Ejemplares_prestados`, `Clasificación`) VALUES
(272, 'Teatro', 'Anton Chejov', 'Porrúa', '968-432-988-1', 'Rusia', 'Español', '22cm,B/N,Pag.145', 'Realismo.- siglos XIX.- literatura rusa', NULL, NULL, NULL, NULL),
(273, 'Teogonia', 'Hesiodo', 'Porrúa', '968-432-606-8', 'Grecia', 'Español', '22cm,B/N,Pag.87', 'Antigüedad.-siglo VIII.-literatura griega', NULL, NULL, NULL, NULL),
(274, 'Tirano Banderas', 'Ramon del Valle-Inclan', 'Porrúa', '970-07-2142-6', 'España', 'Español', '22cm,B/N,Pag.129', 'Vanguardismo.-siglo XX.-literatura española', NULL, NULL, NULL, NULL),
(275, 'Tomochic', 'Heriberto Frias', 'Porrúa', '970-07-1591-4', 'Mexico', 'Español', '22cm,B/N,Pag.153', 'Realismo.-siglo XIX.-literatura mexicana', NULL, NULL, NULL, NULL),
(276, 'Trafalgar', 'Benito Perez Galdos', 'Porrúa', '968-432-257-7', 'España', 'Español', '22cm,B/N,pag. 199', 'Realismo.-siglo XIX.-Literatura española', NULL, NULL, NULL, NULL),
(277, 'Tragedias', 'Corneille', 'Porrua', '970-07-1643-0', 'Francia', 'Español', '22cm,b/n, pág.169', 'Barroco.- siglo XVII.- Literatura francesa', NULL, NULL, NULL, NULL),
(278, 'Tristana', 'Benito Perez Galdos', 'Porrúa', '970-07-2023-3', 'España', 'Español', '22cm,B/N,Pag.215', 'Realismo.- siglo XIX.-Literatura española', NULL, NULL, NULL, NULL),
(279, 'Tu eres la paz', 'Gregorio Martinez sierra', 'Porrua', '968-452-164-2', 'España', 'Español', '22cm,b/n pág.189', 'Modernismo.-sigloXX.-literatura española', NULL, NULL, NULL, NULL),
(280, 'Viajes de colón', 'Martín Fernandes de Navarrete', 'Porrua', '968-452-189-8', 'España', 'Español', '22cm,b/n, pág.351', 'Historiografía.- siglo XIX.- Literatura medieval XIX', NULL, NULL, NULL, NULL),
(281, 'Viajes de gulliver', 'Jonathan Swift', 'Porrua', '970-07-1609-0', 'Irlanda', 'Español', '22cm,b/n pág.219', 'Neoclasicismo.-siglo XVIII.-literatura irlandesa', NULL, NULL, NULL, NULL),
(282, 'Vida de Marcos de Obregon', 'Vicente Espinel', 'Porrúa', '978-970-07-0232-2', 'España', 'Español', '22cm,B/N,Pag.245', 'Realismo.-siglo XIX.-literatura española', NULL, NULL, NULL, NULL),
(283, 'Vida del buscón', 'Lazarillo de Tormes', 'Porrua', '970-07-0033-x', 'España', 'Español', '22cm,b/n, pág.190', 'Barroco.- siglo XVI.- Literatura española', NULL, NULL, NULL, NULL),
(284, 'Voces de Oriente', 'S/A', 'Porrua', '968-432-802-8', 'España', 'Español', '22cm,b/n, pág.195', 'Humanismo.- siglo XX.-Literatura mexicana', NULL, NULL, NULL, NULL),
(285, 'Yolanda', 'Emilio Salgari', 'Porrúa', '970-07-2264-3', 'Italia', 'Español', '22cm,B/N,pag. 171', 'Romanticismo.-siglo XVII.-Novela de aventuras, Piratas', NULL, NULL, NULL, NULL),
(286, 'La fontana de oro', 'Benito Perez Galdos', 'Porrúa', '968-432-810-9', 'España', 'Español', '22cm,B/N,pag.248', NULL, NULL, NULL, NULL, NULL),
(287, 'Aventuras de Robinson cruose', 'Daniel Defoe', 'Porrúa', '970-07-2637-1', 'Inglaterra', 'Español', '22cm,B/N,pag.158', NULL, NULL, NULL, NULL, NULL),
(288, 'Cuatro comedias', 'Juan Ruiz de Alarcon', 'Porrúa', '970-07-1550-7', 'Mexico', 'Español', '22cm,B/N,pag.206', NULL, NULL, NULL, NULL, NULL),
(289, '850.914 S35 1773', 'Emilio Salgari', 'Porrúa', '968-432-820-6', 'Italia', 'Español', '22cm,B/N,pag.246', NULL, NULL, NULL, NULL, NULL),
(290, 'EL lirio en el valle', 'Honorato de Balzac', 'Porrúa', '970-07-2027-6', 'Francia', 'Español', '22cm,B/N,pag.173', NULL, NULL, NULL, NULL, NULL),
(291, 'La vuelta de los muertos', 'Vicente Riva Palacio', 'Porrúa', '968-452-154-5', 'Mexico', 'Español', '22cm,B/N,pag.293', NULL, NULL, NULL, NULL, NULL),
(292, 'Leyendas Historicas Mexicanas y otros relatos', 'Heriberto Frias', 'Porrúa', '968-452-971-6', 'Mexico', 'Español', '22cm,B/N,pag.420', NULL, NULL, NULL, NULL, NULL),
(293, 'Don Juan Tenorio y El puñal del godo', 'Jose Zorrilla', 'Porrúa', '970-07-2665-8', 'España', 'Español', '22cm,B/N,pag.144', 'Romanticismo.-siglo XlX .-Literatura española', NULL, NULL, NULL, NULL),
(294, 'Keraban el testarudo', 'Julio Verne', 'Porrúa', '968-452-272-x', 'Francia', 'Español', '22cm,B/N,pag.251', NULL, NULL, NULL, NULL, NULL),
(295, 'Alicia en el pais de las maravillas (Ej. 2)', 'Lewis Carroll', 'Porrúa', '970-07-6752-4', 'Reino Unido', 'Español', '22cm,B/N,pag.235', NULL, NULL, NULL, NULL, NULL),
(296, 'Almacen de antigüedades', 'Carlos Dickens', 'Porrúa', '968-452-754-3', 'Reino Unido', 'Español', '22cm,B/N,pag.357', NULL, NULL, NULL, NULL, NULL),
(297, 'Graziella', 'Alfonso de Lamartine', 'Porrúa', '968-432-610-6', 'Francia', 'Español', '22cm,B/N,pag.184', NULL, NULL, NULL, NULL, NULL),
(298, 'Comedias', 'Moliere', 'Porrúa', '968-432-206-2', 'Francia', 'Español', '22cm,B/N,pag.161', NULL, NULL, NULL, NULL, NULL),
(299, 'La campana de huesca', 'Antonio Cánovas del Castillo', 'Porrúa', '968-432-950-4', 'España', 'Español', '22cm,B/N,pag.198', NULL, NULL, NULL, NULL, NULL),
(300, 'Madame Bovary', 'Gustavo Flaubert', 'Porrúa', '970-07-2852-8', 'España', 'Español', '22cm,B/N,pag.195', NULL, NULL, NULL, NULL, NULL),
(301, 'EL ultimo verano de Klingsor', 'Hermann Hesse', 'Porrúa', '970-07-1301-6', 'Alemania', 'Español', '22cm,B/N,pag.257', NULL, NULL, NULL, NULL, NULL),
(302, '20 poemas de amor y una canción desesperada', 'Pablo Neruda', 'Porrúa', '970-07-1752-6', 'Chile', 'Español', '22cm,B/N,pag.160', 'Posmodernismo.-siglo XX.-literatura hispanoamericana', NULL, NULL, NULL, NULL),
(303, 'Jerusalen liberada', 'Torcuato Tasso', 'Porrúa', '970-07-2526-X', 'Italia', 'Español', '22cm,B/N,pag.320', 'Renacimiento.-siglo XVI.-literatura italiana', NULL, NULL, NULL, NULL),
(304, 'Veinte mil leguas de viaje submarino', 'Julio Verne', 'Porrúa', '970-07-2416-0', 'Francia', 'Español', '22cm,B/N,pag.351', 'Realismo.-siglo XIX.-literatura francesa', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `prestamos`
--

CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL,
  `id_alumnos` int(11) DEFAULT NULL,
  `id_profesores` int(11) DEFAULT NULL,
  `id_libro` int(11) NOT NULL,
  `Cantidad` int(11) DEFAULT 1,
  `fecha_salida` date NOT NULL,
  `fecha_devolucion_esperada` date NOT NULL,
  `fecha_devolucion_real` date DEFAULT NULL,
  `estado_libro` varchar(100) DEFAULT NULL,
  `Renovacion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `prestamos`
--

INSERT INTO `prestamos` (`id_prestamo`, `id_alumnos`, `id_profesores`, `id_libro`, `Cantidad`, `fecha_salida`, `fecha_devolucion_esperada`, `fecha_devolucion_real`, `estado_libro`, `Renovacion`) VALUES
(1, NULL, 8, 3, 1, '2026-10-20', '2026-10-20', NULL, NULL, NULL),
(2, NULL, 12, 12, 1, '2026-10-22', '2026-10-31', NULL, NULL, NULL),
(3, NULL, 13, 5, 1, '2026-10-22', '2026-10-31', NULL, NULL, NULL),
(4, NULL, 1, 289, 1, '2026-10-22', '2026-10-31', NULL, NULL, NULL),
(5, NULL, 14, 1, 1, '2026-10-22', '2026-10-31', NULL, NULL, NULL),
(6, NULL, 15, 6, 1, '2026-10-22', '2026-10-31', NULL, NULL, NULL),
(7, NULL, 15, 13, 1, '2026-10-22', '2026-10-31', NULL, NULL, NULL),
(8, NULL, 16, 5, 5, '2026-10-23', '2026-10-31', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesores`
--

CREATE TABLE `profesores` (
  `id_profesores` int(11) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Materia` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesores`
--

INSERT INTO `profesores` (`id_profesores`, `Nombre`, `Materia`) VALUES
(22, 'Camila', 'Manufactura del Vestido'),
(24, 'LuzMaria', 'Lengua y Comunicacion'),
(25, 'Ada', 'Educacion Fisica Y Artes'),
(26, 'Alilne', 'Iformatica, Contabilidad y Electronica'),
(28, 'Ana', 'Iformatica, Contabilidad y Electronica'),
(29, 'Brisa', 'Manufactura del Vestido'),
(30, 'Moserrath', 'Educacion Fisica Y Artes'),
(31, 'Alexa', 'Gastronomia y Enfermeria'),
(32, 'Zoe', 'Iformatica, Contabilidad y Electronica'),
(33, 'Ambar', 'Lengua y Comunicacion'),
(34, 'Soledad', 'Manufactura del Vestido'),
(35, 'Laura', 'Informatica, Contabilidad y Electronica');

--
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
  ADD KEY `id_libros` (`id_libro`);

--
-- Indices de la tabla `profesores`
--
ALTER TABLE `profesores`
  ADD PRIMARY KEY (`id_profesores`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `libros`
--
ALTER TABLE `libros`
  MODIFY `id_libro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=305;

--
-- AUTO_INCREMENT de la tabla `prestamos`
--
ALTER TABLE `prestamos`
  MODIFY `id_prestamo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `profesores`
--
ALTER TABLE `profesores`
  MODIFY `id_profesores` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;
--
-- Base de datos: `phpmyadmin`
--
CREATE DATABASE IF NOT EXISTS `phpmyadmin` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `phpmyadmin`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__bookmark`
--

CREATE TABLE `pma__bookmark` (
  `id` int(10) UNSIGNED NOT NULL,
  `dbase` varchar(255) NOT NULL DEFAULT '',
  `user` varchar(255) NOT NULL DEFAULT '',
  `label` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `query` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Bookmarks';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__central_columns`
--

CREATE TABLE `pma__central_columns` (
  `db_name` varchar(64) NOT NULL,
  `col_name` varchar(64) NOT NULL,
  `col_type` varchar(64) NOT NULL,
  `col_length` text DEFAULT NULL,
  `col_collation` varchar(64) NOT NULL,
  `col_isNull` tinyint(1) NOT NULL,
  `col_extra` varchar(255) DEFAULT '',
  `col_default` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Central list of columns';

--
-- Volcado de datos para la tabla `pma__central_columns`
--

INSERT INTO `pma__central_columns` (`db_name`, `col_name`, `col_type`, `col_length`, `col_collation`, `col_isNull`, `col_extra`, `col_default`) VALUES
('biblioteca-bd', 'Edición', 'varchar', '50', 'utf8mb4_general_ci', 1, ',', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__column_info`
--

CREATE TABLE `pma__column_info` (
  `id` int(5) UNSIGNED NOT NULL,
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `column_name` varchar(64) NOT NULL DEFAULT '',
  `comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `mimetype` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `transformation` varchar(255) NOT NULL DEFAULT '',
  `transformation_options` varchar(255) NOT NULL DEFAULT '',
  `input_transformation` varchar(255) NOT NULL DEFAULT '',
  `input_transformation_options` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Column information for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__designer_settings`
--

CREATE TABLE `pma__designer_settings` (
  `username` varchar(64) NOT NULL,
  `settings_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Settings related to Designer';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__export_templates`
--

CREATE TABLE `pma__export_templates` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL,
  `export_type` varchar(10) NOT NULL,
  `template_name` varchar(64) NOT NULL,
  `template_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved export templates';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__favorite`
--

CREATE TABLE `pma__favorite` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Favorite tables';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__history`
--

CREATE TABLE `pma__history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db` varchar(64) NOT NULL DEFAULT '',
  `table` varchar(64) NOT NULL DEFAULT '',
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp(),
  `sqlquery` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='SQL history for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__navigationhiding`
--

CREATE TABLE `pma__navigationhiding` (
  `username` varchar(64) NOT NULL,
  `item_name` varchar(64) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Hidden items of navigation tree';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__pdf_pages`
--

CREATE TABLE `pma__pdf_pages` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `page_nr` int(10) UNSIGNED NOT NULL,
  `page_descr` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='PDF relation pages for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__recent`
--

CREATE TABLE `pma__recent` (
  `username` varchar(64) NOT NULL,
  `tables` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Recently accessed tables';

--
-- Volcado de datos para la tabla `pma__recent`
--

INSERT INTO `pma__recent` (`username`, `tables`) VALUES
('root', '[{\"db\":\"biblioteca-bd\",\"table\":\"libros\"},{\"db\":\"biblioteca-bd\",\"table\":\"profesores\"},{\"db\":\"biblioteca-bd\",\"table\":\"prestamos\"},{\"db\":\"biblioteca-bd\",\"table\":\"inventario\"},{\"db\":\"biblioteca-bd\",\"table\":\"alumnos\"}]');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__relation`
--

CREATE TABLE `pma__relation` (
  `master_db` varchar(64) NOT NULL DEFAULT '',
  `master_table` varchar(64) NOT NULL DEFAULT '',
  `master_field` varchar(64) NOT NULL DEFAULT '',
  `foreign_db` varchar(64) NOT NULL DEFAULT '',
  `foreign_table` varchar(64) NOT NULL DEFAULT '',
  `foreign_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Relation table';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__savedsearches`
--

CREATE TABLE `pma__savedsearches` (
  `id` int(5) UNSIGNED NOT NULL,
  `username` varchar(64) NOT NULL DEFAULT '',
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `search_name` varchar(64) NOT NULL DEFAULT '',
  `search_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Saved searches';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_coords`
--

CREATE TABLE `pma__table_coords` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `pdf_page_number` int(11) NOT NULL DEFAULT 0,
  `x` float UNSIGNED NOT NULL DEFAULT 0,
  `y` float UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table coordinates for phpMyAdmin PDF output';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_info`
--

CREATE TABLE `pma__table_info` (
  `db_name` varchar(64) NOT NULL DEFAULT '',
  `table_name` varchar(64) NOT NULL DEFAULT '',
  `display_field` varchar(64) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Table information for phpMyAdmin';

--
-- Volcado de datos para la tabla `pma__table_info`
--

INSERT INTO `pma__table_info` (`db_name`, `table_name`, `display_field`) VALUES
('biblioteca-bd', 'libros', 'Titulo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__table_uiprefs`
--

CREATE TABLE `pma__table_uiprefs` (
  `username` varchar(64) NOT NULL,
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `prefs` text NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Tables'' UI preferences';

--
-- Volcado de datos para la tabla `pma__table_uiprefs`
--

INSERT INTO `pma__table_uiprefs` (`username`, `db_name`, `table_name`, `prefs`, `last_update`) VALUES
('root', 'biblioteca-bd', 'libros', '{\"CREATE_TIME\":\"2026-10-19 14:34:52\"}', '2026-10-22 15:18:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__tracking`
--

CREATE TABLE `pma__tracking` (
  `db_name` varchar(64) NOT NULL,
  `table_name` varchar(64) NOT NULL,
  `version` int(10) UNSIGNED NOT NULL,
  `date_created` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `schema_snapshot` text NOT NULL,
  `schema_sql` text DEFAULT NULL,
  `data_sql` longtext DEFAULT NULL,
  `tracking` set('UPDATE','REPLACE','INSERT','DELETE','TRUNCATE','CREATE DATABASE','ALTER DATABASE','DROP DATABASE','CREATE TABLE','ALTER TABLE','RENAME TABLE','DROP TABLE','CREATE INDEX','DROP INDEX','CREATE VIEW','ALTER VIEW','DROP VIEW') DEFAULT NULL,
  `tracking_active` int(1) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Database changes tracking for phpMyAdmin';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__userconfig`
--

CREATE TABLE `pma__userconfig` (
  `username` varchar(64) NOT NULL,
  `timevalue` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `config_data` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User preferences storage for phpMyAdmin';

--
-- Volcado de datos para la tabla `pma__userconfig`
--

INSERT INTO `pma__userconfig` (`username`, `timevalue`, `config_data`) VALUES
('root', '2026-10-29 15:23:50', '{\"Console\\/Mode\":\"collapse\",\"lang\":\"es\",\"NavigationWidth\":0}');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__usergroups`
--

CREATE TABLE `pma__usergroups` (
  `usergroup` varchar(64) NOT NULL,
  `tab` varchar(64) NOT NULL,
  `allowed` enum('Y','N') NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='User groups with configured menu items';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pma__users`
--

CREATE TABLE `pma__users` (
  `username` varchar(64) NOT NULL,
  `usergroup` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='Users and their assignments to user groups';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pma__central_columns`
--
ALTER TABLE `pma__central_columns`
  ADD PRIMARY KEY (`db_name`,`col_name`);

--
-- Indices de la tabla `pma__column_info`
--
ALTER TABLE `pma__column_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `db_name` (`db_name`,`table_name`,`column_name`);

--
-- Indices de la tabla `pma__designer_settings`
--
ALTER TABLE `pma__designer_settings`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_user_type_template` (`username`,`export_type`,`template_name`);

--
-- Indices de la tabla `pma__favorite`
--
ALTER TABLE `pma__favorite`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__history`
--
ALTER TABLE `pma__history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`,`db`,`table`,`timevalue`);

--
-- Indices de la tabla `pma__navigationhiding`
--
ALTER TABLE `pma__navigationhiding`
  ADD PRIMARY KEY (`username`,`item_name`,`item_type`,`db_name`,`table_name`);

--
-- Indices de la tabla `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  ADD PRIMARY KEY (`page_nr`),
  ADD KEY `db_name` (`db_name`);

--
-- Indices de la tabla `pma__recent`
--
ALTER TABLE `pma__recent`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__relation`
--
ALTER TABLE `pma__relation`
  ADD PRIMARY KEY (`master_db`,`master_table`,`master_field`),
  ADD KEY `foreign_field` (`foreign_db`,`foreign_table`);

--
-- Indices de la tabla `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `u_savedsearches_username_dbname` (`username`,`db_name`,`search_name`);

--
-- Indices de la tabla `pma__table_coords`
--
ALTER TABLE `pma__table_coords`
  ADD PRIMARY KEY (`db_name`,`table_name`,`pdf_page_number`);

--
-- Indices de la tabla `pma__table_info`
--
ALTER TABLE `pma__table_info`
  ADD PRIMARY KEY (`db_name`,`table_name`);

--
-- Indices de la tabla `pma__table_uiprefs`
--
ALTER TABLE `pma__table_uiprefs`
  ADD PRIMARY KEY (`username`,`db_name`,`table_name`);

--
-- Indices de la tabla `pma__tracking`
--
ALTER TABLE `pma__tracking`
  ADD PRIMARY KEY (`db_name`,`table_name`,`version`);

--
-- Indices de la tabla `pma__userconfig`
--
ALTER TABLE `pma__userconfig`
  ADD PRIMARY KEY (`username`);

--
-- Indices de la tabla `pma__usergroups`
--
ALTER TABLE `pma__usergroups`
  ADD PRIMARY KEY (`usergroup`,`tab`,`allowed`);

--
-- Indices de la tabla `pma__users`
--
ALTER TABLE `pma__users`
  ADD PRIMARY KEY (`username`,`usergroup`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `pma__bookmark`
--
ALTER TABLE `pma__bookmark`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__column_info`
--
ALTER TABLE `pma__column_info`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__export_templates`
--
ALTER TABLE `pma__export_templates`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__history`
--
ALTER TABLE `pma__history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__pdf_pages`
--
ALTER TABLE `pma__pdf_pages`
  MODIFY `page_nr` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pma__savedsearches`
--
ALTER TABLE `pma__savedsearches`
  MODIFY `id` int(5) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- Base de datos: `test`
--
CREATE DATABASE IF NOT EXISTS `test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `test`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

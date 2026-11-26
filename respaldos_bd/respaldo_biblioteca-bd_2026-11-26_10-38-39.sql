-- Respaldo Automatico de Base de Datos: biblioteca-bd
-- Fecha de generacion: 26/11/2026 10:38:39
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `alumnos`;
CREATE TABLE `alumnos` (
  `id_Alumnos` int(11) NOT NULL AUTO_INCREMENT,
  `Matricula` varchar(30) DEFAULT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido_Paterno` varchar(100) NOT NULL DEFAULT '',
  `Apellido_Materno` varchar(100) NOT NULL DEFAULT '',
  `Madre_encargada` varchar(100) DEFAULT NULL,
  `Familia` varchar(100) DEFAULT NULL,
  `Piso` varchar(20) DEFAULT NULL,
  `Grupo` varchar(20) DEFAULT NULL,
  `Renovación` date DEFAULT NULL,
  PRIMARY KEY (`id_Alumnos`),
  UNIQUE KEY `Matricula` (`Matricula`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `alumnos` VALUES("1", "SUL-26-001", "Itzel", "Matínez", "López", "Hna. Rosario", "Sta. Adela", "3", "502", NULL);
INSERT INTO `alumnos` VALUES("2", "SUL-26-002", "Abril de la Cruz", "", "", "Hna. Griseida", "Sta. Bernardita", "2", "501", NULL);
INSERT INTO `alumnos` VALUES("3", "SUL-26-003", "Heidy Vanessa", "", "", "Hna. Griseida", "Sta. Bernardita", "2", "501", NULL);
INSERT INTO `alumnos` VALUES("4", "SUL-26-004", "Sara", "", "", "Hna. Aneli", "Sta. Mónica", "5", "1°Q", NULL);
INSERT INTO `alumnos` VALUES("5", "SUL-26-005", "Alexa Merino", "", "", "Hna. Lucia", "Sta. Mariana", "6", "202", NULL);
INSERT INTO `alumnos` VALUES("7", "SUL-26-007", "Maria del Pilar", "", "", "Hna. Rosa", "Sta. Emili", "6", "1°A", NULL);
INSERT INTO `alumnos` VALUES("8", "SUL-26-008", "Aslinn", "Alejo", "Santos", "Hna.Alma", "Sta. Natalia", "4", "501", NULL);
INSERT INTO `alumnos` VALUES("9", "SUL-26-009", "Rubi", "Cocotle", "Rodriguez", "Hna. Griseida", "Sta. Bernardita", "2", "501", NULL);
INSERT INTO `alumnos` VALUES("10", "SUL-26-010", "Abigailda", "Juárez", "", "Hna. Roberta", "Sta. Irene", "4", "3°N", NULL);

DROP TABLE IF EXISTS `inventario`;
CREATE TABLE `inventario` (
  `id_inventario` int(11) NOT NULL,
  `id_libro` int(11) NOT NULL,
  `Estado_conservación` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_inventario`),
  KEY `id_libro` (`id_libro`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `libros`;
CREATE TABLE `libros` (
  `id_libro` int(11) NOT NULL AUTO_INCREMENT,
  `Titulo` varchar(255) NOT NULL,
  `Autor` varchar(150) DEFAULT NULL,
  `Categoria` varchar(100) DEFAULT 'Novela y Narrativa',
  `Editorial` varchar(100) DEFAULT NULL,
  `ISBN` varchar(100) DEFAULT NULL,
  `ano` int(11) NOT NULL,
  `País` varchar(100) DEFAULT NULL,
  `Idioma` varchar(50) DEFAULT NULL,
  `Descripción` varchar(100) DEFAULT NULL,
  `Encabezamiento de Materia` varchar(100) DEFAULT NULL,
  `Ejemplares_totales` int(11) DEFAULT 0,
  `Ejemplares_disponibles` int(11) DEFAULT 0,
  `Ejemplares_prestados` int(11) DEFAULT 0,
  `Clasificación` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_libro`)
) ENGINE=InnoDB AUTO_INCREMENT=307 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `libros` VALUES("1", "A ninguna de las tres", "Fernando calderon", "Novela y Narrativa", "\'Porrúa", "970-07-0221-9", "1972", "México", "Español", "22cm,b/n pág.159", "Romanticismo.-siglo XIX.-literatura mexicana", "5", "4", "2", "862.5/ C35");
INSERT INTO `libros` VALUES("2", "Al faro", "Virginia Woolf", "Novela y Narrativa", "Porrúa", "970-07-1400-0", "1927", "Reino Unido", "Español", "22cm,b/n pág.228", "Modernismo.-siglo XX.-literatura modernista", "5", "4", "1", "823.91/ W66");
INSERT INTO `libros` VALUES("3", "Al polo norte", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-432-469-4", "1898", "Italia", "Español", "22cm,b/n pág.151", "Romanticismo.-siglo XIX.-literatura de aventuras", "5", "4", "1", NULL);
INSERT INTO `libros` VALUES("4", "Alemania", "Enrique Heine", "Novela y Narrativa", "Porrúa", "968-452-469-2", "1991", "Alemania", "Español", "22cm,b/n pág.181", "Romanticismo.-siglo XIX.-literatura alemana", "5", "4", "1", NULL);
INSERT INTO `libros` VALUES("5", "Alicia en el pais de las maravillas", "Lewis Carroll", "Novela y Narrativa", "Porrúa", "970-07-6752-3", "1865", "Reino Unido", "Español", "22cm,b/n pág.230", "Romanticismo.-siglo XIX.-literatura inglesa", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("6", "Almacén de antigüedades", "Carlos Dickens", "Novela y Narrativa", "Porrúa", "968-452-754-3", "1841", "Reino Unido", "Español", "22cm,b/n pág.351", "Realismo.-Siglo XIX.-literatura inglesa", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("7", "Amadis de Gaula", "Arturo Souto", "Novela y Narrativa", "Porrúa", "970-07-1563-9", "1508", "España", "Español", "22cm,b/n pág.307", "Edad Media.-siglos XIV y XV.-literatura española", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("8", "Amalia", "José Mármol", "Novela y Narrativa", "Porrúa", "970-07-1834-4", "1855", "Buenos Aires", "Español", "22cm,b/n pág.584", "Romanticismo.-siglo XIX.-literatura argentina", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("9", "Amaya o los vascos en el siglo VIII", "Francisco Navarro Villoslada", "Novela y Narrativa", "Porrúa", "968-452-223-1", "1879", "España", "Español", "22cm,b/n pág.588", "Realismo.-siglo XIX.-literatura española", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("10", "Análisis de la expresión literaria", "Arqueles Vela", "Novela y Narrativa", "Porrúa", "970-07-1597-3", "1965", "México", "Español", "22cm,b/n pág.225", "Contemporánea.-siglo XX.-literatura mexicana", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("11", "Antología", "Alfonso el Sabio", "Novela y Narrativa", "Porrúa", "970-07-2114-0", "1889", "España", "Español", "22cm,b/n pág.238", "Edad Media.-siglo XIII.-literatura española", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("12", "Arcipreste de talavera", "Alfonso Martínez De Toledo", "Novela y Narrativa", "Porrúa", "970-07-2143-4", "1498", "España", "Español", "22cm,b/n pág.397", "Edad Media.-siglos XIV y XV.-literatura española", "10", "10", "0", "");
INSERT INTO `libros` VALUES("13", "Ariel", "José Enrique Rodó", "Novela y Narrativa", "Porrúa", "968-432-225-9", "1900", "Uruguay", "Español", "22cm,b/n pág.245", "Modernismo.-siglo XX.-literatura modernista", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("14", "Así se templo el acero", "Nikolai Ostrovski", "Novela y Narrativa", "Porrúa", "968-432-937-7", "1936", "Rusia", "Español", "22cm,b/n pág.261", "Realismo.-siglo XX.-literatura rusa", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("15", "Atala-Rene", "Rene Chateaubriand", "Novela y Narrativa", "Porrúa", "968-452-204-5", "1802", "Francia", "Español", "22cm,b/n pág.198", "Romanticismo.-siglo XIX.-literatura francesa y grecorromana", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("16", "Autobiografía", "Benvenuto Cellini", "Novela y Narrativa", "Porrúa", "968-452-755-1", "1728", "Francia", "Español", "22cm,b/n pág.357", "Renacimiento.-siglos XVI y XVII.-literatura francesa y grecorromana", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("17", "Aventuras de Robinson Crusoe", "Daniel Defoe", "Novela y Narrativa", "Porrúa", "970-07-2637-1", "1719", "Inglaterra", "Español", "22cm,b/n pág.158", "Neoclasicismo.-siglo XVIII.-literatura inglesa", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("18", "Aventuras de telemaco", "Fenelon", "Novela y Narrativa", "Porrúa", "968-432-808-7", "1983", "Francia", "Español", "22cm,b/n pág.231", "Romance.-siglo XVII.-literatura francesa", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("19", "Aventuras del capitan hatteras", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-265-7", "1886", "Francia", "Español", "22cm,b/n pág.351", "Realismo.-siglo XIX.-literatura francesa", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("20", "Azorín", "M. Del Pozo", "Novela y Narrativa", "Porrúa", "970-07-1566-3", "1965", "España", "Español", "22cm,b/n pág.275", "Modernismo.-siglo XX.-literatura contemporánea", "5", "5", "0", NULL);
INSERT INTO `libros` VALUES("21", "Bajo la rueda", "Hermann Hesse", "Novela y Narrativa", "Porrúa", "970-07-1301-6", "1998", "Alemania", "Español", "22cm,b/n pág.257", "Romanticismo.-siglo XIX.-Novela alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("22", "Beatriz", "Gaston Boissier", "Novela y Narrativa", "Porrúa", "968-452-186-3", "1994", "Francia", "Español", "22cm,b/n pág.250", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("23", "Ben-hur", "Lewis Wallace", "Novela y Narrativa", "Porrúa", "968-432-306-0", "1880", "EE.UU.", "Español", "22cm,b/n pág.335", "Romanticismo.-siglo XIX.-literatura estadounidense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("24", "Caballería roja", "Isaac Babel", "Novela y Narrativa", "Porrúa", "968-452-652-3", "1926", "Alemania", "Español", "22cm,b/n pág.235", "Realismo.-siglo XX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("25", "Canción de navidad", "Carlos Dickens", "Novela y Narrativa", "Porrúa", "970-07-1584-1", "1976", "Reino Unido", "Español", "22cm,b/n pág.379", "Realismo.-siglo XIX.-literatura inglesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("26", "Cancionero", "Petrarca", "Novela y Narrativa", "Porrúa", "968-452-117-0", "1986", "Italia", "Español", "22cm,b/n pág.325", "Renacimiento.-siglo XIV.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("27", "Cándido", "Voltaire", "Filosofía y Ciencia", "Porrúa", "970-07-1487-6", "1759", "Francia", "Español", "22cm,b/n pág.193", "Ilustración.-siglo XVIII.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("28", "Cándido o el optimismo", "Voltaire", "Filosofía y Ciencia", "Porrúa", "970-07-1487-6", "1983", "Francia", "Español", "22cm,b/n pág.193", "Ilustración.-siglo XVIII.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("29", "Cantares de Gesta Medievales", "Manuel Alvar", "Novela y Narrativa", "Porrúa", "968-432-720-X", "1991", "España", "Español", "22cm,b/n pág.200", "Medieval.-siglo XII.-literatura europea", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("30", "Cantos de vida y esperanza", "Rubén Darío", "Poesía", "Porrúa", "970-07-1783-6", "1826", "Nicaragua", "Español", "22cm,b/n pág.183", "Modernismo.-siglo XIX.-literatura nicaragüense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("31", "Cárcel de amor", "Diego de San Pedro", "Novela y Narrativa", "Porrúa", NULL, "1492", "España", "Español", "22cm,b/n pág.195", "Prerrenacimiento.-siglo XV.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("32", "Cecilia Valdés", "Cirilo Villaverde", "Novela y Narrativa", "Porrúa", "968-432-326-3", "1972", "Cuba", "Español", "22cm,b/n pág.301", "Romanticismo.-siglo XIX.-literatura cubana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("33", "César Cascabel", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-326-3", "1994", "Francia", "Español", "22cm,b/n pág.265", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("34", "Chapaev", "Dimitri Furmanov", "Novela y Narrativa", "Porrúa", "968-452-650-7", "1890", "Rusia", "Español", "22cm,b/n pág.209", "Realismo.-siglo XX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("35", "Cicerón y sus amigos", "Gaston Boissier", "Novela y Narrativa", "Porrúa", "968-452-186-3", "1992", "Francia", "Español", "22cm,b/n pág.250", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("36", "Clemencia", "Ignacio M. Altamirano", "Novela y Narrativa", "Porrúa", "968-432-027-4", "1865", "México", "Español", "22cm,b/n pág.208", "Romanticismo.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("37", "Comentarios reales", "Garcilaso de la Vega", "Novela y Narrativa", "Porrúa", "968-432-656-3", "1869", "Perú", "Español", "22cm,b/n pág.438", "Renacimiento.-siglo XVII.-literatura peruana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("38", "Cosmopolitas", "W. Somerset Maugham", "Novela y Narrativa", "Porrúa", "968-452-284-0", "1970", "Inglaterra", "Español", "22cm,b/n pág.221", "Modernismo.-siglo XX.-literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("39", "Cuentos de la selva", "Horacio Quiroga", "Cuento y Relato", "Porrúa", "970-07-6727-2", "1609", "Argentina", "Español", "22cm,b/n pág.215", "Cuentos y relatos cortos de América", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("40", "Cuentos judíos", "Ilan Stavans", "Cuento y Relato", "Porrúa", "968-452-762-6", "1988", "México", "Español", "22cm,b/n pág.447", "Contemporánea.-siglo XX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("41", "Cuentos y poemas del antiguo", "Angel Ma. Garibay K.", "Poesía", "Porrúa", "970-07-2032-2", "1961", "Israel", "Español", "22cm,b/n pág.232", "Antigüedad.-siglo X d.C.-literatura hebrea", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("42", "Cuentos escogidos", "Antón Chéjov", "Cuento y Relato", "Porrúa", "968-432-163-9", "1994", "Rusia", "Español", "22cm,b/n pág.312", "Realismo.-siglo XIX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("43", "Cuentos de la cruz", "Alejandro Pérez Lugín", "Cuento y Relato", "Porrúa", "970-07-098-0", "1936", "Madrid", "Español", "22cm,b/n pág.287", "Modernismo.-siglo XX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("44", "Cyrano de Bergerac", "Edmond Rostand", "Novela y Narrativa", "Porrúa", "968-432-798-6", "1886", "Francia", "Español", "22cm,b/n pág.131", "Romanticismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("45", "Dafnis y Cloe", "Longo", "Novela y Narrativa", "Porrúa", "968-432-256-9", "1921", "Mitilene", "Español", "22cm,b/n pág.181", "Romántico.-siglo II a.C.-novela griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("46", "David Copperfield", "Carlos Dickens", "Novela y Narrativa", "Porrúa", "968-452-928-7", "1897", "Inglaterra", "Español", "22cm,b/n pág.542", "Realismo.-siglo XIX.-literatura inglesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("47", "De mi vida poesía y verdad", "J.W. Goethe", "Poesía", "Porrúa", "968-432-997-4", "1975", "Alemania", "Español", "22cm,b/n pág.501", "Romanticismo.-siglos XVIII y XIX.-literatura alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("48", "Diálogo de la lengua", "Juan De Valdés", "Novela y Narrativa", "Porrúa", "968-432-941-5", "1849", "España", "Español", "22cm,b/n pág.135", "Renacimiento.-siglo XVI.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("49", "Diálogos", "Platón", "Filosofía y Ciencia", "Porrúa", "970-07-1481-7", "1896", "Grecia", "Español", "22cm,b/n pág.407", "Antigüedad.-siglo IV d.C.-literatura griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("50", "Diálogos de amor", "León Hebreo", "Filosofía y Ciencia", "Porrúa", "968-432-084-4", "1737", "España", "Español", "22cm,b/n pág.278", "Renacimiento.-siglos XVI-XVII.-literatura italiana y española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("51", "Diario íntimo", "Enrique Federico Amiel", "Novela y Narrativa", "Porrúa", "968-432-160-x", "1983", "Suiza", "Español", "22cm,b/n pág.439", "Realismo.-siglo XIX.-literatura europea", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("52", "Don Álvaro o La fuerza del sino", "Duque de Rivas", "Novela y Narrativa", "Porrúa", "970-07-2222-9", "1590", "Finlandia", "Español", "22cm,b/n pág.407", "Romanticismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("53", "Don Gil de las calzas verdes", "Tirso de Molina", "Novela y Narrativa", "Porrúa", "978-84-7039-105-7", "1968", "España", "Español", "22cm,b/n pág.265", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("54", "Don Carlos", "Federico Schiller", "Novela y Narrativa", "Porrúa", "968-432-996-2", "1884", "Alemania", "Español", "22cm,b/n pág.234", "Romántico.-siglo XVIII.-literatura alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("55", "Don Juan Tenorio", "José Zorrilla", "Teatro y Dramaturgia", "Porrúa", "970-07-2665-7", "1945", "España", "Español", "22cm,b/n pág.143", "Romanticismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("56", "Don segundo sombra", "Ricardo Güiraldes", "Novela y Narrativa", "Porrúa", "968-432-365-5", "1999", "Argentina", "Español", "22cm,b/n pág.129", "Regionalismo.-siglo XX.-literatura argentina", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("57", "Doña Bárbara", "Rómulo Gallegos", "Novela y Narrativa", "Porrúa", "978-84-376-0154-7", "1984", "Venezuela", "Español", "22cm,b/n pág.349", "Realismo.-siglo XX.-literatura venezolana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("58", "Doña Perfecta", "Benito Pérez Galdós", "Novela y Narrativa", "Porrúa", "968-432-257-7", "1888", "España", "Español", "22cm,b/n pág.199", "Realismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("59", "El archipiélago de fuego", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-330-0", "1615", "Francia", "Español", "22cm,b/n pág.204", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("60", "El Cantar de Roldán", "Anónimo", "Novela y Narrativa", "Porrúa", "968-432-514-2", "1926", "Francia", "Español", "22cm,b/n pág.146", "Edad Media.-siglo XII.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("61", "El capitán de la \"D Jumna\"", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-252-2", "1929", "España", "Español", "22cm,b/n pág.167", "Realismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("62", "El carretero de la muerte", "Selma Lagerlöf", "Novela y Narrativa", "Porrúa", "968-452-271-1", "1876", "Suecia", "Español", "22cm,b/n pág.210", "Narrativa.-siglo XX.-literatura sueca", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("63", "El castillo de los carpatos", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-727-7", "1887", "Francia", "Español", "22cm,b/n pág.257", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("64", "El conde lucanor", "Don Juan Manuel", "Novela y Narrativa", "Porrúa", "970-07-1695-6", "1974", "España", "Español", "22cm,b/n pág.195", "Edad Media.-siglo XIV.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("65", "El cantar de los nibelungos", "Anónimo", "Novela y Narrativa", "Porrúa", "970-07-2177-1", "1975", "Austria y Aleman", "Español", "22cm,b/n pág.255", "Edad Media.-siglo XIII.-literatura germánica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("66", "El contrato social", "Juan Jacobo Rousseau", "Filosofía y Ciencia", "Porrúa", "970-07-1481-6", "1892", "Amsterdam", "Español", "22cm,b/n pág.234", "Ilustración.-siglo XVIII.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("67", "El corsario negro", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-432-639-4", "1912", "Italia", "Español", "22cm,b/n pág.170", "Romanticismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("68", "El decamerón", "Boccaccio", "Novela y Narrativa", "Porrúa", "970-07-1688-0", "1877", "Italia", "Español", "22cm,b/n pág.475", "Renacimiento.-siglo XIV.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("69", "El desafío", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-452-6", "1335", "Rusia", "Español", "22cm,b/n pág.205", "Realismo.-siglo XIX.-literatura peruana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("70", "El jinete de la carreta de c.", "Eça de Queiroz", "Novela y Narrativa", "Porrúa", "968-452-191-X", "1890", "Portugal", "Español", "22cm,b/n pág.308", "Realismo.-siglo XIX.-literatura portuguesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("71", "El diablo cojuelo", "Luis Vélez de Guevara", "Novela y Narrativa", "Porrúa", "968-432-344-1", "1755", "España", "Español", "22cm,b/n pág.110", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("72", "El discreto", "Baltasar Gracián", "Novela y Narrativa", "Porrúa", "968-432-163-4", "1975", "España", "Español", "22cm,b/n pág.397", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("73", "El doncel de don Enrique, el doliente", "Mariano José de Larra", "Novela y Narrativa", "Porrúa", "968-452-002-6", "1353", "España", "Español", "22cm,b/n pág.259", "Romanticismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("74", "El examen de maridos", "Juan Ruiz de Alarcón", "Novela y Narrativa", "Porrúa", "968-432-973-3", "1990", "España", "Español", "22cm,b/n pág.229", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("75", "El guarany", "José Martiniano de Alencar", "Novela y Narrativa", "Porrúa", "970-07-2261-9", "1987", "Brasil", "Español", "22cm,b/n pág.305", "Romanticismo.-siglo XIX.-literatura brasileña", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("76", "El hombre de la situación", "Manuel Payno", "Novela y Narrativa", "Porrúa", "968-452-514-1", "1641", "México", "Español", "22cm,b/n pág.288", "Costumbrismo.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("77", "El hombrecillo de los ganzos", "Jakob Wassermann", "Novela y Narrativa", "Porrúa", "970-07-0933-7", "1646", "Alemania", "Español", "22cm,b/n pág.405", "Realismo.-siglo XX.-literatura alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("78", "El indio", "Gregorio López y Fuentes", "Novela y Narrativa", "Porrúa", "968-432-460-X", "1834", "México", "Español", "22cm,b/n pág.125", "Narrativa.-siglo XX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("79", "El ingenioso hidalgo", "Alonso Fernández de Avellaneda", "Novela y Narrativa", "Porrúa", "968-452-153-7", "1635", "España", "Español", "22cm,b/n pág.275", "Renacimiento.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("80", "El jorobado", "Paul Féval", "Novela y Narrativa", "Porrúa", "968-452-170-7", "1857", "Francia", "Español", "22cm,b/n pág.391", "Romanticismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("81", "El judío errante", "Eugenio Sue", "Novela y Narrativa", "Porrúa", "968-452-610-5", "1861", "Francia", "Español", "22cm,b/n pág.693", "Romanticismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("82", "El libro de las tierras vírgenes", "Rudyard Kipling", "Novela y Narrativa", "Porrúa", "970-07-1969-2", "1998", "Reino Unido", "Español", "22cm,b/n pág.229", "Realismo.-siglo XIX.-literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("83", "El lirio en el valle", "Honorato de Balzac", "Novela y Narrativa", "Porrúa", "970-07-2027-6", "1972", "Francia", "Español", "22cm,b/n pág.173", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("84", "El llamado de la selva", "Jack London", "Novela y Narrativa", "Porrúa", "970-07-1873-5", "1614", "Estados Unidos", "Español", "22cm,b/n pág.199", "Realismo.-siglo XX.-literatura estadounidense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("85", "El mar", "Jack London", "Novela y Narrativa", "Porrúa", "968-432-421-9", "1986", "Estados Unidos", "Español", "22cm,b/n pág.200", "Realismo.-siglo XX.-literatura estadounidense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("86", "El mito de Sísifo", "Albert Camus", "Novela y Narrativa", "Porrúa", "970-07-1688-0", "1844", "Francia", "Español", "22cm,b/n pág.195", "Existencialismo.-siglo XX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("87", "El mártir del gólgota", "Enrique Pérez Escrich", "Novela y Narrativa", "Porrúa", "970-07-1622-8", "1972", "España", "Español", "22cm,b/n pág.422", "Realismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("88", "El negro del narciso", "Joseph Conrad", "Novela y Narrativa", "Porrúa", "968-452-257-5", "1984", "Inglaterra", "Español", "22cm,b/n pág.187", "Neoclasicismo.-siglo XIX.-literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("89", "El origen de las especies", "Carlos Darwin", "Novela y Narrativa", "Porrúa", "970-07-2102", "1903", "Reino Unido", "Español", "22cm,b/n pág.232", "Positivismo.-siglo XIX.-literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("90", "El pájaro azul", "Maurice Maeterlinck", "Teatro y Dramaturgia", "Porrúa", "970-07-2147-7", "1914", "Bélgica", "Español", "22cm,b/n pág.83", "Modernismo.-siglo XX.-literatura teatral", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("91", "El paje del duque de Saboya", "Alejandro Dumas", "Novela y Narrativa", "Porrúa", "968-432-659-7", "1992", "Francia", "Español", "22cm,b/n pág.375", "Romanticismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("92", "El periquillo sarniento", "José Joaquín Fernández de Lizardi", "Novela y Narrativa", "Porrúa", "970-07-1606-6", "1871", "México", "Español", "22cm,b/n pág.374", "Barroco.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("93", "El poder y la gloria", "Graham Greene", "Novela y Narrativa", "Porrúa", "970-07-1636-8", "1941", "Reino Unido", "Español", "22cm,b/n pág.174", "Realismo.-siglo XX.-literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("94", "El príncipe idiota", "Fedor M. Dostoievski", "Novela y Narrativa", "Porrúa", "968-432-679-0", "1859", "Rusia", "Español", "22cm,b/n pág.193", "Realismo.-siglo XIX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("95", "El príncipe idiota", "Paul Féval", "Novela y Narrativa", "Porrúa", "968-452-170-7", "1908", "Francia", "Español", "22cm,b/n pág.290", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("96", "El principito", "Antoine de Saint-Exupéry", "Novela y Narrativa", "Porrúa", "970-07-2742-4", "1938", "Francia", "Español", "22cm,b/n pág.88", "Modernismo.-siglo XX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("97", "El ramayana", "Valmiki", "Novela y Narrativa", "Porrúa", "970-07-2033-x", "0", "India", "Español", "22cm,b/n pág.107", "Antigüedad.-siglo III.-literatura hindú", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("98", "El rey del mar", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "970-07-1625-2", "1816", "Italia", "Español", "22cm,b/n pág.239", "Realismo.-siglo XIX.-literatura de aventuras", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("99", "El romanticismo", "Raimundo Lazo", "Novela y Narrativa", "Porrúa", "968-432-377-0", "1940", "Imperio Romano", "Español", "22cm,b/n pág.235", "Romántico.-siglo III d.C.-literatura latina", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("100", "El señor de bembibre", "Enrique Gil y Carrasco", "Historia y Biografía", "Porrúa", "968-432-935-0", "1868", "España", "Español", "22cm,b/n pág.266", "Novela histórica.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("101", "El sol de mayo", "Juan A. Mateos", "Novela y Narrativa", "Porrúa", "970-07-0935", "1873", "México", "Español", "22cm,b/n pág.235", "Romanticismo.-siglo XIX.-Intervención europea", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("102", "El sueño de makar", "Vladimir G. Korolenko", "Novela y Narrativa", "Porrúa", "968-452-270-2", "1943", "Rusia", "Español", "22cm,b/n pág.279", "Realismo.-siglo XIX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("103", "El tejedor de segovia", "Juan Ruiz de Alarcón", "Novela y Narrativa", "Porrúa", "968-432-973-3", "1854", "España", "Español", "22cm,b/n pág.229", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("104", "El trabajo intelectual", "Jean Guitton", "Novela y Narrativa", "Porrúa", "978-84-371-2155-7", "1973", "Francia", "Español", "22cm,b/n pág.225", "siglo XX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("105", "El varón de rabinal", "Rabinal-achí", "Novela y Narrativa", "Porrúa", "968-432-866-9", "1971", "Guatemala", "Español", "22cm,b/n pág.120", "siglos XIII.-literatura prehispánica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("106", "El vergonzoso en el palacio", "Tirso de Molina", "Novela y Narrativa", "Porrúa", "978-84-7039-105-7", "1968", "España", "Español", "22cm,b/n pág.265", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("107", "El viejo y el mar", "Ernest Hemingway", "Novela y Narrativa", "Porrúa", "970-07-1051-3", "1971", "Estados Unidos", "Español", "22cm,b/n pág.120", "Vanguardismo.-siglo XX.-literatura estadounidense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("108", "El vizconde de bragelonne", "Alejandro Dumas", "Novela y Narrativa", "Porrúa", "970-07-2155-1", "1987", NULL, "Español", "22cm,b/n pág.610", "Romanticismo.-siglo XVII.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("109", "Elogio de la locura", "Erasmo de Rotterdam", "Novela y Narrativa", "Porrúa", "970-07-2166-3", "1635", "Europa", "Español", "22cm,b/n pág.255", "Renacimiento.-siglo XVI.-literatura neolatina", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("110", "En el mar de las perlas", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-277-0", "1951", "Italia", "Español", "22cm,b/n pág.255", "Romanticismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("111", "En las fronteras del Far-west", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-757-6", "1929", "Italia", "Español", "22cm,b/n pág.285", "Romanticismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("112", "En los suburbios de la india", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-252-2", "1624", "Italia", "Español", "22cm,b/n pág.267", "Romanticismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("113", "Ensayos Completos", "Montaigne", "Novela y Narrativa", "Porrúa", "970-07-1627-4", "1952", "Francia", "Español", "22cm,b/n pág.958", "Renacimiento.-siglos XVI y XVII.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("114", "Enéida", "Virgilio", "Novela y Narrativa", "Porrúa", "968-432-131-3", "1982", "Grecia", "Español", "22cm,b/n pág.299", "Antigüedad.-siglo I.-literatura romana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("115", "Episodios Nacionales", "Victoriano Salado Álvarez", "Novela y Narrativa", "Porrúa", "968-432-892-X", "1509", "España", "Español", "22cm,b/n pág.202", "Realismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("116", "Escritos literarios", "Francisco Zarco", "Novela y Narrativa", "Porrúa", "968-452-455-0", "1903", "México", "Español", "22cm,b/n pág.247", "Ensayos.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("117", "Estudios y ensayos", "Marcelino Menéndez Pelayo", "Novela y Narrativa", "Porrúa", "970-07-2011-4", "1908", "España", "Español", "22cm,b/n pág.429", "Ensayos y críticas literarias contemporáneas", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("118", "Fábulas", "Anónimo", "Cuento y Relato", "Porrúa", "970-07-1563-1", "1895", "Grecia", "Español", "22cm,b/n pág.293", "Ilustración.-siglo VI.-literatura griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("119", "Fausto y Werther", "J.W. Goethe", "Novela y Narrativa", "Porrúa", "968-432-997-4", "1580", "Alemania", "Español", "22cm,b/n pág.501", "Romanticismo.-siglos XVIII-XIX.-literatura alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("120", "Fera", "Stefan Zweig", "Historia y Biografía", "Porrúa", "970-07-1380-6", "1615", "Austria", "Español", "22cm,b/n pág.149", "Histórica.-siglo XX.-literatura narrativa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("121", "Fuenteovejuna", "Lope de Vega", "Novela y Narrativa", "Porrúa", "970-07-2291-0", "1984", "España", "Español", "22cm,b/n pág.229", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("122", "Guzmán de Alfarache", "Mateo Alemán", "Novela y Narrativa", "Porrúa", "968-452-059-1", "1968", "España", "Español", "22cm,b/n pág.427", "Renacimiento.-siglo XVI.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("123", "Gertrudis Gómez", "Raimundo Lazo", "Novela y Narrativa", "Porrúa", "968-432-448-X", "1992", "Cuba", "Español", "22cm,b/n pág.155", "Romanticismo.-siglo XIX.-literatura hispanoamericana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("124", "Gil Blas de Santillán", "Le Sage", "Novela y Narrativa", "Porrúa", "968-452-422-4", "1868", "Francia", "Español", "22cm,b/n pág.534", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("125", "Gil Gómez", "Juan Díaz Covarrubias", "Novela y Narrativa", "Porrúa", "968-452-496-X", "1963", "México", "Español", "22cm,b/n pág.224", "Romanticismo.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("126", "Graziella Rafael", "Alfonso de Lamartine", "Novela y Narrativa", "Porrúa", "968-432-610-6", "1929", "Francia", "Español", "22cm,b/n pág.184", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("127", "Historia de la literatura hispanoamericana", "Raimundo Lazo", "Historia y Biografía", "Porrúa", "968-432-413-8", "1962", "México", "Español", "22cm,b/n pág.270", "Histórica.-siglos XVI al XX.-literatura hispanoamericana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("128", "Hambre", "Knut Hamsun", "Novela y Narrativa", "Porrúa", "968-432-887-9", "1982", "Noruega", "Español", "22cm,b/n pág.177", "Modernismo.-siglo XIX.-literatura noruega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("129", "Héctor Servadac", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-265-7", "1990", "Francia", "Español", "22cm,b/n pág.351", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("130", "Hogar y patria", "Juan de Dios Peza", "Novela y Narrativa", "Porrúa", "968-432-133-3", "1935", "México", "Español", "22cm,b/n pág.225", "Romanticismo.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("131", "Ilusiones Perdidas", "Honorato de Balzac", "Novela y Narrativa", "Porrúa", "970-07-2265-0", "1859", "Francia", "Español", "22cm,b/n pág.515", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("132", "Iliada", "Homero", "Novela y Narrativa", "Porrúa", "970-07-1572-8", "1849", "Grecia", "Español", "22cm,b/n pág.445", "Antigüedad.-siglo VIII.-literatura griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("133", "Infancia adolescencia juventud", "León Tolstoi", "Novela y Narrativa", "Porrúa", "968-432-817-5", "1971", "Rusia", "Español", "22cm,b/n pág.380", "Realismo.-siglo XIX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("134", "Indagación de la literatura española", "Arqueles Vela", "Novela y Narrativa", "Porrúa", "970-07-1597-3", "1894", "México", "Español", "22cm,b/n pág.225", "Contemporánea.-siglo XX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("135", "Ivanhoe o el cruzado", "Walter Scott", "Novela y Narrativa", "Porrúa", "970-07-0137-4", "1877", "Escocia", "Español", "22cm,b/n pág.275", "Modernismo.-siglo XIX.-literatura cubana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("136", "Joyas de la amistad", "Anónimo", "Novela y Narrativa", "Porrúa", "970-07-077-4", "1965", "Francia", "Español", "22cm,b/n pág.376", "Romanticismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("137", "Juanita la larga", "Juan Valera", "Novela y Narrativa", "Porrúa", "968-432-104-X", "1891", "España", "Español", "22cm,b/n pág.220", "Romanticismo.-siglo XIX.-Novela española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("138", "Karaban", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-272-X", "2001", "Francia", "Español", "22cm,b/n pág.251", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("139", "Libro del buen amor", "Arcipreste De Hita", "Novela y Narrativa", "Porrúa", "970-07-2143-4", "1852", "España", "Español", "22cm,b/n pág.397", "Edad Media.-siglo XIV.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("140", "La araucana", "Alonso de Ercilla", "Novela y Narrativa", "Porrúa", "968-432-457-X", "1852", "España", "Español", "22cm,b/n pág.521", "Renacimiento.-siglo XVI.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("141", "La barraca", "Blasco Ibáñez", "Novela y Narrativa", "Porrúa", "970-07-1580-9", "1891", "España", "Español", "22cm,b/n pág.321", "Realismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("142", "La batalla de los arapiles", "Benito Pérez Galdós", "Historia y Biografía", "Porrúa", "968-432-199-X", "1820", "España", "Español", "22cm,b/n pág.199", "Histórica.-siglo XIX.-literatura realista", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("143", "La bacante", "Guy de Maupassant", "Novela y Narrativa", "Porrúa", "970-07-2250-3", "1963", "Francia", "Español", "22cm,b/n pág.311", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("144", "La cabaña del tío tom", "Harriet Beecher Stowe", "Novela y Narrativa", "Porrúa", "970-07-1608-2", "1874", "Estados Unidos", "Español", "22cm,b/n pág.279", "Realismo.-siglo XIX.-literatura estadounidense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("145", "La caída de un empire", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-467-7", "1884", "Italia", "Español", "22cm,b/n pág.223", "Aventuras.-siglo XX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("146", "La ciudad de los milagros", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-255-0", "1973", "Italia", "Español", "22cm,b/n pág.232", "Aventuras.-siglo XX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("147", "La campana de yuta", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-255-0", "1968", "Italia", "Español", "22cm,b/n pág.228", "Aventuras.-siglo XX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("148", "La carreta", "Enrique Amorim", "Novela y Narrativa", "Porrúa", "968-432-027-4", "1999", "Uruguay", "Español", "22cm,b/n pág.332", "Realismo.-siglo XX.-literatura uruguaya", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("149", "La cazadora de cabelleras", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-277-0", "1875", "Italia", "Español", "22cm,b/n pág.255", "Romanticismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("150", "La celestina", "Fernando de Rojas", "Novela y Narrativa", "Porrúa", "970-07-2059-3", "1875", "España", "Español", "22cm,b/n pág.235", "Novela dialogada.-siglo XVI.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("151", "La ciudad antigua", "Fustel de Coulanges", "Novela y Narrativa", "Porrúa", "970-07-2831-X", "1883", "Francia", "Español", "22cm,b/n pág.298", "siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("152", "La ciudad de las de oro verde y vert.", "A. J. Cronin", "Novela y Narrativa", "Porrúa", "968-452-467-1", "1996", "Escocia", "Español", "22cm,b/n pág.192", "Contemporánea.-siglo XX.-literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("153", "La conquista de las rutas oceánicas", "Carlos Pereyra", "Historia y Biografía", "Porrúa", "970-07-1540-X", "1911", "Austria", "Español", "22cm,b/n pág.313", "Historiografía.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("154", "La de Bringas", "Benito Pérez Galdós", "Novela y Narrativa", "Porrúa", "968-452-103-0", "1890", "España", "Español", "22cm,b/n pág.365", "Realismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("155", "La Desheredada", "Benito Pérez Galdós", "Novela y Narrativa", "Porrúa", "968-452-514-1", "0", "Madrid", "Español", "22cm,b/n pág.302", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("156", "La favorita del mahdi", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-252-2", "1959", "Italia", "Español", "22cm,b/n pág.167", "Romanticismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("157", "La fuerza del sino", "Duque de Rivas", "Novela y Narrativa", "Porrúa", "970-07-2222-9", "1932", "Finlandia", "Español", "22cm,b/n pág.113", "Romanticismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("158", "La gloria de don Ramiro", "Enrique Larreta", "Novela y Narrativa", "Porrúa", "970-07-1834-3", "1909", "Argentina", "Español", "22cm,b/n pág.235", "Modernismo.-siglo XX.-literatura argentina", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("159", "La gitanilla", "Anónimo", "Novela y Narrativa", "Porrúa", "970-07-1563-1", "1968", "España", "Español", "22cm,b/n pág.293", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("160", "La intuición y el estilo", "Pío Baroja", "Novela y Narrativa", "Porrúa", "968-452-380-7", "1971", "España", "Español", "22cm,b/n pág.417", "Realismo.-siglo XX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("161", "La jangada", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-161-8", "1997", "Francia", "Español", "22cm,b/n pág.235", "Novela de aventuras.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("162", "La luna nueva", "Rabindranath Tagore", "Poesía", "Porrúa", NULL, "1940", "India", "Español", "22cm,b/n pág.259", "Poesía lírica.-siglo XX.-literatura bengalí moderna", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("163", "La luna y seis peniques", "William Somerset Maugham", "Novela y Narrativa", "Porrúa", "970-07-1400-0", "1846", "Reino Unido", "Español", "22cm,b/n pág.228", "Modernismo.-siglo XX.-literatura inglesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("164", "La madre", "Máximo Gorki", "Novela y Narrativa", "Porrúa", "970-07-1646-5", "1881", "Rusia", "Español", "22cm,b/n pág.325", "Realismo.-siglo XX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("165", "La madre naturaleza", "Emilia Pardo Bazán", "Novela y Narrativa", "Porrúa", "968-452-125-1", "1887", "España", "Español", "22cm,b/n pág.182", "Naturalismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("166", "La mala hora", "Gabriel García Márquez", "Novela y Narrativa", "Porrúa", "978-84-376-0154-7", "1983", "Colombia", "Español", "22cm,b/n pág.349", "Realismo mágico.-siglo XX.-literatura colombiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("167", "La montaña de los siete Círculos", "Thomas Merton", "Novela y Narrativa", "Porrúa", "970-07-1732-1", "1835", "Estados Unidos", "Español", "22cm,b/n pág.429", "Espiritual.-siglo XX.-literatura religiosa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("168", "La occisa", "G.E. Lessing", "Novela y Narrativa", "Porrúa", "968-452-662-8", "1908", "Alemania", "Español", "22cm,b/n pág.181", "Neoclasicismo.-siglo XVIII.-literatura alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("169", "La perfecta casada", "Fray Luis de León", "Novela y Narrativa", "Porrúa", "970-07-2222-9", "1867", "España", "Español", "22cm,b/n pág.113", "Renacimiento.-siglo XVI.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("170", "La perla roja", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-269-X", "1989", "Italia", "Español", "22cm,b/n pág.219", "Romanticismo.-siglo XIX.-literatura de aventuras", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("171", "La prueba de las promesas", "Juan Ruiz de Alarcón", "Novela y Narrativa", "Porrúa", "968-432-973-3", "1968", "México", "Español", "22cm,b/n pág.229", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("172", "La reina de los caribes", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-432-884-2", "1965", "Italia", "Español", "22cm,b/n pág.173", "Romanticismo.-siglo XVII.-Novela de aventuras, Piratas", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("173", "La resurrección de rocambole", "Ponson du Terrail", "Novela y Narrativa", "Porrúa", "968-452-186-0", "1919", "Francia", "Español", "22cm,b/n pág.711", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("174", "La vida y hechos de Estebanillo González", "Anónimo", "Novela y Narrativa", "Porrúa", "968-452-143-X", "1907", "España", "Español", "22cm,b/n pág.151", "Realismo.-siglo XVII.-literatura española o novela", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("175", "La soberana del campo de oro", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-432-808-0", "1887", "Francia", "Español", "22cm,b/n pág.231", "Aventuras.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("176", "La tía Tula", "Miguel de Unamuno", "Novela y Narrativa", "Porrúa", "968-432-884-2", "1895", "España", "Español", "22cm,b/n pág.103", "Contemporánea.-siglo XX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("177", "La vorágine", "José Eustasio Rivera", "Novela y Narrativa", "Porrúa", "968-452-131-7", "1999", "Colombia", "Español", "22cm,b/n pág.157", "Realismo.-siglo XX.-literatura colombiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("178", "La vida es sueño y El alcalde de Zalamea", "Calderón de la Barca", "Novela y Narrativa", "Porrúa", "970-07-2143-7", "1993", "España", "Español", "22cm,b/n pág.160", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("179", "La vida intelectual", "A. D. Sertillanges", "Novela y Narrativa", "Porrúa", "968-432-944-X", "1970", "Francia", "Español", "22cm,b/n pág.225", "siglo XX.-literatura europea/francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("180", "La vuelta al mundo en ochenta días", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "970-07-1889-1", "1906", "Alemania", "Español", "22cm,b/n pág.257", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("181", "Laocoonte", "G.E. Lessing", "Filosofía y Ciencia", "Porrúa", "968-452-662-8", "1635", "Alemania", "Español", "22cm,b/n pág.181", "Ilustración.-siglo XVIII.-literatura comparada", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("182", "Las minas del rey Salomón", "Henry Rider Haggard", "Historia y Biografía", "Porrúa", "968-452-165-0", "1885", "Reino Unido", "Español", "22cm,b/n pág.213", "Histórica.-siglo XIX.-literatura inglesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("183", "Las aventuras de Tom Sawyer", "Mark Twain", "Novela y Narrativa", "Porrúa", "968-452-929-5", "1866", "Estados Unidos", "Español", "22cm,b/n pág.126", "Realismo.-siglo XIX.-literatura estadounidense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("184", "Las cien mejores poesías", "Marcelino Menéndez y Pelayo", "Poesía", "Porrúa", "968-432-511-8", "1866", "España", "Español", "22cm,b/n pág.250", "Poesía.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("185", "Las dos emparedadas", "Vicente Riva Palacio", "Novela y Narrativa", "Porrúa", "968-452-045-X", "1923", "México", "Español", "22cm,b/n pág.275", "Romanticismo.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("186", "Las flores del mal", "Carlos Baudelaire", "Novela y Narrativa", "Porrúa", "968-452-913-X", "1921", "Francia", "Español", "22cm,b/n pág.194", "Modernismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("187", "Las historias de la literatura hispanoamericana", "Raimundo Lazo", "Historia y Biografía", "Porrúa", "968-432-413-8", "1636", "México", "Español", "22cm,b/n pág.270", "Histórica.-siglos XVI al XX.-literatura hispanoamericana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("188", "Las llaves del reino", "A. J. Cronin", "Novela y Narrativa", "Porrúa", "968-452-467-1", "1651", "Escocia", "Español", "22cm,b/n pág.192", "Contemporánea.-siglo XX.-literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("189", "Las mocedades del cid", "Guillén de Castro", "Novela y Narrativa", "Porrúa", "968-452-057-3", "1920", "España", "Español", "22cm,b/n pág.161", "Barroco.-siglo XVII.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("190", "Las once comedias", "Aristófanes", "Teatro y Dramaturgia", "Porrúa", "970-07-1644-9", "1971", "Grecia", "Español", "22cm,b/n pág.292", "Antigüedad.-siglo IV.-literatura griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("191", "Las siete tragedias", "Sófocles", "Teatro y Dramaturgia", "Porrúa", "970-07-1780-3", "1971", "España/México", "Español", "22cm,b/n pág.222", "Antigüedad.-siglo V d.C.-literatura griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("192", "Lázaro de Tormes", "Anónimo", "Novela y Narrativa", "Porrúa", "970-07-0033-X", "1872", "España", "Español", "22cm,b/n pág.190", "Renacimiento.-siglo XVI.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("193", "Lecciones Preliminares de filosofía", "Manuel García Morente", "Filosofía y Ciencia", "Porrúa", "968-432-241-0", "1993", "España", "Español", "22cm,b/n pág.302", "Neoclasicismo.-siglo XX.-Filosofía", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("194", "Lectura para mujeres", "Gabriela Mistral", "Novela y Narrativa", "Porrúa", "968-432-537-1", "1985", "México", "Español", "22cm,b/n pág.255", "Modernismo.-siglo XX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("195", "Lecturas Españolas", "Azorín", "Novela y Narrativa", "Porrúa", "970-07-1568-X", "1972", "España", "Español", "22cm,b/n pág.297", "siglo XX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("196", "Leyendas Históricas", "Juan de Dios Peza", "Cuento y Relato", "Porrúa", "970-07-1032-4", "1970", "México", "Español", "22cm,b/n pág.242", "Modernismo.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("197", "Los amotinados de la bounty", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-252-2", "1985", "Francia", "Español", "22cm,b/n pág.267", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("198", "Los caracteres", "La Bruyère", "Novela y Narrativa", "Porrúa", "968-452-277-7", "1857", "Francia", "Español", "22cm,b/n pág.276", "Clasicismo.-siglo XVII.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("199", "Los cazadores de microbios", "Paul de Kruif", "Filosofía y Ciencia", "Porrúa", "970-07-1688-2", "1984", "Francia", "Español", "22cm,b/n pág.355", "Ciencia moderna", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("200", "Los cosacos", "León Tolstoi", "Novela y Narrativa", "Porrúa", "968-432-816-8", "1996", "Rusia", "Español", "22cm,b/n pág.283", "Realismo.-siglo XIX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("201", "Los dioses tienen sed", "Anatole France", "Historia y Biografía", "Porrúa", "968-452-984-8", "1986", "Francia", "Español", "22cm,b/n pág.253", "Novela histórica.-siglo XX.-literatura francesa moderna", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("202", "Los misterios de la india", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-252-2", "1618", "Italia", "Español", "22cm,b/n pág.167", "Romanticismo.-siglo XIX.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("203", "Los misterios de París", "Eugenio Sue", "Novela y Narrativa", "Porrúa", "968-452-212-6", "1634", "Francia", "Español", "22cm,b/n pág.505", "Romanticismo.-siglo XIX.-literatura romana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("204", "Los naufragos de liguria", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-175-8", "1903", "Italia", "Español", "22cm,B/N,pag. 181", "Realismo.-siglo XX .-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("205", "Los operarios de la viña", "Giovanni Papini", "Novela y Narrativa", "Porrua", "968-452-750-0", "1971", "Italia", "Español", "22cm,b/n pág.206", "Religiosa.-siglo XX .-literatura italiana.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("206", "Los Operarios de la viña", "Giovanni Papini", "Novela y Narrativa", "Porrúa", "968-452-750-0", "1924", "Italia", "Español", "22cm,B/N,pag. 386", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("207", "Los pardaillan", "Miguel Zevaco", "Novela y Narrativa", "Porrúa", "973-07-2344-5", "1999", "Francia", "Español", "22cm,B/N,Pag.289", "Romanticismo.-siglo XX.-literatura francesa.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("208", "Los piratas de las Bermudas", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-290-8", "1897", "Italia", "Español", "22cm,B/N,Pag.356", "Romanticismo.-siglo XIX.-literatura mexicana, ", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("209", "Los Pueblos", "Azorín", "Novela y Narrativa", "Porrúa", "970-07-1566-3", "1964", "España", "Español", "22cm,B/N,Pag.275", "Modernismo.-siglo XX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("210", "Los tres mosqueteros", "Alexandre Dumas", "Novela y Narrativa", "Porrúa", "970-07-2670-3", "1688", "Francia", "Español", "22cm,B/N,pag. 484", "Romanticismo.-siglo XIX.- literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("211", "Los tres mosqueteros", "Alejandro Dumas", "Novela y Narrativa", "Porrua", "970-07-2670-3", "1993", "Francia", "Español", "22cm, b/n, pág.486", "Romanticismo.-siglo XIX.-Literatura francesa.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("212", "Los últimos filibusteros", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-256-8", "1985", "Italia", "Español", "22cm,B/N,pag. 221", "Novela.-siglo XX .-", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("213", "Macías", "Mariano José De Larra", "Novela y Narrativa", "Porrúa", "968-452-002-6", "1983", "España.", "Español", "22cm,B/N,pag. 256", "Romanticismo.-siglo XX .-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("214", "Malvaloca", "Serafin y Joaquin Alvarez Quintero", "Novela y Narrativa", "Porrua", "968-432-474-X", "1887", "España", "Español", "22cm,b/n pág.215", "Realismo.- siglo XX.-literatura española.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("215", "Mandame Bovary", "Gustavo Flaubert", "Novela y Narrativa", "Porrúa", "970-07-2852-8", "1846", "Francia", "Español", "22cm,B/N,pag. 195", "Romanticismo.-siglo XlX .-Novela realista", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("216", "Maria Estuardo", "Federico Schiller", "Novela y Narrativa", "Porrúa", "970-07-1781-x", "1986", "Alemania", "Español", "22cm,B/N,Pag.207", "Clasicismo.-siglos XVIII.- literatura alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("217", "Marianela", "Benito Pérez Galdós", "Novela y Narrativa", "Porrua", "978-970-07-7328-0", "1994", "España", "Español", "22cm,b/n, pág.135", "Realismo.-siglo XIX.-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("218", "Martín Fierro", "José Hernández", "Novela y Narrativa", "Porrúa", "968-432-424-3", "1953", "argentina", "español", "22cm,B/N,pag. 104", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("219", "Mas cuentos", "Horacio Quiroga", "Cuento y Relato", "Porrúa", "970-07-6727-2", "1896", "Argentina", "Español", "22cm,B/N,Pag.215", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("220", "Memorias de mis tiempos", "Guillermo Prieto", "Novela y Narrativa", "Porrúa", "968-452-099-9", "1909", "México", "Español", "22cm,B/N,pag. 355", "Romanticismo.-siglo XIX.- literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("221", "Memorias, reliquias y retratos", "Juan de Dios Peza", "Novela y Narrativa", "Porrua", "968-452-427-7", "1999", "Mexico", "Español", "22cm,b/n pág.271", "Romanticismo.- siglo XIX.-Literatura mexicana.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("222", "Miau, Marianela", "Benito Pérez Galdós", "Novela y Narrativa", "Porrúa", "970-07-2181-7", "1844", "España", "Español", "22cm,B/N,pag. 104", "Realismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("223", "Miguel strogoff", "Julio Verne", "Aventuras y Juvenil", "Porrua", "968-432-510-x", "1844", "Francia", "Español", "22cm,b/n, pág.205", "Realismo.-siglo XIX.-Literatura francesa.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("224", "Moby dick", "Herman Melville", "Novela y Narrativa", "Porrúa", "968-452-152-9", "1987", "Estados unidos", "Español", "22cm,B/N,Pag.426", "Renacimiento.-siglo XIX.-literatura estadounidense", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("225", "Momentos estelares de la huminadad", "Stefan Zweig", "Historia y Biografía", "Porrua", "970-07-1380-6", "1984", "Austria", "Español", "22cm,b/n, pág.149", "Histórica.-siglo XX.-Literatura narrativa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("226", "Motivos de Proteo", "Jose Enrique Rodo", "Novela y Narrativa", "Porrúa", "968-432-225-9", "1973", "Uruguay", "Español", "22cm,B/N,pag.245", "Modernismo.-siglo XX.-", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("227", "Mudarse por mejorarse", "Juan Ruiz De Alarcón", "Novela y Narrativa", "Porrúa", "968-432-973-3", "1981", "México", "Español", "22cm,B/N,pag. 229", "Barroco.-siglo XVII .-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("228", "Musa Callejera", "Guillermo Prieto", "Poesía", "Porrúa", "968-452-064-6", "1804", "México", "Español", "22cm,B/N,pag. 275", "Romanticismo.-siglo XIX .- Poesía mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("229", "Napoleón en chamarrin", "Benito Pérez Galdós", "Novela y Narrativa", "Porrúa", "9678-452-302-5", "1878", "España", "Español", "22cm,B/N,pag. 239", "Realismo.-Siglo XIX.-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("230", "Nayar", "Miguel Ángel Menéndez", "Novela y Narrativa", "Porrúa", "968-432-187-2", "1872", "México", "Español", "22cm,B/N,pag. 278", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("231", "Nido de Hidalgos", "Ivan Turguenev", "Novela y Narrativa", "Porrúa", "970-07-2374-7", "1980", "España", "Español", "22cm,B/N,Pag.329", "Realismo.-siglo XIX.-literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("232", "Niebla", "Miguel de Unamuno", "Novela y Narrativa", "Porrúa", "970-07-1692-9", "1985", "España", "Español", "22cm,B/N,Pag.235", "Realismo.-siglo XX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("233", "Novelas cortas", "Anton Chejov", "Novela y Narrativa", "Porrúa", "968-452-659-8", "1990", "Rusia", "Español", "22cm,B/N,pag. 277", "Romanticismo.-siglo XlX .-Novela rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("234", "Novelas ejemplares", "Miguel de Cervantes Saavedra", "Novela y Narrativa", "Porrúa", "970-07-2021-7", "1888", "España", "Español", "22cm,B/N,Pag.335", "Renacimiento.-siglo XVI.-Literatura española.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("235", "Nuevas aventuras", "Emilio salgari", "Aventuras y Juvenil", "Porrua", "968-452-291-6", "1876", "Italia", "Español", "22cm,b/n, pág.197", "Narrativa.-siglo XX.-Literatura de aventuras", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("236", "Nuevas Aventuras de cabeza de piedra", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-452-291-6", "1851", "Italia", "Español", "22cm,B/N,pag. 197", "Romanticismo.-siglo XIX .-", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("237", "Obras completas", "Sor Juana Ines De La Cruz", "Novela y Narrativa", "Porrúa", "970-07-2769-6", "1927", "Mexico", "Español", "22cm,B/N,pag.941", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("238", "Ocho siglos de poesia", "Francisco Montes de Oca", "Poesía", "Porrúa", "968-452-953-8", "1909", "México", "Español", "22cm,B/N,pag. 385", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("239", "Oliver Twist", "Carlos Dickens", "Novela y Narrativa", "Porrúa", "968-432-731-5", "1635", "Reino Unido", "Español", "22cm,B/N,pag. 292", "Realismo.-siglo XIX.-literatura inglesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("240", "Panorama literario de los pueblos Nahuas", "Angel Ma. Garibay K.", "Novela y Narrativa", "Porrúa", "968-432-355-7", "1971", "México", "Español", "22cm,B/N,pag. 202", "siglo XII al XVI.- literatura de México", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("241", "Papa Goriot", "Horato De Balzac", "Novela y Narrativa", "Porrúa", "970-07--1966-9", "1970", "Francia", "Español", "22cm,B/N,Pag.168", "Realismo.-siglo XIX.- literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("242", "Pensamientos y otros escritos", "Blas pascal", "Novela y Narrativa", "Porrúa", "968-452-353-X", "1940", "Francia", "Francés", "22cm,B/N,pag. 46", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("243", "Peñas arriba", "Jose M. De pereda", "Novela y Narrativa", "Porrúa", "968-432-840", "1859", "España", "Español", "22cm,B/N,pag. 394", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("244", "Pepita Jiménez y Juanita L a Larga", "Juan Valera", "Novela y Narrativa", "Porrúa", "968-432-104-x", "1983", "España", "Español", "22cm,B/N,pag. 220", "Romanticismo.-siglo XlX .- Novela española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("245", "Pequeñeses", "P.Luis Coloma", "Novela y Narrativa", "Porrúa", "970-07-1623-6", "1993", "España", "Español", "22cm,B/N,Pag.502", "Realismo.-siglo XIX.-literatura española.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("246", "Platero y Yo", "Juan Ramón Jiménez", "Novela y Narrativa", "Porrúa", "970-07-2126-4", "1613", "España", "Español", "22cm,B/N,pag. 220", "Romanticismo.-siglo XX .-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("247", "Pobreza y riqueza", "Carlos Ignacio Gonzalez", "Novela y Narrativa", "Porrúa", "968-452-289-4", "1970", "México", "Español", "22cm,B/N,pag. 179", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("248", "Poema de mio cid", "Anónimo", "Poesía", "Porrúa", "970-07-1608-2", "1988", "España", "Español", "22cm,B/N,pag. 279", "Edad Media.-siglo XI.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("249", "Poesia lirica", "Lope de Vega", "Poesía", "Porrúa", "968-452-883-3", "1969", "Grecia", "Español", "22cm,B/N,Pag.427", "Edad Media.-Siglos VII.-literatura griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("250", "Poesia mexicana", "Francisco Montes de Oca", "Poesía", "Porrúa", "968-432-658-0", "0", "México", "Español", "22cm,B/N,pag. 419", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("251", "Poesías", "Luis de Góngora", "Poesía", "Porrúa", "970-07-1522-1", "1961", "España", "Español", "22cm,B/N,pag. 339", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("252", "Poesias completas", "Gaspar Nuñez de Arce", "Poesía", "Porrúa", "968-432-749-8", "1838", "España", "Español", "22cm,B/N,pag. 250", "Poesía española .-siglo XIX .-lieteratura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("253", "Poesias completas", "Julio Herrera y Reissig", "Poesía", "Porrúa", "968-452-308-4", "1963", "Uruguay", "Español", "22cm,B/N,pag. 160", "Poesía.-siglo XIX .-", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("254", "Popol Wuj", "Anonimo", "Novela y Narrativa", "Porrúa", "970-07-1952-9", "1835", "Guatemala", "Español", "22cm,B/N,pag. 166", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("255", "Proverbios de Salomón", "Angel Ma. Garibay K.", "Novela y Narrativa", "Porrúa", "970-07-2032-2", "1989", "Antiguo israel", "Español", "22cm,B/N,Pag.232", "Antigüedad.-siglo X.- literatura hebrea", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("256", "Rebelión en la granja", "George orwell", "Novela y Narrativa", "Porrua", "970-07-1694-5", "1895", "Reino Unido", "Español", "22cm,b/n, pág.349", "Contemporánea.-siglo.-XVI.-Literatura británica", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("257", "Recuerdo de Vasco de Quiroga", "Silvio Zavala", "Novela y Narrativa", "Porrúa", "968-452-244-4", "1966", "Mexico", "Español", "22cm,B/N,pag. 331", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("258", "Recuerdos del tiempo viejo", "Jose Zorrilla", "Novela y Narrativa", "Porrúa", "970-07-1056-4", "1968", "España", "Español", "22cm,B/N,pag. 502", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("259", "Refranes populares de mexico", "Guadalupe Appendini", "Novela y Narrativa", "Porrúa", "970-07-1032-7", "1967", "Mexico", "Español", "22cm,B/N,Pag.185", "Costumbrismo.-siglos XIX.-Literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("260", "Retrato del arista adolescente", "James Joyce", "Novela y Narrativa", "Porrúa", "968-432-816-8", "1988", "Irlanda", "Español", "22cm,B/N,pag. 283", "Modernismo.-siglo XX.-literatura irlandesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("261", "Rima, leyendas y narraciones", "Becquer", "Cuento y Relato", "Porrúa", "968-432-281-X", "1779", "España", "Español", "22cm,B/N,Pag.217", "Romanticismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("262", "Robin Hood", "Anonimo", "Novela y Narrativa", "Porrúa", "970-07-2165-5", "1995", "Inglaterra", "Español", "22cm,B/N,pag.133", "Edad Media.-siglos XIV y XV.-literatura inglesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("263", "Rojo y negro", "Stendhal", "Novela y Narrativa", "Porrúa", "968-432-708-0", "1968", "Francia", "Español", "22cm,B/N,pag. 311", "Romanticismo.-Siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("264", "Sandokan", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-432-466-9", "1966", "Italia", "Español", "22cm,B/N,pag. 174", "Romanticismo.-siglo XlX .-Literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("265", "Sin novedad en el frente", "Erich Maria Remarque", "Novela y Narrativa", "Porrúa", "970-07-0937-x", "1627", "Alemania", "Español", "22cm,B/N,Pag.174", "Realismo.-Siglo XX.-Literatura alemana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("266", "Sinuhé, el egipcio", "Mika Waltari", "Historia y Biografía", "Porrúa", "970-07-1298-2", "1982", "España", "Español", "22cm,B/N,Pag.195", "Histórica.-siglo XX.-literatura finlandesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("267", "Su único hijo", "Leopoldo Alas <<Clarín>>", "Novela y Narrativa", "Porrua", "968-452-322-x", "1977", "España", "Español", "22cm,b/n, pág.269", "Realismo.-siglo XIX.-Literatura francesa.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("268", "Sueños", "Francisco de Quevedo y Villegas", "Novela y Narrativa", "Porrúa", "968-452-062-X", "1554", "España", "Español", "22cm,B/N,pag. 243", "Barroco.-Siglo XVII.-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("269", "Sus mejores páginas", "José Martí", "Novela y Narrativa", "Porrua", "968-432-197-x", "1994", "Cuba", "Español", "22cm, b/n, pág.251", "Modernismo.- siglo XIX.- Literatura española.", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("270", "Tabaré", "Juan Zorrilla de San Martín", "Novela y Narrativa", "Porrua", "968-432-218-6", "1966", "Uruguay", "Español", "22cm,b/n, pág.213", "Romanticismo.- siglo XIX.-Literatura uruguaya", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("271", "Taras bulba", "Nikolai V. Gogol", "Novela y Narrativa", "Porrúa", "970-07-2113-2", "1945", "Ucrania", "Español", "22cm,B/N,Pag.163", "Nacionalismo.-siglo XIX.- literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("272", "Teatro", "Anton Chejov", "Teatro y Dramaturgia", "Porrúa", "968-432-988-1", "1965", "Rusia", "Español", "22cm,B/N,Pag.145", "Realismo.- siglos XIX.- literatura rusa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("273", "Teogonia", "Hesiodo", "Novela y Narrativa", "Porrúa", "968-432-606-8", "1998", "Grecia", "Español", "22cm,B/N,Pag.87", "Antigüedad.-siglo VIII.-literatura griega", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("274", "Tirano Banderas", "Ramon del Valle-Inclan", "Novela y Narrativa", "Porrúa", "970-07-2142-6", "1892", "España", "Español", "22cm,B/N,Pag.129", "Vanguardismo.-siglo XX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("275", "Tomochic", "Heriberto Frias", "Novela y Narrativa", "Porrúa", "970-07-1591-4", "1997", "Mexico", "Español", "22cm,B/N,Pag.153", "Realismo.-siglo XIX.-literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("276", "Trafalgar", "Benito Perez Galdos", "Novela y Narrativa", "Porrúa", "968-432-257-7", "1999", "España", "Español", "22cm,B/N,pag. 199", "Realismo.-siglo XIX.-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("277", "Tragedias", "Corneille", "Teatro y Dramaturgia", "Porrua", "970-07-1643-0", "1983", "Francia", "Español", "22cm,b/n, pág.169", "Barroco.- siglo XVII.- Literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("278", "Tristana", "Benito Perez Galdos", "Novela y Narrativa", "Porrúa", "970-07-2023-3", "1996", "España", "Español", "22cm,B/N,Pag.215", "Realismo.- siglo XIX.-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("279", "Tu eres la paz", "Gregorio Martinez sierra", "Novela y Narrativa", "Porrua", "968-452-164-2", "1963", "España", "Español", "22cm,b/n pág.189", "Modernismo.-sigloXX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("280", "Viajes de colón", "Martín Fernandes de Navarrete", "Historia y Biografía", "Porrua", "968-452-189-8", "1984", "España", "Español", "22cm,b/n, pág.351", "Historiografía.- siglo XIX.- Literatura medieval XIX", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("281", "Viajes de gulliver", "Jonathan Swift", "Historia y Biografía", "Porrua", "970-07-1609-0", "1830", "Irlanda", "Español", "22cm,b/n pág.219", "Neoclasicismo.-siglo XVIII.-literatura irlandesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("282", "Vida de Marcos de Obregon", "Vicente Espinel", "Novela y Narrativa", "Porrúa", "978-970-07-0232-2", "1869", "España", "Español", "22cm,B/N,Pag.245", "Realismo.-siglo XIX.-literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("283", "Vida del buscón", "Lazarillo de Tormes", "Novela y Narrativa", "Porrua", "970-07-0033-x", "1972", "España", "Español", "22cm,b/n, pág.190", "Barroco.- siglo XVI.- Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("284", "Voces de Oriente", "S/A", "Novela y Narrativa", "Porrua", "968-432-802-8", "1929", "España", "Español", "22cm,b/n, pág.195", "Humanismo.- siglo XX.-Literatura mexicana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("285", "Yolanda", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "970-07-2264-3", "1835", "Italia", "Español", "22cm,B/N,pag. 171", "Romanticismo.-siglo XVII.-Novela de aventuras, Piratas", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("286", "La fontana de oro", "Benito Perez Galdos", "Novela y Narrativa", "Porrúa", "968-432-810-9", "1970", "España", "Español", "22cm,B/N,pag.248", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("287", "Aventuras de Robinson cruose", "Daniel Defoe", "Novela y Narrativa", "Porrúa", "970-07-2637-1", "1931", "Inglaterra", "Español", "22cm,B/N,pag.158", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("288", "Cuatro comedias", "Juan Ruiz de Alarcon", "Teatro y Dramaturgia", "Porrúa", "970-07-1550-7", "1890", "Mexico", "Español", "22cm,B/N,pag.206", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("289", "850.914 S35 1773", "Emilio Salgari", "Aventuras y Juvenil", "Porrúa", "968-432-820-6", "1978", "Italia", "Español", "22cm,B/N,pag.246", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("290", "EL lirio en el valle", "Honorato de Balzac", "Novela y Narrativa", "Porrúa", "970-07-2027-6", "1970", "Francia", "Español", "22cm,B/N,pag.173", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("291", "La vuelta de los muertos", "Vicente Riva Palacio", "Novela y Narrativa", "Porrúa", "968-452-154-5", "1888", "Mexico", "Español", "22cm,B/N,pag.293", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("292", "Leyendas Historicas Mexicanas y otros relatos", "Heriberto Frias", "Cuento y Relato", "Porrúa", "968-452-971-6", "1834", "Mexico", "Español", "22cm,B/N,pag.420", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("293", "Don Juan Tenorio y El puñal del godo", "Jose Zorrilla", "Teatro y Dramaturgia", "Porrúa", "970-07-2665-8", "1984", "España", "Español", "22cm,B/N,pag.144", "Romanticismo.-siglo XlX .-Literatura española", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("294", "Keraban el testarudo", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "968-452-272-x", "1493", "Francia", "Español", "22cm,B/N,pag.251", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("295", "Alicia en el pais de las maravillas (Ej. 2)", "Lewis Carroll", "Novela y Narrativa", "Porrúa", "970-07-6752-4", "1926", "Reino Unido", "Español", "22cm,B/N,pag.235", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("296", "Almacen de antigüedades", "Carlos Dickens", "Novela y Narrativa", "Porrúa", "968-452-754-3", "1893", "Reino Unido", "Español", "22cm,B/N,pag.357", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("297", "Graziella", "Alfonso de Lamartine", "Novela y Narrativa", "Porrúa", "968-432-610-6", "1889", "Francia", "Español", "22cm,B/N,pag.184", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("298", "Comedias", "Moliere", "Teatro y Dramaturgia", "Porrúa", "968-432-206-2", "1967", "Francia", "Español", "22cm,B/N,pag.161", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("299", "La campana de huesca", "Antonio Cánovas del Castillo", "Novela y Narrativa", "Porrúa", "968-432-950-4", "0", "España", "Español", "22cm,B/N,pag.198", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("300", "Madame Bovary", "Gustavo Flaubert", "Novela y Narrativa", "Porrúa", "970-07-2852-8", "0", "España", "Español", "22cm,B/N,pag.195", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("301", "EL ultimo verano de Klingsor", "Hermann Hesse", "Novela y Narrativa", "Porrúa", "970-07-1301-6", "0", "Alemania", "Español", "22cm,B/N,pag.257", NULL, NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("302", "20 poemas de amor y una canción desesperada", "Pablo Neruda", "Poesía", "Porrúa", "970-07-1752-6", "0", "Chile", "Español", "22cm,B/N,pag.160", "Posmodernismo.-siglo XX.-literatura hispanoamericana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("303", "Jerusalen liberada", "Torcuato Tasso", "Novela y Narrativa", "Porrúa", "970-07-2526-X", "0", "Italia", "Español", "22cm,B/N,pag.320", "Renacimiento.-siglo XVI.-literatura italiana", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("304", "Veinte mil leguas de viaje submarino", "Julio Verne", "Aventuras y Juvenil", "Porrúa", "970-07-2416-0", "0", "Francia", "Español", "22cm,B/N,pag.351", "Realismo.-siglo XIX.-literatura francesa", NULL, NULL, NULL, NULL);
INSERT INTO `libros` VALUES("305", "El extranjero", "Albert Camus", "Novela y Narrativa", "Grupo Editorial Planeta", "978-607-07-3854-8", "2019", "Argentina", "Español", "En buen estado", "fqwf", "4", "4", "0", "28751");
INSERT INTO `libros` VALUES("306", "El extranjero", "Albert Camus", "Novela y Narrativa", "Grupo Editorial Planeta", "978-607-07-3854-8", "2019", "Argentina", "Español", "En buen estado", "fqwf", "5", "5", "0", "28751");

DROP TABLE IF EXISTS `prestamos`;
CREATE TABLE `prestamos` (
  `id_prestamo` int(11) NOT NULL AUTO_INCREMENT,
  `id_alumnos` int(11) DEFAULT NULL,
  `id_profesores` int(11) DEFAULT NULL,
  `id_libro` int(11) NOT NULL,
  `Cantidad` int(11) DEFAULT 1,
  `tipo_movimiento` varchar(50) DEFAULT 'Prestado',
  `fecha_salida` date NOT NULL,
  `fecha_devolucion_esperada` date NOT NULL,
  `fecha_devolucion_real` date DEFAULT NULL,
  `estado_libro` varchar(100) DEFAULT NULL,
  `Renovacion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_prestamo`),
  KEY `id_alumnos` (`id_alumnos`),
  KEY `id_profesores` (`id_profesores`),
  KEY `id_libros` (`id_libro`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `prestamos` VALUES("1", NULL, "8", "3", "1", "Prestado", "2026-10-20", "2026-10-20", "2026-11-03", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("2", NULL, "12", "12", "1", "Prestado", "2026-10-22", "2026-10-31", "2026-11-03", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("3", NULL, "13", "5", "1", "Prestado", "2026-10-22", "2026-10-31", "2026-10-29", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("4", NULL, "1", "289", "1", "Prestado", "2026-10-22", "2026-10-31", "2026-11-03", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("5", NULL, "14", "1", "1", "Prestado", "2026-10-22", "2026-10-31", "2026-11-03", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("6", NULL, "15", "6", "1", "Prestado", "2026-10-22", "2026-10-31", "2026-11-03", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("7", NULL, "15", "13", "1", "Prestado", "2026-10-22", "2026-10-31", "2026-11-03", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("8", NULL, "16", "5", "5", "Prestado", "2026-10-23", "2026-10-31", "2026-10-29", "Excelente", NULL);
INSERT INTO `prestamos` VALUES("9", "6", NULL, "2", "1", "Prestado", "2026-11-03", "2026-11-12", "2026-11-03", "Excelente", "0");
INSERT INTO `prestamos` VALUES("10", "6", NULL, "2", "1", "Prestado", "2026-11-03", "2026-11-12", "2026-11-05", "Excelente", "0");
INSERT INTO `prestamos` VALUES("11", NULL, "34", "1", "1", "Prestado", "2026-11-05", "2026-11-14", "2026-11-06", "Excelente", "0");
INSERT INTO `prestamos` VALUES("12", "2", NULL, "5", "1", "Prestado", "2026-11-05", "2026-11-14", "2026-11-06", "Excelente", "0");
INSERT INTO `prestamos` VALUES("13", NULL, "22", "16", "1", "Prestado", "2026-11-05", "2026-11-14", "2026-11-06", "Excelente", "0");
INSERT INTO `prestamos` VALUES("14", NULL, "22", "1", "1", "Prestado", "2026-11-05", "2026-11-14", "2026-11-06", "Excelente", "0");
INSERT INTO `prestamos` VALUES("15", NULL, "33", "6", "1", "Prestado", "2026-11-05", "2026-11-14", "2026-11-06", "Excelente", "0");
INSERT INTO `prestamos` VALUES("16", NULL, "34", "7", "1", "Prestado", "2026-11-05", "2026-11-14", "2026-11-12", "Excelente", "0");
INSERT INTO `prestamos` VALUES("17", "3", NULL, "6", "1", "Apartado", "2026-11-06", "2026-11-08", "2026-11-09", "Excelente", "0");
INSERT INTO `prestamos` VALUES("18", "3", NULL, "9", "1", "Prestado", "2026-11-06", "2026-11-15", "2026-11-12", "Excelente", "0");
INSERT INTO `prestamos` VALUES("19", "2", NULL, "1", "1", "Prestado", "2026-11-06", "2026-11-15", "2026-11-12", "Excelente", "0");
INSERT INTO `prestamos` VALUES("20", NULL, "32", "1", "1", "Prestado", "2026-11-06", "2026-11-15", "2026-11-12", "Excelente", "0");
INSERT INTO `prestamos` VALUES("21", NULL, "35", "1", "1", "Prestado", "2026-11-12", "2026-11-21", "2026-11-12", "Excelente", "0");
INSERT INTO `prestamos` VALUES("22", "2", NULL, "3", "1", "Prestado", "2026-11-12", "2026-11-21", "2026-11-12", "Excelente", "0");
INSERT INTO `prestamos` VALUES("23", "2", NULL, "4", "1", "Prestado", "2026-11-13", "2026-11-29", "2026-11-20", "Excelente", "0");
INSERT INTO `prestamos` VALUES("24", "3", NULL, "4", "1", "Apartado", "2026-11-13", "2026-11-15", "2026-11-17", "Excelente", "0");
INSERT INTO `prestamos` VALUES("25", "1", NULL, "4", "1", "Prestado", "2026-11-13", "2026-11-22", "2026-11-23", "Excelente", "0");
INSERT INTO `prestamos` VALUES("26", NULL, "22", "4", "1", "Prestado", "2026-11-13", "2026-11-22", NULL, NULL, "0");
INSERT INTO `prestamos` VALUES("27", "4", NULL, "4", "1", "Prestado", "2026-11-13", "2026-11-22", "2026-11-19", "Excelente", "0");
INSERT INTO `prestamos` VALUES("28", NULL, "29", "1", "1", "Prestado", "2026-11-17", "2026-11-26", NULL, NULL, "0");
INSERT INTO `prestamos` VALUES("29", "7", NULL, "2", "1", "Prestado", "2026-11-17", "2026-11-26", NULL, NULL, "0");
INSERT INTO `prestamos` VALUES("30", "1", NULL, "1", "1", "Prestado", "2026-11-23", "2026-12-23", NULL, "Renovado", "2");
INSERT INTO `prestamos` VALUES("31", NULL, "22", "305", "1", "Prestado", "2026-11-23", "2026-12-02", "2026-11-23", "Perdido", "0");
INSERT INTO `prestamos` VALUES("32", "5", NULL, "3", "1", "Apartado", "2026-11-24", "2026-11-26", NULL, NULL, "0");

DROP TABLE IF EXISTS `profesores`;
CREATE TABLE `profesores` (
  `id_profesores` int(11) NOT NULL AUTO_INCREMENT,
  `Matricula` varchar(20) NOT NULL,
  `Nombre` varchar(100) NOT NULL,
  `Apellido_Paterno` varchar(100) NOT NULL DEFAULT '',
  `Apellido_Materno` varchar(100) NOT NULL DEFAULT '',
  `Materia` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_profesores`),
  UNIQUE KEY `Matricula` (`Matricula`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `profesores` VALUES("22", "DOC-26-022", "Camila", "", "", "Manufactura del Vestido");
INSERT INTO `profesores` VALUES("24", "DOC-26-024", "LuzMaria", "", "", "Lengua y Comunicacion");
INSERT INTO `profesores` VALUES("25", "DOC-26-025", "Ada", "", "", "Educacion Fisica Y Artes");
INSERT INTO `profesores` VALUES("26", "DOC-26-026", "Alilne", "", "", "Iformatica, Contabilidad y Electronica");
INSERT INTO `profesores` VALUES("28", "DOC-26-028", "Ana", "", "", "Iformatica, Contabilidad y Electronica");
INSERT INTO `profesores` VALUES("29", "DOC-26-029", "Brisa", "", "", "Manufactura del Vestido");
INSERT INTO `profesores` VALUES("30", "DOC-26-030", "Moserrath", "", "", "Educacion Fisica Y Artes");
INSERT INTO `profesores` VALUES("31", "DOC-26-031", "Alexa", "", "", "Gastronomia y Enfermeria");
INSERT INTO `profesores` VALUES("32", "DOC-26-032", "Zoe", "", "", "Iformatica, Contabilidad y Electronica");
INSERT INTO `profesores` VALUES("33", "DOC-26-033", "Ambar", "", "", "Lengua y Comunicacion");
INSERT INTO `profesores` VALUES("34", "DOC-26-034", "Soledad", "", "", "Manufactura del Vestido");
INSERT INTO `profesores` VALUES("35", "DOC-26-035", "Laura", "", "", "Informatica, Contabilidad y Electronica");
INSERT INTO `profesores` VALUES("36", "DOC-26-036", "Iván Rueda Galván", "", "", "Educacion Fisica Y Artes");
INSERT INTO `profesores` VALUES("39", "DOC-26-037", "LuzMaria", "De los Santos", "Sanchez", "Lengua y Comunicacion");

SET FOREIGN_KEY_CHECKS=1;

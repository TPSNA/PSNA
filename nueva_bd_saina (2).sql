-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-12-2025 a las 02:02:17
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
-- Base de datos: `nueva_bd_saina`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(11) NOT NULL,
  `nacionalidad` varchar(50) NOT NULL,
  `ci` varchar(20) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `sexo` enum('masculino','femenino') NOT NULL,
  `estado_civil` enum('soltero(a)','casado(a)','divorciado(a)','viudo(a)') NOT NULL,
  `direccion_ubicacion` varchar(255) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `cuenta_bancaria` varchar(50) NOT NULL,
  `tipo_trabajador` enum('ctd','cti','lnr') NOT NULL,
  `grado_instruccion` enum('primaria','bachiller','tecnico_profesional','licenciado','ingeniero','especialista','maestria','doctorado') NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `sede` enum('admin','cafo','cate','csai','csb') NOT NULL,
  `dependencia` enum('admin','cafo','cate','csai','csb') NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `cod_siantel` varchar(50) NOT NULL,
  `ubicacion_estante` varchar(50) DEFAULT NULL,
  `estatus` enum('activo','inactivo') NOT NULL,
  `fecha_egreso` date DEFAULT NULL,
  `motivo_retiro` text DEFAULT NULL,
  `ubicacion_estante_retiro` varchar(50) DEFAULT NULL,
  `tipo_sangre` enum('a+','a-','b+','b-','ab+','ab-','o+','o-') NOT NULL,
  `lateralidad` enum('diestro(a)','zurdo(a)') NOT NULL,
  `peso_trabajador` decimal(5,2) DEFAULT NULL,
  `altura_trabajador` decimal(5,2) DEFAULT NULL,
  `calzado_trabajador` varchar(10) DEFAULT NULL,
  `camisa_trabajador` varchar(10) DEFAULT NULL,
  `pantalon_trabajador` varchar(10) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado_id` int(11) DEFAULT 1,
  `municipio_id` int(11) DEFAULT NULL,
  `parroquia_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nacionalidad`, `ci`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nacimiento`, `sexo`, `estado_civil`, `direccion_ubicacion`, `telefono`, `correo`, `cuenta_bancaria`, `tipo_trabajador`, `grado_instruccion`, `cargo`, `sede`, `dependencia`, `fecha_ingreso`, `cod_siantel`, `ubicacion_estante`, `estatus`, `fecha_egreso`, `motivo_retiro`, `ubicacion_estante_retiro`, `tipo_sangre`, `lateralidad`, `peso_trabajador`, `altura_trabajador`, `calzado_trabajador`, `camisa_trabajador`, `pantalon_trabajador`, `foto`, `fecha_registro`, `estado_id`, `municipio_id`, `parroquia_id`) VALUES
(1, 'venezolano(a)', '28220429', 'JUANGELYN', 'DAINED', 'SANCHEZ', 'APOSTOL', '2000-10-11', '', '', 'CARORITA ABAJO', '04125201352', 'JUANGELIN.18@GMAIL.COM', '01022433820100135524', 'cti', 'ingeniero', 'DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES', 'admin', 'admin', '2024-11-20', '00001', 'A-1', 'activo', '0000-00-00', '', '', 'o+', 'diestro(a)', 70.00, 1.63, '40', 'M', '34', '', '2025-11-22 19:22:09', 1, NULL, NULL),
(2, 'venezolano(a)', '30227004', 'DAYAN', 'YONAIRE', 'SANCHEZ', 'APOSTOL', '2003-04-27', '', '', 'CARORITA ABAJO', '04125201353', 'YONAIRE.2703@GMAIL.COM', '01022433820100135526', 'ctd', 'ingeniero', 'COORDINADOR DE NUTRICION', 'admin', 'cafo', '2023-07-13', '00002', 'A-2', 'inactivo', '2025-10-24', 'RENUNCIA', 'B-2', 'o+', 'diestro(a)', 62.00, 1.60, '40', 'M', '32', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p (1).png', '2025-11-22 19:55:48', 1, NULL, NULL),
(3, 'venezolano(a)', '11787974', 'NEIDA', 'ZULAY', 'APOSTOL', 'ARENAS', '1973-11-03', 'femenino', 'casado(a)', 'CARORITA ABAJO', '04125201354', 'NEIDA40@GMAIL.COM', '01022433820100135528', 'cti', 'bachiller', 'ENFERMERA', 'admin', 'cate', '2018-06-11', '00003', 'A-1', 'inactivo', '2025-10-18', 'RENUNCIA', 'B-1', 'o+', 'diestro(a)', 70.00, 1.60, '38', 'M', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/datosyy.jpg', '2025-11-22 20:38:25', 1, NULL, NULL),
(4, 'venezolano(a)', '5241736', 'ZENAIDA', 'DEL CARMEN', 'CAMACARO', '-', '1953-10-12', 'femenino', 'viudo(a)', 'CARORITA ABAJO', '04125202353', 'ZENAIDA52@GMAIL.COM', '01022436840100135526', 'ctd', 'bachiller', 'ENFERMERA', 'admin', 'cafo', '2000-05-17', '00006', 'A-4', 'inactivo', '2022-08-13', 'JUBILACION', 'B-4', 'b-', 'diestro(a)', 80.00, 1.58, '43', 'M', '36', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-23 19:30:48', 1, NULL, NULL),
(5, 'VENEZOLANO(A)', '33719654', 'YONAIKER', 'MIGUEL', 'SANCHEZ', 'APOSTOL', '2009-12-04', 'masculino', 'soltero(a)', 'CARORITA ABAJO', '04127359808', 'SANCHEZ.MIGUEL4011@GMAIL.COM', '01022436840100735526', 'ctd', 'bachiller', 'DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES', 'admin', 'admin', '2025-09-11', '00007', 'A-1', 'activo', '0000-00-00', '', '', 'o+', 'diestro(a)', 78.00, 1.80, '45', 'M', '32', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-23 19:59:43', 1, NULL, NULL),
(6, 'VENEZOLANO(A)', '22345676', 'LEONARDO', 'SIMON', 'GUERRA', 'RUIZ', '2002-04-27', '', '', 'ANTENA', '04125202433', 'LEONARDO12@GMAIL.COM', '01022400840100735026', 'ctd', 'ingeniero', 'COORDINADOR DE BIENES', 'cafo', 'admin', '2022-10-19', '00008', 'A-2', 'activo', '0000-00-00', '', '', 'a-', 'diestro(a)', 70.00, 1.80, '45', 'L', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Mapa_Conceptual_Yonaire_Sanchez.png', '2025-11-30 02:10:17', 1, NULL, NULL),
(7, 'VENEZOLANO(A)', '23345676', 'WILMER', 'JOSE', 'COLINAS', 'APOSTOL', '1993-02-07', '', '', 'CABUDARE', '04125201234', 'WILMER7@GMAIL.COM', '0108212345401007355', 'cti', '', 'COORDINADOR DE NUTRICION', 'admin', 'cate', '2019-12-01', '00004', 'A-1', 'activo', '0000-00-00', '', '', 'ab+', 'diestro(a)', 68.00, 1.80, '43', 'L', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-30 04:09:03', 1, NULL, NULL),
(8, 'VENEZOLANO(A)', '12345678', 'ROBERTO', 'JOSE', 'SANCHEZ', 'BARCOS', '2004-03-11', '', '', 'CABUDARE', '04145201352', 'ROBERTO_21@GMAIL.COM', '01022436840112345678', 'ctd', 'doctorado', 'COORDINADOR DE TRANSPORTE', 'admin', 'cate', '2023-09-12', '00009', 'A-4', 'activo', '0000-00-00', '', '', 'ab+', 'zurdo(a)', 78.00, 1.75, '45', 'L', '36', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-30 10:20:32', 1, NULL, NULL),
(9, 'VENEZOLANO(A)', '28220428', 'JUANGEL', 'DAI', 'APOSTOL', 'SANCHEZ', '2001-10-11', 'femenino', 'soltero(a)', 'LA PAZ', '04142674134', 'JUANGEL_1@GMAIL.COM', '01022433820100200528', 'ctd', 'bachiller', 'ASISTENTE DE OFICINA', 'cate', 'admin', '2022-10-12', '00010', 'A-5', 'activo', '0000-00-00', '', '', 'ab-', 'diestro(a)', 74.00, 1.63, '40', 'M', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/unefescu2.jpg', '2025-11-30 10:31:12', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id`, `nombre`) VALUES
(1, 'Lara');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familiares`
--

CREATE TABLE `familiares` (
  `id` int(11) NOT NULL,
  `ci_trabajador` varchar(20) NOT NULL,
  `cedula_familiar` varchar(20) NOT NULL,
  `parentesco` enum('Esposo/a','Hijo/a','Padre','Madre','Otro') NOT NULL,
  `edad` int(11) NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(5,2) DEFAULT NULL,
  `talla_zapato` varchar(10) DEFAULT NULL,
  `talla_camisa` varchar(10) DEFAULT NULL,
  `talla_pantalon` varchar(10) DEFAULT NULL,
  `tipo_sangre` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `fecha_registro` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `familiares`
--

INSERT INTO `familiares` (`id`, `ci_trabajador`, `cedula_familiar`, `parentesco`, `edad`, `peso`, `altura`, `talla_zapato`, `talla_camisa`, `talla_pantalon`, `tipo_sangre`, `fecha_registro`) VALUES
(1, '5241736', '13269805', 'Hijo/a', 52, 70.00, 1.60, '38', 'M', '32', 'B-', '2025-11-23'),
(2, '33719654', '11787974', 'Madre', 52, 70.00, 1.60, '38', 'M', '34', 'O+', '2025-11-23'),
(3, '22345676', '28220429', 'Otro', 25, 70.00, 163.00, '', '', '34', 'A-', '2025-11-29'),
(4, '23345676', '12345678', 'Hijo/a', 7, 38.00, 130.00, '37', 'S', '16', 'AB+', '2025-11-30'),
(5, '12345678', '12345679', 'Padre', 46, 80.00, 180.00, '45', 'XL', '36', 'AB+', '2025-11-30'),
(6, '28220428', '98765432', 'Padre', 45, 85.00, 180.00, '45', 'XL', '36', 'AB-', '2025-11-30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `estado_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id`, `nombre`, `estado_id`) VALUES
(1, 'Andrés Eloy Blanco', 1),
(2, 'Crespo', 1),
(3, 'Iribarren', 1),
(4, 'Jiménez', 1),
(5, 'Morán', 1),
(6, 'Palavecino', 1),
(7, 'Simón Planas', 1),
(8, 'Torres', 1),
(9, 'Urdaneta', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parroquias`
--

CREATE TABLE `parroquias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `municipio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `parroquias`
--

INSERT INTO `parroquias` (`id`, `nombre`, `municipio_id`) VALUES
(34, 'Agua Viva', 6),
(13, 'Aguedo Felipe Alvarado', 3),
(54, 'Altagracia', 8),
(39, 'Antonio Díaz', 8),
(25, 'Anzoátegui', 5),
(24, 'Bolívar', 5),
(14, 'Buena Vista', 3),
(36, 'Buría', 7),
(32, 'Cabudare', 6),
(40, 'Camacaro', 8),
(41, 'Castañeda', 8),
(6, 'Catedral', 3),
(42, 'Cecilio Zubillaga', 8),
(43, 'Chiquinquirá', 8),
(7, 'Concepción', 3),
(23, 'Coronel Mariano Peraza', 4),
(17, 'Cuara', 4),
(18, 'Diego de Lozada', 4),
(44, 'El Blanco', 8),
(8, 'El Cují', 3),
(45, 'Espinoza de los Monteros', 8),
(4, 'Fréitez', 2),
(26, 'Guarico', 5),
(37, 'Gustavo Vegas León', 7),
(52, 'Heriberto Arroyo', 8),
(27, 'Hilario Luna y Luna', 5),
(28, 'Humocaro Alto', 5),
(29, 'Humocaro Bajo', 5),
(22, 'José Bernardo Dorante', 4),
(33, 'José Gregorio Bastidas', 6),
(5, 'José María Blanco', 2),
(16, 'Juan Bautista Rodríguez', 4),
(9, 'Juan de Villegas', 3),
(15, 'Juárez', 3),
(30, 'La Candelaria', 5),
(46, 'Lara', 8),
(47, 'Las Mercedes', 8),
(48, 'Manuel Morillo', 8),
(49, 'Montaña Verde', 8),
(50, 'Montes de Oca', 8),
(31, 'Morán', 5),
(56, 'Moroturo', 9),
(19, 'Paraíso de San José', 4),
(2, 'Pío Tamayo', 1),
(1, 'Quebrada Honda de Guache', 1),
(53, 'Reyes Vargas', 8),
(20, 'San Miguel', 4),
(57, 'San Miguel', 9),
(10, 'Santa Rosa', 3),
(35, 'Sarare', 7),
(55, 'Siquisique', 9),
(11, 'Tamaca', 3),
(21, 'Tintorero', 4),
(51, 'Torres', 8),
(38, 'Trinidad Samuel', 8),
(12, 'Unión', 3),
(58, 'Xaguas', 9),
(3, 'Yacambú', 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ci` (`ci`),
  ADD KEY `idx_empleados_ci` (`ci`),
  ADD KEY `idx_empleados_estatus` (`estatus`),
  ADD KEY `estado_id` (`estado_id`),
  ADD KEY `municipio_id` (`municipio_id`),
  ADD KEY `parroquia_id` (`parroquia_id`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `familiares`
--
ALTER TABLE `familiares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_familiares_ci_trabajador` (`ci_trabajador`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_estado` (`nombre`,`estado_id`),
  ADD KEY `estado_id` (`estado_id`);

--
-- Indices de la tabla `parroquias`
--
ALTER TABLE `parroquias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_municipio` (`nombre`,`municipio_id`),
  ADD KEY `municipio_id` (`municipio_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `familiares`
--
ALTER TABLE `familiares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `parroquias`
--
ALTER TABLE `parroquias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`municipio_id`) REFERENCES `municipios` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `empleados_ibfk_3` FOREIGN KEY (`parroquia_id`) REFERENCES `parroquias` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `familiares`
--
ALTER TABLE `familiares`
  ADD CONSTRAINT `familiares_ibfk_1` FOREIGN KEY (`ci_trabajador`) REFERENCES `empleados` (`ci`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD CONSTRAINT `municipios_ibfk_1` FOREIGN KEY (`estado_id`) REFERENCES `estados` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `parroquias`
--
ALTER TABLE `parroquias`
  ADD CONSTRAINT `parroquias_ibfk_1` FOREIGN KEY (`municipio_id`) REFERENCES `municipios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

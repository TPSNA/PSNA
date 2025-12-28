-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 28-12-2025 a las 03:44:43
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
  `sexo` enum('MASCULINO','FEMENINO') NOT NULL,
  `estado_civil` enum('SOLTERO(A)','CASADO(A)','DIVORCIADO(A)','VIUDO(A)') NOT NULL,
  `direccion_ubicacion` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `cuenta_bancaria` varchar(50) DEFAULT NULL,
  `tipo_trabajador` enum('CTD','CTI','LNR') NOT NULL,
  `grado_instruccion` enum('PRIMARIA','BACHILLER','TECNICO_PROFESIONAL','LICENCIADO','INGENIERO','ESPECIALISTA','MAESTRIA','DOCTORADO') NOT NULL,
  `cargo` varchar(100) NOT NULL,
  `sede` enum('ADMIN','CAFO','CATE','CSAI','CSB') NOT NULL,
  `dependencia` enum('ADMIN','CAFO','CATE','CSAI','CSB') NOT NULL,
  `fecha_ingreso` date NOT NULL,
  `cod_siantel` varchar(50) NOT NULL,
  `ubicacion_estante` varchar(50) DEFAULT NULL,
  `estatus` enum('ACTIVO','INACTIVO') NOT NULL,
  `fecha_egreso` date DEFAULT NULL,
  `motivo_retiro` text DEFAULT NULL,
  `ubicacion_estante_retiro` varchar(50) DEFAULT NULL,
  `tipo_sangre` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `lateralidad` enum('DIESTRO(A)','ZURDO(A)') NOT NULL,
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
(1, 'venezolano(a)', '28220429', 'JUANGELYN', 'DAINED', 'SANCHEZ', 'APOSTOL', '2000-10-11', '', '', 'CARORITA ABAJO', '04125201352', 'JUANGELIN.18@GMAIL.COM', '01022433820100135524', 'CTI', 'INGENIERO', 'DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES', 'ADMIN', 'ADMIN', '2024-11-20', '00001', 'A-1', 'ACTIVO', '0000-00-00', '', '', 'O+', 'DIESTRO(A)', 70.00, 1.63, '40', 'M', '34', '', '2025-11-22 19:22:09', 1, NULL, NULL),
(2, 'venezolano(a)', '30227004', 'DAYAN', 'YONAIRE', 'SANCHEZ', 'APOSTOL', '2003-04-27', '', '', 'CARORITA ABAJO', '04125201353', 'YONAIRE.2703@GMAIL.COM', '01022433820100135526', 'CTD', 'INGENIERO', 'COORDINADOR DE NUTRICION', 'ADMIN', 'CAFO', '2023-07-13', '00002', 'A-2', 'INACTIVO', '2025-10-24', 'RENUNCIA', 'B-2', 'O+', 'DIESTRO(A)', 62.00, 1.60, '40', 'M', '32', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p (1).png', '2025-11-22 19:55:48', 1, NULL, NULL),
(3, 'venezolano(a)', '11787974', 'NEIDA', 'ZULAY', 'APOSTOL', 'ARENAS', '1973-11-03', 'FEMENINO', 'CASADO(A)', 'CARORITA ABAJO', '04125201354', 'NEIDA40@GMAIL.COM', '01022433820100135528', 'CTI', 'BACHILLER', 'ENFERMERA', 'ADMIN', 'CATE', '2018-06-11', '00003', 'A-1', 'INACTIVO', '2025-10-18', 'RENUNCIA', 'B-1', 'O+', 'DIESTRO(A)', 70.00, 1.60, '38', 'M', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/datosyy.jpg', '2025-11-22 20:38:25', 1, NULL, NULL),
(4, 'VENEZOLANO(A)', '5241736', 'ZENAIDA', 'DEL CARMEN', 'CAMACARO', '-', '1953-10-12', 'FEMENINO', 'VIUDO(A)', 'CARORITA ABAJO', '04125202353', 'ZENAIDA52@GMAIL.COM', '01022436840100135526', 'CTD', 'BACHILLER', 'ENFERMERA', 'ADMIN', 'CAFO', '2000-05-17', '00006', 'A-4', 'ACTIVO', NULL, NULL, NULL, 'B+', '', 80.00, 1.58, '43', 'M', '36', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-23 19:30:48', 1, NULL, NULL),
(5, 'VENEZOLANO(A)', '33719654', 'YONAIKER', 'MIGUEL', 'SANCHEZ', 'APOSTOL', '2009-12-04', 'MASCULINO', 'SOLTERO(A)', 'CARORITA ABAJO', '04127359808', 'SANCHEZ.MIGUEL4011@GMAIL.COM', '01022436840100735526', 'CTD', 'BACHILLER', 'DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES', 'ADMIN', 'ADMIN', '2025-09-11', '00007', 'A-1', 'ACTIVO', '0000-00-00', '', '', 'O+', 'DIESTRO(A)', 78.00, 1.80, '45', 'M', '32', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-23 19:59:43', 1, NULL, NULL),
(6, 'VENEZOLANO(A)', '22345676', 'LEONARDO', 'SIMON', 'GUERRA', 'RUIZ', '2002-04-27', '', '', 'ANTENA', '04125202433', 'LEONARDO12@GMAIL.COM', '01022400840100735026', 'CTD', 'INGENIERO', 'COORDINADOR DE BIENES', 'CAFO', 'ADMIN', '2022-10-19', '00008', 'A-2', 'ACTIVO', '0000-00-00', '', '', 'A-', 'DIESTRO(A)', 70.00, 1.80, '45', 'L', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Mapa_Conceptual_Yonaire_Sanchez.png', '2025-11-30 02:10:17', 1, NULL, NULL),
(7, 'VENEZOLANO(A)', '23345676', 'WILMER', 'JOSE', 'COLINAS', 'APOSTOL', '1993-02-07', '', '', 'CABUDARE', '04125201234', 'WILMER7@GMAIL.COM', '0108212345401007355', 'CTI', '', 'COORDINADOR DE NUTRICION', 'ADMIN', 'CATE', '2019-12-01', '00004', 'A-1', 'ACTIVO', '0000-00-00', '', '', 'AB+', 'DIESTRO(A)', 68.00, 1.80, '43', 'L', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-30 04:09:03', 1, NULL, NULL),
(8, 'VENEZOLANO(A)', '12345678', 'ROBERTO', 'JOSE', 'SANCHEZ', 'BARCOS', '2004-03-11', '', '', 'CABUDARE', '04145201352', 'ROBERTO_21@GMAIL.COM', '01022436840112345678', 'CTD', 'DOCTORADO', 'COORDINADOR DE TRANSPORTE', 'ADMIN', 'CATE', '2023-09-12', '00009', 'A-4', 'ACTIVO', '0000-00-00', '', '', 'AB+', 'ZURDO(A)', 78.00, 1.75, '45', 'L', '36', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-30 10:20:32', 1, NULL, NULL),
(10, 'VENEZOLANO(A)', '28259890', 'RACHELD', 'ANDREINA', 'GUTIERREZ', 'PEREZ', '2001-11-19', 'FEMENINO', 'CASADO(A)', 'CALLE 48', '04127740856', 'RACHELD@GMAIL.COM', '01022436840100135525', 'CTD', 'INGENIERO', 'ASISTENTE DE OFICINA', 'ADMIN', 'ADMIN', '2025-02-22', '', 'A-19', 'ACTIVO', '0000-00-00', '', '', 'A+', 'DIESTRO(A)', 70.00, 1.60, '39', 'M', '30', '../php/uploads/default.png', '2025-12-23 21:39:13', 1, 3, 7),
(15, 'VENEZOLANO(A)', '28220942', 'YONAIRE', 'DAINED', 'CAMACARO', 'SANCHEZ', '2000-12-27', 'FEMENINO', 'SOLTERO(A)', 'ANTENA', '04145204327', 'YONAIRE.2708@GMAIL.COM', '01022430020100135528', 'CTD', '', 'COORDINADOR DE SEGURIDAD', 'ADMIN', 'ADMIN', '2017-07-13', '00016', 'A-4', 'ACTIVO', NULL, NULL, NULL, 'A+', 'DIESTRO(A)', 70.00, 1.63, '40', 'M', '34', '../../uploads/empleado_28220942_1766804953.jpeg', '2025-12-27 08:09:13', 1, 5, 35),
(16, 'VENEZOLANO(A)', '29220004', 'SANDRO', 'miguel', 'CASTAñEDA', 'PEREZ', '2000-12-28', 'MASCULINO', 'SOLTERO(A)', 'SAINA', '04125232101', 'sandro1@gmail.com', '01082532840112345678', 'CTD', '', 'DIRECTOR TALENTO HUMANO', 'ADMIN', 'ADMIN', '2023-02-14', '00017', 'A-3', 'ACTIVO', NULL, NULL, NULL, 'O+', 'DIESTRO(A)', 65.00, 1.63, '38', 's', '32', '../../uploads/empleado_29220004_1766882620.jpeg', '2025-12-28 05:43:40', 1, 1, 1),
(17, 'VENEZOLANO(A)', '3422708', 'EDICSON', 'DANIEL', 'MOLINA', 'SUAREZ', '1998-05-11', 'MASCULINO', 'SOLTERO(A)', 'BARRIO UNION', '04165601353', 'daniel1@gmail.com', '01028765432112345678', 'CTI', 'INGENIERO', 'COORDINADOR DE PRESUPUESTO', 'ADMIN', 'CATE', '2025-12-27', '00020', 'A-5', 'ACTIVO', NULL, NULL, NULL, 'AB+', 'DIESTRO(A)', NULL, NULL, '', '', '', '../../uploads/empleado_3422708_1766884425.jpeg', '2025-12-28 06:13:45', 1, NULL, NULL),
(18, 'VENEZOLANO(A)', '14269805', 'MARIA', 'PILAR', 'SANCHEZ', 'CAMACARO', '1980-05-12', 'FEMENINO', 'SOLTERO(A)', 'CARORITA ABAJO', '04142301234', 'maria_pilar2023@gmail.com', '01082435000100234529', 'CTD', 'BACHILLER', 'DIRECTOR TALENTO HUMANO', 'ADMIN', 'CATE', '2023-08-16', '00111', 'A-3', 'ACTIVO', NULL, NULL, NULL, 'O+', 'ZURDO(A)', 62.00, 1.60, '40', '', '34', '../../uploads/empleado_14269805_1766886263.jpeg', '2025-12-28 06:44:23', 1, 4, 25),
(19, 'VENEZOLANO(A)', '15787974', 'CARLOS', 'EDUARDO', 'SANCHEZ', 'GIMENEZ', '1983-07-06', 'MASCULINO', 'SOLTERO(A)', 'BARRIO UNION', '04146491352', 'carlos2@gmail.com', '01022400840345235026', 'CTI', '', 'COORDINADOR DE ARCHIVOS', 'ADMIN', 'ADMIN', '2025-12-28', '', '', 'ACTIVO', NULL, NULL, NULL, 'B-', 'DIESTRO(A)', 78.00, 1.58, '45', 's', '', '../../uploads/default.png', '2025-12-28 07:07:18', 1, 2, 8);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familiares`
--

CREATE TABLE `familiares` (
  `id` int(11) NOT NULL,
  `ci_trabajador` varchar(20) NOT NULL,
  `cedula_familiar` varchar(20) NOT NULL,
  `nombre_familiar` varchar(255) NOT NULL,
  `apellido_familiar` varchar(255) NOT NULL,
  `parentesco` enum('ESPOSO/A','HIJO/A','PADRE','MADRE','OTRO') NOT NULL,
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

INSERT INTO `familiares` (`id`, `ci_trabajador`, `cedula_familiar`, `nombre_familiar`, `apellido_familiar`, `parentesco`, `edad`, `peso`, `altura`, `talla_zapato`, `talla_camisa`, `talla_pantalon`, `tipo_sangre`, `fecha_registro`) VALUES
(1, '5241736', '13269805', '', '', 'HIJO/A', 52, 70.00, 1.60, '38', 'M', '32', 'B-', '2025-11-23'),
(2, '33719654', '11787974', '', '', 'MADRE', 52, 70.00, 1.60, '38', 'M', '34', 'O+', '2025-11-23'),
(3, '22345676', '28220429', '', '', 'OTRO', 25, 70.00, 163.00, '', '', '34', 'A-', '2025-11-29'),
(4, '23345676', '12345678', '', '', 'HIJO/A', 7, 38.00, 130.00, '37', 'S', '16', 'AB+', '2025-11-30'),
(5, '12345678', '12345679', '', '', 'PADRE', 46, 80.00, 180.00, '45', 'XL', '36', 'AB+', '2025-11-30'),
(7, '28220942', '13269806', '', '', 'PADRE', 47, 70.00, 170.00, '45', '0', '36', 'A+', '2025-12-27'),
(8, '29220004', '87654321', '', '', 'PADRE', 50, 70.00, 170.00, '40', '0', '36', 'O+', '2025-12-28');

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
-- Indices de la tabla `familiares`
--
ALTER TABLE `familiares`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_familiares_ci_trabajador` (`ci_trabajador`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `familiares`
--
ALTER TABLE `familiares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

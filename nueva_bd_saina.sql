-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2025 a las 21:13:24
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
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nacionalidad`, `ci`, `primer_nombre`, `segundo_nombre`, `primer_apellido`, `segundo_apellido`, `fecha_nacimiento`, `sexo`, `estado_civil`, `direccion_ubicacion`, `telefono`, `correo`, `cuenta_bancaria`, `tipo_trabajador`, `grado_instruccion`, `cargo`, `sede`, `dependencia`, `fecha_ingreso`, `cod_siantel`, `ubicacion_estante`, `estatus`, `fecha_egreso`, `motivo_retiro`, `ubicacion_estante_retiro`, `tipo_sangre`, `lateralidad`, `peso_trabajador`, `altura_trabajador`, `calzado_trabajador`, `camisa_trabajador`, `pantalon_trabajador`, `foto`, `fecha_registro`) VALUES
(1, 'venezolano(a)', '28220429', 'JUANGELYN', 'DAINED', 'SANCHEZ', 'APOSTOL', '2000-10-11', 'femenino', 'soltero(a)', 'CARORITA ABAJO', '04125201352', 'JUANGELIN.18@GMAIL.COM', '01022433820100135524', 'ctd', 'ingeniero', 'DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES', 'admin', 'admin', '2024-11-20', '00001', 'A-1', 'activo', '0000-00-00', '', '', 'o+', 'diestro(a)', 70.00, 1.63, '40', 'M', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-22 19:22:09'),
(2, 'venezolano(a)', '30227004', 'YONAIRE', 'DAYAN', 'SANCHEZ', 'APOSTOL', '2003-04-27', 'femenino', 'soltero(a)', 'CARORITA ABAJO', '04125201353', 'YONAIRE.2703@GMAIL.COM', '01022433820100135526', 'ctd', 'ingeniero', 'COORDINADOR DE NUTRICION', 'admin', 'cafo', '2023-07-13', '00002', 'A-2', 'inactivo', '2025-10-24', 'RENUNCIA', 'B-2', 'o+', 'diestro(a)', 62.00, 1.60, '40', 'M', '32', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p (1).png', '2025-11-22 19:55:48'),
(3, 'venezolano(a)', '11787974', 'NEIDA', 'ZULAY', 'APOSTOL', 'ARENAS', '1973-11-03', 'femenino', 'casado(a)', 'CARORITA ABAJO', '04125201354', 'NEIDA40@GMAIL.COM', '01022433820100135528', 'cti', 'bachiller', 'ENFERMERA', 'admin', 'cate', '2018-06-11', '00003', 'A-1', 'inactivo', '2025-10-18', 'RENUNCIA', 'B-1', 'o+', 'diestro(a)', 70.00, 1.60, '38', 'M', '34', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/datosyy.jpg', '2025-11-22 20:38:25'),
(4, 'venezolano(a)', '5241736', 'ZENAIDA', 'DEL CARMEN', 'CAMACARO', '-', '1953-10-12', 'femenino', 'viudo(a)', 'CARORITA ABAJO', '04125202353', 'ZENAIDA52@GMAIL.COM', '01022436840100135526', 'ctd', 'bachiller', 'ENFERMERA', 'admin', 'cafo', '2000-05-17', '00006', 'A-4', 'inactivo', '2022-08-13', 'JUBILACION', 'B-4', 'b-', 'diestro(a)', 80.00, 1.58, '43', 'M', '36', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-23 19:30:48'),
(5, 'VENEZOLANO(A)', '33719654', 'YONAIKER', 'MIGUEL', 'SANCHEZ', 'APOSTOL', '2009-12-04', 'masculino', 'soltero(a)', 'CARORITA ABAJO', '04127359808', 'SANCHEZ.MIGUEL4011@GMAIL.COM', '01022436840100735526', 'ctd', 'bachiller', 'DIRECTOR DE TECNOLOGIA INFORMATICA Y TELECOMUNICACIONES', 'admin', 'admin', '2025-09-11', '00007', 'A-1', 'activo', '0000-00-00', '', '', 'o+', 'diestro(a)', 78.00, 1.80, '45', 'M', '32', 'C:\\xampp\\htdocs\\proyecto_saina\\php/uploads/Gemini_Generated_Image_am0pl1am0pl1am0p.png', '2025-11-23 19:59:43');

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
(2, '33719654', '11787974', 'Madre', 52, 70.00, 1.60, '38', 'M', '34', 'O+', '2025-11-23');

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
  ADD KEY `idx_empleados_estatus` (`estatus`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `familiares`
--
ALTER TABLE `familiares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `familiares`
--
ALTER TABLE `familiares`
  ADD CONSTRAINT `familiares_ibfk_1` FOREIGN KEY (`ci_trabajador`) REFERENCES `empleados` (`ci`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

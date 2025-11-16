-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-11-2025 a las 02:35:07
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
-- Base de datos: `registro_trabajadores`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carga_familiar`
--

CREATE TABLE `carga_familiar` (
  `Id_familiar` int(11) NOT NULL,
  `Cédula_trabajador` varchar(20) NOT NULL,
  `Cédula_familiar` varchar(20) NOT NULL,
  `Parentesco` enum('Esposo/a','Hijo/a','Hija/o','Padre','Madre','Otro') NOT NULL,
  `Edad` int(11) NOT NULL,
  `Peso` decimal(5,2) DEFAULT NULL,
  `Altura` decimal(4,2) DEFAULT NULL,
  `Talla_zapato` int(11) DEFAULT NULL,
  `Talla_camisa` varchar(10) DEFAULT NULL,
  `Talla_pantalón` varchar(10) DEFAULT NULL,
  `Tipo_sangre` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `Fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carga_familiar`
--

INSERT INTO `carga_familiar` (`Id_familiar`, `Cédula_trabajador`, `Cédula_familiar`, `Parentesco`, `Edad`, `Peso`, `Altura`, `Talla_zapato`, `Talla_camisa`, `Talla_pantalón`, `Tipo_sangre`, `Fecha_registro`) VALUES
(1, '12345678', '87654321', 'Esposo/a', 40, 80.00, 1.75, 42, 'L', '40', 'A+', '2025-11-06 22:05:22'),
(2, '12345678', '11223344', 'Hijo/a', 12, 45.00, 1.50, 35, 'S', '32', 'O+', '2025-11-06 22:05:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados`
--

CREATE TABLE `estados` (
  `id_estado` int(11) NOT NULL,
  `estado` varchar(250) NOT NULL,
  `iso_3166-2` varchar(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `estados`
--

INSERT INTO `estados` (`id_estado`, `estado`, `iso_3166-2`) VALUES
(1, 'Lara', 'VE-K');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipios`
--

CREATE TABLE `municipios` (
  `id_municipio` int(11) NOT NULL,
  `id_estado` int(11) NOT NULL,
  `municipio` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `municipios`
--

INSERT INTO `municipios` (`id_municipio`, `id_estado`, `municipio`) VALUES
(1, 1, 'Andrés Eloy Blanco'),
(2, 1, 'Crespo'),
(3, 1, 'Iribarren'),
(4, 1, 'Jiménez'),
(5, 1, 'Morán'),
(6, 1, 'Palavecino'),
(7, 1, 'Simón Planas'),
(8, 1, 'Torres'),
(9, 1, 'Urdaneta');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parroquias`
--

CREATE TABLE `parroquias` (
  `id_parroquia` int(11) NOT NULL,
  `id_municipio` int(11) NOT NULL,
  `parroquia` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `parroquias`
--

INSERT INTO `parroquias` (`id_parroquia`, `id_municipio`, `parroquia`) VALUES
(1, 1, 'Quebrada Honda de Guache'),
(2, 1, 'Pío Tamayo'),
(3, 1, 'Yacambú'),
(4, 2, 'Fréitez'),
(5, 2, 'José María Blanco'),
(6, 3, 'Catedral'),
(7, 3, 'Concepción'),
(8, 3, 'El Cují'),
(9, 3, 'Juan de Villegas'),
(10, 3, 'Santa Rosa'),
(11, 3, 'Tamaca'),
(12, 3, 'Unión'),
(13, 3, 'Aguedo Felipe Alvarado'),
(14, 3, 'Buena Vista'),
(15, 3, 'Juárez'),
(16, 4, 'Juan Bautista Rodríguez'),
(17, 4, 'Cuara'),
(18, 4, 'Diego de Lozada'),
(19, 4, 'Paraíso de San José'),
(20, 4, 'San Miguel'),
(21, 4, 'Tintorero'),
(22, 4, 'José Bernardo Dorante'),
(23, 4, 'Coronel Mariano Peraza'),
(24, 5, 'Bolívar'),
(25, 5, 'Anzoátegui'),
(26, 5, 'Guarico'),
(27, 5, 'Hilario Luna y Luna'),
(28, 5, 'Humocaro Alto'),
(29, 5, 'Humocaro Bajo'),
(30, 5, 'La Candelaria'),
(31, 5, 'Morán'),
(32, 6, 'Cabudare'),
(33, 6, 'José Gregorio Bastidas'),
(34, 6, 'Agua Viva'),
(35, 7, 'Sarare'),
(36, 7, 'Buría'),
(37, 7, 'Gustavo Vegas León'),
(38, 8, 'Trinidad Samuel'),
(39, 8, 'Antonio Díaz'),
(40, 8, 'Camacaro'),
(41, 8, 'Castañeda'),
(42, 8, 'Cecilio Zubillaga'),
(43, 8, 'Chiquinquirá'),
(44, 8, 'El Blanco'),
(45, 8, 'Espinoza de los Monteros'),
(46, 8, 'Lara'),
(47, 8, 'Las Mercedes'),
(48, 8, 'Manuel Morillo'),
(49, 8, 'Montaña Verde'),
(50, 8, 'Montes de Oca'),
(51, 8, 'Torres'),
(52, 8, 'Heriberto Arroyo'),
(53, 8, 'Reyes Vargas'),
(54, 8, 'Altagracia'),
(55, 9, 'Siquisique'),
(56, 9, 'Moroturo'),
(57, 9, 'San Miguel'),
(58, 9, 'Xaguas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `Id_trabajador` int(11) NOT NULL,
  `Nacionalidad` varchar(20) NOT NULL,
  `Cédula` varchar(20) NOT NULL,
  `Primer_nombre` varchar(50) NOT NULL,
  `Segundo_nombre` varchar(50) DEFAULT NULL,
  `Primer_apellido` varchar(50) NOT NULL,
  `Segundo_apellido` varchar(50) DEFAULT NULL,
  `Fecha_nacimiento` date NOT NULL,
  `Sexo` enum('Masculino','Femenino') NOT NULL,
  `Estado_civil` enum('Soltero','Casado','Divorciado','Viudo') NOT NULL,
  `Tipo_empleado` enum('CTD','LNR') NOT NULL,
  `Cargo` varchar(100) NOT NULL,
  `Fecha_ingreso` date NOT NULL,
  `Ubicación` varchar(100) DEFAULT NULL,
  `Presupuesto` varchar(50) DEFAULT NULL,
  `Dirección_habitación` text DEFAULT NULL,
  `Teléfono` varchar(20) DEFAULT NULL,
  `Código_carnet_santel` varchar(50) DEFAULT NULL,
  `Cuenta_bancaria` varchar(50) DEFAULT NULL,
  `Centro` enum('SEDE','SAINA','CAFO') NOT NULL,
  `Cantidad_hijos` int(11) DEFAULT 0,
  `Peso` decimal(5,2) DEFAULT NULL,
  `Altura` decimal(4,2) DEFAULT NULL,
  `Talla_zapato` int(11) DEFAULT NULL,
  `Talla_camisa` varchar(10) DEFAULT NULL,
  `Talla_pantalón` varchar(10) DEFAULT NULL,
  `Tipo_sangre` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `Correo` varchar(100) DEFAULT NULL,
  `Profesión` varchar(100) DEFAULT NULL,
  `Estado` varchar(50) DEFAULT NULL,
  `Municipio` varchar(50) DEFAULT NULL,
  `Parroquia` varchar(50) DEFAULT NULL,
  `Mano_dominante` enum('Derecho','Zurdo','Ambidiestro') NOT NULL,
  `Fecha_egreso` date DEFAULT NULL,
  `Motivo_egreso` text DEFAULT NULL,
  `Estante_archivo` char(1) NOT NULL,
  `Activo` tinyint(1) DEFAULT 1,
  `foto_path` varchar(255) DEFAULT NULL,
  `Fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`Id_trabajador`, `Nacionalidad`, `Cédula`, `Primer_nombre`, `Segundo_nombre`, `Primer_apellido`, `Segundo_apellido`, `Fecha_nacimiento`, `Sexo`, `Estado_civil`, `Tipo_empleado`, `Cargo`, `Fecha_ingreso`, `Ubicación`, `Presupuesto`, `Dirección_habitación`, `Teléfono`, `Código_carnet_santel`, `Cuenta_bancaria`, `Centro`, `Cantidad_hijos`, `Peso`, `Altura`, `Talla_zapato`, `Talla_camisa`, `Talla_pantalón`, `Tipo_sangre`, `Correo`, `Profesión`, `Estado`, `Municipio`, `Parroquia`, `Mano_dominante`, `Fecha_egreso`, `Motivo_egreso`, `Estante_archivo`, `Activo`, `foto_path`, `Fecha_registro`) VALUES
(1, 'Venezolana', '12345678', 'María', 'Alejandra', 'González', 'Pérez', '1985-03-15', 'Femenino', 'Casado', 'LNR', 'Analista de Recursos Humanos', '2020-05-10', 'Oficina Central', '2024-001', 'Av. Principal #123, Urbanización Las Flores', '0414-5551234', 'CS-2024-001', '0102-1234-5678-9012', 'SEDE', 2, 65.50, 1.65, 37, 'M', '38', 'O+', 'maria.gonzalez@empresa.com', 'Ingeniero Industrial', 'Lara', 'Iribarren', 'Concepción', 'Derecho', NULL, NULL, 'A', 1, NULL, '2025-11-06 22:05:11');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carga_familiar`
--
ALTER TABLE `carga_familiar`
  ADD PRIMARY KEY (`Id_familiar`),
  ADD KEY `Cédula_trabajador` (`Cédula_trabajador`);

--
-- Indices de la tabla `estados`
--
ALTER TABLE `estados`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD PRIMARY KEY (`id_municipio`),
  ADD KEY `id_estado` (`id_estado`);

--
-- Indices de la tabla `parroquias`
--
ALTER TABLE `parroquias`
  ADD PRIMARY KEY (`id_parroquia`),
  ADD KEY `id_municipio` (`id_municipio`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`Id_trabajador`),
  ADD UNIQUE KEY `Cédula` (`Cédula`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carga_familiar`
--
ALTER TABLE `carga_familiar`
  MODIFY `Id_familiar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `estados`
--
ALTER TABLE `estados`
  MODIFY `id_estado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `municipios`
--
ALTER TABLE `municipios`
  MODIFY `id_municipio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `parroquias`
--
ALTER TABLE `parroquias`
  MODIFY `id_parroquia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `Id_trabajador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carga_familiar`
--
ALTER TABLE `carga_familiar`
  ADD CONSTRAINT `carga_familiar_ibfk_1` FOREIGN KEY (`Cédula_trabajador`) REFERENCES `trabajadores` (`Cédula`) ON DELETE CASCADE;

--
-- Filtros para la tabla `municipios`
--
ALTER TABLE `municipios`
  ADD CONSTRAINT `municipios_ibfk_1` FOREIGN KEY (`id_estado`) REFERENCES `estados` (`id_estado`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `parroquias`
--
ALTER TABLE `parroquias`
  ADD CONSTRAINT `parroquias_ibfk_1` FOREIGN KEY (`id_municipio`) REFERENCES `municipios` (`id_municipio`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

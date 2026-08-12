-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-08-2026 a las 18:39:57
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
-- Base de datos: `icfes_platform`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `materias`
--

CREATE TABLE `materias` (
  `id_materia` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `materias`
--

INSERT INTO `materias` (`id_materia`, `nombre`, `descripcion`) VALUES
(1, 'Matemáticas', 'Desarrollo del pensamiento matemático, álgebra, geometría, estadística y resolución de problemas.'),
(2, 'Lectura Crítica', 'Comprensión, interpretación y análisis de textos.'),
(3, 'Ciencias Naturales', 'Biología, Física y Química aplicadas al entorno.'),
(4, 'Sociales y Ciudadanas', 'Historia, geografía, constitución política y competencias ciudadanas.'),
(5, 'Inglés', 'Comprensión y uso del idioma inglés según el Marco Común Europeo.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recursos`
--

CREATE TABLE `recursos` (
  `id_recurso` int(11) NOT NULL,
  `id_tema` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `tipo` enum('video','articulo','blog','app','pdf') NOT NULL,
  `url` varchar(500) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `fecha_publicacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `recursos`
--

INSERT INTO `recursos` (`id_recurso`, `id_tema`, `titulo`, `tipo`, `url`, `descripcion`, `imagen`, `fecha_publicacion`) VALUES
(1, 1, 'Introducción al Álgebra', 'video', 'https://www.youtube.com/watch?v=example1', 'Conceptos básicos de álgebra para estudiantes de noveno', NULL, '2026-07-29 14:44:48'),
(2, 1, 'Ejercicios de Álgebra', 'blog', 'https://www.superprof.co/blog/algebra-basica/', 'Ejercicios y explicaciones de álgebra', NULL, '2026-07-29 14:44:48'),
(3, 2, 'Geometría Básica', 'video', 'https://www.youtube.com/watch?v=example2', 'Figuras geométricas y propiedades', NULL, '2026-07-29 14:44:48'),
(4, 2, 'Guía de Geometría', 'pdf', 'https://ejemplo.com/geometria.pdf', 'Material de apoyo para geometría', NULL, '2026-07-29 14:44:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`) VALUES
(1, 'Administrador'),
(2, 'Estudiante');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `temas`
--

CREATE TABLE `temas` (
  `id_tema` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `grado` enum('9','10','11') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `temas`
--

INSERT INTO `temas` (`id_tema`, `id_materia`, `nombre`, `descripcion`, `grado`) VALUES
(1, 1, 'Álgebra', 'Expresiones algebraicas y ecuaciones', '9'),
(2, 1, 'Geometría', 'Figuras geométricas y propiedades', '9'),
(3, 1, 'Estadística', 'Recolección e interpretación de datos', '9'),
(4, 1, 'Funciones', 'Concepto y representación de funciones', '10'),
(5, 1, 'Probabilidad', 'Eventos y cálculo de probabilidades', '10'),
(6, 1, 'Geometría Analítica', 'Plano cartesiano y rectas', '10'),
(7, 1, 'Trigonometría', 'Razones trigonométricas y aplicaciones', '11'),
(8, 1, 'Cálculo Básico', 'Límites y derivadas introductorias', '11'),
(9, 1, 'Razonamiento Cuantitativo', 'Resolución de problemas tipo ICFES', '11'),
(10, 2, 'Comprensión Literal', 'Identificación de información explícita', '9'),
(11, 2, 'Comprensión Inferencial', 'Deducción de información implícita', '9'),
(12, 2, 'Tipos de Texto', 'Narrativo, expositivo y argumentativo', '9'),
(13, 2, 'Análisis de Textos', 'Interpretación y análisis textual', '10'),
(14, 2, 'Estructura Argumentativa', 'Tesis, argumentos y conclusiones', '10'),
(15, 2, 'Lectura Comparativa', 'Comparación entre textos', '10'),
(16, 2, 'Pensamiento Crítico', 'Evaluación crítica de contenidos', '11'),
(17, 2, 'Interpretación Avanzada', 'Análisis complejo de textos', '11'),
(18, 2, 'Competencias ICFES', 'Estrategias para preguntas Saber 11', '11'),
(19, 3, 'Biología Básica', 'Seres vivos y ecosistemas', '9'),
(20, 3, 'Física Básica', 'Movimiento y energía', '9'),
(21, 3, 'Química Básica', 'Materia y sus propiedades', '9'),
(22, 3, 'Genética', 'Herencia y ADN', '10'),
(23, 3, 'Mecánica', 'Leyes del movimiento', '10'),
(24, 3, 'Enlaces Químicos', 'Tipos de enlaces y reacciones', '10'),
(25, 3, 'Ecología', 'Relación entre organismos y ambiente', '11'),
(26, 3, 'Electricidad y Magnetismo', 'Fenómenos eléctricos', '11'),
(27, 3, 'Química Orgánica', 'Compuestos del carbono', '11'),
(28, 4, 'Historia de Colombia', 'Principales procesos históricos', '9'),
(29, 4, 'Geografía de Colombia', 'Relieve, clima y regiones', '9'),
(30, 4, 'Participación Ciudadana', 'Derechos y deberes', '9'),
(31, 4, 'Constitución Política', 'Principios fundamentales', '10'),
(32, 4, 'Democracia y Estado', 'Organización política colombiana', '10'),
(33, 4, 'Globalización', 'Impacto mundial y local', '10'),
(34, 4, 'Competencias Ciudadanas', 'Resolución de conflictos y convivencia', '11'),
(35, 4, 'Conflicto Armado Colombiano', 'Contexto histórico y social', '11'),
(36, 4, 'Análisis Social', 'Problemáticas sociales contemporáneas', '11'),
(37, 5, 'Vocabulary', 'Vocabulario básico', '9'),
(38, 5, 'Grammar Basics', 'Estructuras gramaticales básicas', '9'),
(39, 5, 'Reading Basics', 'Comprensión de textos sencillos', '9'),
(40, 5, 'Intermediate Grammar', 'Tiempos verbales y estructuras', '10'),
(41, 5, 'Reading Comprehension', 'Comprensión de textos intermedios', '10'),
(42, 5, 'Listening Strategies', 'Estrategias de comprensión auditiva', '10'),
(43, 5, 'Advanced Reading', 'Comprensión avanzada de textos', '11'),
(44, 5, 'ICFES English Skills', 'Competencias evaluadas en Saber 11', '11'),
(45, 5, 'Contextual Vocabulary', 'Vocabulario en contexto', '11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `grado` enum('9','10','11') NOT NULL,
  `avatar` varchar(255) DEFAULT 'avatar_default.png',
  `puntos` int(11) DEFAULT 0,
  `nivel` int(11) DEFAULT 1,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `materias`
--
ALTER TABLE `materias`
  ADD PRIMARY KEY (`id_materia`);

--
-- Indices de la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD PRIMARY KEY (`id_recurso`),
  ADD KEY `id_tema` (`id_tema`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `temas`
--
ALTER TABLE `temas`
  ADD PRIMARY KEY (`id_tema`),
  ADD KEY `id_materia` (`id_materia`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `materias`
--
ALTER TABLE `materias`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `recursos`
--
ALTER TABLE `recursos`
  MODIFY `id_recurso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `temas`
--
ALTER TABLE `temas`
  MODIFY `id_tema` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `recursos`
--
ALTER TABLE `recursos`
  ADD CONSTRAINT `recursos_ibfk_1` FOREIGN KEY (`id_tema`) REFERENCES `temas` (`id_tema`) ON DELETE CASCADE;

--
-- Filtros para la tabla `temas`
--
ALTER TABLE `temas`
  ADD CONSTRAINT `temas_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `materias` (`id_materia`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

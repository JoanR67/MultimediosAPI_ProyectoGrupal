-- =====================================================================
--  Sistema de Tickets TI  -  Base de Datos
--  IF7102 - Multimedios (UCR) - Entrega 2 (API PHP + MySQL)
-- =====================================================================
--
--  COMO IMPORTAR (paso a paso):
--    1. Abre el XAMPP Control Panel.
--    2. Dale "Start" a Apache y a MySQL (deben quedar en verde).
--    3. Al lado de MySQL, presiona el boton "Admin" (abre phpMyAdmin).
--    4. En phpMyAdmin, clic en la pestania "Importar" (arriba).
--    5. Clic en "Seleccionar archivo" y elige este archivo:
--         proyectoGrupal_bd.sql
--    6. Baja y presiona "Importar".
--    7. Listo: se crea la base "multimedios_tickets" con sus 9 tablas.
--
--  NOTA: no hay que crear la base a mano; el script ya la crea solo.
-- =====================================================================

DROP DATABASE IF EXISTS multimedios_tickets;
CREATE DATABASE multimedios_tickets CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE multimedios_tickets;

-- Roles de usuario
CREATE TABLE roles (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Usuarios (solicitantes, técnicos y administradores)
CREATE TABLE usuarios (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(120) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id        INT NOT NULL,
    creado_en     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Categorías de los tickets
CREATE TABLE categorias (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(80) NOT NULL UNIQUE
);

-- Prioridades (nivel ayuda a ordenarlas)
CREATE TABLE prioridades (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    nivel  TINYINT NOT NULL DEFAULT 1
);

-- Estados del ciclo de vida del ticket
CREATE TABLE estados (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- Tabla principal: tickets
CREATE TABLE tickets (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    titulo              VARCHAR(150) NOT NULL,
    descripcion         TEXT NOT NULL,
    categoria_id        INT NOT NULL,
    prioridad_id        INT NOT NULL,
    estado_id           INT NOT NULL,
    solicitante_id      INT NOT NULL,
    tecnico_id          INT NULL,
    fecha_creacion      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id)   REFERENCES categorias(id),
    FOREIGN KEY (prioridad_id)   REFERENCES prioridades(id),
    FOREIGN KEY (estado_id)      REFERENCES estados(id),
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id),
    FOREIGN KEY (tecnico_id)     REFERENCES usuarios(id)
);

-- Asignaciones de tickets (quién asignó, a qué técnico y cuándo)
CREATE TABLE asignaciones (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id    INT NOT NULL,
    tecnico_id   INT NOT NULL,
    asignado_por INT NOT NULL,
    fecha        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id)    REFERENCES tickets(id),
    FOREIGN KEY (tecnico_id)   REFERENCES usuarios(id),
    FOREIGN KEY (asignado_por) REFERENCES usuarios(id)
);

-- Comentarios y seguimiento del ticket
CREATE TABLE comentarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id  INT NOT NULL,
    usuario_id INT NOT NULL,
    contenido  TEXT NOT NULL,
    fecha      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id)  REFERENCES tickets(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Historial de cambios del ticket (estados, asignaciones, etc.)
CREATE TABLE historial (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id       INT NOT NULL,
    usuario_id      INT NOT NULL,
    accion          VARCHAR(150) NOT NULL,
    valor_anterior  VARCHAR(150) NULL,
    valor_nuevo     VARCHAR(150) NULL,
    fecha           DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id)  REFERENCES tickets(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

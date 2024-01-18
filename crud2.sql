
CREATE TABLE tipos_producto (
    id INT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL
);

CREATE TABLE productos (
    id INT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    cantidad INT NOT NULL,
    tipo_producto_id INT,
    FOREIGN KEY (tipo_producto_id) REFERENCES tipos_producto(id)
);

INSERT INTO `tipos_producto` (`id`, `nombre`) VALUES ('818', 'Comida'), ('816', 'Ropa'), ('817', 'Bebida');

INSERT INTO `productos` (`id`, `nombre`, `cantidad`, `tipo_producto_id`) VALUES ('915', 'agua', '4', '817'), ('916', 'Playera', '3', '816'), ('917', 'Platillos', '3', '818');
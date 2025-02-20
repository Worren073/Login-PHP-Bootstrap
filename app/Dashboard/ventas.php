<?php
session_start();

// Si la sesión no está activa, redirigir a la página de login
if ($_SESSION["s_username"] === null) {
    header("Location: ../login/index.php");
    exit();
}

// Conectar a la base de datos
include_once '../db/conexion.php';
$objeto = new Conexion();
$conexion = $objeto->Conectar();

// Manejar el formulario de envío para los clientes y las ventas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['guardar'])) {
    $nombre_client = $_POST['nombre_client'];
    $apellido_client = $_POST['apellido_client'];
    $documento = $_POST['documento'];
    $num_telefono = $_POST['num_telefono'];
    $correo = $_POST['correo'];
    $fecha = date('Y-m-d');

    // Verificar si el cliente ya existe
    $consulta_existente = "SELECT * FROM clientes WHERE documento = :documento OR correo = :correo";
    $resultado_existente = $conexion->prepare($consulta_existente);
    $resultado_existente->bindParam(':documento', $documento);
    $resultado_existente->bindParam(':correo', $correo);
    $resultado_existente->execute();
    $cliente_existente = $resultado_existente->fetch(PDO::FETCH_ASSOC);

    if (!$cliente_existente) {
        // Si el cliente no existe, insertar los datos del cliente en la tabla 'clientes'
        $consulta_cliente = "INSERT INTO clientes (nombre_client, apellido_client, documento, num_telefono, correo) 
                             VALUES (:nombre_client, :apellido_client, :documento, :num_telefono, :correo)";
        $resultado_cliente = $conexion->prepare($consulta_cliente);
        $resultado_cliente->bindParam(':nombre_client', $nombre_client);
        $resultado_cliente->bindParam(':apellido_client', $apellido_client);
        $resultado_cliente->bindParam(':documento', $documento);
        $resultado_cliente->bindParam(':num_telefono', $num_telefono);
        $resultado_cliente->bindParam(':correo', $correo);
        $resultado_cliente->execute();
    }

    // Convertir los datos de los productos a formato JSON
    $productos = isset($_POST['productos']) ? $_POST['productos'] : []; // Array de productos
    $productos_json = json_encode($productos);

    // Calcular el precio total de la factura
    $total_factura = 0;
    foreach ($productos as $producto) {
        $total_factura += $producto['precio_total'];
    }

    // Insertar los datos de la venta en la tabla 'facturas'
    $consulta_factura = "INSERT INTO facturas (nombre_client, apellido_client, documento, num_telefono, fecha, productos, precio_total) 
                         VALUES (:nombre_client, :apellido_client, :documento, :num_telefono, :fecha, :productos, :precio_total)";
    $resultado_factura = $conexion->prepare($consulta_factura);
    $resultado_factura->bindParam(':nombre_client', $nombre_client);
    $resultado_factura->bindParam(':apellido_client', $apellido_client);
    $resultado_factura->bindParam(':documento', $documento);
    $resultado_factura->bindParam(':num_telefono', $num_telefono);
    $resultado_factura->bindParam(':fecha', $fecha);
    $resultado_factura->bindParam(':productos', $productos_json);
    $resultado_factura->bindParam(':precio_total', $total_factura);
    $resultado_factura->execute();

    echo "Datos del cliente y de la venta guardados exitosamente.";
}
?>

<?php require_once "vistas/parte_superior.php" ?>

<!--INICIO del cont principal-->
<div class="container">
    <h1>Ventas</h1>

    <body class="sb-nav-fixed">
        <div id="layoutSidenav_content">
            <main>
                <div class="card mb-4">
                    <div class="card-header py-1 bg-success text-light">
                        <i class="fas fa-solid fa-file-invoice-dollar mr-2"></i>
                        Ingrese una venta
                    </div>
                    <div class="card-body">
                        <form method="post" id="ventaForm">
                        <div id="productosContainer">
    <div class="form-row producto">
        <div class="col-md-4">
            <div class="form-group">
                <label class="small mb-1" for="nom_producto">Producto</label>
                <input class="form-control py-3" type="text" name="productos[0][nom_producto]" placeholder="Ingrese el producto" required />
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="small mb-1" for="Descripcion">Descripción</label>
                <input class="form-control py-3" type="text" name="Descripcion" placeholder="Ingrese la Descripción" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="small mb-1" for="Categoria">Categoría</label>
                <select class="custom-select" name="productos[0][categoria]" required>
                    <option value="">Seleccione una opción</option>
                    <option value="ornamental">Ornamental</option>
                    <option value="medicinal">Medicinal</option>
                    <option value="frutal">Frutal</option>
                    <option value="forestal">Forestal</option>
                    <option value="arreglos_florales">Arreglo Floral</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="small mb-1" for="cantidad">Cantidad</label>
                <input class="form-control py-3" type="number" name="productos[0][cantidad]" oninput="calcularTotal(this)" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="small mb-1" for="precio_unidad">Precio Unidad</label>
                <input class="form-control py-3" type="number" step="0.01" name="productos[0][precio_unidad]" oninput="calcularTotal(this)" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="small mb-1" for="precio_total">Precio Total</label>
                <input class="form-control py-3" type="number" step="0.01" name="productos[0][precio_total]" readonly>
            </div>
        </div>
    </div>
</div>
<div class="form-group">
    <button type="button" class="btn btn-primary" onclick="addProducto()">Añadir Producto</button>
    </div>
    <h3>Datos del Cliente</h3>
                            <div class="form-row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="nombre_client">Nombre:</label>
                                        <input type="text" class="form-control" id="nombre_client" name="nombre_client" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="apellido_client">Apellido:</label>
                                        <input type="text" class="form-control" id="apellido_client" name="apellido_client" required>
                                    </div>
                                </div>
                                <div class="col-md4">
                                    <div class="form-group">
                                        <label for="documento">Documento:</label>
                                        <input type="text" class="form-control" id="documento" name="documento" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="num_telefono">Número Telefónico:</label>
                                        <input type="text" class="form-control" id="num_telefono" name="num_telefono" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="correo">Correo:</label>
                                        <input type="text" class="form-control" id="correo" name="correo" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="col-md-4"></div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success btn-block" name="guardar">Generar Factura</button>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <a class="btn btn-danger btn-block" href="ventas.php">Cancelar</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>

        <?php require_once "vistas/parte_inferior.php" ?>
    </body>
    <script>
    function addProducto() {
        const productosContainer = document.getElementById('productosContainer');
        const productoCount = productosContainer.getElementsByClassName('producto').length;

        const newProducto = document.createElement('div');
        newProducto.className = 'form-row producto';
        newProducto.innerHTML = `
        <div class="col-md-4">
            <div class="form-group">
                <label class="small mb-1" for="nom_producto">Producto</label>
                <input class="form-control py-3" type="text" name="productos[${productoCount}][nom_producto]" placeholder="Ingrese el producto" required />
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="small mb-1" for="Descripcion">Descripción</label>
                <input class="form-control py-3" type="text" name="Descripcion" placeholder="Ingrese la Descripción" required />
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="small mb-1" for="Categoria">Categoría</label>
                <select class="custom-select" name="productos[${productoCount}][categoria]" required>
                    <option value="">Seleccione una opción</option>
                    <option value="ornamental">Ornamental</option>
                    <option value="medicinal">Medicinal</option>
                    <option value="frutal">Frutal</option>
                    <option value="forestal">Forestal</option>
                    <option value="arreglos_florales">Arreglo Floral</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="small mb-1" for="cantidad">Cantidad</label>
                <input class="form-control py-3" type="number" name="productos[${productoCount}][cantidad]" oninput="calcularTotal(this)" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="small mb-1" for="precio_unidad">Precio Unidad</label>
                <input class="form-control py-3" type="number" step="0.01" name="productos[${productoCount}][precio_unidad]" oninput="calcularTotal(this)" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="small mb-1" for="precio_total">Precio Total</label>
                <input class="form-control py-3" type="number" step="0.01" name="productos[${productoCount}][precio_total]" readonly>
            </div>
        </div>
    `;
    productosContainer.appendChild(newProducto);
}

function calcularTotal(element) {
    const productoRow = element.closest('.producto');
    const cantidad = productoRow.querySelector('[name*="[cantidad]"]').value;
    const precioUnidad = productoRow.querySelector('[name*="[precio_unidad]"]').value;
    const precioTotal = productoRow.querySelector('[name*="[precio_total]"]');
    
    precioTotal.value = (cantidad * precioUnidad).toFixed(2);
}
</script>
</html>
<?php
/**
 * TAREA 8: INTRODUCCIÓN A PHP (INTERACTIVO CON SESIONES)
 */

// Iniciamos la sesión para poder guardar datos entre recargas de página
session_start();

// 1. VARIABLES Y ESTADOS
$plataforma = "Aprendizaje Brillante";
$fechaActual = date("Y");

// Si es la primera vez que se abre la página, inicializamos la base de datos en la memoria de la sesión
if (!isset($_SESSION['estudiantes'])) {
    $_SESSION['estudiantes'] = [
        ["id" => "ISC-001", "nombre" => "Diego Puebla", "curso" => "Arquitectura Backend", "calificacion" => 95],
        ["id" => "ISC-002", "nombre" => "Evelyn Lozano", "curso" => "Bases de Datos", "calificacion" => 98]
    ];
}

// 2. ESTRUCTURAS DE CONTROL PARA INTERCEPTAR BOTONES (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // Si se presionó el botón de AGREGAR ALUMNO ALEATORIO
    if ($_POST['action'] === 'agregar') {
        $nombres = ["Ana", "Carlos", "Sofia", "Javier", "Lucia", "Andres", "Valeria"];
        $apellidos = ["Gomez", "Perez", "Reyes", "Hernandez", "Martinez", "Lopez"];
        
        $nombreAleatorio = $nombres[array_rand($nombres)] . " " . $apellidos[array_rand($apellidos)];
        $califAleatoria = rand(50, 100);
        $nuevoId = "ISC-00" . (count($_SESSION['estudiantes']) + 1);

        // Empujamos el nuevo registro al final del arreglo
        $_SESSION['estudiantes'][] = [
            "id" => $nuevoId,
            "nombre" => $nombreAleatorio,
            "curso" => "Desarrollo Web Dinámico",
            "calificacion" => $califAleatoria
        ];
    }
    
    // Redirigimos a la misma página para evitar el "Confirmar reenvío de formulario" del navegador
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// 3. FUNCIONES PHP
function evaluarEstado($calificacion) {
    if ($calificacion >= 90) {
        return "<span class='badge badge-success'>Excelente ($calificacion)</span>";
    } elseif ($calificacion >= 75) {
        return "<span class='badge badge-warning'>Regular ($calificacion)</span>";
    } else {
        return "<span class='badge badge-danger'>En Riesgo ($calificacion)</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $plataforma ?> - Panel Estudiantil</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --bg-color: #fcfcfc;
            --text-main: #1a1a1a;
            --text-muted: #666666;
            --accent-green: #596c31; 
            --border-color: #e2e5de;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            margin: 0;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-family: 'Playfair Display', serif; font-size: 45px; margin: 0; }
        .header p { color: var(--accent-green); font-size: 14px; font-weight: 600; text-transform: uppercase; margin-top: 10px;}

        .dashboard-container {
            width: 100%;
            max-width: 900px;
            background-color: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.02);
        }

        /* Botón de Agregar (Estilo Formulario) */
        .btn-add-random {
            background-color: var(--accent-green);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-add-random:hover { opacity: 0.8; }

        .students-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 25px;
        }

        .student-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .student-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; }
        .student-name { font-family: 'Playfair Display', serif; font-size: 20px; font-weight: 600; margin: 0; }
        .student-id { color: var(--text-muted); font-size: 12px; font-family: monospace; }
        
        .student-course { font-size: 14px; color: var(--text-muted); margin-bottom: 20px; }
        .student-course i { margin-right: 5px; color: var(--accent-green); }

        .card-actions {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        /* Etiquetas dinámicas generadas por la función PHP */
        .badge { padding: 4px 10px; border-radius: 50px; font-size: 12px; font-weight: 600; }
        .badge-success { color: #596c31; background: #e9ece4; }
        .badge-warning { color: #e67e22; background: #fdf2e9; }
        .badge-danger { color: #c0392b; background: #fadbd8; }

        footer { margin-top: 50px; font-size: 12px; color: var(--text-muted); }
    </style>
</head>
<body>

    <div class="header">
        <h1><?= $plataforma ?></h1>
        <p>Panel Interactivo - PHP Backend</p>
    </div>

    <div class="dashboard-container">
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 20px;">
            <h2 style="margin: 0; font-size: 18px;">Total Registrados: <?= count($_SESSION['estudiantes']) ?></h2>
            
            <!-- FORMULARIO 1: Botón para agregar aleatorio por POST -->
            <form method="POST" style="margin:0;">
                <input type="hidden" name="action" value="agregar">
                <button type="submit" class="btn-add-random"><i class="fa-solid fa-user-plus"></i> Añadir Aleatorio</button>
            </form>
        </div>

        <div class="students-grid">
            
            <?php
            // 4. CICLO FOREACH PARA RENDERIZAR
            foreach ($_SESSION['estudiantes'] as $estudiante) {
            ?>
                <div class="student-card">
                    <div>
                        <div class="student-header">
                            <h3 class="student-name"><?= $estudiante['nombre'] ?></h3>
                            <span class="student-id"><i class="fa-solid fa-id-badge"></i> <?= $estudiante['id'] ?></span>
                        </div>
                        <div class="student-course">
                            <i class="fa-solid fa-book-open"></i> <?= $estudiante['curso'] ?>
                        </div>
                    </div>
                    
                    <div class="card-actions">
                        <!-- Imprime la etiqueta de calificación alineada a la derecha -->
                        <?= evaluarEstado($estudiante['calificacion']) ?>
                    </div>
                </div>
            <?php 
            } 
            ?>

        </div>
    </div>

    <footer>
        &copy; <?= $fechaActual ?> - Sesiones en PHP activas
    </footer>

</body>
</html>
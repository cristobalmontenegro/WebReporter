<?php
require_once 'config.php';
require_once 'auth.php';
header('Content-Type: application/json');

// Desactivar cualquier salida accidental
error_reporting(0);
ini_set('display_errors', 0);

// Función para limpiar UTF-8 recursivamente
function utf8ize($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) $d[$k] = utf8ize($v);
    } else if (is_string($d)) {
        // Asegurar que sea UTF-8 válido
        return mb_convert_encoding($d, 'UTF-8', 'UTF-8');
    }
    return $d;
}

$report_id = $_GET['id'] ?? '';
$is_export = ($_GET['export'] ?? '') === 'excel';

if (!$report_id || !isset($REPORTS[$report_id])) {
    echo json_encode(['error' => 'Reporte no encontrado.']);
    exit;
}

$report = $REPORTS[$report_id];
$conn = get_db_connection();

if ($is_export || ($_GET['export'] ?? '') === 'print') {
    $export_type = $_GET['export'];
    $searchValue = $_GET['search'] ?? '';
    $base_sql = $report['sql'];
    
    // Apply filters to export
    $filter_sql = "";
    if (!empty($searchValue)) {
        $sample = $conn->query("$base_sql LIMIT 0");
        if ($sample) {
            $fields = $sample->fetch_fields();
            $placeholders = [];
            $types = "";
            $values = [];
            foreach ($fields as $f) {
                $placeholders[] = "`" . $conn->real_escape_string($f->name) . "` LIKE ?";
                $types .= "s";
                $values[] = "%$searchValue%";
            }
            if (!empty($placeholders)) {
                $filter_sql = " WHERE (" . implode(" OR ", $placeholders) . ")";
            }
        }
    }
    
    $final_sql = "SELECT * FROM ($base_sql) as t $filter_sql";
    if (!empty($filter_sql)) {
        $stmt_export = $conn->prepare($final_sql);
        $stmt_export->bind_param($types, ...$values);
        $stmt_export->execute();
        $result = $stmt_export->get_result();
    } else {
        $result = $conn->query($final_sql);
    }
    
    if (!$result) {
        die("Error en exportación: " . $conn->error);
    }
    
    if ($export_type === 'excel') {
        if (isset($report['filename']) && !empty($report['filename'])) {
            $filename = $report['filename'] . ".csv";
        } else {
            $filename = str_replace(' ', '_', $report['title']) . "_" . date('Y-m-d_His') . ".csv";
        }
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM
        $fields = $result->fetch_fields();
        $headers = [];
        foreach ($fields as $f) { $headers[] = $f->name; }
        fputcsv($output, $headers, ";");
        while ($row = $result->fetch_assoc()) { fputcsv($output, array_values($row), ";"); }
        fclose($output);
        exit;
    } else if ($export_type === 'print') {
        header('Content-Type: text/html; charset=utf-8');
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Impresión - " . htmlspecialchars($report['title']) . "</title>";
        echo "<style>
            body { font-family: 'Source Sans Pro', Arial, sans-serif; font-size: 9pt; margin: 0; padding: 20px; color: #333; }
            table { width: 100%; border-collapse: collapse; table-layout: auto; border: 1px solid #000; }
            th, td { border: 1px solid #666; padding: 4px 6px; text-align: left; vertical-align: top; word-break: break-word; }
            th { background-color: #eee !important; font-weight: bold; -webkit-print-color-adjust: exact; }
            .header { margin-bottom: 20px; border-bottom: 3px solid #000; padding-bottom: 10px; overflow: hidden; }
            .header h1 { margin: 0; font-size: 18px; float: left; }
            .header .meta { float: right; text-align: right; font-size: 10pt; }
            @media print { 
                .no-print { display: none; } 
                table { page-break-inside:auto }
                tr    { page-break-inside:avoid; page-break-after:auto }
                thead { display:table-header-group }
            }
        </style></head><body>";
        
        $total_found = $result->num_rows;
        
        echo "<div class='header'><h1>" . htmlspecialchars($report['title']) . "</h1>";
        echo "<div class='meta'>Generado: " . date('Y-m-d H:i') . "<br>Registros: $total_found</div></div>";
        
        echo "<table>";
        $fields = $result->fetch_fields();
        echo "<thead><tr>";
        foreach ($fields as $f) { echo "<th>" . htmlspecialchars($f->name) . "</th>"; }
        echo "</tr></thead><tbody>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            foreach ($row as $v) { echo "<td>" . htmlspecialchars($v) . "</td>"; }
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "</body></html>";
        exit;
    }
}

// Soportar tanto GET como POST por seguridad
$params = array_merge($_GET, $_POST);

$draw = intval($params['draw'] ?? 0);
$start = intval($params['start'] ?? 0);
$length = intval($params['length'] ?? 10);
$searchValue = $params['search']['value'] ?? '';

$base_sql = $report['sql'];

// 1. Conteo Total
$totalRecords = 0;
$count_query = "SELECT COUNT(*) as total FROM ($base_sql) as t_count";
$count_result = $conn->query($count_query);
if ($count_result) {
    $row = $count_result->fetch_assoc();
    $totalRecords = intval($row['total']);
}

// 2. Filtro de búsqueda
$filter_sql = "";
$types = "";
$values = [];
if (!empty($searchValue)) {
    $searchQueries = [];
    $sample = $conn->query("$base_sql LIMIT 0");
    if ($sample) {
        $fields = $sample->fetch_fields();
        foreach ($fields as $f) {
            $searchQueries[] = "`" . $conn->real_escape_string($f->name) . "` LIKE ?";
            $types .= "s";
            $values[] = "%$searchValue%";
        }
        if (!empty($searchQueries)) {
            $filter_sql = " WHERE (" . implode(" OR ", $searchQueries) . ")";
        }
    }
}

// 3. Conteo Filtrado
$totalFiltered = $totalRecords;
if (!empty($filter_sql)) {
    $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM ($base_sql) as t $filter_sql");
    if ($stmt_count) {
        $stmt_count->bind_param($types, ...$values);
        $stmt_count->execute();
        $res_count = $stmt_count->get_result()->fetch_assoc();
        $totalFiltered = intval($res_count['total']);
        $stmt_count->close();
    }
}

// 4. Orden
$order_sql = "";
if (isset($params['order'][0]['column'])) {
    $columnIndex = intval($params['order'][0]['column']);
    $columnDir = ($params['order'][0]['dir'] === 'asc') ? 'ASC' : 'DESC';
    $sample = $conn->query("$base_sql LIMIT 0");
    if ($sample) {
        $fields = $sample->fetch_fields();
        if (isset($fields[$columnIndex])) {
            $safe_column = $conn->real_escape_string($fields[$columnIndex]->name);
            $order_sql = " ORDER BY `$safe_column` $columnDir";
        }
    }
}

// 4. Consulta Final
$final_sql = "SELECT * FROM ($base_sql) as t $filter_sql $order_sql LIMIT ?, ?";

$stmt_final = $conn->prepare($final_sql);
if (!empty($filter_sql)) {
    $bind_types = $types . "ii";
    $bind_values = array_merge($values, [$start, $length]);
    $stmt_final->bind_param($bind_types, ...$bind_values);
} else {
    $stmt_final->bind_param("ii", $start, $length);
}
$stmt_final->execute();
$result = $stmt_final->get_result();
$stmt_final->close();

$data = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        // Limpiar cada valor para asegurar UTF-8 válido
        $clean_row = [];
        foreach ($row as $val) {
            $clean_row[] = $val;
        }
        $data[] = $clean_row;
    }
} else {
    echo json_encode(['error' => 'Error SQL: ' . $conn->error, 'sql' => $final_sql]);
    exit;
}

$response = [
    "draw" => $draw,
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => utf8ize($data) // Limpieza final de UTF-8
];

$json_output = json_encode($response, JSON_PARTIAL_OUTPUT_ON_ERROR);
if ($json_output === false) {
    echo json_encode(['error' => 'JSON Encoding Error: ' . json_last_error_msg()]);
} else {
    echo $json_output;
}
exit;

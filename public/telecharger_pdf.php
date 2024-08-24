<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';
require '../vendor/autoload.php';
use FPDF as GlobalFPDF;

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$acteId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$role = $_SESSION['role'];
$userArrondissementId = $_SESSION['arrondissement_id'];

if (!$acteId) {
    die("Erreur : ID de l'acte non spécifié.");
}

try {
    $sql = "SELECT r.*, a.name as arrondissement_name
            FROM records r
            JOIN arrondissements a ON r.arrondissement_id = a.id
            WHERE r.id = :id";
    if ($role !== 'administrateur') {
        $sql .= " AND r.arrondissement_id = :arrondissement_id";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $acteId, PDO::PARAM_INT);
    if ($role !== 'administrateur') {
        $stmt->bindParam(':arrondissement_id', $userArrondissementId, PDO::PARAM_INT);
    }
    $stmt->execute();
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        die("Erreur : Acte non trouvé ou accès interdit.");
    }

    class PDF extends GlobalFPDF
    {
        function Header()
        {
            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'Détails de l\'Acte', 0, 1, 'C');
            $this->Ln(10);
        }

        function Footer()
        {
            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Acte ID : ' . $record['id'], 0, 1);
    $pdf->Cell(0, 10, 'Type : ' . $record['type'], 0, 1);
    $pdf->Cell(0, 10, 'Arrondissement : ' . $record['arrondissement_name'], 0, 1);
    $pdf->Cell(0, 10, 'Date de création : ' . date('d/m/Y H:i', strtotime($record['created_at'])), 0, 1);
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 14);
    $pdf->Cell(0, 10, 'Contenu de l\'Acte', 0, 1);
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 12);

    // Décoder le contenu JSON
    $content = json_decode($record['body'], true);

    if ($content) {
        foreach ($content as $key => $value) {
            if (is_array($value)) {
                $pdf->Cell(0, 10, ucfirst($key) . ' :', 0, 1);
                foreach ($value as $subKey => $subValue) {
                    $pdf->Cell(10);
                    $pdf->Cell(0, 10, ucfirst($subKey) . ' : ' . $subValue, 0, 1);
                }
            } else {
                $pdf->Cell(0, 10, ucfirst($key) . ' : ' . $value, 0, 1);
            }
        }
    } else {
        $pdf->MultiCell(0, 10, $record['body']);
    }

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Ce document est un extrait officiel de l\'acte d\'état civil.', 0, 1);

    $pdf->Output('D', 'acte_' . $record['id'] . '_' . $record['type'] . '.pdf');

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>
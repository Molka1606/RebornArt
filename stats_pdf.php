<?php
session_start();

require_once __DIR__ . '/../model/config.php';
require_once __DIR__ . '/../vendor/fpdf/fpdf.php';

// 🔐 Sécurité : admin connecté
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    exit('Accès refusé');
}

$pdo = config::getConnexion();

/* ===== STATISTIQUES ===== */

// Total utilisateurs
$totalUsers = $pdo->query("
    SELECT COUNT(*) FROM User WHERE role='user'
")->fetchColumn();

// Actifs
$active = $pdo->query("
    SELECT COUNT(*) FROM User 
    WHERE role='user' AND status='active'
")->fetchColumn();

// Inactifs
$inactive = $pdo->query("
    SELECT COUNT(*) FROM User 
    WHERE role='user' AND status='inactive'
")->fetchColumn();

// Nouveaux ce mois
$newUsers = $pdo->query("
    SELECT COUNT(*) FROM User 
    WHERE role='user'
    AND MONTH(created_at)=MONTH(CURRENT_DATE())
    AND YEAR(created_at)=YEAR(CURRENT_DATE())
")->fetchColumn();

/* ===== PDF ===== */
$pdf = new FPDF();
$pdf->AddPage();

// Titre
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Statistiques Utilisateurs - RebornArt',0,1,'C');
$pdf->Ln(8);

// Contenu
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,10,"Total des utilisateurs : $totalUsers",0,1);
$pdf->Cell(0,10,"Utilisateurs actifs : $active",0,1);
$pdf->Cell(0,10,"Utilisateurs inactifs : $inactive",0,1);
$pdf->Cell(0,10,"Nouveaux utilisateurs ce mois : $newUsers",0,1);

$pdf->Ln(10);
$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,10,"Généré le : ".date('d/m/Y H:i'),0,1,'R');

// Téléchargement
$pdf->Output('D', 'statistiques_rebornart.pdf');
exit;

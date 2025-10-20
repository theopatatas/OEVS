<?php
header('Content-Type: application/json; charset=UTF-8');

include('session.php');
include('dbcon.php');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$after = isset($_GET['after']) ? (int)$_GET['after'] : 0;

$sql = "SELECT history_id, `date`, `action`, `data`, `user`
        FROM history
        WHERE history_id > ?
        ORDER BY history_id DESC
        LIMIT 100";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $after);
$stmt->execute();
$res = $stmt->get_result();

$rows = [];
while ($r = $res->fetch_assoc()) {
  $raw = $r['date'];
  $rows[] = [
    'history_id' => (int)$r['history_id'],
    'date_raw'   => $raw,
    'date_fmt'   => $raw ? date("F j, Y, g:i A", strtotime($raw)) : 'N/A',
    'action'     => h($r['action'] ?? ''),
    'data'       => h($r['data']   ?? ''),
    'user'       => h($r['user']   ?? '')
  ];
}

echo json_encode(['rows'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

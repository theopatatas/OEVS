<?php
// save_voter.php — handles Username/Email, SchoolID/StudentID, MiddleName/Section
// and supplies defaults for Status/Verified/DateVoted/TimeVoted/Room when required.
header('Content-Type: application/json');
require_once 'session.php';
require_once 'dbcon.php';

$out = ['success'=>false,'saved'=>0,'errors'=>[]];
if(!isset($conn) || !($conn instanceof mysqli)){
  echo json_encode(['success'=>false,'message'=>'DB connection not available.']); exit;
}

$table = 'voters'; // change if your table name differs

// ---- load table columns (case-insensitive) ----
$cols = [];
$stmt = $conn->prepare("
  SELECT COLUMN_NAME, IS_NULLABLE, COLUMN_DEFAULT
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?
");
$stmt->bind_param('s', $table);
$stmt->execute();
$res = $stmt->get_result();
while($r = $res->fetch_assoc()){
  $cols[strtolower($r['COLUMN_NAME'])] = $r;
}
$stmt->close();

function hascol($cols, $name){ return isset($cols[strtolower($name)]); }
function actual($cols, $name){ return $cols[strtolower($name)]['COLUMN_NAME']; }
function colname($cols, $prefer, $alt=null){
  if (hascol($cols,$prefer)) return actual($cols,$prefer);
  if ($alt && hascol($cols,$alt)) return actual($cols,$alt);
  return null;
}
function needs_value($cols, $name){
  $k = strtolower($name);
  if (!isset($cols[$k])) return false;
  return $cols[$k]['IS_NULLABLE']==='NO' && $cols[$k]['COLUMN_DEFAULT']===null;
}

// required base columns
foreach (['FirstName','LastName','Year','Password'] as $c){
  if (!hascol($cols,$c)){
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>"Missing required column `$c` in `$table`."]); exit;
  }
}

// actual names in DB (from your screenshot these exist)
$colFirst  = actual($cols,'FirstName');
$colLast   = actual($cols,'LastName');
$colYear   = actual($cols,'Year');
$colPass   = actual($cols,'Password');
$colMiddle = colname($cols,'MiddleName','Section');         // optional
$colEmail  = colname($cols,'Email','UserName');             // table has Email
$colUser   = colname($cols,'Username','UserName');          // table has Username (NOT NULL)
$colSchool = colname($cols,'SchoolID','StudentID');         // table has SchoolID

// optional columns that might be NOT NULL without defaults
$colStatus    = colname($cols,'Status',null);
$colVerified  = colname($cols,'Verified',null);
$colDateVoted = colname($cols,'DateVoted',null);
$colTimeVoted = colname($cols,'TimeVoted',null);
$colRoom      = colname($cols,'Room',null);

if (!$colEmail && !$colUser){
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>"Need an Email/Username column in `$table`."]); exit;
}
if (!$colSchool){
  http_response_code(500);
  echo json_encode(['success'=>false,'message'=>"Need a SchoolID/StudentID column in `$table`."]); exit;
}

// ---- POST arrays from form (UI names) ----
$FN  = $_POST['FirstName'] ?? [];
$LN  = $_POST['LastName']  ?? [];
$MI  = $_POST['Section']   ?? [];  // "Middle Name" field name
$YR  = $_POST['Year']      ?? [];
$EM  = $_POST['UserName']  ?? [];  // form uses UserName[] for email input
$SID = $_POST['SchoolID']  ?? ($_POST['StudentID'] ?? []);
$PW  = $_POST['Password']  ?? [];

if (!is_array($FN) || !count($FN)){
  echo json_encode(['success'=>false,'message'=>'No voter data received.']); exit;
}

// ---- validate rows ----
$rows = [];
$N = max(count($FN),count($LN),count($MI),count($YR),count($EM),count($SID),count($PW));
for ($i=0; $i<$N; $i++){
  $fn  = trim($FN[$i]  ?? '');
  $ln  = trim($LN[$i]  ?? '');
  $mi  = trim($MI[$i]  ?? '');
  $yr  = trim($YR[$i]  ?? '');
  $em  = trim($EM[$i]  ?? '');
  $sid = trim($SID[$i] ?? '');
  $pw  = trim($PW[$i]  ?? '');

  if ($fn===''||$ln===''||$yr===''||$em===''||$sid===''||$pw===''){
    $out['errors'][] = "Row ".($i+1).": missing required fields."; continue;
  }
  if (!filter_var($em, FILTER_VALIDATE_EMAIL)){
    $out['errors'][] = "Row ".($i+1).": invalid email ($em)."; continue;
  }
  $rows[] = compact('fn','ln','mi','yr','em','sid','pw');
}
if (empty($rows)){
  echo json_encode(['success'=>false,'message'=>'All rows invalid.','errors'=>$out['errors']]); exit;
}

// ---- duplicate check (Username/Email OR SchoolID) ----
$dupWheres = [];
$dupTypes  = '';
if ($colUser)      { $dupWheres[] = "`$colUser` = ?";  $dupTypes.='s'; }
else if ($colEmail){ $dupWheres[] = "`$colEmail` = ?"; $dupTypes.='s'; }
$dupWheres[] = "`$colSchool` = ?"; $dupTypes.='s';

$dupSql = "SELECT COUNT(*) FROM `$table` WHERE ".implode(' OR ', $dupWheres);
$dup = $conn->prepare($dupSql);
if (!$dup){ http_response_code(500); echo json_encode(['success'=>false,'message'=>$conn->error]); exit; }

// ---- build INSERT list dynamically ----
$insertCols = [$colFirst,$colLast,$colYear,$colPass,$colSchool]; // always
if ($colMiddle) $insertCols[] = $colMiddle;
// include Email/Username
if ($colEmail) $insertCols[] = $colEmail;
if ($colUser && $colUser !== $colEmail){
  if (!in_array($colUser,$insertCols,true)) $insertCols[] = $colUser; // set = email too
}

// include required extra columns with safe defaults
$includeStatus    = ($colStatus    && needs_value($cols,$colStatus));
$includeVerified  = ($colVerified  && needs_value($cols,$colVerified));
$includeDateVoted = ($colDateVoted && needs_value($cols,$colDateVoted));
$includeTimeVoted = ($colTimeVoted && needs_value($cols,$colTimeVoted));
$includeRoom      = ($colRoom      && needs_value($cols,$colRoom));

if ($includeStatus)    $insertCols[] = $colStatus;
if ($includeVerified)  $insertCols[] = $colVerified;
if ($includeDateVoted) $insertCols[] = $colDateVoted;
if ($includeTimeVoted) $insertCols[] = $colTimeVoted;
if ($includeRoom)      $insertCols[] = $colRoom;

$placeholders = implode(',', array_fill(0, count($insertCols), '?'));
$insSql = "INSERT INTO `$table` (`".implode('`,`', $insertCols)."`) VALUES ($placeholders)";
$ins = $conn->prepare($insSql);
if (!$ins){ http_response_code(500); echo json_encode(['success'=>false,'message'=>$conn->error]); exit; }

try{
  $conn->begin_transaction();
  $saved = 0;

  foreach ($rows as $i=>$r){
    // duplicates
    $dupVals = [];
    if ($colUser)      $dupVals[] = $r['em'];
    else if ($colEmail)$dupVals[] = $r['em'];
    $dupVals[] = $r['sid'];
    $dup->bind_param($dupTypes, ...$dupVals);
    if (!$dup->execute()) throw new Exception($dup->error);
    $dup->bind_result($cnt); $dup->fetch(); $dup->free_result();
    if ($cnt > 0){ $out['errors'][] = "Row ".($i+1).": duplicate email/username or student ID."; continue; }

    // assemble values for INSERT in same order as $insertCols
    $vals = [];
    foreach ($insertCols as $c){
      $lc = strtolower($c);
      if ($lc === strtolower($colFirst))   { $vals[] = $r['fn'];  continue; }
      if ($lc === strtolower($colLast))    { $vals[] = $r['ln'];  continue; }
      if ($lc === strtolower($colYear))    { $vals[] = $r['yr'];  continue; }
      if ($lc === strtolower($colPass))    { $vals[] = $r['pw'];  continue; } // plain to match your table
      if ($lc === strtolower($colSchool))  { $vals[] = $r['sid']; continue; }
      if ($colMiddle && $lc === strtolower($colMiddle)) { $vals[] = $r['mi']; continue; }
      if ($colEmail  && $lc === strtolower($colEmail))  { $vals[] = $r['em']; continue; }
      if ($colUser   && $lc === strtolower($colUser))   { $vals[] = $r['em']; continue; } // username = email

      // defaults for extra NOT NULL columns
      if ($includeStatus    && $lc === strtolower($colStatus))    { $vals[] = 'Unvoted';     continue; }
      if ($includeVerified  && $lc === strtolower($colVerified))  { $vals[] = 'Unverified';  continue; }
      if ($includeDateVoted && $lc === strtolower($colDateVoted)) { $vals[] = '1970-01-01';  continue; }
      if ($includeTimeVoted && $lc === strtolower($colTimeVoted)) { $vals[] = '00:00:00';    continue; }
      if ($includeRoom      && $lc === strtolower($colRoom))      { $vals[] = '';            continue; }

      $vals[] = null;
    }
    $types = str_repeat('s', count($vals));
    $ins->bind_param($types, ...$vals);

    if (!$ins->execute()){
      $out['errors'][] = "Row ".($i+1).": insert failed (".$ins->error.").";
      continue;
    }
    $saved++;
  }

  $conn->commit();
  $out['success'] = $saved > 0;
  $out['saved']   = $saved;
  $out['message'] = $saved ? "Saved $saved voter(s)." : "No voters saved.";
  echo json_encode($out);

} catch (Throwable $e){
  $conn->rollback();
  http_response_code(500);
  echo json_encode([
    'success'=>false,
    'message'=>'Server error while saving voters.',
    'error'=>$e->getMessage(),
    'errors'=>$out['errors']
  ]);
}

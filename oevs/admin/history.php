<?php
include('session.php');
include('dbcon.php');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Pull history (latest first)
$history = [];
$q = mysqli_query($conn, "SELECT history_id, `date`, `action`, `data`, `user` FROM history ORDER BY `date` DESC, history_id DESC");
while ($q && ($r = mysqli_fetch_assoc($q))) { $history[] = $r; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>History | Online Election Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Remove if header.php already loads them -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff;
      --shadow:0 6px 18px rgba(0,0,0,.08); --muted:#6b7280; --radius:14px;
      --ink:#0b1324; --ink-soft:#2a3a52;
    }
    *{box-sizing:border-box}
    body{margin:0; font-family:Inter,system-ui,Segoe UI,Roboto,Arial; background:var(--bg); color:var(--ink)}

    .container{max-width:1280px; margin:22px auto; padding:0 16px}

    /* Toolbar */
    .toolbar{display:flex; gap:10px; flex-wrap:wrap; align-items:end; margin-bottom:12px}
    .field{display:flex; flex-direction:column; gap:6px}
    .field label{font-size:12px; font-weight:700; color:var(--muted)}

    .input, select{
      appearance:none; padding:10px 12px; border:1px solid #d6e0ef; background:#fff; color:var(--ink-soft);
      border-radius:10px; outline:none; min-width:220px; height:44px;
    }

    /* Search styled exactly like other inputs */
    .searchbox{position:relative}
    .searchbox i{
      position:absolute; left:12px; top:50%; transform:translateY(-50%);
      font-size:15px; color:#8292a6; opacity:.95; pointer-events:none; width:18px; height:18px;
    }
    .searchbox input{
      height:44px; padding:10px 12px; padding-left:44px; /* room for icon */
      border:1px solid #d6e0ef; background:#fff; color:var(--ink-soft);
      border-radius:10px; outline:none; min-width:220px; box-sizing:border-box;
    }

    .btn{
      display:inline-flex; align-items:center; gap:8px; padding:10px 14px; background:#fff; color:var(--primary);
      border:1px solid #d6e0ef; border-radius:10px; cursor:pointer; text-decoration:none; font-weight:700; height:44px;
    }
    .btn:hover{background:#f6f9ff}
    .btn.primary{background:var(--primary); color:#fff; border-color:var(--primary)}
    .btn.primary:hover{filter:brightness(.98)}

    /* Card + Table */
    .card{background:#fff; border:1px solid #e8eef7; border-radius:var(--radius); box-shadow:var(--shadow)}
    .card-h{padding:14px 16px; border-bottom:1px solid #eef2f8; display:flex; align-items:center; justify-content:space-between}
    .card-h h2{margin:0; font-size:18px; color:#000}
    .card-b{padding:0}
    .table-wrap{overflow:auto}
    table{width:100%; border-collapse:collapse; min-width:860px}
    thead{background:linear-gradient(135deg,var(--primary),var(--accent))}
    th{color:#fff; text-align:left; padding:13px; font-size:13px; font-weight:700; white-space:nowrap}
    td{padding:13px; border-bottom:1px solid #f0f3f8; font-size:14px; color:var(--ink-soft); vertical-align:top}
    tr:hover td{background:#fbfdff}
    .empty{padding:20px; text-align:center; color:#666}

    .meta{display:flex; gap:8px; align-items:center; color:#334e7b}
    .meta .dot{width:6px; height:6px; background:#9ab4d6; border-radius:50%}

    @media (max-width:720px){
      .input, select{min-width:unset}
      .toolbar{align-items:stretch}
    }
  </style>
</head>
<body>

  <?php
    $activePage = 'history';
    include 'header.php';
  ?>

  <div class="container">
    <!-- Filters / Tools -->
    <div class="toolbar">
      <div class="field searchbox">
        <label for="q">Search</label>
        <i class="fa fa-search"></i>
        <input id="q" type="text" class="input" placeholder="Find by action, data, or user…">
      </div>

      <div class="field">
        <label for="from">From</label>
        <input id="from" type="date" class="input">
      </div>

      <div class="field">
        <label for="to">To</label>
        <input id="to" type="date" class="input">
      </div>

      <!-- Month filter (replaces Sort) -->
      <div class="field">
        <label for="monthSel">Month</label>
        <select id="monthSel" class="input" style="min-width:180px">
          <option value="">All months</option>
          <option value="1">January</option>
          <option value="2">February</option>
          <option value="3">March</option>
          <option value="4">April</option>
          <option value="5">May</option>
          <option value="6">June</option>
          <option value="7">July</option>
          <option value="8">August</option>
          <option value="9">September</option>
          <option value="10">October</option>
          <option value="11">November</option>
          <option value="12">December</option>
        </select>
      </div>

      <div class="field">
        <label>&nbsp;</label>
        <button type="button" id="applyDate" class="btn"><i class="fa fa-filter"></i> Filter</button>
      </div>

      <div class="field">
        <label>&nbsp;</label>
        <button type="button" id="clearFilters" class="btn"><i class="fa fa-rotate-left"></i> Reset</button>
      </div>

      <div class="field" style="margin-left:auto">
        <label>&nbsp;</label>
        <button type="button" id="exportCsv" class="btn primary"><i class="fa fa-file-export"></i> Export CSV</button>
      </div>
    </div>

    <!-- History Table -->
    <div class="card">
      <div class="card-h">
        <h2>Activity History</h2>
        <div class="meta">
          <span id="rowCount"><?php echo count($history); ?></span> rows
          <span class="dot"></span>
          <span>Newest first</span>
        </div>
      </div>
      <div class="card-b">
        <div class="table-wrap">
          <table id="historyTable">
            <thead>
              <tr>
                <th style="width:220px">Date</th>
                <th>Action</th>
                <th>Data</th>
                <th style="width:220px">User</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$history): ?>
                <tr><td colspan="4" class="empty">No history yet.</td></tr>
              <?php else: ?>
                <?php foreach ($history as $r):
                  $raw   = $r['date'];
                  $dateF = $raw ? date("F j, Y, g:i A", strtotime($raw)) : 'N/A';
                ?>
                  <tr data-date="<?php echo h($raw ?: ''); ?>">
                    <td><?php echo h($dateF); ?></td>
                    <td><?php echo h($r['action'] ?? ''); ?></td>
                    <td><?php echo h($r['data'] ?? ''); ?></td>
                    <td><?php echo h($r['user'] ?? ''); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div style="height:14px"></div>
    <footer style="text-align:center; color:#667; font-size:14px;">© 2025 Online Election Voting System</footer>
  </div>

<script>
(function(){
  const qInput    = document.getElementById('q');
  const from      = document.getElementById('from');
  const to        = document.getElementById('to');
  const monthSel  = document.getElementById('monthSel');
  const applyBtn  = document.getElementById('applyDate');    // type="button"
  const clearBtn  = document.getElementById('clearFilters'); // type="button"
  const table     = document.getElementById('historyTable');
  const rows      = Array.from(table.querySelectorAll('tbody tr'));
  const rowCount  = document.getElementById('rowCount');
  const exportBtn = document.getElementById('exportCsv');

  // Parse DB date stored in data-date
  function parseDate(d){
    const t = Date.parse(d);
    return isNaN(t) ? null : new Date(t);
  }

  // Inclusive range check
  function withinRange(d, fromV, toV){
    if (!d) return false; // rows without a date won't pass a date-range filter
    if (fromV && d < fromV) return false;
    if (toV){
      const end = new Date(toV);
      end.setHours(23,59,59,999); // include whole "to" day
      if (d > end) return false;
    }
    return true;
  }

  // Date window explicitly applied by user
  let appliedFrom = null;
  let appliedTo   = null;

  function filterRows(){
    const term = (qInput.value || '').trim().toLowerCase();
    const monthPick = monthSel.value ? parseInt(monthSel.value, 10) : null; // 1..12 or null

    let shown = 0;
    rows.forEach(tr=>{
      const txt  = tr.innerText.toLowerCase();
      const okQ  = term === '' || txt.includes(term);

      const raw  = tr.getAttribute('data-date') || '';
      const d    = parseDate(raw);

      // Month filter (optional)
      const okM  = !monthPick || (d && (d.getMonth()+1) === monthPick);

      // Date range filter (only if user pressed Filter)
      const okD  = (!appliedFrom && !appliedTo) ? true : withinRange(d, appliedFrom, appliedTo);

      const ok   = okQ && okM && okD;
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });

    rowCount.textContent = shown;
  }

  // Live search + month change always re-filter
  qInput.addEventListener('input', filterRows);
  monthSel.addEventListener('change', filterRows);

  // Pressing Filter locks in the selected date range and applies it
  applyBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    appliedFrom = from.value ? new Date(from.value + 'T00:00:00') : null;
    appliedTo   = to.value   ? new Date(to.value   + 'T00:00:00') : null;
    filterRows();
  });

  // Reset clears everything (including applied date window)
  clearBtn.addEventListener('click', ()=>{
    qInput.value = '';
    from.value   = '';
    to.value     = '';
    monthSel.value = '';
    appliedFrom = null;
    appliedTo   = null;
    filterRows();
  });

  // Export only visible rows
  exportBtn.addEventListener('click', ()=>{
    const visible = rows.filter(tr => tr.style.display !== 'none');
    if (!visible.length) return;

    const header = Array.from(table.tHead.rows[0].cells).map(th => th.innerText.trim());
    const data = visible.map(tr => Array.from(tr.cells).map(td => td.innerText.replaceAll('\n',' ').trim()));

    const csv = [header, ...data].map(r => r.map(v => `"${v.replaceAll('"','""')}"`).join(',')).join('\r\n');
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url; a.download = 'history_export.csv';
    document.body.appendChild(a); a.click(); a.remove();
    URL.revokeObjectURL(url);
  });

  // Initial render (no date window applied yet)
  filterRows();
})();
</script>
</body>
</html>

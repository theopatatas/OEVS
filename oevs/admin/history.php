<?php
include('session.php');
include('dbcon.php');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Pull history (newest first) using primary key for true recency
$history = [];
$q = mysqli_query(
  $conn,
  "SELECT history_id, `date`, `action`, `data`, `user`
   FROM history
   ORDER BY history_id DESC"
);
while ($q && ($r = mysqli_fetch_assoc($q))) { $history[] = $r; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>History | Online Election Voting System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <style>
    :root{
      --primary:#002f6c; --accent:#0056b3; --bg:#f4f6f8; --white:#fff;
      --shadow:0 6px 18px rgba(0,0,0,.08); --muted:#6b7280; --radius:14px;
      --ink:#0b1324; --ink-soft:#2a3a52; --danger:#c62828; --danger-bg:#fff1f1;
    }
    *{box-sizing:border-box}
    body{margin:0; font-family:Inter,system-ui,Segoe UI,Roboto,Arial; background:var(--bg); color:var(--ink)}

    .container{max-width:1280px; margin:22px auto; padding:0 16px}

    .toolbar{display:flex; gap:10px; flex-wrap:wrap; align-items:end; margin-bottom:12px}
    .field{display:flex; flex-direction:column; gap:6px}
    .field label{font-size:12px; font-weight:700; color:var(--muted)}

    .input, select{
      appearance:none; padding:10px 12px; border:1px solid #d6e0ef; background:#fff; color:var(--ink-soft);
      border-radius:10px; outline:none; min-width:220px; height:44px;
    }

    .searchbox{position:relative}
    .searchbox i{
      position:absolute; left:12px; top:50%; transform:translateY(-50%);
      font-size:15px; color:#8292a6; opacity:.95; pointer-events:none; width:18px; height:18px;
    }
    .searchbox input{
      height:44px; padding:10px 12px; padding-left:44px;
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

    /* New: deletion highlight */
    .is-deleted td{color:var(--danger)}
    .flash{
      animation: flashIn 2.2s ease-out 1;
      background:var(--danger-bg);
    }
    @keyframes flashIn{
      0%{background:#ffd6d6}
      60%{background:var(--danger-bg)}
      100%{background:transparent}
    }

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

      <div class="field">
        <label for="monthSel">Month</label>
        <select id="monthSel" class="input" style="min-width:180px">
          <option value="">All months</option>
          <option value="1">January</option><option value="2">February</option><option value="3">March</option>
          <option value="4">April</option><option value="5">May</option><option value="6">June</option>
          <option value="7">July</option><option value="8">August</option><option value="9">September</option>
          <option value="10">October</option><option value="11">November</option><option value="12">December</option>
        </select>
      </div>

      <div class="field"><label>&nbsp;</label>
        <button type="button" id="applyDate" class="btn"><i class="fa fa-filter"></i> Filter</button>
      </div>

      <div class="field"><label>&nbsp;</label>
        <button type="button" id="clearFilters" class="btn"><i class="fa fa-rotate-left"></i> Reset</button>
      </div>

      <div class="field" style="margin-left:auto"><label>&nbsp;</label>
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
            <tbody id="historyTbody">
              <?php if (!$history): ?>
                <tr><td colspan="4" class="empty">No history yet.</td></tr>
              <?php else: ?>
                <?php foreach ($history as $r):
                  $raw   = $r['date'];
                  $dateF = $raw ? date("F j, Y, g:i A", strtotime($raw)) : 'N/A';
                ?>
                  <tr data-id="<?php echo (int)$r['history_id']; ?>"
                      data-date="<?php echo h($raw ?: ''); ?>">
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
  const applyBtn  = document.getElementById('applyDate');
  const clearBtn  = document.getElementById('clearFilters');
  const table     = document.getElementById('historyTable');
  const tbody     = document.getElementById('historyTbody');
  let   rows      = Array.from(tbody.querySelectorAll('tr'));
  const rowCount  = document.getElementById('rowCount');
  const exportBtn = document.getElementById('exportCsv');

  function parseDate(d){ const t = Date.parse(d); return isNaN(t) ? null : new Date(t); }
  function withinRange(d, fromV, toV){
    if (!d) return false;
    if (fromV && d < fromV) return false;
    if (toV){ const end = new Date(toV); end.setHours(23,59,59,999); if (d > end) return false; }
    return true;
  }

  let appliedFrom = null, appliedTo = null;

  function filterRows(){
    const term = (qInput.value || '').trim().toLowerCase();
    const monthPick = monthSel.value ? parseInt(monthSel.value, 10) : null;

    let shown = 0;
    rows.forEach(tr=>{
      if (tr.classList.contains('removed')) return;
      const txt  = tr.innerText.toLowerCase();
      const okQ  = term === '' || txt.includes(term);

      const raw  = tr.getAttribute('data-date') || '';
      const d    = parseDate(raw);
      const okM  = !monthPick || (d && (d.getMonth()+1) === monthPick);
      const okD  = (!appliedFrom && !appliedTo) ? true : withinRange(d, appliedFrom, appliedTo);

      const ok   = okQ && okM && okD;
      tr.style.display = ok ? '' : 'none';
      if (ok) shown++;
    });
    rowCount.textContent = shown;
  }

  qInput.addEventListener('input', filterRows);
  monthSel.addEventListener('change', filterRows);

  applyBtn.addEventListener('click', (e)=>{
    e.preventDefault();
    appliedFrom = from.value ? new Date(from.value + 'T00:00:00') : null;
    appliedTo   = to.value   ? new Date(to.value   + 'T00:00:00') : null;
    filterRows();
  });

  clearBtn.addEventListener('click', ()=>{
    qInput.value = ''; from.value = ''; to.value = ''; monthSel.value = '';
    appliedFrom = null; appliedTo = null;
    filterRows();
  });

  exportBtn.addEventListener('click', ()=>{
    const header = Array.from(table.tHead.rows[0].cells).map(th => th.innerText.trim());
    const visible = rows.filter(tr => tr.style.display !== 'none');
    if (!visible.length) return;
    const data = visible.map(tr => Array.from(tr.cells).map(td => td.innerText.replaceAll('\n',' ').trim()));
    const csv = [header, ...data].map(r => r.map(v => `"${v.replaceAll('"','""')}"`).join(',')).join('\r\n');
    const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a'); a.href=url; a.download='history_export.csv';
    document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
  });

  // ---------- Real-time polling ----------
  function getLastId(){
    let max = 0;
    rows.forEach(tr => { const id = parseInt(tr.getAttribute('data-id')||'0',10); if (id>max) max=id; });
    return max;
  }
  let lastId = getLastId();

  function makeRow(r){
    const tr = document.createElement('tr');
    tr.setAttribute('data-id', r.history_id);
    tr.setAttribute('data-date', r.date_raw || '');

    // mark deleted events
    const isDeleted = /^deleted\s+voter/i.test(r.action);
    if (isDeleted) tr.classList.add('is-deleted','flash');

    tr.innerHTML = `
      <td>${r.date_fmt}</td>
      <td>${r.action}</td>
      <td>${r.data}</td>
      <td>${r.user}</td>
    `;
    // remove flash bg after a few seconds
    if (isDeleted) setTimeout(()=>tr.classList.remove('flash'), 2300);
    return tr;
  }

  async function poll(){
    try{
      const res = await fetch('history_feed.php?after=' + encodeURIComponent(lastId), {cache:'no-store'});
      if (!res.ok) return;
      const payload = await res.json(); // {rows:[...]}
      if (!payload || !Array.isArray(payload.rows) || !payload.rows.length) return;

      // Prepend newest-first
      payload.rows.forEach(item=>{
        const tr = makeRow(item);
        tbody.insertBefore(tr, tbody.firstChild);
        rows.unshift(tr);
        lastId = Math.max(lastId, parseInt(item.history_id,10));
      });

      filterRows(); // keep filters respected
    }catch(e){ /* ignore transient errors */ }
  }

  // poll every 5s
  setInterval(poll, 5000);

  // Initial paint
  filterRows();
})();
</script>
</body>
</html>

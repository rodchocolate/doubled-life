<?php
// Guard: only render when included by the authed index (never on direct hit).
if (!isset($subjects)) { http_response_code(404); exit; }
// doubled.life authed console — TWO index shells over the same live data:
//   Napster = DESKTOP (window chrome, sortable transfer-list grid, tall rows).
//   ESPN    = MOBILE  (editorial single column, tall rows, no rail).
// A desktop/mobile toggle switches between them, persists in localStorage, and
// defaults by screen width (<=820px -> mobile). Every view carries the same
// Library / Videos / Queue / Notes tabs. All view CSS is scoped under
// #view-napster / #view-espn so the two designs' overlapping class names
// (.art .vgrid .vt .pane .play table …) do not collide. Included by index.php.

// Queue pane: a table whose tbody (.dlq-body) is filled client-side from
// gate_status.php. Notes pane: a POST form; index.php persists $_POST['note']
// and flips $note_ok.
function dl_queue_html() {
  return '<table class="dlq"><thead><tr><th style="width:46%">Item</th><th>Status</th><th>Detail</th></tr></thead>'
       . '<tbody class="dlq-body"><tr><td colspan="3" style="opacity:.5">loading…</td></tr></tbody></table>';
}
function dl_notes_html($note_ok) {
  return '<form method="post" class="dlnotes"><textarea name="note" placeholder="a note for hermes — recorded for planning alongside the research corpus"></textarea>'
       . '<button type="submit">send to hermes</button><div class="hint">'
       . ($note_ok ? 'sent — hermes will log it' : 'goes into hermes planning') . '</div></form>';
}
?>
<style>
  /* ===== shared: toggle + queue + notes (dl- prefixed, no collision) ===== */
  .dltoggle { display:flex; gap:0; max-width:1080px; margin:0 auto; padding:14px 20px 0; }
  .dltoggle button { background:var(--panel); border:1px solid var(--line); color:var(--text);
    font-family:'Space Grotesk',sans-serif; font-size:.72rem; text-transform:lowercase; letter-spacing:.06em;
    padding:.45em 1.1em; cursor:pointer; opacity:.55; border-right:none; }
  .dltoggle button:last-child { border-right:1px solid var(--line); }
  .dltoggle button.active { opacity:1; color:var(--link); }
  .dlview { display:none; } .dlview.active { display:block; }
  .dlrow-hidden { display:none !important; }
  .dltoggle { display:none; }   /* ESPN-only for now — re-enable to restore the desktop/mobile toggle */

  .dlq { width:100%; border-collapse:collapse; font-size:12px; }
  .dlq th { background:rgba(255,252,249,.05); border-bottom:1px solid var(--line); padding:6px 10px;
    text-align:left; font-size:10px; text-transform:uppercase; color:var(--porcelain); }
  .dlq td { border-bottom:1px solid var(--line-soft); padding:6px 10px; color:var(--text); }
  .dlq a { color:var(--porcelain); text-decoration:underline; }
  .dlnotes { padding:12px; }
  .dlnotes textarea { width:100%; min-height:8rem; background:var(--bg); color:var(--porcelain);
    border:1px solid var(--line); padding:.8rem; font:12px/1.5 inherit; }
  .dlnotes button { margin-top:.6rem; background:var(--link); color:var(--porcelain); border:1px solid var(--line);
    padding:6px 16px; font:11px inherit; cursor:pointer; }
  .dlnotes .hint { font-size:.78rem; opacity:.5; margin-top:.5rem; color:var(--text); }

  /* =====================================================================
     NAPSTER — DESKTOP  (window chrome, 3 tall rows fill the viewport,
     sortable columns + horizontal scroll to detail columns)
     ===================================================================== */
  #view-napster { --chrome:210px; --row:calc((100vh - var(--chrome)) / 3);
    color:#d8d8d8; font:12px/1.5 Tahoma,"MS Sans Serif",Verdana,sans-serif; padding:8px 20px 20px; }
  #view-napster a { color:inherit; text-decoration:none; }
  #view-napster .win { max-width:1080px; margin:0 auto; background:var(--bg-deep); border:1px solid var(--line); }
  #view-napster .titlebar { background:linear-gradient(90deg,var(--link),var(--visited)); color:var(--porcelain);
    padding:4px 8px; font-weight:bold; display:flex; justify-content:space-between; align-items:center; }
  #view-napster .titlebar .wbtns span { display:inline-block; width:16px; height:14px; margin-left:3px;
    border:1px solid rgba(0,0,0,.4); background:rgba(255,255,255,.25); text-align:center; line-height:12px; font-size:10px; color:#111; }
  #view-napster .menubar { background:var(--panel); color:var(--porcelain); font-size:11px; padding:2px 6px; border-bottom:1px solid var(--line); }
  #view-napster .menubar span { padding:2px 8px; opacity:.8; }
  #view-napster .toolbar { background:var(--panel); padding:5px 6px; display:flex; gap:4px; align-items:center; border-bottom:1px solid var(--line); }
  #view-napster .toolbar button { font:11px Tahoma; padding:3px 14px; background:var(--bg-slot); color:var(--text);
    border:1px solid var(--line); cursor:pointer; opacity:.7; }
  #view-napster .toolbar button.down { background:var(--link); color:var(--porcelain); opacity:1; }
  #view-napster .toolbar .tbsearch { margin-left:auto; background:var(--bg); border:1px solid var(--line); color:var(--porcelain);
    font:11px Tahoma; padding:3px 10px; width:12rem; }
  #view-napster .well { background:#12283c; margin:6px; border:2px inset var(--line); }
  #view-napster .pane { display:none; } #view-napster .pane.active { display:block; }
  #view-napster .scroll { max-height:calc(var(--row) * 3 + 26px); overflow:auto; }
  #view-napster .scroll::-webkit-scrollbar { width:14px; height:14px; background:var(--panel); }
  #view-napster .scroll::-webkit-scrollbar-thumb { background:var(--line); border:2px solid #12283c; }
  #view-napster table { border-collapse:collapse; font-size:11px; min-width:1520px; }
  #view-napster thead th { background:var(--panel); color:var(--porcelain); border:2px outset var(--line); padding:3px 8px;
    text-align:left; font-weight:normal; white-space:nowrap; position:sticky; top:0; z-index:1; cursor:pointer; user-select:none; }
  #view-napster thead th.sort::after { content:" ▾"; color:var(--visited); }
  #view-napster thead th.sort.asc::after { content:" ▴"; }
  #view-napster tbody td { padding:6px 8px; border-bottom:1px solid #223244; vertical-align:middle; }
  #view-napster tbody tr { height:var(--row); }
  #view-napster tbody tr:hover td { background:var(--link); color:var(--porcelain); }
  #view-napster tbody tr:hover a, #view-napster tbody tr:hover .rsum { color:var(--porcelain); }
  #view-napster .c-art { width:auto; padding:0; }
  #view-napster .c-title { width:190px; }
  #view-napster .c-sum { width:440px; }
  #view-napster .c-meta { width:112px; white-space:nowrap; }
  #view-napster td.c-meta.num { text-align:right; font-variant-numeric:tabular-nums; }
  #view-napster td.c-meta.ok { color:var(--visited); }
  #view-napster .art { height:calc(var(--row) - 6px); aspect-ratio:16/9; width:auto; display:flex; align-items:center;
    justify-content:center; color:#fff; font-weight:700; font-size:40px; border:1px solid #000; overflow:hidden;
    background:linear-gradient(135deg,#33465a,#16324b); }
  #view-napster .art img { width:100%; height:100%; object-fit:cover; }
  #view-napster .art.k-producer { background:linear-gradient(135deg,#ef476f,#16324b); }
  #view-napster .art.k-concept  { background:linear-gradient(135deg,#06d6a0,#1e3a2f); }
  #view-napster .art.k-venue    { background:linear-gradient(135deg,#ffd166,#33465a); }
  #view-napster .art.k-video    { background:linear-gradient(135deg,#fffcf9,#26547c); color:#16324b; }
  #view-napster .art.k-question { background:linear-gradient(135deg,#9ecbff,#1e415f); }
  #view-napster .rtitle { font-size:15px; font-weight:700; color:var(--porcelain); white-space:nowrap; overflow:hidden;
    text-overflow:ellipsis; display:block; }
  #view-napster .rsum { font-size:12px; color:var(--text); opacity:.65; line-height:1.4; }
  #view-napster .rsum .dot { opacity:.4; }
  #view-napster .vgrid { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; padding:8px; }
  #view-napster .vt { position:relative; aspect-ratio:1; border:1px solid var(--line); background:var(--bg-slot);
    display:flex; align-items:flex-end; color:var(--porcelain); overflow:hidden; }
  #view-napster .vt img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
  #view-napster .vt .pl { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; }
  #view-napster .vt .pl b { width:34px; height:34px; background:var(--link); display:flex; align-items:center; justify-content:center; }
  #view-napster .vt .pl b::after { content:""; border-style:solid; border-width:7px 0 7px 11px;
    border-color:transparent transparent transparent #fff; margin-left:3px; }
  #view-napster .vt .cap { position:relative; padding:.5rem .6rem; font-size:.72rem; font-weight:600; width:100%;
    background:linear-gradient(transparent,rgba(0,0,0,.85)); }
  #view-napster .qwrap, #view-napster .nwrap { padding:8px; }
  #view-napster .statusbar { background:var(--panel); color:var(--porcelain); font-size:11px; padding:3px 8px;
    border-top:1px solid var(--line); display:flex; }
  #view-napster .statusbar div { border:1px inset var(--line); padding:1px 10px; margin-right:4px; opacity:.85; }

  /* =====================================================================
     ESPN — MOBILE  (editorial single column, tall rows, no rail)
     ===================================================================== */
  #view-espn { --chrome:150px; --row:calc((100vh - var(--chrome)) / 3);
    color:var(--text); font:12px/1.4 Arial,Helvetica,sans-serif; padding:8px 20px 20px; }
  #view-espn a { color:var(--link); text-decoration:none; } #view-espn a:hover { text-decoration:underline; }
  #view-espn .portal { max-width:1080px; margin:0 auto; }
  #view-espn .subnav { display:flex; align-items:stretch; flex-wrap:wrap; background:var(--bg-deep);
    border:1px solid var(--line); border-bottom:3px solid var(--link); }
  #view-espn .subnav a { display:flex; align-items:center; padding:8px 16px; color:var(--porcelain); font-weight:bold;
    font-size:11px; text-transform:uppercase; letter-spacing:.5px; opacity:.6; cursor:pointer; border-right:1px solid var(--line-soft); }
  #view-espn .subnav a:hover { opacity:.9; text-decoration:none; }
  #view-espn .subnav a.active { opacity:1; background:var(--link); color:var(--porcelain); }
  #view-espn .subnav .sp { margin-left:auto; display:flex; align-items:center; padding:0 8px; }
  #view-espn .subnav input { background:var(--bg); border:1px solid var(--line); color:var(--porcelain); font:11px Arial; padding:4px 10px; width:12rem; }
  #view-espn .mod { border:1px solid var(--line); border-top:0; background:var(--panel); }
  #view-espn .mod-h { background:rgba(255,252,249,.05); border-bottom:1px solid var(--line); padding:6px 10px;
    font-size:11px; font-weight:bold; letter-spacing:.5px; text-transform:uppercase; color:var(--porcelain); }
  #view-espn .pane { display:none; } #view-espn .pane.active { display:block; }
  #view-espn .flist { max-height:calc(var(--row) * 3); overflow-y:auto; }
  #view-espn .flist::-webkit-scrollbar { width:12px; }
  #view-espn .flist::-webkit-scrollbar-thumb { background:var(--line); border:2px solid var(--panel); }
  #view-espn .frow { display:grid; grid-template-columns:auto 1fr; gap:16px; align-items:center; height:var(--row);
    padding:0 12px; border-bottom:1px solid var(--line-soft); }
  #view-espn .frow:hover { background:rgba(239,71,111,.12); }
  #view-espn .art { height:calc(var(--row) - 20px); aspect-ratio:16/9; width:auto; display:flex; align-items:center;
    justify-content:center; color:#fff; font-weight:700; font-size:40px; border:1px solid var(--line); overflow:hidden;
    background:linear-gradient(135deg,#33465a,#16324b); }
  #view-espn .art img { width:100%; height:100%; object-fit:cover; }
  #view-espn .art.k-producer { background:linear-gradient(135deg,#ef476f,#16324b); }
  #view-espn .art.k-concept  { background:linear-gradient(135deg,#06d6a0,#1e3a2f); }
  #view-espn .art.k-venue    { background:linear-gradient(135deg,#ffd166,#33465a); }
  #view-espn .art.k-video    { background:linear-gradient(135deg,#fffcf9,#26547c); color:#16324b; }
  #view-espn .art.k-question { background:linear-gradient(135deg,#9ecbff,#1e415f); }
  #view-espn .fbody { min-width:0; }
  #view-espn .ftitle { font-size:18px; font-weight:bold; color:var(--porcelain); line-height:1.15; }
  #view-espn .ftitle a { color:var(--porcelain); } #view-espn .ftitle a:hover { color:var(--link); text-decoration:none; }
  #view-espn .fsum { display:block; font-size:13px; color:var(--text); opacity:.72; line-height:1.4; margin:.28rem 0; }
  #view-espn .fsum .dot { opacity:.4; }
  #view-espn .fmeta { display:block; font-size:11px; color:var(--porcelain); opacity:.5; text-transform:uppercase; letter-spacing:.4px; }
  #view-espn .vgrid { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; padding:10px; }
  #view-espn .vt { position:relative; aspect-ratio:1; border:1px solid var(--line); background:var(--bg-slot);
    display:flex; align-items:flex-end; color:var(--porcelain); overflow:hidden; }
  #view-espn .vt img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
  #view-espn .vt .pl { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; }
  #view-espn .vt .pl b { width:36px; height:36px; background:var(--link); display:flex; align-items:center; justify-content:center; }
  #view-espn .vt .pl b::after { content:""; border-style:solid; border-width:7px 0 7px 12px;
    border-color:transparent transparent transparent #fff; margin-left:3px; }
  #view-espn .vt .cap { position:relative; padding:.55rem .65rem; font-size:.75rem; font-weight:bold; width:100%;
    background:linear-gradient(transparent,rgba(0,0,0,.85)); }
  @media (max-width:768px) { #view-espn .vgrid { grid-template-columns:1fr; } }
</style>

<div class="dltoggle">
  <button data-view="napster">desktop</button>
  <button data-view="espn">mobile</button>
</div>

<!-- ============ NAPSTER — DESKTOP ============ -->
<div class="dlview" id="view-napster">
  <div class="win">
    <div class="titlebar"><span id="napWtitle">Library</span><span class="wbtns"><span>_</span><span>▢</span><span>✕</span></span></div>
    <div class="menubar"><span>File</span><span>Actions</span><span>View</span><span>Help</span></div>
    <div class="toolbar">
      <button data-tab="library" class="down">Library</button>
      <button data-tab="videos">Videos</button>
      <button data-tab="queue">Queue</button>
      <button data-tab="notes">Notes</button>
      <input type="search" class="tbsearch napq" placeholder="Search">
    </div>
    <div class="well">
      <div class="pane active" data-pane="library">
        <div class="scroll">
          <table>
            <thead><tr>
              <th class="c-art"></th>
              <th class="c-title" data-k="title">Subject</th>
              <th class="c-sum">Summary</th>
              <th class="c-meta" data-k="kind">Kind</th>
              <th class="c-meta" data-k="refs">Sources</th>
              <th class="c-meta sort" data-k="date">Date</th>
              <th class="c-meta" data-k="status">Status</th>
            </tr></thead>
            <tbody class="napRows"></tbody>
          </table>
        </div>
      </div>
      <div class="pane" data-pane="videos"><div class="vgrid napVgrid"></div></div>
      <div class="pane" data-pane="queue"><div class="qwrap"><?= dl_queue_html() ?></div></div>
      <div class="pane" data-pane="notes"><div class="nwrap"><?= dl_notes_html($note_ok) ?></div></div>
    </div>
    <div class="statusbar"><div>Online</div><div class="napSub">0 subjects</div><div class="napVid">0 videos</div><div><?= count($subjects) ?> processed</div></div>
  </div>
</div>

<!-- ============ ESPN — MOBILE ============ -->
<div class="dlview" id="view-espn">
  <div class="portal">
    <div class="subnav">
      <a data-tab="library" class="active">Library</a>
      <a data-tab="videos">Videos</a>
      <a data-tab="queue">Queue</a>
      <a data-tab="notes">Notes</a>
      <span class="sp"><input type="search" class="espnq" placeholder="Search subjects"></span>
    </div>
    <div class="mod">
      <div class="mod-h" id="espnModH">All subjects</div>
      <div class="pane active" data-pane="library"><div class="flist espnRows"></div></div>
      <div class="pane" data-pane="videos"><div class="vgrid espnVgrid"></div></div>
      <div class="pane" data-pane="queue"><?= dl_queue_html() ?></div>
      <div class="pane" data-pane="notes"><?= dl_notes_html($note_ok) ?></div>
    </div>
  </div>
</div>

<script>
const SUBJECTS_RAW = <?= json_encode($subjects, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) ?>;
const VIDEOS_RAW   = <?= json_encode($videos,   JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES) ?>;
const esc = s => (s == null ? '' : String(s)).replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]));

// Derive up to 3 dot-separated summary chunks from the lede.
function summ(lede){ return (lede||'').split(/[,;—]/).map(x => x.trim()).filter(Boolean).slice(0,3); }
// Normalize a live subject record into the shape both views render from.
function norm(s){
  const kind = s.kind || '';
  return { title:s.name || '', kind, date:s.date || '', url:s.url || '#', thumb:s.thumb || '',
    lede:s.lede || '', refs:(s.refs != null ? s.refs : null),
    sum:summ(s.lede), status:(kind === 'video' ? 'transcribed' : 'researched') };
}
const SUBJECTS = SUBJECTS_RAW.map(norm);
const VIDEOS   = VIDEOS_RAW.map(norm);

// 16:9 art tile: cover image when a thumb exists, else kind-colored gradient + initial.
function artHTML(s){
  const k = 'k-' + (s.kind || 'x');
  if (s.thumb) return `<span class="art ${k}"><img src="${esc(s.thumb)}" alt=""></span>`;
  return `<span class="art ${k}">${esc((s.title || '?').charAt(0))}</span>`;
}
function openAttr(){ return 'target="_blank" rel="noopener"'; }

/* ---- Napster (desktop): sortable grid ---- */
const napBody = document.querySelector('#view-napster .napRows');
let napSortKey = 'date', napSortAsc = false, napView = SUBJECTS.slice();
function napRender(){
  napBody.innerHTML = napView.map(s => `
    <tr>
      <td class="c-art"><a href="${esc(s.url)}" ${openAttr()}>${artHTML(s)}</a></td>
      <td class="c-title"><span class="rtitle"><a href="${esc(s.url)}" ${openAttr()}>${esc(s.title)}</a></span></td>
      <td class="c-sum"><span class="rsum">${s.sum.map(esc).join('<span class="dot"> · </span>')}</span></td>
      <td class="c-meta">${esc(s.kind)}</td>
      <td class="c-meta num">${s.refs != null ? s.refs : ''}</td>
      <td class="c-meta num">${esc(s.date)}</td>
      <td class="c-meta ok">${esc(s.status)}</td>
    </tr>`).join('');
  document.querySelector('#view-napster .napSub').textContent = SUBJECTS.length + ' subjects';
  document.querySelector('#view-napster .napVid').textContent = VIDEOS.length + ' videos';
}
function napApplySort(){
  napView.sort((a,b) => {
    let x = a[napSortKey], y = b[napSortKey];
    if (x == null) x = ''; if (y == null) y = '';
    const c = (typeof x === 'number' && typeof y === 'number') ? x - y : String(x).localeCompare(String(y));
    return napSortAsc ? c : -c;
  });
  napRender();
}
napApplySort();
document.querySelectorAll('#view-napster thead th[data-k]').forEach(th => th.addEventListener('click', () => {
  const k = th.dataset.k;
  if (k === napSortKey) napSortAsc = !napSortAsc; else { napSortKey = k; napSortAsc = true; }
  document.querySelectorAll('#view-napster thead th').forEach(x => x.classList.remove('sort','asc'));
  th.classList.add('sort'); if (napSortAsc) th.classList.add('asc');
  napApplySort();
}));

/* ---- ESPN (mobile): editorial list ---- */
const espnBody = document.querySelector('#view-espn .espnRows');
function espnRender(items){
  espnBody.innerHTML = items.map(s => `
    <a class="frow" href="${esc(s.url)}" ${openAttr()}>
      ${artHTML(s)}
      <span class="fbody">
        <span class="ftitle">${esc(s.title)}</span>
        <span class="fsum">${s.sum.map(esc).join('<span class="dot"> · </span>')}</span>
        <span class="fmeta">${esc(s.kind)}${s.refs != null ? ' · ' + s.refs + ' source' + (s.refs === 1 ? '' : 's') : ''}${s.date ? ' · ' + esc(s.date) : ''} · ${esc(s.status)}</span>
      </span>
    </a>`).join('');
}
espnRender(SUBJECTS);

/* ---- video grids (both views) ---- */
function vgridHTML(){
  return VIDEOS.map(v =>
    `<a class="vt" href="${esc(v.url)}" ${openAttr()}>${v.thumb ? `<img src="${esc(v.thumb)}" alt="">` : ''}<span class="pl"><b></b></span><span class="cap">${esc(v.title)}</span></a>`).join('')
    || '<div style="padding:12px;opacity:.5">no video research yet</div>';
}
document.querySelector('#view-napster .napVgrid').innerHTML = vgridHTML();
document.querySelector('#view-espn .espnVgrid').innerHTML = vgridHTML();

/* ---- tabs (scoped per view) ---- */
document.querySelectorAll('#view-napster .toolbar button').forEach(b => b.addEventListener('click', () => {
  document.querySelectorAll('#view-napster .toolbar button').forEach(x => x.classList.remove('down'));
  document.querySelectorAll('#view-napster .pane').forEach(p => p.classList.remove('active'));
  b.classList.add('down');
  document.querySelector('#view-napster .pane[data-pane="'+b.dataset.tab+'"]').classList.add('active');
  document.getElementById('napWtitle').textContent = b.textContent;
  if (b.dataset.tab === 'queue') loadQueue();
}));
const ESPN_MODH = { library:'All subjects', videos:'Video notes', queue:'Research queue', notes:'Note to hermes' };
document.querySelectorAll('#view-espn .subnav a').forEach(a => a.addEventListener('click', () => {
  document.querySelectorAll('#view-espn .subnav a').forEach(x => x.classList.remove('active'));
  document.querySelectorAll('#view-espn .pane').forEach(p => p.classList.remove('active'));
  a.classList.add('active');
  document.querySelector('#view-espn .pane[data-pane="'+a.dataset.tab+'"]').classList.add('active');
  document.getElementById('espnModH').textContent = ESPN_MODH[a.dataset.tab];
  if (a.dataset.tab === 'queue') loadQueue();
}));

/* ---- search (per active view) ---- */
function match(s, t){ return (s.title+' '+s.kind+' '+s.status+' '+s.sum.join(' ')).toLowerCase().includes(t); }
document.querySelector('#view-napster .napq').addEventListener('input', e => {
  const t = e.target.value.toLowerCase().trim();
  napView = SUBJECTS.filter(s => match(s, t));
  napApplySort();
});
document.querySelector('#view-espn .espnq').addEventListener('input', e => {
  const t = e.target.value.toLowerCase().trim();
  espnRender(SUBJECTS.filter(s => match(s, t)));
});

/* ---- queue: fill every .dlq-body from gate_status.php ---- */
function loadQueue(){
  fetch('gate_status.php').then(r => r.json()).then(d => {
    const jobs = (d && d.jobs) || [];
    const rows = jobs.map(j => {
      const link = j.url ? '<a href="'+esc(j.url)+'" target="_blank" rel="noopener">'+esc(j.target)+'</a>' : esc(j.target || 'request');
      return '<tr><td>'+link+'</td><td>'+esc(j.status || '')+'</td><td>'+esc(j.detail || '')+'</td></tr>';
    }).join('') || '<tr><td colspan="3" style="opacity:.5">nothing in the last 24 hours</td></tr>';
    document.querySelectorAll('.dlq-body').forEach(tb => tb.innerHTML = rows);
  }).catch(() => {});
}

/* ---- toggle: desktop=napster / mobile=espn; localStorage; width default ---- */
function setView(v){
  if (v !== 'napster' && v !== 'espn') v = 'napster';
  document.querySelectorAll('.dltoggle button').forEach(b => b.classList.toggle('active', b.dataset.view === v));
  document.querySelectorAll('.dlview').forEach(x => x.classList.toggle('active', x.id === 'view-' + v));
  try { localStorage.setItem('dl_view', v); } catch(e){}
}
document.querySelectorAll('.dltoggle button').forEach(b => b.addEventListener('click', () => setView(b.dataset.view)));
/* ESPN-only for now — ignore stored/width and force the mobile view.
   Restore `setView(stored || (window.innerWidth <= 820 ? 'espn' : 'napster'))`
   (and remove the .dltoggle{display:none} rule) to bring the toggle back. */
setView('espn');
</script>

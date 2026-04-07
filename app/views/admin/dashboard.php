<?php
$labels   = json_encode(array_column($monthlyStats,'month'));
$revenues = json_encode(array_map('floatval',array_column($monthlyStats,'revenue')));
$ordersC  = json_encode(array_map('intval',  array_column($monthlyStats,'orders')));
$sc = $statusCounts;
?>
<!-- Stat Cards -->
<div class="row g-3 mb-4">
  <div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon si-green"><i class="bi bi-box-seam-fill"></i></div><div><div class="stat-num"><?= $productCount ?></div><div class="stat-lbl">Total Produk</div></div></div></div>
  <div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon si-blue"><i class="bi bi-receipt-cutoff"></i></div><div><div class="stat-num"><?= $orderCount ?></div><div class="stat-lbl">Total Pesanan</div></div></div></div>
  <div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon si-purple"><i class="bi bi-people-fill"></i></div><div><div class="stat-num"><?= $userCount ?></div><div class="stat-lbl">Pengguna</div></div></div></div>
  <div class="col-6 col-xl-3"><div class="stat-card"><div class="stat-icon si-orange"><i class="bi bi-cash-stack"></i></div><div><div class="stat-num" style="font-size:1.1rem"><?= formatRupiah($revenue) ?></div><div class="stat-lbl">Pendapatan (Selesai)</div></div></div></div>
</div>

<!-- Status badges -->
<div class="row g-2 mb-4">
  <?php $sConf=['pending'=>['#a16207','#fef9c3'],'processing'=>['#1d4ed8','#dbeafe'],'shipped'=>['#6d28d9','#ede9fe'],'completed'=>['#009958','#e6faf2'],'cancelled'=>['#dc2626','#fee2e2']];
  foreach($sConf as $s=>[$color,$bg]): $cnt=$statusCounts[$s]??0; ?>
  <div class="col"><a href="<?= APP_URL ?>/admin/orders?status=<?= $s ?>" class="text-center d-block p-3 rounded-3 text-decoration-none" style="background:<?= $bg ?>;border:1px solid <?= $color ?>20">
    <div style="font-family:'Outfit',sans-serif;font-weight:900;font-size:1.5rem;color:<?= $color ?>"><?= $cnt ?></div>
    <div style="font-size:.72rem;color:<?= $color ?>;font-weight:700;text-transform:capitalize"><?= $s ?></div>
  </a></div>
  <?php endforeach; ?>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
  <div class="col-lg-8">
    <div class="adm-card">
      <div class="adm-card-head"><h6><i class="bi bi-bar-chart-line me-2 text-success"></i>Pendapatan Bulanan</h6><span class="badge" style="background:var(--brand-light);color:var(--brand)">6 Bulan</span></div>
      <div class="p-3"><canvas id="revenueChart" style="max-height:220px"></canvas></div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="adm-card">
      <div class="adm-card-head"><h6><i class="bi bi-pie-chart me-2 text-primary"></i>Pesanan</h6></div>
      <div class="p-3"><canvas id="orderChart" style="max-height:220px"></canvas></div>
    </div>
  </div>
</div>

<!-- Recent orders + best sellers -->
<div class="row g-3">
  <div class="col-lg-7">
    <div class="adm-card">
      <div class="adm-card-head"><h6>Pesanan Terbaru</h6><a href="<?= APP_URL ?>/admin/orders" class="btn btn-xs btn-outline-primary">Semua</a></div>
      <div class="table-responsive">
        <table class="table adm-table">
          <thead><tr><th>Pesanan</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead>
          <tbody>
            <?php if(empty($recentOrders)): ?><tr><td colspan="4" class="text-center text-muted py-4">Belum ada pesanan</td></tr>
            <?php else: foreach($recentOrders as $o): ?>
            <tr>
              <td><div class="fw-700 small"><?= generateOrderNumber($o['id']) ?></div><div class="tiny text-muted"><?= date('d M Y', strtotime($o['created_at'])) ?></div></td>
              <td class="fw-600 small"><?= e($o['user_name']) ?></td>
              <td class="fw-700 text-success"><?= formatRupiah($o['total_price']) ?></td>
              <td><span class="status-pill <?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span></td>
            </tr>
            <?php endforeach; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="adm-card mb-3">
      <div class="adm-card-head"><h6><i class="bi bi-trophy-fill text-warning me-2"></i>Produk Terlaris</h6></div>
      <div class="p-3">
        <?php if(empty($bestSellers)): ?><p class="text-muted small mb-0">Belum ada data.</p>
        <?php else: foreach($bestSellers as $i=>$b): ?>
        <div class="d-flex align-items-center gap-3 mb-2 pb-2 <?= $i<count($bestSellers)-1?'border-bottom':'' ?>">
          <span class="fw-900" style="color:<?= ['#f59e0b','#94a3b8','#cd7c2f'][$i]??'#64748b' ?>;width:18px;font-family:'Outfit',sans-serif"><?= $i+1 ?></span>
          <div class="flex-grow-1">
            <div class="fw-600 small"><?= e(truncate($b['name'],35)) ?></div>
            <div class="tiny text-muted"><?= $b['total_qty']??0 ?> terjual · <?= formatRupiah($b['revenue']??0) ?></div>
          </div>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
    <div class="adm-card">
      <div class="adm-card-head"><h6><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Stok Menipis</h6><a href="<?= APP_URL ?>/admin/stock" class="btn btn-xs btn-outline-warning">Detail</a></div>
      <div class="p-3">
        <?php if(empty($lowStock)): ?><p class="text-muted small mb-0">Semua stok aman ✓</p>
        <?php else: foreach(array_slice($lowStock,0,5) as $p): ?>
        <div class="d-flex justify-content-between align-items-center mb-2">
          <div class="small fw-600"><?= e(truncate($p['name'],28)) ?></div>
          <span class="stock-chip <?= $p['stock']==0?'zero':($p['stock']<=5?'low':'ok') ?>"><?= $p['stock'] ?></span>
        </div>
        <?php endforeach; endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6b7280';
new Chart(document.getElementById('revenueChart'),{
  type:'bar',
  data:{labels:<?= $labels ?>,datasets:[{label:'Pendapatan',data:<?= $revenues ?>,backgroundColor:'rgba(0,185,107,.15)',borderColor:'#00b96b',borderWidth:2,borderRadius:8,borderSkipped:false}]},
  options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{display:false}},scales:{y:{ticks:{callback:v=>'Rp'+Number(v).toLocaleString('id-ID')},grid:{color:'#f0f2f5'}},x:{grid:{display:false}}}}
});
new Chart(document.getElementById('orderChart'),{
  type:'doughnut',
  data:{labels:<?= $labels ?>,datasets:[{data:<?= $ordersC ?>,backgroundColor:['#00b96b','#22c55e','#4ade80','#86efac','#bbf7d0','#e6faf2'],borderWidth:0}]},
  options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{position:'bottom',labels:{font:{size:11},boxWidth:12}}}}
});
</script>

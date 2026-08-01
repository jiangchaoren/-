<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-label">7天访问量</span>
    </div>
    <div class="stat-list">
      <?php foreach($days as $d): ?>
      <div class="stat-row">
        <span class="stat-date"><?=$d?></span>
        <div class="stat-bar-wrapper">
          <div class="stat-bar" style="width:<?=max(5, ($views[$d]/max(max($views),1))*100)?>%"></div>
        </div>
        <b class="stat-num"><?=$views[$d]?></b>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="stat-card">
    <div class="stat-card-header">
      <span class="stat-label">链接点击量</span>
    </div>
    <div class="stat-list">
      <?php if(empty($clicks)): ?>
        <div class="stat-empty">暂无数据</div>
      <?php else: ?>
        <?php foreach($clicks as $c): ?>
        <div class="stat-row">
          <span class="stat-date" title="<?=htmlspecialchars($c['title'])?>"><?=mb_strlen($c['title'])>10?mb_substr($c['title'],0,10).'...':$c['title']?></span>
          <div class="stat-bar-wrapper">
            <div class="stat-bar stat-bar-purple" style="width:<?=$c['total']>0?max(5, ($c['total']/max(max(array_column($clicks,'total')),1))*100).'%':'0'?>"></div>
          </div>
          <b class="stat-num"><?=$c['total']?></b>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

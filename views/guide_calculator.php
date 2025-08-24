<?php
?>
<section class="container section">
  <h1><?php echo e($item['title']); ?></h1>
  <?php foreach (($item['content'] ?? []) as $p): ?><p><?php echo e($p); ?></p><?php endforeach; ?>
  <form class="card" oninput="calcFrig(); return false;">
    <label><?php echo e(t('calc.area')); ?> (m²) *<input id="m2" type="number" min="5" max="300" value="20" required></label>
    <label><?php echo e(t('calc.orientation')); ?>
      <select id="ori">
        <option value="1.0"><?php echo e(t('calc.orientation.n')); ?></option>
        <option value="1.1"><?php echo e(t('calc.orientation.e')); ?></option>
        <option value="1.2"><?php echo e(t('calc.orientation.s')); ?></option>
        <option value="1.1"><?php echo e(t('calc.orientation.w')); ?></option>
      </select>
    </label>
    <label><?php echo e(t('calc.insulation')); ?>
      <select id="ins">
        <option value="0.9"><?php echo e(t('calc.insulation.good')); ?></option>
        <option value="1.0"><?php echo e(t('calc.insulation.avg')); ?></option>
        <option value="1.2"><?php echo e(t('calc.insulation.poor')); ?></option>
      </select>
    </label>
    <p><strong><?php echo e(t('calc.result')); ?>: <span id="out">2000</span> frig/h</strong></p>
    <p class="muted"><?php echo e(t('calc.disclaimer')); ?></p>
  </form>
</section>
<script>
function calcFrig(){
  var m2 = parseFloat(document.getElementById('m2').value||0);
  var o = parseFloat(document.getElementById('ori').value||1);
  var i = parseFloat(document.getElementById('ins').value||1);
  var base = 100; // ~100 frig/m²
  var frig = Math.round(m2 * base * o * i);
  document.getElementById('out').textContent = frig.toString();
}
calcFrig();
</script>
<?php include __DIR__.'/partials/contact.php'; ?>

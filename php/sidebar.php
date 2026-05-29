<?php
/**
 * Sidebar reutilizável — inclua em todas as páginas com:
 * <?php $pagina_ativa = 'dashboard'; include 'php/sidebar.php'; ?>
 *
 * Valores aceitos em $pagina_ativa:
 *   'dashboard' | 'pdv' | 'produtos' | 'funcionarios'
 */

if (!isset($pagina_ativa)) $pagina_ativa = '';

function nav_link($href, $icone, $label, $id, $ativo) {
    $class = ($id === $ativo) ? ' active' : '';
    echo "<a href=\"{$href}\" class=\"{$class}\">
            <i class=\"bi bi-{$icone}\"></i> {$label}
          </a>";
}
?>

<nav class="sidebar">

  <div class="sidebar-logo">
    <div class="logo-icon">🧀</div>
    <div>
      <div class="logo-text">Container do Queijo</div>
      <div class="logo-sub">Sistema PDV</div>
    </div>
  </div>

  <span class="sidebar-label">Menu</span>

  <?php nav_link('index.php',         'speedometer2',  'Dashboard',     'dashboard',    $pagina_ativa); ?>
  <?php nav_link('pdv.html',          'cart3',         'PDV',           'pdv',          $pagina_ativa); ?>
  <?php nav_link('produtos.html',     'box-seam',      'Produtos',      'produtos',     $pagina_ativa); ?>
  <?php nav_link('funcionarios.html', 'people',        'Funcionários',  'funcionarios', $pagina_ativa); ?>

  <div class="sidebar-footer">
    &copy; <?= date('Y') ?> Container do Queijo
  </div>

</nav>
<?php
require_once 'config.php';
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['game_id'])) {
    $stmt = $pdo->prepare("SELECT title, price, discount_percent FROM games WHERE id = ?");
    $stmt->execute([(int)$_POST['game_id']]);
    $game = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($game) {
        $finalPrice = $game['price'] * (1 - $game['discount_percent'] / 100);
        $player = trim($_POST['player_name'] ?? '') ?: 'Player1';
        $ins = $pdo->prepare("INSERT INTO orders (game_id, player_name, paid_price) VALUES (?, ?, ?)");
        $ins->execute([(int)$_POST['game_id'], $player, $finalPrice]);
        $msg = "Compra confirmada: {$game['title']} para {$player} por R$ " . number_format($finalPrice, 2, ',', '.');
    }
}

$games = $pdo->query("SELECT * FROM games ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
$orders = $pdo->query("SELECT o.id, o.player_name, g.title, o.paid_price, o.created_at FROM orders o JOIN games g ON g.id = o.game_id ORDER BY o.id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Mini-Steam | Loja de Games</title>
  <link rel="stylesheet" href="assets/style.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<div class="wrap">
  <h1>MINI-STEAM // BLACK FRIDAY WEEK (-50% OFF)</h1>
  <div class="bf-banner">PROMOÇÃO RELÂMPAGO ATIVA: Descontos especiais aplicados no catálogo!</div>
  <?php if ($msg): ?><div class="alert"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <div class="toolbar">
    <button type="button" class="filter-btn" onclick="filtrarPromocoes(false)">Todos os Jogos</button>
    <button type="button" class="filter-btn" onclick="filtrarPromocoes(true)">Apenas em Promoção</button>
  </div>

  <div class="grid">
    <?php foreach ($games as $g):
      $final = $g['price'] * (1 - $g['discount_percent'] / 100);
    ?>
    <div class="card" data-promo="<?= $g['discount_percent'] > 0 ? '1' : '0' ?>">
      <div><strong><?= htmlspecialchars($g['title']) ?></strong></div>
      <div style="font-size:12px;color:#8f98a0;"><?= htmlspecialchars($g['genre']) ?></div>
      <div style="margin-top:10px;">
        <?php if ($g['discount_percent'] > 0): ?>
          <span class="badge">-<?= $g['discount_percent'] ?>%</span>
          <span class="old">R$ <?= number_format($g['price'], 2, ',', '.') ?></span>
        <?php endif; ?>
        <div class="price">R$ <?= number_format($final, 2, ',', '.') ?></div>
      </div>
      <form method="POST" data-title="<?= htmlspecialchars($g['title']) ?>">
        <input type="hidden" name="game_id" value="<?= $g['id'] ?>">
        <input type="text" name="player_name" placeholder="Seu Nick (ex: Gobirudo)" required>
        <button type="submit">Comprar Agora</button>
      </form>
    </div>
    <?php endforeach; ?>
  </div>

  <h2>Últimas Compras Registradas (MariaDB)</h2>
  <table>
    <tr><th>Pedido</th><th>Jogador</th><th>Jogo</th><th>Valor Pago</th><th>Data</th></tr>
    <?php foreach ($orders as $o): ?>
    <tr>
      <td>#<?= $o['id'] ?></td>
      <td><?= htmlspecialchars($o['player_name']) ?></td>
      <td><?= htmlspecialchars($o['title']) ?></td>
      <td>R$ <?= number_format($o['paid_price'], 2, ',', '.') ?></td>
      <td><?= $o['created_at'] ?></td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>
</body>
</html>

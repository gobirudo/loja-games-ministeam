function filtrarPromocoes(apenasPromo) {
  document.querySelectorAll('.card').forEach(card => {
    const temDesconto = card.dataset.promo === '1';
    card.style.display = (!apenasPromo || temDesconto) ? 'block' : 'none';
  });
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', e => {
      const jogo = form.dataset.title;
      if (!confirm(`Confirmar a compra de "${jogo}"?`)) {
        e.preventDefault();
      }
    });
  });
});

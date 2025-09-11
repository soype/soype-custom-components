(function(){
  function closeItem(item){
    const btn = item.querySelector('.soype-accordion__button');
    const panel = item.querySelector('.soype-accordion__panel');
    if (!btn || !panel) return;
    btn.setAttribute('aria-expanded', 'false');
    panel.hidden = true;
    item.classList.remove('is-open');
  }

  function openItem(item){
    const btn = item.querySelector('.soype-accordion__button');
    const panel = item.querySelector('.soype-accordion__panel');
    if (!btn || !panel) return;
    btn.setAttribute('aria-expanded', 'true');
    panel.hidden = false;
    item.classList.add('is-open');
  }

  document.addEventListener('click', function(e){
    const btn = e.target.closest('.soype-accordion__button');
    if (!btn) return;

    const item = btn.closest('.soype-accordion__item');
    const acc  = btn.closest('.soype-accordion');
    if (!item || !acc) return;

    const single = acc.getAttribute('data-single') === 'true';
    const isOpen = btn.getAttribute('aria-expanded') === 'true';

    if (single) {
      acc.querySelectorAll('.soype-accordion__item.is-open').forEach(closeItem);
    }

    if (isOpen) {
      closeItem(item);
    } else {
      openItem(item);
    }
  });
})();

(function () {
  function debounce(fn, delay) {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, arguments), delay);
    };
  }

  function initCarousel(root) {
    const track   = root.querySelector('.soype-carousel__track');
    const nextBtn = root.querySelector('.soype-carousel-next');
    const prevBtn = root.querySelector('.soype-carousel-prev');
    if (!track || !nextBtn || !prevBtn) return;

    let slideCount = track.children.length;
    if (slideCount === 0) return;

    // Helper to compute current slide width
    function getSlideWidth() {
      const first = track.firstElementChild;
      return first ? first.getBoundingClientRect().width : 0;
    }

    // Hide controls in desktop if exactly 3 cards
    if (slideCount === 3) {
      root.classList.add('is-exact-3');
    } else {
      root.classList.remove('is-exact-3');
    }

    let slideW = getSlideWidth();
    let animating = false;

    function goNext() {
      if (animating) return;
      animating = true;
      track.style.transition = 'transform 300ms ease';
      track.style.transform = `translateX(${-slideW}px)`;

      const onEnd = () => {
        track.removeEventListener('transitionend', onEnd);
        track.style.transition = 'none';
        track.appendChild(track.firstElementChild);
        track.style.transform = 'translateX(0)';
        void track.offsetWidth; // reflow
        track.style.transition = 'transform 300ms ease';
        animating = false;
      };
      track.addEventListener('transitionend', onEnd, { once: true });
    }

    function goPrev() {
      if (animating) return;
      animating = true;
      track.style.transition = 'none';
      track.prepend(track.lastElementChild);
      track.style.transform = `translateX(${-slideW}px)`;
      void track.offsetWidth;
      track.style.transition = 'transform 300ms ease';
      track.style.transform = 'translateX(0)';

      track.addEventListener(
        'transitionend',
        () => { animating = false; },
        { once: true }
      );
    }

    prevBtn.addEventListener('click', goPrev);
    nextBtn.addEventListener('click', goNext);

    // Recalculate slide width on resize
    const onResize = debounce(() => {
      slideW = getSlideWidth();
    }, 150);
    window.addEventListener('resize', onResize);
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.soype-carousel').forEach(initCarousel);
  });
})();

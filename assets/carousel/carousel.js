/**
 * SoyPe Carousel - Infinite with bottom controls and dots
 * - Responsive visible slides: 3 (desktop), 2 (<=1120px), 1 (<=768px)
 * - Controls bar (buttons + dots) below the carousel
 * - Hide controls when slideCount <= visibleCount
 * - Dots reflect the leftmost visible slide (logical index)
 */

(function () {
  function debounce(fn, delay) {
    let t;
    return function () {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, arguments), delay);
    };
  }

  // Visible slides based on current viewport width
  function getVisibleCount() {
    const w = window.innerWidth;
    if (w <= 768) return 1;
    if (w <= 1120) return 2;
    return 3;
  }

  function initCarousel(root) {
    const viewport = root.querySelector('.soype-carousel__viewport');
    const track    = root.querySelector('.soype-carousel__track');
    if (!viewport || !track) return;

    // Create controls container under the carousel if not present
    let controls = root.querySelector('.soype-carousel__controls');
    if (!controls) {
      controls = document.createElement('div');
      controls.className = 'soype-carousel__controls';
      root.appendChild(controls);
    }

    // Create prev/next buttons if not present
    let prevBtn = controls.querySelector('.soype-carousel-prev');
    let nextBtn = controls.querySelector('.soype-carousel-next');
    if (!prevBtn) {
      prevBtn = document.createElement('button');
      prevBtn.className = 'soype-carousel-prev';
      prevBtn.type = 'button';
      prevBtn.setAttribute('aria-label', 'Anterior');
      prevBtn.innerHTML = '<span aria-hidden="true">‹</span>';
      controls.appendChild(prevBtn);
    }

    // Create dots container
    let dotsWrap = controls.querySelector('.soype-carousel__dots');
    if (!dotsWrap) {
      dotsWrap = document.createElement('div');
      dotsWrap.className = 'soype-carousel__dots';
      dotsWrap.setAttribute('role', 'tablist');
      controls.appendChild(dotsWrap);
    }

    if (!nextBtn) {
      nextBtn = document.createElement('button');
      nextBtn.className = 'soype-carousel-next';
      nextBtn.type = 'button';
      nextBtn.setAttribute('aria-label', 'Siguiente');
      nextBtn.innerHTML = '<span aria-hidden="true">›</span>';
      controls.appendChild(nextBtn);
    }

    // Ensure each initial slide has a stable "original index"
    const slides = Array.from(track.children);
    const slideCount = slides.length;
    if (slideCount === 0) return;

    slides.forEach((el, i) => {
      if (!el.dataset.idx) el.dataset.idx = String(i);
    });

    // Build dots
    function buildDots() {
      dotsWrap.innerHTML = '';
      for (let i = 0; i < slideCount; i++) {
        const btn = document.createElement('button');
        btn.className = 'soype-carousel__dot';
        btn.type = 'button';
        btn.setAttribute('role', 'tab');
        btn.setAttribute('aria-label', `Ir a testimonio ${i + 1}`);
        btn.dataset.targetIdx = String(i);
        dotsWrap.appendChild(btn);
      }
    }
    buildDots();

    function getSlideWidth() {
      const first = track.firstElementChild;
      return first ? first.getBoundingClientRect().width : 0;
    }

    function getCurrentIndex() {
      // Leftmost visible slide's original index
      const first = track.firstElementChild;
      return first ? Number(first.dataset.idx) : 0;
    }

    function updateDotsActive() {
      const curr = getCurrentIndex();
      dotsWrap.querySelectorAll('.soype-carousel__dot').forEach((dot) => {
        const isActive = Number(dot.dataset.targetIdx) === curr;
        dot.classList.toggle('is-active', isActive);
        dot.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
    }

    function updateControlsVisibility() {
      const visible = getVisibleCount();
      if (slideCount <= visible) {
        root.classList.add('is-controls-hidden');
      } else {
        root.classList.remove('is-controls-hidden');
      }
    }

    let slideW = getSlideWidth();
    let animating = false;

    function goNext(steps = 1) {
      if (animating) return;
      animating = true;

      const distance = slideW * steps;
      track.style.transition = 'transform 300ms ease';
      track.style.transform = `translateX(${-distance}px)`;

      const onEnd = () => {
        track.removeEventListener('transitionend', onEnd);
        // Rotate 'steps' slides to end
        track.style.transition = 'none';
        for (let i = 0; i < steps; i++) {
          track.appendChild(track.firstElementChild);
        }
        track.style.transform = 'translateX(0)';
        // Force reflow then re-enable transition
        void track.offsetWidth;
        track.style.transition = 'transform 300ms ease';
        animating = false;
        updateDotsActive();
      };

      track.addEventListener('transitionend', onEnd, { once: true });
    }

    function goPrev(steps = 1) {
      if (animating) return;
      animating = true;

      // Pre-position: move 'steps' slides from end to front and offset track
      track.style.transition = 'none';
      for (let i = 0; i < steps; i++) {
        track.prepend(track.lastElementChild);
      }
      track.style.transform = `translateX(${-slideW * steps}px)`;
      void track.offsetWidth; // reflow

      // Animate back to 0
      track.style.transition = 'transform 300ms ease';
      track.style.transform = 'translateX(0)';
      track.addEventListener(
        'transitionend',
        () => {
          animating = false;
          updateDotsActive();
        },
        { once: true }
      );
    }

    // Dots navigation (minimal motion: choose shortest direction)
    function goTo(targetIdx) {
      const curr = getCurrentIndex();
      if (targetIdx === curr) return;

      let diff = (targetIdx - curr + slideCount) % slideCount; // forward steps
      const back = (slideCount - diff) % slideCount;           // backward steps

      if (diff <= back) {
        goNext(diff);
      } else {
        goPrev(back);
      }
    }

    // Wire buttons
    controls.addEventListener('click', (e) => {
      const el = e.target.closest('button');
      if (!el) return;

      if (el.classList.contains('soype-carousel-next')) {
        goNext(1);
      } else if (el.classList.contains('soype-carousel-prev')) {
        goPrev(1);
      } else if (el.classList.contains('soype-carousel__dot')) {
        const target = Number(el.dataset.targetIdx);
        goTo(target);
      }
    });

    // Keyboard support when focus is within controls
    controls.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowRight') { e.preventDefault(); goNext(1); }
      if (e.key === 'ArrowLeft')  { e.preventDefault(); goPrev(1); }
    });

    // Initial state
    updateDotsActive();
    updateControlsVisibility();

    // Resize handling: recompute width and controls visibility
    const onResize = debounce(() => {
      slideW = getSlideWidth();
      updateControlsVisibility();
    }, 150);
    window.addEventListener('resize', onResize);

    // Cleanup hook if ever needed
    root._soypeDestroyCarousel = () => {
      window.removeEventListener('resize', onResize);
    };
  }

  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.soype-carousel').forEach(initCarousel);
  });
})();

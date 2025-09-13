/**
 * SoyPe Carousel - Infinite, always 3 cards visible.
 *
 * Behavior:
 * - Shows exactly 3 slides at a time (each slide is 33.3333% width).
 * - Infinite loop by rotating DOM nodes after each animation.
 * - Hides controls on desktop if exactly 3 slides (.is-exact-3 class on root).
 * - Recomputes slide width on resize.
 */

(function () {
  // Simple debounce to avoid thrashing on resize
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

    // Slides collection (live updates after DOM rotations)
    let slideCount = track.children.length;
    if (slideCount === 0) return;

    // Mark root with .is-exact-3 if exactly 3 cards (used by CSS to hide controls on desktop)
    if (slideCount === 3) {
      root.classList.add('is-exact-3');
    } else {
      root.classList.remove('is-exact-3');
    }

    // Compute the width of one slide (they are 33.3333% of viewport).
    // We measure the first child to account for paddings/borders/gutters.
    function getSlideWidth() {
      const first = track.firstElementChild;
      return first ? first.getBoundingClientRect().width : 0;
    }

    let slideW = getSlideWidth();
    let animating = false;

    function goNext() {
      if (animating) return;
      animating = true;

      // Animate the track left by exactly one card.
      track.style.transition = 'transform 300ms ease';
      track.style.transform = `translateX(${-slideW}px)`;
      track.classList.add('is-animating');

      const onEnd = () => {
        track.removeEventListener('transitionend', onEnd);
        // Snap back without animation and rotate first slide to the end
        track.style.transition = 'none';
        track.appendChild(track.firstElementChild);
        track.style.transform = 'translateX(0)';
        // Force reflow to apply the snap before enabling transitions again
        void track.offsetWidth;
        track.style.transition = 'transform 300ms ease';
        track.classList.remove('is-animating');
        animating = false;
      };

      track.addEventListener('transitionend', onEnd, { once: true });
    }

    function goPrev() {
      if (animating) return;
      animating = true;

      // Pre-position: move last slide to front and offset track by -slideW without transition
      track.style.transition = 'none';
      track.prepend(track.lastElementChild);
      track.style.transform = `translateX(${-slideW}px)`;
      void track.offsetWidth; // reflow

      // Animate back to 0
      track.style.transition = 'transform 300ms ease';
      track.style.transform = 'translateX(0)';
      track.classList.add('is-animating');

      const onEnd = () => {
        track.removeEventListener('transitionend', onEnd);
        track.classList.remove('is-animating');
        animating = false;
      };

      track.addEventListener('transitionend', onEnd, { once: true });
    }

    // Wire controls
    prevBtn.addEventListener('click', goPrev);
    nextBtn.addEventListener('click', goNext);

    // Keyboard support (optional)
    root.addEventListener('keydown', (e) => {
      // Only act if focus is inside the carousel region
      if (!root.contains(document.activeElement)) return;
      if (e.key === 'ArrowLeft')  { e.preventDefault(); goPrev(); }
      if (e.key === 'ArrowRight') { e.preventDefault(); goNext(); }
    });

    // Recalculate slide width on resize (debounced)
    const onResize = debounce(() => {
      slideW = getSlideWidth();
    }, 150);
    window.addEventListener('resize', onResize);

    // Clean up when needed (if you dynamically destroy carousels)
    root._soypeDestroyCarousel = () => {
      window.removeEventListener('resize', onResize);
      prevBtn.removeEventListener('click', goPrev);
      nextBtn.removeEventListener('click', goNext);
    };
  }

  document.addEventListener('DOMContentLoaded', function () {
    const carousels = document.querySelectorAll('.soype-carousel');
    carousels.forEach(initCarousel);
  });
})();

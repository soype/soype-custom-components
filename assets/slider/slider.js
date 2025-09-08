(function () {
  document.addEventListener("DOMContentLoaded", function () {
    const sliders = document.querySelectorAll(".soype-slider");

    sliders.forEach((slider) => {
      const track = slider.querySelector(".soype-slider__track");
      const next = slider.querySelector(".soype-slider-next");
      const prev = slider.querySelector(".soype-slider-prev");
      const slideWidth = track.firstElementChild.offsetWidth;

      let animating = false;

      function goNext() {
        if (animating) return;
        animating = true;

        const w = slideWidth;
        track.style.transition = "transform 300ms ease";
        track.style.transform = `translateX(${-w}px)`;

        track.addEventListener(
          "transitionend",
          function handler() {
            track.removeEventListener("transitionend", handler);
            // Snap back without animation and rotate DOM
            track.style.transition = "none";
            track.appendChild(track.firstElementChild);
            track.style.transform = "translateX(0)";
            // force reflow to apply the snap
            void track.offsetWidth;
            track.style.transition = "transform 300ms ease";
            animating = false;
          },
          { once: true }
        );
      }

      function goPrev() {
        if (animating) return;
        animating = true;

        const w = slideWidth;
        // Pre-position without animation: move last to front and offset left by -w
        track.style.transition = "none";
        track.prepend(track.lastElementChild);
        track.style.transform = `translateX(${-w}px)`;
        // force reflow
        void track.offsetWidth;

        // Now animate back to 0
        track.style.transition = "transform 300ms ease";
        track.style.transform = "translateX(0)";

        track.addEventListener(
          "transitionend",
          () => {
            animating = false;
          },
          { once: true }
        );
      }

      prev.addEventListener("click", goPrev);
      next.addEventListener("click", goNext);
    });

  });
})();

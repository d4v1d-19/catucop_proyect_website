(function () {
  var burger = document.querySelector('.nav-burger');
  var mobile = document.getElementById('nav-mobile');
  if (burger && mobile) {
    burger.addEventListener('click', function () {
      var open = burger.classList.toggle('is-open');
      burger.setAttribute('aria-expanded', open ? 'true' : 'false');
      mobile.hidden = !open;
    });
  }

  document.querySelectorAll('[data-filter]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var filter = btn.getAttribute('data-filter');
      document.querySelectorAll('[data-filter]').forEach(function (other) {
        other.classList.toggle('is-active', other === btn);
      });
      document.querySelectorAll('.aff-card').forEach(function (card) {
        card.hidden = filter !== 'all' && card.getAttribute('data-cat') !== filter;
      });
    });
  });

  var modal = document.getElementById('aff-modal');
  function closeAff() {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
    modal.querySelectorAll('.aff-panel').forEach(function (panel) {
      panel.hidden = true;
    });
  }
  if (modal) {
    document.querySelectorAll('[data-open]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var panel = document.getElementById(btn.getAttribute('data-open'));
        if (!panel) return;
        modal.querySelectorAll('.aff-panel').forEach(function (item) {
          item.hidden = item !== panel;
        });
        modal.hidden = false;
        document.body.style.overflow = 'hidden';
      });
    });
    modal.addEventListener('click', function (event) {
      if (event.target === modal) closeAff();
    });
    modal.querySelectorAll('[data-close-aff]').forEach(function (btn) {
      btn.addEventListener('click', closeAff);
    });
    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeAff();
    });
  }

  var openMap = document.getElementById('map-open');
  var frameWrap = document.getElementById('map-frame');
  var frame = document.getElementById('map-iframe');
  var preview = document.getElementById('map-preview');
  var closeMap = document.getElementById('map-close');
  if (openMap && frame && frameWrap && preview) {
    openMap.addEventListener('click', function () {
      var src = openMap.getAttribute('data-src');
      if (window.matchMedia('(max-width: 767px)').matches) {
        window.open(src, '_blank', 'noopener');
        return;
      }
      frame.src = src;
      frame.hidden = false;
      frameWrap.hidden = false;
      preview.hidden = true;
    });
  }
  if (closeMap && frame && frameWrap && preview) {
    closeMap.addEventListener('click', function () {
      frame.removeAttribute('src');
      frame.hidden = true;
      frameWrap.hidden = true;
      preview.hidden = false;
    });
  }
})();

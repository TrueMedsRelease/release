export function initCategoriesNav() {
  const catNavOpeners = document.querySelectorAll('[data-cat-nav-opener]');
  const catNav = document.querySelector('.cat-nav');
  const catNavLinks = catNav.querySelectorAll('.sublist-toggler');
  const catSubLists = catNav.querySelectorAll('.nav__sublist');
  const catCloseButton = catNav.querySelector('.nav__close-button');
  const catOverlay = document.querySelector('.cat-overlay');
  const mqSmall = window.matchMedia('(max-width: 829.98px)');

  // Open categories menu
  catNavOpeners.forEach((button) => {
    button.addEventListener('click', () => {
      catNav.classList.toggle('is-visible');
      catOverlay.classList.toggle('is-visible');
      catNavOpeners.forEach((button) => {
        button.classList.toggle('is-active');
      });
      document.body.classList.toggle('is-fixed', catNav.classList.contains('is-visible'));
      document.body.classList.toggle(
        'has-scroll',
        document.body.scrollHeight > window.innerHeight && catNav.classList.contains('is-visible'),
      );
    });
  });

  // Toggle the categories menu state
  function toggleCatMenu() {
    if (mqSmall.matches) {
      catNavLinks.forEach((link) => {
        link.removeEventListener('mouseover', mouseoverCatlinksHandler);
        link.addEventListener('click', clickCatLinksHandler);
      });
      catSubLists.forEach((sublist) => sublist.classList.remove('is-visible'));
      return;
    }
    catNavLinks.forEach((link) => {
      link.removeEventListener('click', clickCatLinksHandler);
      link.addEventListener('mouseover', mouseoverCatlinksHandler);
    });
    catSubLists[0].classList.add('is-visible');
  }

  // Mobile categories menu
  function clickCatLinksHandler(e) {
    e.preventDefault();
    const link = e.currentTarget;
    catSubLists.forEach((sublist) => {
      if (sublist.dataset.sublistIndex === link.dataset.sublistIndex) {
        return sublist.classList.add('is-visible');
      }
      sublist.classList.remove('is-visible');
    });
  }

  // Desktop categories menu
  function mouseoverCatlinksHandler() {
    const link = this;
    link.classList.add('is-active');
    catNavLinks.forEach((otherLink) => {
      if (otherLink !== link) {
        otherLink.classList.remove('is-active');
      }
    });
    catSubLists.forEach((sublist) => {
      if (sublist.dataset.sublistIndex === link.dataset.sublistIndex) {
        return sublist.classList.add('is-visible');
      }
      sublist.classList.remove('is-visible');
    });
  }

  mqSmall.addEventListener('change', toggleCatMenu);

  toggleCatMenu();

  // Mobile categories menu return buttons
  const mobileNavReturnButtons = document.querySelectorAll('.nav__mobile-return');
  mobileNavReturnButtons.forEach((button) => {
    button.addEventListener('click', () => {
      const submenu = button.closest('.nav__sublist');
      submenu.classList.remove('is-visible');
    });
  });

  catOverlay.addEventListener('click', () => {
    closeCatNav();
  });

  catCloseButton.addEventListener('click', () => {
    closeCatNav();
  });

  window.addEventListener('click', (e) => {
    if (!catNav.contains(e.target) && !catNavOpeners[0].contains(e.target) && !catNavOpeners[1]?.contains(e.target)) {
      closeCatNav();
    }
  });

  function closeCatNav() {
    catNav.classList.remove('is-visible');
    catOverlay.classList.remove('is-visible');
    catNavOpeners.forEach((button) => {
      button.classList.remove('is-active');
    });
    document.body.classList.remove('is-fixed');
    document.body.classList.remove('has-scroll');
  }
}

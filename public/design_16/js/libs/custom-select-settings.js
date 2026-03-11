/* Custom select settings */
export function initCustomSelect() {
  if (typeof customSelect === 'undefined') return;

  const headerSelects = customSelect('.header-select');
  const selects = customSelect('.select');

  // Improve custom select aria
  const selectOpeners = document.querySelectorAll('.custom-select-opener');

  selectOpeners.forEach((opener) => {
    opener.setAttribute('aria-label', 'Select category');
  });

  // Inject data-flag into custom options
  document.querySelectorAll('.header-lang').forEach((customSelect) => {
    const instance = customSelect.querySelector('.header-select').customSelect;
    const select = instance.select;
    const options = customSelect.querySelectorAll('option');
    const customItems = customSelect.parentNode.querySelectorAll('.custom-select-option');

    options.forEach((option, index) => {
      const flag = option.dataset.flag;
      if (flag && customItems[index]) {
        customItems[index].setAttribute('data-flag', flag);
      }
    });

    // Set initial flag on opener
    const selectedOption = select.options[select.selectedIndex];
    const initialFlag = selectedOption.dataset.flag;
    if (initialFlag) {
      instance.opener.setAttribute('data-flag', initialFlag);
    }

    select.addEventListener('change', () => {
      const selectedOption = select.options[select.selectedIndex];
      const flag = selectedOption.dataset.flag;

      if (flag) {
        instance.opener.setAttribute('data-flag', flag);
      }
    });
  });
}

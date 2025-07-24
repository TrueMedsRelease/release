/* Pop-ups & modal dialogs */
import { createOverlay } from '../utils/utils';

export function initDialog() {
  const dialogs = document.querySelectorAll('.dialog-container');
  let dialogOverlay = document.querySelector('.dialog-overlay');

  dialogs.forEach((dialog) => {
    const openButtons = document.querySelectorAll(`[data-dialog=${dialog.dataset.name}]`);
    const closeButtons = dialog.querySelectorAll('.dialog__close-button');

    openButtons.forEach((openButton) => {
      openButton.addEventListener('click', () => {
        openDialog(openButton);
      });
    });

    closeButtons.forEach((closeButton) => {
      closeButton.addEventListener('click', () => {
        closeDialog();
      });
    });

    function openDialog(button) {
      const dialogName = button.getAttribute('data-dialog');
      const dialog = document.querySelector(`.dialog-container[data-name="${dialogName}"]`);

      if (dialog) {
        dialogOverlay || (dialogOverlay = createOverlay('dialog-overlay'));
        if (dialog.hasAttribute('data-modal')) {
          dialog.showModal();
          dialogOverlay.classList.add('is-visible');
        } else {
          dialog.show();
        }
        // Remove initial focus from the first interactive element inside the dialog
        dialog.focus();
      }
    }

    function closeDialog() {
      dialog.classList.add('close-dialog-animation');
      dialogOverlay.classList.remove('is-visible');
      // Reset listeners when Escape key is pressed before animation completion
      dialog.addEventListener('animationcancel', cancelDialogAnimation);
      dialog.addEventListener('animationend', completeDialogAnimation, { once: true });
    }

    function completeDialogAnimation() {
      dialog.classList.remove('close-dialog-animation');
      dialog.close();
      dialog.removeEventListener('animationcancel', cancelDialogAnimation);
    }

    function cancelDialogAnimation() {
      dialog.classList.remove('close-dialog-animation');
      dialog.removeEventListener('animationend', completeDialogAnimation);
    }

    dialog.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        dialogOverlay.classList.remove('is-visible');
      }
    });

    // Overlay appears beneath transparent .dialog-container backdrop
    if (dialog.hasAttribute('data-modal')) {
      dialog.addEventListener('click', (e) => {
        e.target === dialog && closeDialog();
      });
    }
  });
}

/* Check if the current browser supports dialogs */
export function checkDialogSupport() {
  document.addEventListener('DOMContentLoaded', function () {
    const dialog = document.querySelector('dialog');
    try {
      dialog && dialog.close();
    } catch (e) {
      const head = document.getElementsByTagName('HEAD')[0];
      const link = document.createElement('link');
      const script = document.createElement('script');
      const dialogs = document.querySelectorAll('dialog');
      link.rel = 'stylesheet';
      link.type = 'text/css';
      link.href = 'vendor/dialog-polyfill/dialog-polyfill.min.css';
      script.src = 'vendor/dialog-polyfill/dialog-polyfill.min.js';
      head.append(link, script);
      script.addEventListener('load', () => {
        dialogs.forEach((dialog) => {
          dialogPolyfill.registerDialog(dialog);
        });
      });
    }
  });
}

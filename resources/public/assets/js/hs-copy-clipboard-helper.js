(function () {
  window.addEventListener('load', () => {
    const $clipboards = document.querySelectorAll('.js-clipboard');
    $clipboards.forEach((el) => {
      const clipboard = new ClipboardJS(el, {
        text: (trigger) => {
          const clipboardText = trigger.dataset.clipboardText;
          
          if (clipboardText) return clipboardText;
          
          const clipboardTarget = trigger.dataset.clipboardTarget;
          const $element = document.querySelector(clipboardTarget);
          
          if (
            $element.tagName === 'SELECT'
            || $element.tagName === 'INPUT'
            || $element.tagName === 'TEXTAREA'
          ) return $element.value
          else return $element.textContent;
        }
      });
      clipboard.on('success', () => {
        const $default = el.querySelector('.js-clipboard-default');
        const $success = el.querySelector('.js-clipboard-success');
        const $successText = el.querySelector('.js-clipboard-success-text');
        const successText = el.dataset.clipboardSuccessText || '';
        const tooltip = el.closest('.hs-tooltip');
        let oldSuccessText;
        
        if ($successText) {
          oldSuccessText = $successText.textContent
          $successText.textContent = successText
        }
        if ($default && $success) {
          $default.style.display = 'none'
          $success.style.display = 'block'
        }
        if (tooltip) HSTooltip.show(tooltip);
        
        setTimeout(function () {
          if ($successText && oldSuccessText) $successText.textContent = oldSuccessText;
          if (tooltip) HSTooltip.hide(tooltip);
          if ($default && $success) {
            $success.style.display = '';
            $default.style.display = '';
          }
        }, 800);
      });
    });
  })
})();
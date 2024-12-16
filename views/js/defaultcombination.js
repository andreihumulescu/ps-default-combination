/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT Free License
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/mit
 *
 * @author    Andrei H
 * @copyright Since 2024 Andrei H
 * @license   MIT
 */

(function () {
  'use strict';

  function ready(fn) {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  }
  ready(init);

  function init() {
    attachEvents();
  }

  function attachEvents() {
    const setButton = document.getElementById('set-button');

    if (!setButton) {
      return;
    }

    setButton.addEventListener('click', async () => {
      const productIds = Array.from(document.querySelectorAll('.id-product')).map((el) => +el.innerHTML);

      if (productIds && productIds.length === 0) {
        return;
      }

      setButton.setAttribute('disabled', 'disabled');

      const response = await fetch(window.defaultcombination.url, {
        method: 'POST',
        body: JSON.stringify({ productIds }),
      });

      const result = await response.json();

      window.alert(result.message);

      if (result.success) {
        window.location.reload();
      }
    });
  }
})();

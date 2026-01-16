(() => {
  const flash = document.querySelector('[data-flash-autohide]');
  if (flash) setTimeout(() => flash.remove(), 4500);
})();

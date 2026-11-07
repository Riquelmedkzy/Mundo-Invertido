'use strict';
// Runs before paint: saved preference overrides the system default.
(() => {
 const media = matchMedia('(prefers-reduced-motion: reduce)');
 let saved = null;
 try { saved = localStorage.getItem('mundo-motion'); } catch {}
 let enabled = saved === null ? !media.matches : saved === 'on';
 const apply = () => { document.documentElement.classList.toggle('motion-off', !enabled); };
 window.worldMotion = {
  get enabled() { return enabled; },
  set(value) {
   enabled = Boolean(value); saved = enabled ? 'on' : 'off';
   try { localStorage.setItem('mundo-motion', saved); } catch {}
   apply(); window.dispatchEvent(new CustomEvent('world:motion', {detail: enabled}));
  }
 };
 media.addEventListener('change', () => {
  if(saved !== null) return;
  enabled = !media.matches; apply();
  window.dispatchEvent(new CustomEvent('world:motion', {detail: enabled}));
 });
 apply();
})();

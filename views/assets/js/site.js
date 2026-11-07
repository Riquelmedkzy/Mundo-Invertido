'use strict';
(() => {
 const enabled = () => window.worldMotion?.enabled ?? !matchMedia('(prefers-reduced-motion: reduce)').matches;
 const header=document.querySelector('.site-header'),menu=document.querySelector('.menu-toggle');
 const closeMenu=()=>{header?.classList.remove('menu-open');menu?.setAttribute('aria-expanded','false');};
 menu?.addEventListener('click',()=>{const open=menu.getAttribute('aria-expanded')!=='true';menu.setAttribute('aria-expanded',String(open));header.classList.toggle('menu-open',open);});
 document.addEventListener('keydown',e=>{if(e.key==='Escape' && header?.classList.contains('menu-open')){closeMenu();menu.focus();}});
 document.querySelectorAll('#navigation a').forEach(link=>link.addEventListener('click',closeMenu));
 const motionButtons=[...document.querySelectorAll('[data-motion-toggle]')];
 const showMotion=()=>motionButtons.forEach(b=>{b.setAttribute('aria-pressed',String(!enabled()));b.querySelector('span').textContent=enabled()?'Desativar animações':'Ativar animações';});
 motionButtons.forEach(b=>b.addEventListener('click',()=>window.worldMotion?.set(!enabled())));
 addEventListener('world:motion',showMotion);showMotion();
 // Random letter roll: same text, random stagger, stable width and accessible name.
 const rolls=[];
 if(window.gsap) document.querySelectorAll('#navigation>a:not(.nav-cta)').forEach(link=>{
  const label=link.textContent.trim();link.setAttribute('aria-label',label);link.textContent='';
  const word=document.createElement('span');word.className='swap-label';word.setAttribute('aria-hidden','true');
  for(const char of label){const slot=document.createElement('span');slot.className='swap-letter';const original=document.createElement('span');original.className='letter-original';original.textContent=char;const copy=document.createElement('span');copy.className='letter-copy';copy.textContent=char;slot.append(original,copy);word.append(slot);}link.append(word);
  // One reversible timeline: no CSS translate offset survives in the copy.
  const originals=word.querySelectorAll('.letter-original'),copies=word.querySelectorAll('.letter-copy');
  gsap.set(originals,{y:0,yPercent:0});gsap.set(copies,{y:0,yPercent:115});
  const timeline=gsap.timeline({paused:true});
  gsap.utils.shuffle([...word.children]).forEach((slot,i)=>{
   timeline.to(slot.children[0],{yPercent:-115,duration:.42,ease:'power3.inOut'},i*.025)
    .to(slot.children[1],{yPercent:0,duration:.42,ease:'power3.inOut'},i*.025);
  });
  const reset=()=>{timeline.pause(0);gsap.set(originals,{y:0,yPercent:0});gsap.set(copies,{y:0,yPercent:115});};
  const play=()=>{if(enabled())timeline.play();};
  const restore=()=>{if(enabled())timeline.reverse();else reset();};
  link.addEventListener('pointerenter',play);link.addEventListener('pointerleave',restore);
  link.addEventListener('focus',play);link.addEventListener('blur',restore);rolls.push(reset);
 });
 const audio=document.querySelector('#world-audio'),sounds=document.querySelectorAll('[data-sound]');let sound=false;
 const soundState=()=>sounds.forEach(b=>{b.setAttribute('aria-pressed',String(sound));b.textContent=sound?'Desativar som ♪':'Ativar som ♪';});
 sounds.forEach(b=>b.addEventListener('click',async()=>{if(!audio)return;if(sound){audio.pause();sound=false;}else{audio.volume=.2;try{await audio.play();sound=true;}catch{sound=false;b.textContent='Som indisponível';return;}}soundState();}));
 let normal=false;try{normal=localStorage.getItem('mundo-theme')==='normal';}catch{}
 const applyWorld=()=>{document.body.classList.toggle('world-normal',normal);document.body.dataset.world=normal?'normal':'inverted';document.querySelectorAll('[data-switch-world]').forEach(b=>{b.setAttribute('aria-pressed',String(normal));b.setAttribute('aria-label',normal?'Entrar no Mundo Invertido':'Voltar ao mundo normal');});if(audio){audio.src=audio.src.replace(/(?:normal|inverted)-world\.mpeg/,(normal?'normal':'inverted')+'-world.mpeg');if(sound)audio.play().catch(()=>{sound=false;soundState();});}};
 applyWorld();let flashTimer;
 document.querySelectorAll('[data-switch-world]').forEach(b=>b.addEventListener('click',()=>{normal=!normal;try{localStorage.setItem('mundo-theme',normal?'normal':'inverted');}catch{}if(enabled()){document.body.classList.remove('switching');requestAnimationFrame(()=>document.body.classList.add('switching'));clearTimeout(flashTimer);flashTimer=setTimeout(()=>document.body.classList.remove('switching'),850);}applyWorld();}));
 // Pointer parallax only schedules work after a pointer event, never an idle loop.
 const poster=document.querySelector('.poster');let pointerFrame=0;
 const resetPointer=()=>{cancelAnimationFrame(pointerFrame);poster?.style.setProperty('--pointer','0px');poster?.style.setProperty('--pointer-y','0px');};
 if(poster && matchMedia('(pointer: fine)').matches){poster.addEventListener('pointermove',e=>{if(!enabled())return;cancelAnimationFrame(pointerFrame);pointerFrame=requestAnimationFrame(()=>{const r=poster.getBoundingClientRect();poster.style.setProperty('--pointer',((e.clientX-r.left)/r.width-.5)*12+'px');poster.style.setProperty('--pointer-y',((e.clientY-r.top)/r.height-.5)*8+'px');});},{passive:true});poster.addEventListener('pointerleave',resetPointer);}
 // Curtain progress is calculated only while visible and only when scrolling.
 const curtain=document.querySelector('[data-footer-reveal]');let footerVisible=false,footerFrame=0;
 const updateFooter=()=>{footerFrame=0;if(!curtain || !enabled())return;const r=curtain.getBoundingClientRect();const progress=Math.max(0,Math.min(1,(innerHeight-r.top)/Math.min(innerHeight,r.height)));curtain.style.setProperty('--footer-progress',progress.toFixed(3));};
 const scheduleFooter=()=>{if(footerVisible && enabled() && !footerFrame)footerFrame=requestAnimationFrame(updateFooter);};
 if(curtain && 'IntersectionObserver' in window){new IntersectionObserver(entries=>{footerVisible=entries[0].isIntersecting;curtain.querySelector('footer').inert=enabled()&&!footerVisible;curtain.classList.toggle('footer-in-view',footerVisible);if(footerVisible)scheduleFooter();}).observe(curtain);addEventListener('scroll',scheduleFooter,{passive:true});addEventListener('resize',scheduleFooter,{passive:true});}
 addEventListener('world:motion',()=>{rolls.forEach(reset=>reset());resetPointer();document.body.classList.remove('switching');if(curtain){curtain.style.setProperty('--footer-progress','1');curtain.querySelector('footer').inert=enabled()&&!footerVisible;}scheduleFooter();});
 document.addEventListener('visibilitychange',()=>{curtain?.classList.toggle('footer-in-view',footerVisible&&!document.hidden);});
 document.querySelectorAll('form').forEach(form=>form.addEventListener('submit',()=>{if(!form.checkValidity())return;const button=form.querySelector('button[type="submit"],button:not([type])');if(button){button.disabled=true;button.dataset.original=button.textContent;button.textContent='Enviando…';}}));
 addEventListener('pageshow',()=>document.querySelectorAll('button[data-original]').forEach(button=>{button.disabled=false;button.textContent=button.dataset.original;delete button.dataset.original;}));
})();

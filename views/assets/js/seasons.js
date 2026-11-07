'use strict';
(() => {
 const hero=document.querySelector('[data-seasons]');if(!hero)return;
 const choices=[...hero.querySelectorAll('[data-season]')],scenes=[...hero.querySelectorAll('[data-season-scene]')],details=[...document.querySelectorAll('[data-season-detail]')];
 const copy=hero.querySelector('.season-copy'),title=hero.querySelector('h1'),intro=hero.querySelector('[data-season-intro]'),meta=hero.querySelector('[data-season-meta]'),ambient=hero.querySelector('.season-ambient img'),announcement=hero.querySelector('[data-season-announcement]'),error=hero.querySelector('[data-season-error]');
 let active=0,requested=0,busy=false,queued=null,timeline=null,finishCurrent=null;
 const enabled=()=>window.worldMotion?.enabled ?? !matchMedia('(prefers-reduced-motion: reduce)').matches;
 function stopVideos(){document.querySelectorAll('.season-video video').forEach(v=>v.pause());document.querySelectorAll('.season-video iframe').forEach(f=>{f.previousElementSibling.hidden=false;f.remove();});}
 function content(index){
  const button=choices[index];
  button.dataset.lines.split('|').forEach((line,i)=>{title.querySelectorAll('.title-mask>span')[i].textContent=line;});
  meta.replaceChildren(document.createTextNode('Temporada '+(index+1)+' '));const info=document.createElement('span');info.textContent=button.dataset.year+' · '+button.dataset.episodes+' episódios';meta.append(info);intro.textContent=button.dataset.intro;
  hero.querySelector('[data-hero-trailer]').href='#trailer-'+(index+1);hero.querySelector('[data-hero-story]').href='#history-'+(index+1);
  hero.querySelector('[data-season-number]').textContent=String(index+1).padStart(2,'0');hero.querySelector('[data-season-counter]').textContent=String(index+1).padStart(2,'0')+' / 05';
  choices.forEach((b,i)=>{b.classList.toggle('active',i===index);b.setAttribute('aria-current',String(i===index));});details.forEach((d,i)=>{d.hidden=i!==index;});
  stopVideos();announcement.textContent='Temporada '+(index+1)+'. '+button.dataset.title;active=index;
 }
 async function select(index){
  index=(index+choices.length)%choices.length;requested=index;
  if(busy){queued=index;return;}if(index===active)return;
  busy=true;hero.setAttribute('aria-busy','true');error.hidden=true;
  const next=scenes[index],prev=scenes[active],image=next.querySelector('img');
  try{image.loading='eager';if(!image.complete)await image.decode();if(!image.naturalWidth)throw new Error('image');}
  catch{error.textContent='Não foi possível carregar esta temporada. Tente novamente.';error.hidden=false;busy=false;hero.removeAttribute('aria-busy');const pending=queued;queued=null;if(pending!==null&&pending!==index)select(pending);return;}
  next.hidden=false;
  const finish=()=>{
   prev.hidden=true;next.hidden=false;
   if(window.gsap){gsap.set([prev,next,prev.querySelector('img'),image,copy,ambient,...title.querySelectorAll('.title-mask>span'),meta,intro,hero.querySelector('.season-actions')],{clearProps:'transform,opacity,zIndex,willChange'});}
   busy=false;hero.removeAttribute('aria-busy');finishCurrent=null;timeline=null;
   const pending=queued;queued=null;if(pending!==null&&pending!==active)select(pending);
  };
  finishCurrent=()=>{timeline?.kill();content(index);ambient.src=image.src;finish();};
  if(!enabled()||!window.gsap){content(index);ambient.src=image.src;finish();return;}
  gsap.set(next,{zIndex:1});gsap.set(prev,{zIndex:2,willChange:'transform'});gsap.set(image,{scale:1.12});
  timeline=gsap.timeline({onComplete:finish});
  timeline.to(copy,{opacity:0,y:-12,duration:.18,ease:'power2.in'},0)
   .to(prev,{yPercent:105,duration:1.05,ease:'power3.inOut'},0)
   .to(prev.querySelector('img'),{scale:1.09,duration:1.05,ease:'power3.inOut'},0)
   .to(image,{scale:1,duration:1.2,ease:'power3.out'},.08)
   .to(ambient,{opacity:0,duration:.28},0)
   .call(()=>{ambient.src=image.src;content(index);gsap.set(copy,{opacity:1,y:0});gsap.set(title.querySelectorAll('.title-mask>span'),{yPercent:110});gsap.set([meta,intro,hero.querySelector('.season-actions')],{opacity:0,y:15});},null,.28)
   .to(ambient,{opacity:.3,duration:.7,ease:'power2.out'},.3)
   .to('.season-copy .title-mask>span',{yPercent:0,duration:.75,stagger:.08,ease:'power3.out'},.4)
   .to([meta,intro,hero.querySelector('.season-actions')],{opacity:1,y:0,duration:.55,stagger:.09,ease:'power3.out'},.65);
 }
 choices.forEach((b,index)=>b.addEventListener('click',e=>{e.preventDefault();select(index);}));
 hero.querySelector('[data-season-prev]').addEventListener('click',()=>select((queued??requested)-1));hero.querySelector('[data-season-next]').addEventListener('click',()=>select((queued??requested)+1));
 hero.addEventListener('keydown',e=>{if(e.altKey||e.ctrlKey||e.metaKey||!['ArrowLeft','ArrowRight','Home','End'].includes(e.key)||e.target.closest('input,textarea,select,video,iframe'))return;e.preventDefault();const next=e.key==='Home'?0:e.key==='End'?4:(queued??requested)+(e.key==='ArrowRight'?1:-1);const index=(next+5)%5;select(index);choices[index].focus({preventScroll:true});});
 addEventListener('world:motion',()=>{if(!enabled())finishCurrent?.();});
 document.querySelectorAll('[data-video-id]').forEach(button=>button.addEventListener('click',()=>{
  stopVideos();const preview=button.closest('[data-video-preview]'),iframe=document.createElement('iframe');iframe.title=button.getAttribute('aria-label');iframe.src='https://www.youtube-nocookie.com/embed/'+encodeURIComponent(button.dataset.videoId)+'?autoplay=1&rel=0';iframe.allow='autoplay; encrypted-media; picture-in-picture; fullscreen';iframe.allowFullscreen=true;iframe.referrerPolicy='strict-origin-when-cross-origin';preview.hidden=true;preview.after(iframe);iframe.focus();
 }));
 details.forEach((d,i)=>{d.hidden=i!==0;});
 // Deep links remain valid; without JavaScript all five articles remain visible.
 const initial=location.hash.match(/^#(?:season|history|trailer)-(\d)$/);if(initial){const index=Number(initial[1])-1;if(index>=0&&index<5){scenes[0].hidden=index!==0;scenes[index].hidden=false;ambient.src=scenes[index].querySelector('img').src;content(index);requestAnimationFrame(()=>document.getElementById(location.hash.slice(1))?.scrollIntoView());}}
})();

'use strict';
(() => {
 // One canvas. Bounded resolution, particle count and 30fps, independent of React/DOM.
 const canvas=document.querySelector('[data-rain]');if(!canvas)return;
 const ctx=canvas.getContext('2d',{alpha:true});if(!ctx)return;
 const surface=canvas.parentElement;
 let width=0,height=0,particles=[],frame=0,last=0,visible=true;
 const pointer={x:-1000,y:-1000};
 const enabled=()=>window.worldMotion?.enabled ?? !matchMedia('(prefers-reduced-motion: reduce)').matches;
 const resize=()=>{const r=surface.getBoundingClientRect();width=r.width;height=r.height;const dpr=Math.min(devicePixelRatio||1,1.25);canvas.width=Math.round(width*dpr);canvas.height=Math.round(height*dpr);ctx.setTransform(dpr,0,0,dpr,0,0);particles=Array.from({length:Math.min(180,Math.round(width*height/5200))},()=>({x:Math.random()*width,y:Math.random()*height,speed:.5+Math.random()*1.5,len:3+Math.random()*11,alpha:.18+Math.random()*.42,ox:0,oy:0,vx:0,vy:0,red:Math.random()>.65}));};
 const draw=now=>{frame=0;if(!enabled()||!visible||document.hidden)return;frame=requestAnimationFrame(draw);if(now-last<33.3)return;const dt=Math.min((now-last)/33.3,2);last=now;ctx.clearRect(0,0,width,height);
  for(const p of particles){p.y+=p.speed*dt;p.x+=.12*dt;if(p.y>height+40){p.y=-30;p.x=Math.random()*width;p.ox=p.oy=p.vx=p.vy=0;}
   const dx=p.x+p.ox-pointer.x,dy=p.y+p.oy-pointer.y,d2=dx*dx+dy*dy;
   if(d2<19600){const distance=Math.sqrt(d2)||1,force=(1-distance/140)*2.8*dt;p.vx+=dx/distance*force;p.vy+=dy/distance*force;}
   p.vx=(p.vx-p.ox*.012*dt)*.88;p.vy=(p.vy-p.oy*.012*dt)*.88;p.ox+=p.vx*dt;p.oy+=p.vy*dt;
   const x=p.x+p.ox,y=p.y+p.oy;ctx.globalAlpha=p.alpha;ctx.strokeStyle=p.red?'#fa365d':'#d4bfc9';ctx.lineWidth=p.red?.85:.65;ctx.beginPath();ctx.moveTo(x,y);ctx.lineTo(x-.8-p.vx*.8,y-p.len-p.vy*.8);ctx.stroke();
  }ctx.globalAlpha=1;
 };
 const sync=()=>{cancelAnimationFrame(frame);frame=0;last=0;if(enabled()&&visible&&!document.hidden){frame=requestAnimationFrame(draw);}else{ctx.clearRect(0,0,width,height);pointer.x=pointer.y=-1000;}};
 surface.addEventListener('pointermove',e=>{if(!enabled()||!visible||e.pointerType==='touch')return;const r=canvas.getBoundingClientRect();pointer.x=e.clientX-r.left;pointer.y=e.clientY-r.top;},{passive:true});surface.addEventListener('pointerleave',()=>{pointer.x=pointer.y=-1000;},{passive:true});
 if('ResizeObserver' in window)new ResizeObserver(()=>{resize();sync();}).observe(surface);else addEventListener('resize',()=>{resize();sync();},{passive:true});
 if('IntersectionObserver' in window)new IntersectionObserver(e=>{visible=e[0].isIntersecting;sync();}).observe(surface);
 document.addEventListener('visibilitychange',sync);addEventListener('world:motion',sync);addEventListener('pagehide',()=>cancelAnimationFrame(frame));addEventListener('pageshow',sync);resize();sync();
})();

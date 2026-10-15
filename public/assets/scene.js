/** Interactive CV studio. All Three.js assets are served locally. */
export async function mountScene(canvas) {
  const THREE = await import('/assets/vendor/three.module.js');
  const preference = matchMedia('(prefers-reduced-motion: reduce)');
  let reduced = preference.matches, paused = false, disposed = false;
  const host = canvas.parentElement;
  host.dataset.mode='0';
  let renderer;
  try { renderer = new THREE.WebGLRenderer({canvas, antialias:true, alpha:true, powerPreference:'low-power'}); }
  catch { canvas.hidden = true; const fallback=document.createElement('div'); fallback.className='scene-fallback'; fallback.innerHTML='<span>DENİZ YILMAZ</span><strong>Deneyimin.<br>Becerilerin.<br>Yeni bir sayfa.</strong><p>Okunabilir, sade, sana ait.</p>'; host.prepend(fallback); return; }
  renderer.setPixelRatio(Math.min(devicePixelRatio, 1.65));
  renderer.setClearColor(0x171914, 0); renderer.outputColorSpace=THREE.SRGBColorSpace;
  const scene = new THREE.Scene();
  const camera = new THREE.PerspectiveCamera(33, 1, .1, 60);
  camera.position.set(0, .05, 8.8);
  scene.add(new THREE.AmbientLight(0xf0f5df, 2.1));
  const light = new THREE.DirectionalLight(0xfff9df, 3.2); light.position.set(-3,6,7); scene.add(light);
  const rim = new THREE.DirectionalLight(0xd5ed87, 2.3); rim.position.set(4,0,-3); scene.add(rim);
  const group = new THREE.Group(); scene.add(group);
  group.rotation.set(-.09,-.30,-.12);
  const textures=[];
  function paperTexture(kind) {
    const surface=document.createElement('canvas');surface.width=960;surface.height=1280;
    const ctx=surface.getContext('2d');
    const accent=kind===1?'#52683b':kind===2?'#4e5941':'#303c28';
    ctx.fillStyle=kind===1?'#e4e8d8':kind===2?'#e9eadd':'#f2f1e8';ctx.fillRect(0,0,960,1280);
    ctx.fillStyle=accent;ctx.fillRect(74,74,64,7);
    ctx.font='16px Arial';ctx.fillStyle='#7b846e';ctx.fillText(kind===1?'HEDEF ROL / GEREKSİNİMLER':'ÖZGEÇMİŞ / 2026',74,123);
    ctx.fillStyle=accent;ctx.font='bold 57px Arial';
    ctx.fillText(kind===1?'FRONTEND':'DENİZ',74,205);ctx.fillText(kind===1?'DEVELOPER':'YILMAZ',74,271);
    ctx.font='24px Arial';ctx.fillStyle='#667259';ctx.fillText(kind===1?'İlanınla aynı dili konuş.':'Yazılım Geliştirici',76,320);
    ctx.font='13px Arial';ctx.fillText(kind===1?'İstanbul  /  Hibrit  /  Tam zamanlı':'İstanbul  ·  deniz@example.com  ·  github.com/deniz',76,362);
    ctx.fillStyle='#bfc8b2';ctx.fillRect(74,394,812,2);
    const line=(y,w=790,col='#b6bfa9')=>{ctx.fillStyle=col;ctx.fillRect(74,y,w,8);};
    const section=(title,y)=>{ctx.font='bold 16px Arial';ctx.fillStyle=accent;ctx.fillText(title,74,y);};
    section(kind===1?'ARADIĞIMIZ DENEYİM':'PROFESYONEL ÖZET',448);
    line(480);line(507,760);line(534,650);
    section(kind===1?'TEKNİK YETKİNLİKLER':'DENEYİM',614);
    ctx.font='bold 21px Arial';ctx.fillStyle=accent;ctx.fillText(kind===1?'React · TypeScript · REST API':'Frontend Developer',74,655);
    ctx.font='15px Arial';ctx.fillStyle='#7a856c';ctx.fillText(kind===1?'Ürün geliştirme deneyimi':'2023 — 2026  /  İstanbul',74,686);
    line(721,790);line(747,710);line(773,750);line(799,510);
    section(kind===1?'SENİNLE EŞLEŞENLER':'EĞİTİM',875);line(906,610);line(932,410);
    section(kind===1?'GELİŞTİRİLEBİLECEK ALANLAR':'BECERİLER',1012);
    let x=74;for(const word of (kind===1?['SQL','Docker','İngilizce']:['React','TypeScript','Figma'])){ctx.font='17px Arial';const w=ctx.measureText(word).width+40;ctx.fillStyle=kind===1?'#cddbb6':'#dce3ce';ctx.fillRect(x,1043,w,38);ctx.fillStyle='#4c6136';ctx.fillText(word,x+20,1068);x+=w+12;}
    ctx.fillStyle='#bdc6b0';ctx.fillRect(74,1175,812,1);ctx.font='13px Arial';ctx.fillStyle='#6b795b';ctx.fillText('CVera / Yeni bir sayfa.',74,1211);ctx.fillText('01',856,1211);
    const texture=new THREE.CanvasTexture(surface);texture.colorSpace=THREE.SRGBColorSpace;texture.anisotropy=Math.min(renderer.capabilities.getMaxAnisotropy(),8);textures.push(texture);return texture;
  }
  const documents=[];
  for(let i=0;i<3;i++){
    const side=new THREE.MeshStandardMaterial({color:i===0?0xe2e4d7:0xb2c49b,roughness:.75});
    const front=new THREE.MeshBasicMaterial({map:paperTexture(i)});
    const back=new THREE.MeshStandardMaterial({color:i===1?0xb8ce91:0x9dae82,roughness:.75});
    const mesh=new THREE.Mesh(new THREE.BoxGeometry(2.65,3.54,.035),[side,side,side,side,front,back]);
    group.add(mesh);documents.push(mesh);
  }
  const scanMaterial=new THREE.MeshBasicMaterial({color:0xb3d56e,transparent:true,opacity:.8,depthWrite:false});
  const scan=new THREE.Mesh(new THREE.PlaneGeometry(2.66,.018),scanMaterial);documents[0].add(scan);scan.position.z=.021;
  const glow=new THREE.Mesh(new THREE.PlaneGeometry(2.66,.24),new THREE.MeshBasicMaterial({color:0xc3e987,transparent:true,opacity:.16,depthWrite:false}));glow.position.y=.1;scan.add(glow);
  const orbitMaterial=new THREE.MeshBasicMaterial({color:0x8b9e65,transparent:true,opacity:.28});
  const orbit=new THREE.Mesh(new THREE.TorusGeometry(2.8,.004,5,128),orbitMaterial);orbit.rotation.set(.6,.4,-.3);orbit.position.z=-.8;scene.add(orbit);
  const nodes=[];for(let i=0;i<4;i++){const dot=new THREE.Mesh(new THREE.OctahedronGeometry(.045,0),new THREE.MeshStandardMaterial({color:0xc6df8d,roughness:.3,metalness:.5}));const a=i*Math.PI/2+.4;dot.position.set(Math.cos(a)*2.7,Math.sin(a)*2.3,-.6);scene.add(dot);nodes.push(dot);}
  let mode=0,visible=true,frame=0,pointer={x:0,y:0},drag=false,scroll=0,started=0,last=0;
  const formation=[
    [{x:-.06,y:0,z:.18,rz:0,ry:0},{x:.21,y:.02,z:-.04,rz:-.06,ry:0},{x:-.19,y:.04,z:-.23,rz:.065,ry:0}],
    [{x:-.77,y:-.12,z:.21,rz:.09,ry:.11},{x:.91,y:.2,z:-.09,rz:-.085,ry:-.1},{x:.15,y:.1,z:-.37,rz:0,ry:0}],
    [{x:0,y:.14,z:.35,rz:0,ry:0},{x:1.04,y:-.04,z:0,rz:-.22,ry:-.1},{x:-1.04,y:-.04,z:-.06,rz:.22,ry:.1}]
  ];
  const modes=['Analiz','Eşleştir','Oluştur'];
  const controls=document.createElement('div');controls.className='scene-controls';controls.setAttribute('role','group');controls.setAttribute('aria-label','Üç boyutlu CV görünümü');
  const modeButtons=modes.map((label,index)=>{const button=document.createElement('button');button.type='button';button.className='scene-mode';button.textContent=label;button.setAttribute('aria-pressed',String(index===0));button.addEventListener('click',()=>{mode=index;modeButtons.forEach((b,i)=>b.setAttribute('aria-pressed',String(index===i)));host.dataset.mode=String(index);const number=host.querySelector('.scene-topline>span:last-child');if(number)number.textContent='0'+(index+1)+' / 03';draw(performance.now(),reduced||paused);start();});controls.append(button);return button;});host.append(controls);
  const toggle=document.createElement('button');toggle.type='button';toggle.className='scene-motion';toggle.textContent=reduced?'▶':'Ⅱ';toggle.setAttribute('aria-label',reduced?'Hareketi başlat':'Hareketi durdur');toggle.setAttribute('aria-pressed',String(!reduced));host.append(toggle);
  const toggleMotion=()=>{if(reduced){reduced=false;paused=false;}else paused=!paused;toggle.textContent=paused?'▶':'Ⅱ';toggle.setAttribute('aria-label',paused?'Hareketi başlat':'Hareketi durdur');toggle.setAttribute('aria-pressed',String(!paused));if(paused){cancelAnimationFrame(frame);frame=0;}else start();};toggle.addEventListener('click',toggleMotion);
  const move=e=>{if(e.pointerType==='touch'&&!drag)return;const b=canvas.getBoundingClientRect();pointer.x=Math.max(-.7,Math.min(.7,(e.clientX-b.left)/b.width-.5));pointer.y=Math.max(-.7,Math.min(.7,(e.clientY-b.top)/b.height-.5));if(reduced||paused)draw(performance.now(),true);};
  const leave=()=>{if(!drag){pointer.x=pointer.y=0;if(reduced||paused)draw(performance.now(),true);}};
  const down=e=>{drag=true;canvas.setPointerCapture?.(e.pointerId);move(e);};const up=e=>{drag=false;if(canvas.hasPointerCapture?.(e.pointerId))canvas.releasePointerCapture(e.pointerId);leave();};
  canvas.addEventListener('pointermove',move);canvas.addEventListener('pointerleave',leave);canvas.addEventListener('pointerdown',down);canvas.addEventListener('pointerup',up);canvas.addEventListener('pointercancel',up);
  
  function draw(now,snap=false){
    if(disposed)return;
    if(!started)started=now;const t=(now-started)*.001;const delta=Math.min(40,now-(last||now));last=now;const ease=snap?1:1-Math.exp(-delta/110);
    const motion=!reduced&&!paused;
    if(motion){const b=host.getBoundingClientRect();scroll=Math.max(0,Math.min(1,-b.top/(b.height*.95)));}
    const targetY=-.28+pointer.x*.75+(motion?scroll*.3:0);const targetX=-.1+pointer.y*.35;
    group.rotation.y+=(targetY-group.rotation.y)*ease;group.rotation.x+=(targetX-group.rotation.x)*ease;
    group.rotation.z+=(-.105+(motion?scroll*.13:0)-group.rotation.z)*ease;
    group.position.y=motion?Math.sin(t*.65)*.045-scroll*.13:0;
    const targetScale=mode===0?1:mode===1?.85:.8;group.scale.lerp(new THREE.Vector3(targetScale,targetScale,targetScale),ease);
    documents.forEach((paper,i)=>{const f=formation[mode][i];paper.position.x+=(f.x+(motion&&i>0?scroll*(i===1?.25:-.25):0)-paper.position.x)*ease;paper.position.y+=(f.y-paper.position.y)*ease;paper.position.z+=(f.z-paper.position.z)*ease;paper.rotation.z+=(f.rz-paper.rotation.z)*ease;paper.rotation.y+=(f.ry-paper.rotation.y)*ease;});
    scan.visible=mode!==2;scan.position.y=motion?1.54-((t*.55)%3.05):.45;scanMaterial.opacity=mode===1?.45:.8;
    orbit.rotation.z=motion?-.3+t*.025:-.3;nodes.forEach((node,i)=>{node.rotation.y=motion?t*.3+i:i;});
    renderer.render(scene,camera);canvas.dataset.sceneReady='true';
  }
  function loop(now){frame=0;if(disposed||!visible||document.hidden||reduced||paused)return;draw(now);frame=requestAnimationFrame(loop);}
  function start(){if(!frame&&!disposed&&visible&&!document.hidden&&!reduced&&!paused)frame=requestAnimationFrame(loop);}
  function resize(){const b=canvas.getBoundingClientRect();if(!b.width||!b.height)return;renderer.setSize(b.width,b.height,false);camera.aspect=b.width/b.height;camera.position.z=b.width<390?9.8:8.8;camera.updateProjectionMatrix();draw(performance.now(),true);}
  const ro=new ResizeObserver(resize);ro.observe(canvas);
  const io=new IntersectionObserver(entries=>{visible=entries[0].isIntersecting;if(visible)start();else{cancelAnimationFrame(frame);frame=0;}},{rootMargin:'50px'});io.observe(canvas);
  const visibility=()=>{if(document.hidden){cancelAnimationFrame(frame);frame=0;}else{last=0;start();}};document.addEventListener('visibilitychange',visibility);
  const changePreference=()=>{reduced=preference.matches;paused=false;toggle.textContent=reduced?'▶':'Ⅱ';toggle.setAttribute('aria-label',reduced?'Hareketi başlat':'Hareketi durdur');toggle.setAttribute('aria-pressed',String(!reduced));if(reduced){cancelAnimationFrame(frame);frame=0;draw(performance.now(),true);}else start();};preference.addEventListener('change',changePreference);
  // Progressive enhancement: content remains readable before any observer fires.
  const revealObserver=new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('is-shown');if(!reduced)entry.target.animate([{opacity:.35,transform:'translateY(24px)'},{opacity:1,transform:'translateY(0)'}],{duration:850,easing:'cubic-bezier(.22,1,.36,1)'});revealObserver.unobserve(entry.target);}}),{threshold:.12});
  document.querySelectorAll('.section-heading,.analysis-feature,.feature-row,.template-intro,.paper-gallery,.step,.tools-heading,.tool-link,.faq-section,.cta-panel').forEach((el,i)=>{el.classList.add('motion-enter');revealObserver.observe(el);});
  const rewriteButton=document.querySelector('.rewrite-toggle');
  const rewriteExample=document.querySelector('.rewrite-example');
  const rewrite=()=>{const applied=rewriteButton.getAttribute('aria-pressed')!=='true';rewriteButton.setAttribute('aria-pressed',String(applied));rewriteExample.textContent=applied?'“React ile 12 proje geliştirdim; sayfa yüklenme süresini %35 azalttım.”':'“React projeleri geliştirdim.”';rewriteButton.innerHTML=applied?'İlk hâlini gör <span aria-hidden="true">↶</span>':'Örneği iyileştir <span aria-hidden="true">↗</span>';};
  rewriteButton?.addEventListener('click',rewrite);
  const gallery=document.querySelector('.paper-gallery');
  const galleryMove=e=>{if(reduced||e.pointerType==='touch')return;const b=gallery.getBoundingClientRect();const x=(e.clientX-b.left)/b.width-.5;const y=(e.clientY-b.top)/b.height-.5;gallery.style.transform='rotateY('+x*13+'deg) rotateX('+(-y*9)+'deg)';};
  const galleryLeave=()=>{gallery.style.transform='';};gallery?.addEventListener('pointermove',galleryMove);gallery?.addEventListener('pointerleave',galleryLeave);
  resize();draw(performance.now(),true);start();
  window.addEventListener('pagehide',()=>{disposed=true;cancelAnimationFrame(frame);ro.disconnect();io.disconnect();revealObserver.disconnect();document.removeEventListener('visibilitychange',visibility);preference.removeEventListener('change',changePreference);canvas.removeEventListener('pointermove',move);canvas.removeEventListener('pointerleave',leave);canvas.removeEventListener('pointerdown',down);canvas.removeEventListener('pointerup',up);canvas.removeEventListener('pointercancel',up);gallery?.removeEventListener('pointermove',galleryMove);gallery?.removeEventListener('pointerleave',galleryLeave);toggle.removeEventListener('click',toggleMotion);rewriteButton?.removeEventListener('click',rewrite);const geometries=new Set(),materials=new Set();scene.traverse(object=>{if(object.geometry)geometries.add(object.geometry);if(object.material)[].concat(object.material).forEach(material=>materials.add(material));});geometries.forEach(geometry=>geometry.dispose());materials.forEach(material=>material.dispose());textures.forEach(texture=>texture.dispose());renderer.dispose();},{once:true});
}

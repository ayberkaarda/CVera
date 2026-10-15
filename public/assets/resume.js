import {layouts} from './templates.js';
const escape = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
const labels = {
  tr:{summary:'Profesyonel Özet',experience:'Deneyim',education:'Eğitim',skills:'Beceriler',projects:'Projeler',languages:'Diller',certifications:'Sertifikalar',contact:'İletişim'},
  en:{summary:'Professional Summary',experience:'Experience',education:'Education',skills:'Skills',projects:'Projects',languages:'Languages',certifications:'Certifications',contact:'Contact'}
};
export function renderResume(data, {thumbnail=false}={}) {
  const template=layouts.find(t=>t.id===data.template)||layouts.find(t=>t.id==='modern');
  const copy=labels[data.language==='en'?'en':'tr'];
  const initials=(data.name||'CV').trim().split(/\s+/).slice(0,2).map(w=>w[0]).join('');
  const validPhoto=/^data:image\/(jpeg|png|webp);base64,[A-Za-z0-9+/=]+$/.test(data.photo||'');
  const portrait=template.photo?'<div class="cv-portrait">'+(validPhoto?'<img src="'+data.photo+'" alt="'+escape(data.name||'')+'">':'<span>'+escape(initials)+'</span>')+'</div>':'';
  const identity='<div class="cv-identity"><h2>'+escape(data.name||(data.language==='en'?'Your name':'Adın Soyadın'))+'</h2><p class="resume-role">'+escape(data.role||(data.language==='en'?'Your role':'Hedef pozisyonun'))+'</p></div>';
  const contact='<div class="resume-contact">'+[data.email,data.phone,data.city,data.link].filter(Boolean).map(s=>'<span>'+escape(s)+'</span>').join('')+'</div>';
  const section=(key,index)=>data[key]?'<section class="cv-section cv-section-'+key+'"><h3>'+(template.variant==='indexed'?'<span class="cv-section-number">'+String(index+1).padStart(2,'0')+'</span>':'')+copy[key]+'</h3><p>'+escape(data[key])+'</p></section>':'';
  let content;
  if(template.columns===2){
    content='<div class="cv-columns"><aside class="cv-sidebar"><header>'+portrait+identity+'</header><section class="cv-contact-block"><h3>'+copy.contact+'</h3>'+contact+'</section>'+['skills','certifications','languages'].map(section).join('')+'</aside><div class="cv-main">'+['summary','experience','education','projects'].map(section).join('')+'</div></div>';
  }else{
    const header=template.variant==='duo'?'<header class="cv-header"><div class="cv-name-panel">'+identity+'</div><div class="cv-contact-panel">'+contact+portrait+'</div></header>':'<header class="cv-header cv-photo-'+template.photo+'">'+portrait+identity+contact+'</header>';
    content=header+'<div class="cv-body">'+['summary','experience','education','projects','skills','certifications','languages'].map(section).join('')+'</div>';
  }
  return '<article class="resume-preview cv-sheet cv-'+template.variant+(thumbnail?' cv-thumbnail':'')+'" data-template="'+template.id+'" style="--cv-accent:'+template.accent+';--cv-font:'+template.font+'">'+content+'</article>';
}

export async function portraitData(file){
  if(!file||!['image/jpeg','image/png','image/webp'].includes(file.type)||file.size>5*1024*1024)throw new Error('En fazla 5 MB PNG, JPG veya WebP fotoğraf seç.');
  const image=await createImageBitmap(file),canvas=document.createElement('canvas');
  canvas.width=canvas.height=220;
  const size=Math.min(image.width,image.height);
  canvas.getContext('2d').drawImage(image,(image.width-size)/2,(image.height-size)/2,size,size,0,0,220,220);
  image.close();
  return canvas.toDataURL('image/jpeg',.8);
}

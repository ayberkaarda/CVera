import {chromium} from 'playwright';
import fs from 'node:fs/promises';
import {portfolioLayouts,portfolioDocument} from '../public/assets/portfolio-templates.js';

const data={name:'Deniz Yılmaz',role:'Frontend Developer',email:'deniz@example.com',city:'İstanbul',summary:'Kullanıcı deneyimine ve erişilebilirliğe odaklanan yazılım geliştirici. React ve TypeScript ile web uygulamaları geliştiriyorum.',experience:'Frontend Developer | Örnek Teknoloji | 2023 – 2026\nReact ve TypeScript ile 12 proje geliştirdim.',projects:'Erişilebilir Görev Yönetim Uygulaması\nReact ve REST API ile görev takibi.',skills:'React, TypeScript, JavaScript, HTML, CSS, Figma',education:'Bilgisayar Mühendisliği | 2019 – 2023'};
await fs.mkdir('public/assets/portfolio-previews',{recursive:true});
const browser=await chromium.launch();
try{
 const page=await browser.newPage({viewport:{width:1200,height:800},deviceScaleFactor:1});
 for(const template of portfolioLayouts){
  await page.setContent(portfolioDocument({...data,portfolioTemplate:template.id,color:template.accent}));
  await page.screenshot({path:'public/assets/portfolio-previews/'+template.id+'.webp',type:'webp',quality:85});
 }
}finally{await browser.close();}
console.log('18 portfolio previews saved.');

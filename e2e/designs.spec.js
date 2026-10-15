import {test,expect} from '@playwright/test';
import {layouts} from '../public/assets/templates.js';
import {portfolioLayouts,portfolioDocument} from '../public/assets/portfolio-templates.js';

test('all CV designs preserve content, create distinct layouts and print',async({page})=>{
 await page.goto('/cv-olustur');await page.locator('#load-sample').click();
 expect(new Set(layouts.map(t=>t.name)).size).toBe(25);
 for(const t of layouts){
  await page.locator('#template').selectOption(t.id);
  await expect(page.locator('.cv-sheet')).toHaveClass(new RegExp('cv-'+t.variant));
  await expect(page.locator('.cv-sheet')).toContainText('Deniz Yılmaz');
  await expect(page.locator('.cv-sheet')).toContainText('12 proje geliştirdim');
  expect(await page.locator('.cv-sidebar').count()).toBe(t.columns===2?1:0);
  expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),t.name).toBeTruthy();
 }
 await page.locator('#template').selectOption('technology');
 await page.evaluate(()=>window.print=()=>{});await page.locator('#print-cv').click();
 await page.emulateMedia({media:'print'});await expect(page.locator('#print-root .cv-sidebar')).toBeVisible();
});

test('gallery uses the same CV renderer as the editor and has preview dialog',async({page})=>{
 await page.goto('/sablonlar');await expect(page.locator('.template-preview .cv-sheet')).toHaveCount(25);
 await page.locator('[data-template-preview=technology]').click();
 await expect(page.locator('#template-dialog')).toBeVisible();
 await expect(page.locator('#template-dialog .cv-sidebar')).toBeVisible();
 await page.locator('#template-dialog-close').click();await expect(page.locator('#template-dialog')).toBeHidden();
});

test('portfolio gallery selects and previews all visible reference designs',async({page})=>{
 await page.goto('/portfolyo-sablonlari');await expect(page.locator('[data-portfolio-choice]')).toHaveCount(18);
 await page.locator('[data-portfolio-preview=block]').click();
 await expect(page.frameLocator('#portfolio-design-frame').locator('.pf-block')).toBeVisible();
 await page.locator('#portfolio-design-close').click();
 await page.locator('[data-portfolio-choice=block]').click();await expect(page.locator('#p-portfolioTemplate')).toHaveValue('block');
 await page.locator('#p-name').fill('Deniz Tasarım');await expect(page.frameLocator('#portfolio-preview').locator('.pf-block')).toBeVisible();
 await expect(page.frameLocator('#portfolio-preview').getByRole('heading',{name:'Deniz Tasarım',exact:true})).toBeVisible();
 const pending=page.waitForEvent('download');await page.locator('#portfolio-download').click();
 const download=await pending;expect(download.suggestedFilename()).toContain('Portfolyo.html');
});

test('all portfolio outputs render without mobile overflow or unsafe markup',async({page})=>{
 for(const t of portfolioLayouts){
  const html=portfolioDocument({name:'Deniz Yılmaz',role:'Web geliştirici',summary:'Kullanıcı deneyimi ve web uygulamaları üzerine çalışıyorum.',experience:'React ile 12 proje geliştirdim.',projects:'Görev yönetimi uygulaması',skills:'React, TypeScript',email:'deniz@example.com',portfolioTemplate:t.id});
  await page.setContent(html);await expect(page.locator('.pf-'+t.id)).toBeVisible();
  expect(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),t.name).toBeTruthy();
 }
 const html=portfolioDocument({name:'<script>window.injected=true</script>',portfolioTemplate:'spark'});await page.setContent(html);
 expect(await page.evaluate(()=>window.injected)).toBeUndefined();
});

test('profile photo is cropped locally and survives a saved CV draft',async({page})=>{
 await page.goto('/cv-olustur');await page.locator('#load-sample').click();
 const photo=await page.evaluate(()=>{const c=document.createElement('canvas');c.width=400;c.height=300;const x=c.getContext('2d');x.fillStyle='#52776a';x.fillRect(0,0,400,300);return c.toDataURL().split(',')[1];});
 await page.locator('#cv-photo').setInputFiles({name:'portrait.png',mimeType:'image/png',buffer:Buffer.from(photo,'base64')});
 await expect(page.locator('.cv-portrait img')).toBeVisible();await page.locator('#save-local').click();await page.reload();
 await expect(page.locator('.cv-portrait img')).toBeVisible();
 await page.locator('#remove-cv-photo').click();await expect(page.locator('.cv-portrait img')).toHaveCount(0);
});

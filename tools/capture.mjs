/** Capture une page HTML locale en PNG : node tools/capture.mjs page.html sortie.png */
import { chromium } from 'playwright-core';
import { resolve } from 'node:path';

const [page = 'previews/scenes.html', out = 'previews/scenes.png'] = process.argv.slice(2);
const browser = await chromium.launch({ executablePath: process.env.CHROME_PATH ?? '/usr/bin/google-chrome' });
const tab = await browser.newPage({ viewport: { width: 2580, height: 800 } });
await tab.goto(`file://${resolve(page)}`);
await tab.waitForTimeout(1500);
await tab.screenshot({ path: out, fullPage: true });
await browser.close();
console.log(out);

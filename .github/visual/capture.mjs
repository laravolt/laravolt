// Screenshots Laravolt pages for visual comparison between two builds.
// Usage: APP_URL=http://127.0.0.1:8000 node capture.mjs <output-dir>
import { mkdirSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { chromium } from 'playwright';

const baseUrl = process.env.APP_URL ?? 'http://127.0.0.1:8000';
const outDir = process.argv[2] ?? 'shots';
mkdirSync(outDir, { recursive: true });

const components = [
    'button', 'input', 'select', 'textarea', 'checkbox', 'switch', 'advanced-datepicker',
    'card', 'panel', 'table', 'modal', 'offcanvas', 'dropdown', 'tooltip', 'accordion',
    'tab', 'stepper', 'alert', 'toast', 'badge', 'avatar', 'progress', 'pagination',
];

const pages = [
    { name: 'login', path: '/auth/login', auth: false },
    { name: 'home', path: '/home' },
    { name: 'users', path: '/epicentrum/users' },
    { name: 'roles', path: '/epicentrum/roles' },
    { name: 'playground-form', path: '/platform/playground/form' },
    { name: 'preline-showcase', path: '/platform/preline' },
    { name: 'components', path: '/platform/components' },
    ...components.map((c) => ({ name: `component-${c}`, path: `/platform/components/${c}` })),
];

// Open the first instance of an interactive widget, then screenshot again.
const interactions = {
    'component-modal': '[data-hs-overlay]',
    'component-offcanvas': '[data-hs-overlay]',
    'component-dropdown': '.hs-dropdown-toggle',
    'component-advanced-datepicker': '[data-hs-datepicker]',
    'component-select': '[data-hs-select] ~ .hs-select-toggle, .hs-select > button',
};

const viewports = [
    { name: 'desktop', width: 1280, height: 800 },
    { name: 'mobile', width: 390, height: 844 },
];
const themes = ['light', 'dark'];

const report = { pages: {}, hoverGated: null };
const browser = await chromium.launch();

async function login(context) {
    const page = await context.newPage();
    await page.goto(`${baseUrl}/auth/login`);
    await page.fill('input[name=email]', process.env.ADMIN_EMAIL ?? 'admin@laravolt.test');
    await page.fill('input[name=password]', process.env.ADMIN_PASSWORD ?? 'password');
    await Promise.all([page.waitForLoadState('networkidle'), page.click('button[type=submit]')]);
    await page.close();
}

for (const theme of themes) {
    for (const viewport of viewports) {
        const context = await browser.newContext({
            viewport: { width: viewport.width, height: viewport.height },
            reducedMotion: 'reduce',
        });
        await context.addInitScript((t) => localStorage.setItem('hs_theme', t), theme);
        const anonymous = await browser.newContext({
            viewport: { width: viewport.width, height: viewport.height },
            reducedMotion: 'reduce',
        });
        await anonymous.addInitScript((t) => localStorage.setItem('hs_theme', t), theme);
        await login(context);

        for (const target of pages) {
            const page = await (target.auth === false ? anonymous : context).newPage();
            const errors = [];
            page.on('pageerror', (e) => errors.push(`pageerror: ${e.message}`));
            page.on('console', (m) => m.type() === 'error' && errors.push(`console: ${m.text()}`));
            const key = `${target.name}--${viewport.name}--${theme}`;
            const response = await page.goto(`${baseUrl}${target.path}`, { waitUntil: 'networkidle' });
            await page.waitForTimeout(300);
            await page.screenshot({ path: join(outDir, `${key}.png`), fullPage: true });

            const selector = interactions[target.name];
            if (selector && viewport.name === 'desktop') {
                const trigger = page.locator(selector).first();
                if (await trigger.count()) {
                    await trigger.click({ timeout: 3000 }).catch((e) => errors.push(`click: ${e.message.split('\n')[0]}`));
                    await page.waitForTimeout(400);
                    await page.screenshot({ path: join(outDir, `${key}--open.png`), fullPage: false });
                } else {
                    errors.push(`interaction: no element for ${selector}`);
                }
            }

            if (report.hoverGated === null) {
                report.hoverGated = await page.evaluate(() => {
                    for (const sheet of document.styleSheets) {
                        if (!sheet.href || !sheet.href.includes('main.min.css')) continue;
                        return sheet.ownerNode && [...sheet.cssRules].some(function walk(rule) {
                            if (rule.media && rule.media.mediaText.includes('hover')) return true;
                            return rule.cssRules ? [...rule.cssRules].some(walk) : false;
                        });
                    }
                    return false;
                });
            }

            report.pages[key] = { status: response?.status() ?? 0, errors };
            await page.close();
        }
        await context.close();
        await anonymous.close();
    }
}

await browser.close();
writeFileSync(join(outDir, 'report.json'), JSON.stringify(report, null, 2));
console.log(`captured ${Object.keys(report.pages).length} pages into ${outDir}`);

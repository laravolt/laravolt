// End-to-end check of form()->uploader() (FilePond) on the playground form:
// upload, undo, existing files, extension filter, and what the form posts.
// Usage: APP_URL=http://127.0.0.1:8000 node uploader.mjs <output-dir>
import { mkdirSync, writeFileSync } from 'node:fs';
import { join } from 'node:path';
import { chromium } from 'playwright';

const baseUrl = process.env.APP_URL ?? 'http://127.0.0.1:8000';
const outDir = process.argv[2] ?? 'uploader';
mkdirSync(outDir, { recursive: true });

const pngBytes = Buffer.from(
    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg==',
    'base64',
);
const png = (name) => ({ name, mimeType: 'image/png', buffer: pngBytes });
const text = (name) => ({ name, mimeType: 'text/plain', buffer: Buffer.from(`hello from ${name}\n`) });

const results = [];
function check(ok, message) {
    results.push({ ok: Boolean(ok), message });
    console.log(`${ok ? 'ok  ' : 'FAIL'} ${message}`);
}

const browser = await chromium.launch();
const context = await browser.newContext({ viewport: { width: 1280, height: 900 }, reducedMotion: 'reduce' });
const page = await context.newPage();
const errors = [];
page.on('pageerror', (e) => errors.push(`pageerror: ${e.message}`));
page.on('console', (m) => m.type() === 'error' && errors.push(`console: ${m.text()}`));

const pond = (name) => page.locator(`input[type=hidden][name="_${name}"] + .filepond--root`);
const list = async (name) => JSON.parse(await page.locator(`input[type=hidden][name="_${name}"]`).inputValue());
const itemsIn = (name, state) =>
    pond(name).locator(state ? `.filepond--item[data-filepond-item-state="${state}"]` : '.filepond--item');
const isUpload = (r) => r.url().includes('/media/media') && r.request().method() === 'POST';

try {
    await page.goto(`${baseUrl}/auth/login`);
    await page.fill('input[name=email]', process.env.ADMIN_EMAIL ?? 'admin@laravolt.test');
    await page.fill('input[name=password]', process.env.ADMIN_PASSWORD ?? 'password');
    await Promise.all([page.waitForLoadState('networkidle'), page.click('button[type=submit]')]);

    await page.goto(`${baseUrl}/platform/playground/form`, { waitUntil: 'networkidle' });
    await page.waitForSelector('.filepond--root.filepond', { timeout: 10000 });
    check((await page.locator('.filepond--root:not(.filepond)').count()) === 3, 'the 3 playground uploaders become FilePond fields');
    check((await page.locator('input[type=file].uploader').count()) === 0, 'no bare uploader input is left');
    check((await page.locator('.filepond--root.filepond').count()) === 1, 'form()->filepond() becomes a FilePond field from the bundled assets');

    // Single file, uploaded as soon as it is picked.
    const uploaded = page.waitForResponse(isUpload);
    await pond('avatar').locator('input.filepond--browser').setInputFiles(png('avatar.png'));
    const response = await uploaded;
    const body = await response.json();
    check(response.status() === 200 && body.success === true, `upload endpoint accepts the file (HTTP ${response.status()})`);
    await itemsIn('avatar', 'processing-complete').waitFor({ timeout: 10000 });
    const avatar = await list('avatar');
    check(avatar.length === 1 && avatar[0] === body.files[0].file, `_avatar lists the uploaded URL: ${JSON.stringify(avatar)}`);

    // Two files, then undo the first: the server deletes it again.
    const both = Promise.all([page.waitForResponse(isUpload), page.waitForResponse(isUpload)]);
    await pond('attachments').locator('input.filepond--browser').setInputFiles([text('one.txt'), text('two.txt')]);
    await both;
    await page.waitForFunction(
        () => document.querySelectorAll('input[name="_attachments"] + .filepond--root [data-filepond-item-state="processing-complete"]').length === 2,
        null,
        { timeout: 10000 },
    );
    check((await list('attachments')).length === 2, '_attachments lists both uploads');
    const deleted = page.waitForResponse(isUpload);
    await itemsIn('attachments').first().locator('.filepond--action-revert-item-processing').click();
    const undo = await deleted;
    check(undo.status() === 200 && (await undo.text()).trim() === 'true', `undo deletes the upload on the server (HTTP ${undo.status()})`);
    await page.waitForFunction(
        () => JSON.parse(document.querySelector('input[name="_attachments"]').value).length === 1,
        null,
        { timeout: 10000 },
    );
    check(true, '_attachments drops the undone file');

    // A field added after page load, with an existing file and an extension filter.
    await page.evaluate(() => {
        const form = document.querySelector('input[name="_avatar"]').form;
        const input = document.createElement('input');
        input.type = 'file';
        input.className = 'uploader';
        input.name = 'documents';
        input.dataset.limit = '3';
        input.dataset.extensions = 'txt';
        input.dataset.mediaUrl = document.querySelector('input[name="_avatar"]').form.action.replace(/\/platform\/dump.*$/, '/media/media?handler=fileuploader');
        input.dataset.token = form.querySelector('input[name="_token"]').value;
        input.dataset.files = JSON.stringify([
            { file: 'https://example.test/storage/9/old.txt', name: 'old.txt', size: 12, type: 'text/plain' },
        ]);
        form.querySelector('button[type=submit], input[type=submit]').before(input);
    });
    await pond('documents').waitFor({ timeout: 10000 });
    await itemsIn('documents').first().waitFor({ timeout: 10000 });
    check(
        JSON.stringify(await list('documents')) === JSON.stringify(['https://example.test/storage/9/old.txt']),
        'a field added later is picked up, and lists its existing file',
    );
    await pond('documents').locator('input.filepond--browser').setInputFiles(png('photo.png'));
    await pond('documents').locator('.filepond--item[data-filepond-item-state*="invalid"]').waitFor({ timeout: 10000 });
    check((await list('documents')).length === 1, 'a file with an extension that is not allowed is rejected');
    await pond('documents').locator('.filepond--item[data-filepond-item-state*="invalid"] .filepond--action-remove-item').click();
    await page.waitForFunction(
        () => document.querySelectorAll('input[name="_documents"] + .filepond--root [data-filepond-item-state*="invalid"]').length === 0,
        null,
        { timeout: 10000 },
    );

    // Without an upload endpoint the files are posted with the form.
    await pond('attachments_non_ajax').locator('input.filepond--browser').setInputFiles([text('a.txt'), text('b.txt')]);
    await page.waitForFunction(
        () => document.querySelectorAll('input[name="_attachments_non_ajax"] + .filepond--root .filepond--item').length === 2,
        null,
        { timeout: 10000 },
    );

    // form()->filepond() without server(): the file is posted with the form.
    await page.locator('.filepond--root.filepond input.filepond--browser').setInputFiles([text('c.txt')]);
    await page.locator('.filepond--root.filepond .filepond--item').first().waitFor({ timeout: 10000 });

    await page.screenshot({ path: join(outDir, 'uploader--light.png'), fullPage: true });
    await page.evaluate(() => document.documentElement.classList.add('dark'));
    await page.screenshot({ path: join(outDir, 'uploader--dark.png'), fullPage: true });
    await page.evaluate(() => document.documentElement.classList.remove('dark'));

    check(errors.length === 0, `no script errors before submit${errors.length ? `: ${errors.join(' | ')}` : ''}`);

    // What the browser would post: every named field of the form.
    const entries = await page.locator('input[name="_avatar"]').evaluate((el) =>
        [...new FormData(el.form).entries()].map(([key, value]) => [key, typeof value === 'string' ? value : `file:${value.name}`]),
    );
    const field = (name) => entries.find(([key]) => key === name)?.[1] ?? null;
    check(field('_avatar') === JSON.stringify(avatar), `form posts _avatar=${field('_avatar')}`);
    check(JSON.parse(field('_attachments') ?? '[]').length === 1, `form posts _attachments=${field('_attachments')}`);
    check(field('_documents') === JSON.stringify(['https://example.test/storage/9/old.txt']), `form posts _documents=${field('_documents')}`);
    const files = entries.filter(([key]) => key === 'attachments_non_ajax[]').map(([, value]) => value).sort();
    check(JSON.stringify(files) === JSON.stringify(['file:a.txt', 'file:b.txt']), `form posts attachments_non_ajax[]: ${files.join(', ')}`);
    const pondFiles = entries.filter(([key]) => key === 'pond').map(([, value]) => value);
    check(pondFiles.includes('file:c.txt'), `form()->filepond() posts pond: ${pondFiles.join(', ')}`);
    const stray = entries.filter(([key]) => ['avatar', 'attachments', 'documents', ''].includes(key));
    check(stray.length === 0, `FilePond adds no other upload fields${stray.length ? `: ${JSON.stringify(stray)}` : ''}`);

    // And the server receives them (the playground form posts to a dd() of the request).
    await Promise.all([
        page.waitForLoadState('load'),
        page.locator('input[name="_avatar"]').evaluate((el) => el.form.querySelector('button[type=submit], input[type=submit]').click()),
    ]);
    await page.waitForURL(/\/platform\/dump/, { timeout: 10000 });
    const dump = (await page.textContent('body')) ?? '';
    check(dump.includes(avatar[0]) && dump.includes('a.txt') && dump.includes('b.txt') && dump.includes('c.txt'), 'the server receives the URL list and the posted files');
} catch (e) {
    check(false, `unexpected error: ${e.message.split('\n')[0]}`);
    await page.screenshot({ path: join(outDir, 'uploader--error.png'), fullPage: true }).catch(() => {});
} finally {
    await browser.close();
}

writeFileSync(join(outDir, 'results.json'), JSON.stringify({ results, errors }, null, 2));
const failed = results.filter((r) => !r.ok);
const summary = [
    '### Uploader end-to-end',
    '',
    ...results.map((r) => `- ${r.ok ? '✅' : '❌'} ${r.message.replace(/[\\|]/g, '\\$&')}`),
    '',
].join('\n');
if (process.env.GITHUB_STEP_SUMMARY) writeFileSync(process.env.GITHUB_STEP_SUMMARY, summary, { flag: 'a' });
process.exit(failed.length ? 1 : 0);

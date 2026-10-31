// Rebuilds the generated parts of resources/assets.zip:
//   public/assets/css/main.min.css           <- resources/css/laravolt.css (Tailwind)
//   public/assets/vendor/preline/**          <- node_modules/preline
//   public/assets/vendor/toastify-js/**      <- node_modules/toastify-js
// Every other entry of the zip is kept byte for byte. The zip is written
// deterministically (sorted entries, fixed timestamps), so running this twice
// with the same Node version and lockfile gives an identical file.
//
// Usage: npm ci && npm run build:assets

import { execFileSync } from 'node:child_process';
import { createHash } from 'node:crypto';
import { mkdtempSync, readFileSync, readdirSync, rmSync, statSync, writeFileSync } from 'node:fs';
import { tmpdir } from 'node:os';
import { join, relative } from 'node:path';
import { deflateRawSync, inflateRawSync } from 'node:zlib';

const root = new URL('..', import.meta.url).pathname;
const zipPath = join(root, 'resources/assets.zip');
const versionPath = join(root, 'resources/assets.version');

// Fixed DOS timestamp: 2020-01-01 00:00:00.
const DOS_TIME = 0;
const DOS_DATE = ((2020 - 1980) << 9) | (1 << 5) | 1;

const GENERATED_PREFIXES = [
    'public/assets/css/main.min.css',
    'public/assets/vendor/preline/',
    'public/assets/vendor/toastify-js/',
];

function crc32(buf) {
    let crc = ~0;
    for (const byte of buf) {
        crc ^= byte;
        for (let k = 0; k < 8; k++) crc = (crc >>> 1) ^ (0xedb88320 & -(crc & 1));
    }
    return ~crc >>> 0;
}

function readZip(path) {
    const buf = readFileSync(path);
    let eocd = buf.length - 22;
    while (buf.readUInt32LE(eocd) !== 0x06054b50) eocd--;
    const count = buf.readUInt16LE(eocd + 10);
    let p = buf.readUInt32LE(eocd + 16);
    const entries = new Map();
    for (let i = 0; i < count; i++) {
        const method = buf.readUInt16LE(p + 10);
        const compSize = buf.readUInt32LE(p + 20);
        const nameLen = buf.readUInt16LE(p + 28);
        const extraLen = buf.readUInt16LE(p + 30);
        const commentLen = buf.readUInt16LE(p + 32);
        const offset = buf.readUInt32LE(p + 42);
        const name = buf.toString('utf8', p + 46, p + 46 + nameLen);
        const dataStart = offset + 30 + buf.readUInt16LE(offset + 26) + buf.readUInt16LE(offset + 28);
        const raw = buf.subarray(dataStart, dataStart + compSize);
        if (!name.endsWith('/')) {
            entries.set(name, method === 8 ? inflateRawSync(raw) : Buffer.from(raw));
        }
        p += 46 + nameLen + extraLen + commentLen;
    }
    return entries;
}

function writeZip(path, entries) {
    const names = [...entries.keys()].sort();
    const locals = [];
    const centrals = [];
    let offset = 0;
    for (const name of names) {
        const data = entries.get(name);
        const nameBuf = Buffer.from(name, 'utf8');
        const deflated = deflateRawSync(data, { level: 9 });
        const crc = crc32(data);
        const local = Buffer.alloc(30);
        local.writeUInt32LE(0x04034b50, 0);
        local.writeUInt16LE(20, 4);
        local.writeUInt16LE(0x0800, 6); // UTF-8 names
        local.writeUInt16LE(8, 8);
        local.writeUInt16LE(DOS_TIME, 10);
        local.writeUInt16LE(DOS_DATE, 12);
        local.writeUInt32LE(crc, 14);
        local.writeUInt32LE(deflated.length, 18);
        local.writeUInt32LE(data.length, 22);
        local.writeUInt16LE(nameBuf.length, 26);
        locals.push(local, nameBuf, deflated);

        const central = Buffer.alloc(46);
        central.writeUInt32LE(0x02014b50, 0);
        central.writeUInt16LE(0x0314, 4); // made by: unix, 2.0
        central.writeUInt16LE(20, 6);
        central.writeUInt16LE(0x0800, 8);
        central.writeUInt16LE(8, 10);
        central.writeUInt16LE(DOS_TIME, 12);
        central.writeUInt16LE(DOS_DATE, 14);
        central.writeUInt32LE(crc, 16);
        central.writeUInt32LE(deflated.length, 20);
        central.writeUInt32LE(data.length, 24);
        central.writeUInt16LE(nameBuf.length, 28);
        central.writeUInt32LE((0o100644 << 16) >>> 0, 38);
        central.writeUInt32LE(offset, 42);
        centrals.push(central, nameBuf);

        offset += 30 + nameBuf.length + deflated.length;
    }
    const centralBuf = Buffer.concat(centrals);
    const end = Buffer.alloc(22);
    end.writeUInt32LE(0x06054b50, 0);
    end.writeUInt16LE(names.length, 8);
    end.writeUInt16LE(names.length, 10);
    end.writeUInt32LE(centralBuf.length, 12);
    end.writeUInt32LE(offset, 16);
    writeFileSync(path, Buffer.concat([...locals, centralBuf, end]));
}

function addTree(entries, srcDir, destPrefix, filter = () => true) {
    for (const name of readdirSync(srcDir)) {
        const full = join(srcDir, name);
        if (statSync(full).isDirectory()) {
            addTree(entries, full, `${destPrefix}${name}/`, filter);
        } else if (filter(full)) {
            entries.set(`${destPrefix}${name}`, readFileSync(full));
        }
    }
}

const entries = readZip(zipPath);
for (const name of [...entries.keys()]) {
    if (GENERATED_PREFIXES.some((prefix) => name.startsWith(prefix))) entries.delete(name);
}

const tmp = mkdtempSync(join(tmpdir(), 'laravolt-assets-'));
try {
    const css = join(tmp, 'main.min.css');
    execFileSync(
        join(root, 'node_modules/.bin/tailwindcss'),
        ['--input', join(root, 'resources/css/laravolt.css'), '--output', css, '--minify'],
        { cwd: root, stdio: 'inherit' },
    );
    entries.set('public/assets/css/main.min.css', readFileSync(css));
} finally {
    rmSync(tmp, { recursive: true, force: true });
}

const preline = join(root, 'node_modules/preline');
addTree(entries, join(preline, 'dist'), 'public/assets/vendor/preline/dist/', (f) => /\.(m?js)$/.test(f));
for (const file of ['package.json', 'LICENSE']) {
    entries.set(`public/assets/vendor/preline/${file}`, readFileSync(join(preline, file)));
}

const toastify = join(root, 'node_modules/toastify-js');
for (const file of ['src/toastify.js', 'src/toastify.css', 'package.json', 'LICENSE']) {
    entries.set(`public/assets/vendor/toastify-js/${file}`, readFileSync(join(toastify, file)));
}

writeZip(zipPath, entries);

// Cache-busting token for the generated files, used by LaravoltBladeDirectives.
const hash = createHash('sha256');
for (const name of [...entries.keys()].sort()) {
    if (GENERATED_PREFIXES.some((prefix) => name.startsWith(prefix))) {
        hash.update(name).update(entries.get(name));
    }
}
writeFileSync(versionPath, `${hash.digest('hex').slice(0, 12)}\n`);

console.log(`${relative(root, zipPath)}: ${entries.size} files, version ${readFileSync(versionPath, 'utf8').trim()}`);

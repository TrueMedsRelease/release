// E2EE interop smoke (single process — сессия одна, как вкладка браузера):
// JS seal (real e2ee.js + nacl.min.js) -> PHP open (real E2eeService) ->
// PHP seal -> JS open. TEST_PORTAL_PK/TEST_SEED env.
const fs = require('fs');
const os = require('os');
const path = require('path');
const crypto = require('crypto');
const Module = require('module');
const { spawnSync } = require('child_process');

const dir = path.join(__dirname, '..', 'public', 'js', 'cabinet');

function loadBrowserJs(file) {
    const m = new Module(file);
    m._compile(fs.readFileSync(path.join(dir, file), 'utf8'), path.join(dir, file));
    return m.exports;
}

global.window = {};
global.window.nacl = loadBrowserJs('nacl.min.js');
global.fetch = async () => ({ json: async () => ({ kid: KID, pk: TEST_PK_B64 }) });
loadBrowserJs('e2ee.js');
const E2EE = global.window.E2EE;

const TEST_PK_B64 = process.env.TEST_PORTAL_PK;
const TEST_SEED = process.env.TEST_SEED;
if (!TEST_PK_B64 || !TEST_SEED) {
    console.error('TEST_PORTAL_PK/TEST_SEED required');
    process.exit(1);
}
const pkRaw = Buffer.from(TEST_PK_B64.replace(/-/g, '+').replace(/_/g, '/'), 'base64');
const KID = crypto.createHash('sha256').update(pkRaw).digest('base64')
    .replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '').slice(0, 12);

function b64uDecode(s) {
    return new Uint8Array(Buffer.from(String(s).replace(/-/g, '+').replace(/_/g, '/'), 'base64'));
}

const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'e2ee-smoke-'));
const ENV_FILE = path.join(tmp, 'env.json');
const RESP_FILE = path.join(tmp, 'resp.json');

(async () => {
    const keys = await E2EE.ensureKeys();
    if (!keys || keys.kid !== KID) throw new Error('keys failed');
    E2EE.setCode('123456', 'user@example.com');
    const sealed = E2EE.sealRequest('/account/api/contact', 'POST', { message: 'hello portal' }, keys);
    if (!sealed || !sealed.envelope.kb) throw new Error('seal failed');
    const g = E2EE.sealGet();
    fs.writeFileSync(ENV_FILE, JSON.stringify(sealed.envelope));
    console.log('sealed-ok kid=' + KID);

    const php = process.platform === 'win32' ? 'php' : 'php';
    const r = spawnSync(php, [process.env.PORTAL_SMOKE_PHP], {
        env: {
            ...process.env,
            TEST_SEED,
            IN_ENVELOPE: ENV_FILE,
            IN_EPH: 'unused',
            OUT_RESPONSE: RESP_FILE,
            BROWSER_EPK_B64: E2EE.b64uEncode(g.epk),
        },
        encoding: 'utf8',
    });
    if (r.status !== 0) {
        console.error('php side failed:\n' + (r.stdout || '') + (r.stderr || ''));
        process.exit(1);
    }
    process.stdout.write(r.stdout);

    const resp = JSON.parse(fs.readFileSync(RESP_FILE, 'utf8'));
    const data = E2EE.openResponse(resp, g.esk);
    if (!data || data.echo !== 'hello portal') throw new Error('open-response mismatch');
    console.log('open-response-ok');
    console.log('INTEROP-OK');
})().catch((e) => {
    console.error('smoke failed: ' + (e && e.message));
    process.exit(1);
});

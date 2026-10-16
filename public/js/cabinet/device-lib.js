/**
 * DeviceLib — сигналы браузера и устройства для стабильного хэша.
 * Нейтральное имя специально: старые fingerprint-имена режутся баннерами.
 */
class DeviceLib {
  constructor(options = {}) {
    this.options = {
      debug: false,
      canvas: true,
      webgl: true,
      audio: true,
      fonts: true,
      plugins: true,
      screen: true,
      navigator: true,
      timezone: true,
      touch: true,
      hardware: true,
      permissions: true,
      storage: true,
      network: true,
      math: true,
      media: true,
      speech: true,
      ...options,
    };
    this.components = {};
  }
  async get() {
    const startTime = performance.now();
    const collectors = [
      this.options.navigator && this._getNavigatorInfo(),
      this.options.screen && this._getScreenInfo(),
      this.options.timezone && this._getTimezone(),
      this.options.canvas && this._getCanvas(),
      this.options.webgl && this._getWebGL(),
      this.options.audio && this._getAudio(),
      this.options.fonts && this._getFont(),
      this.options.plugins && this._getPlugins(),
      this.options.touch && this._getTouchSupport(),
      this.options.hardware && this._getHardwareInfo(),
      this.options.permissions && this._getPermissions(),
      this.options.storage && this._getStorageInfo(),
      this.options.network && this._getNetworkInfo(),
      this.options.math && this._getMathConstants(),
      this.options.media && this._getMediaDevices(),
      this.options.speech && this._getSpeechVoices(),
      this._getCSSInfo(),
      this._getWindowProperties(),
      this._getDOMProperties(),
      this._getHeadersInfo(),
    ].filter(Boolean);
    const results = await Promise.allSettled(collectors);
    results.forEach((result) => {
      if (result.status === "fulfilled" && result.value) {
        Object.assign(this.components, result.value);
      }
    });
    const elapsed = performance.now() - startTime;
    const digest = this._generateHash(this.components);
    return {
      hash: digest,
      components: this.components,
      elapsed: Math.round(elapsed * 100) / 100,
    };
  }
  _getNavigatorInfo() {
    return Promise.resolve({
      navigator: {
        userAgent: navigator.userAgent,
        appCodeName: navigator.appCodeName,
        appName: navigator.appName,
        appVersion: navigator.appVersion,
        platform: navigator.platform,
        product: navigator.product,
        productSub: navigator.productSub,
        vendor: navigator.vendor,
        vendorSub: navigator.vendorSub,
        language: navigator.language,
        languages: navigator.languages,
        cookieEnabled: navigator.cookieEnabled,
        doNotTrack: navigator.doNotTrack,
        onLine: navigator.onLine,
        pdfViewerEnabled: navigator.pdfViewerEnabled,
        webdriver: navigator.webdriver,
        maxTouchPoints: navigator.maxTouchPoints,
        credentialsEnabled: this._safeCheck(() => typeof navigator.credentials !== "undefined"),
        bluetoothEnabled: this._safeCheck(() => typeof navigator.bluetooth !== "undefined"),
        usbEnabled: this._safeCheck(() => typeof navigator.usb !== "undefined"),
        serialEnabled: this._safeCheck(() => typeof navigator.serial !== "undefined"),
        hidEnabled: this._safeCheck(() => typeof navigator.hid !== "undefined"),
        gamepadEnabled: this._safeCheck(() => typeof navigator.getGamepads === "function"),
        serviceWorkerEnabled: this._safeCheck(() => typeof navigator.serviceWorker !== "undefined"),
        mediaDevicesEnabled: this._safeCheck(() => typeof navigator.mediaDevices !== "undefined"),
        clipboardEnabled: this._safeCheck(() => typeof navigator.clipboard !== "undefined"),
        shareEnabled: this._safeCheck(() => typeof navigator.share === "function"),
        vibrationEnabled: this._safeCheck(() => typeof navigator.vibrate === "function"),
      },
    });
  }
  _getScreenInfo() {
    const s = window.screen || {};
    return Promise.resolve({
      screen: {
        width: s.width,
        height: s.height,
        availWidth: s.availWidth,
        availHeight: s.availHeight,
        colorDepth: s.colorDepth,
        pixelDepth: s.pixelDepth,
        devicePixelRatio: window.devicePixelRatio,
        orientation: this._safeCheck(() => screen.orientation?.type || "unknown"),
        availLeft: s.availLeft,
        availTop: s.availTop,
        innerWidth: window.innerWidth,
        innerHeight: window.innerHeight,
        outerWidth: window.outerWidth,
        outerHeight: window.outerHeight,
        screenX: window.screenX,
        screenY: window.screenY,
      },
    });
  }
  _getTimezone() {
    return Promise.resolve({
      timezone: {
        timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
        timezoneOffset: new Date().getTimezoneOffset(),
        dateFormat: this._safeCheck(() => new Intl.DateTimeFormat().resolvedOptions().locale || "unknown"),
        dateLocale: this._safeCheck(() => { const d = new Date(); return d.toLocaleDateString(); }),
      },
    });
  }
  async _getCanvas() {
    try {
      const canvas = document.createElement("canvas");
      canvas.width = 280;
      canvas.height = 60;
      const ctx = canvas.getContext("2d");
      if (!ctx) return { canvas: { supported: false } };
      const gradient = ctx.createLinearGradient(0, 0, 280, 0);
      gradient.addColorStop(0, "#ff6b6b");
      gradient.addColorStop(0.5, "#4ecdc4");
      gradient.addColorStop(1, "#45b7d1");
      ctx.fillStyle = gradient;
      ctx.fillRect(0, 0, 280, 60);
      ctx.fillStyle = "rgba(102, 204, 0, 0.7)";
      ctx.font = "18px Arial";
      ctx.textBaseline = "alphabetic";
      ctx.fillText("FingerprintLib <canvas> 1.0", 4, 20);
      ctx.fillStyle = "rgba(0, 0, 255, 0.5)";
      ctx.font = "14px 'Times New Roman'";
      ctx.fillText("Unique rendering 🎨", 4, 40);
      ctx.beginPath();
      ctx.arc(240, 30, 20, 0, Math.PI * 2);
      ctx.fillStyle = "rgba(255, 0, 128, 0.4)";
      ctx.fill();
      ctx.strokeStyle = "#333";
      ctx.lineWidth = 2;
      ctx.stroke();
      ctx.globalCompositeOperation = "multiply";
      ctx.fillStyle = "rgb(255, 0, 255)";
      ctx.beginPath();
      ctx.arc(220, 30, 15, 0, Math.PI * 2);
      ctx.fill();
      ctx.globalCompositeOperation = "source-over";
      ctx.fillStyle = "rgb(0, 255, 255)";
      ctx.beginPath();
      ctx.arc(250, 25, 10, 0, Math.PI * 2);
      ctx.fill();
      ctx.beginPath();
      ctx.moveTo(10, 55);
      ctx.bezierCurveTo(50, 10, 200, 55, 270, 50);
      ctx.strokeStyle = "rgba(100, 50, 200, 0.6)";
      ctx.lineWidth = 3;
      ctx.stroke();
      const dataUrl = canvas.toDataURL();
      const winding = this._supportsWinding(ctx);
      return { canvas: { supported: true, hash: this._murmurhash3(dataUrl), winding } };
    } catch (e) { return { canvas: { supported: false, error: e.message } }; }
  }
  _supportsWinding(ctx) { ctx.rect(0, 0, 10, 10); ctx.rect(2, 2, 6, 6); return ctx.isPointInPath(5, 5, "evenodd") === false; }
  async _getWebGL() {
    try {
      const canvas = document.createElement("canvas");
      const gl = canvas.getContext("webgl2") || canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
      if (!gl) return { webgl: { supported: false } };
      const vendor = gl.getParameter(gl.VENDOR);
      const renderer = gl.getParameter(gl.RENDERER);
      const version = gl.getParameter(gl.VERSION);
      const shadingLang = gl.getParameter(gl.SHADING_LANGUAGE_VERSION);
      let unmaskedVendor = ""; let unmaskedRenderer = "";
      const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
      if (debugInfo) { unmaskedVendor = gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL); unmaskedRenderer = gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL); }
      const extensions = gl.getSupportedExtensions() || [];
      const renderHash = this._webglRender(gl, canvas);
      const params = {
        maxTextureSize: gl.getParameter(gl.MAX_TEXTURE_SIZE),
        maxViewportDims: Array.from(gl.getParameter(gl.MAX_VIEWPORT_DIMS) || []),
        maxRenderbufferSize: gl.getParameter(gl.MAX_RENDERBUFFER_SIZE),
        maxVertexAttribs: gl.getParameter(gl.MAX_VERTEX_ATTRIBS),
        maxVertexUniformVectors: gl.getParameter(gl.MAX_VERTEX_UNIFORM_VECTORS),
        maxVaryingVectors: gl.getParameter(gl.MAX_VARYING_VECTORS),
        maxFragmentUniformVectors: gl.getParameter(gl.MAX_FRAGMENT_UNIFORM_VECTORS),
        maxVertexTextureImageUnits: gl.getParameter(gl.MAX_VERTEX_TEXTURE_IMAGE_UNITS),
        maxCombinedTextureImageUnits: gl.getParameter(gl.MAX_COMBINED_TEXTURE_IMAGE_UNITS),
        maxTextureImageUnits: gl.getParameter(gl.MAX_TEXTURE_IMAGE_UNITS),
        aliasedLineWidthRange: Array.from(gl.getParameter(gl.ALIASED_LINE_WIDTH_RANGE) || []),
        aliasedPointSizeRange: Array.from(gl.getParameter(gl.ALIASED_POINT_SIZE_RANGE) || []),
        maxCubeMapTextureSize: gl.getParameter(gl.MAX_CUBE_MAP_TEXTURE_SIZE),
        stencilBits: gl.getParameter(gl.STENCIL_BITS),
        depthBits: gl.getParameter(gl.DEPTH_BITS),
        antialiasing: gl.getContextAttributes()?.antialias || false,
      };
      gl.getExtension("WEBGL_lose_context")?.loseContext();
      return { webgl: { supported: true, vendor, renderer, version, shadingLanguageVersion: shadingLang, unmaskedVendor, unmaskedRenderer, extensions: extensions.length, extensionsList: extensions, params, renderHash } };
    } catch (e) { return { webgl: { supported: false, error: e.message } }; }
  }
  _webglRender(gl, canvas) {
    try {
      canvas.width = 64; canvas.height = 64;
      gl.clearColor(0.2, 0.4, 0.6, 1.0); gl.clear(gl.COLOR_BUFFER_BIT);
      const vs = gl.createShader(gl.VERTEX_SHADER); gl.shaderSource(vs, `attribute vec2 position; void main() { gl_Position = vec4(position, 0.0, 1.0); }`); gl.compileShader(vs);
      const fs = gl.createShader(gl.FRAGMENT_SHADER); gl.shaderSource(fs, `precision mediump float; void main() { gl_FragColor = vec4(1.0, 0.5, 0.2, 1.0); }`); gl.compileShader(fs);
      const program = gl.createProgram(); gl.attachShader(program, vs); gl.attachShader(program, fs); gl.linkProgram(program); gl.useProgram(program);
      const vertices = new Float32Array([-0.5, -0.5, 0.5, -0.5, 0.0, 0.5]);
      const buffer = gl.createBuffer(); gl.bindBuffer(gl.ARRAY_BUFFER, buffer); gl.bufferData(gl.ARRAY_BUFFER, vertices, gl.STATIC_DRAW);
      const posLoc = gl.getAttribLocation(program, "position"); gl.enableVertexAttribArray(posLoc); gl.vertexAttribPointer(posLoc, 2, gl.FLOAT, false, 0, 0);
      gl.drawArrays(gl.TRIANGLES, 0, 3);
      const pixels = new Uint8Array(64 * 64 * 4); gl.readPixels(0, 0, 64, 64, gl.RGBA, gl.UNSIGNED_BYTE, pixels);
      let hash = 0; for (let i = 0; i < pixels.length; i += 17) { hash = ((hash << 5) - hash + pixels[i]) | 0; }
      gl.deleteProgram(program); gl.deleteShader(vs); gl.deleteShader(fs); gl.deleteBuffer(buffer);
      return hash;
    } catch (e) { return 0; }
  }
  async _getAudio() {
    try {
      const AudioContext = window.OfflineAudioContext || window.webkitOfflineAudioContext;
      if (!AudioContext) return { audio: { supported: false } };
      const context = new AudioContext(1, 5000, 44100);
      const oscillator = context.createOscillator(); oscillator.type = "triangle"; oscillator.frequency.setValueAtTime(10000, context.currentTime);
      const compressor = context.createDynamicsCompressor(); compressor.threshold.setValueAtTime(-50, context.currentTime); compressor.knee.setValueAtTime(40, context.currentTime); compressor.ratio.setValueAtTime(12, context.currentTime); compressor.attack.setValueAtTime(0, context.currentTime); compressor.release.setValueAtTime(0.25, context.currentTime);
      oscillator.connect(compressor); compressor.connect(context.destination); oscillator.start(0);
      return new Promise((resolve) => {
        const timeout = setTimeout(() => { resolve({ audio: { supported: true, hash: "timeout" } }); }, 1000);
        context.startRendering();
        context.oncomplete = (event) => {
          clearTimeout(timeout);
          try {
            const samples = event.renderedBuffer.getChannelData(0); let sum = 0; for (let i = 4500; i < 5000; i++) { sum += Math.abs(samples[i]); }
            resolve({ audio: { supported: true, hash: this._murmurhash3(sum.toString()), sampleRate: context.sampleRate, numberOfChannels: 1, state: context.state } });
          } catch (e) { resolve({ audio: { supported: true, error: e.message } }); }
        };
      });
    } catch (e) { return { audio: { supported: false, error: e.message } }; }
  }
  async _getFont() {
    try {
      const baseFonts = ["monospace", "sans-serif", "serif"];
      const testFonts = ["Arial","Arial Black","Book Antiqua","Calibri","Cambria","Century Gothic","Comic Sans MS","Consolas","Courier New","Georgia","Helvetica","Impact","Lucida Console","Microsoft Sans Serif","MS Gothic","Palatino Linotype","Segoe UI","Tahoma","Times New Roman","Trebuchet MS","Verdana","Roboto","Open Sans","Lato","Montserrat","Ubuntu","DejaVu Sans","Noto Sans"];
      const testString = "mmmmmmmmmmlli"; const testSize = "72px";
      const span = document.createElement("span"); span.style.position = "absolute"; span.style.left = "-9999px"; span.style.top = "-9999px"; span.style.fontSize = testSize; span.style.lineHeight = "normal"; span.innerText = testString; document.body.appendChild(span);
      const baseSizes = {}; for (const font of baseFonts) { span.style.fontFamily = font; baseSizes[font] = { width: span.offsetWidth, height: span.offsetHeight }; }
      const detectedFonts = []; for (const font of testFonts) { let detected = false; for (const base of baseFonts) { span.style.fontFamily = `'${font}', ${base}`; if (span.offsetWidth !== baseSizes[base].width || span.offsetHeight !== baseSizes[base].height) { detected = true; break; } } if (detected) detectedFonts.push(font); }
      document.body.removeChild(span);
      return { fonts: { detected: detectedFonts, detectedCount: detectedFonts.length, hash: this._murmurhash3(detectedFonts.join(",")) } };
    } catch (e) { return { fonts: { error: e.message } }; }
  }
  _getPlugins() {
    try {
      const plugins = []; if (navigator.plugins) { for (let i = 0; i < navigator.plugins.length; i++) { const p = navigator.plugins[i]; plugins.push({ name: p.name, description: p.description, filename: p.filename }); } }
      return Promise.resolve({ plugins: { count: plugins.length, list: plugins, hash: this._murmurhash3(JSON.stringify(plugins)) } });
    } catch (e) { return Promise.resolve({ plugins: { error: e.message } }); }
  }
  _getTouchSupport() {
    return Promise.resolve({ touch: { maxTouchPoints: navigator.maxTouchPoints || 0, hasTouch: "ontouchstart" in window || navigator.maxTouchPoints > 0 } });
  }
  _getHardwareInfo() {
    return Promise.resolve({ hardware: { cpuCores: navigator.hardwareConcurrency || "unknown", deviceMemory: navigator.deviceMemory || "unknown", platform: navigator.platform } });
  }
  _getPermissions() {
    if (!navigator.permissions) return Promise.resolve({ permissions: { supported: false } });
    return Promise.resolve({ permissions: { supported: true } });
  }
  _getStorageInfo() {
    return Promise.resolve({ storage: { localStorage: this._safeCheck(() => { const t="__fp_test__"; localStorage.setItem(t,t); localStorage.removeItem(t); return true; }), sessionStorage: this._safeCheck(() => { const t="__fp_test__"; sessionStorage.setItem(t,t); sessionStorage.removeItem(t); return true; }), indexedDB: this._safeCheck(() => typeof indexedDB !== "undefined"), cookies: navigator.cookieEnabled } });
  }
  _getNetworkInfo() {
    const conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
    if (!conn) return Promise.resolve({ network: { connectionAPI: false, onLine: navigator.onLine } });
    return Promise.resolve({ network: { connectionAPI: true, onLine: navigator.onLine, effectiveType: conn.effectiveType, downlink: conn.downlink } });
  }
  _getMathConstants() {
    const mathResults = {}; const ops = [["acos",0.5],["asin",0.5],["sin",-1e100],["cos",-1e100],["tan",-1e100],["exp",1],["log2",10]]; for (const [f,a] of ops) { mathResults[f]=Math[f](a).toString(); }
    return Promise.resolve({ math: { results: mathResults, hash: this._murmurhash3(JSON.stringify(mathResults)) } });
  }
  async _getMediaDevices() {
    try { if (!navigator.mediaDevices?.enumerateDevices) return { media: { supported: false } }; const devices = await navigator.mediaDevices.enumerateDevices(); return { media: { supported: true, deviceCount: devices.length } }; } catch(e){ return { media: { supported: false, error: e.message } }; }
  }
  async _getSpeechVoices() {
    return new Promise((resolve)=>{ if(!window.speechSynthesis) { resolve({speech:{supported:false}}); return; } const v= speechSynthesis.getVoices(); resolve({ speech:{supported:true, voiceCount: v.length}}); });
  }
  _getCSSInfo() {
    const el=document.createElement("div"); const props=["grid","backdropFilter","clipPath"]; const feats={}; for(const p of props){ feats[p]= p in el.style; }
    return Promise.resolve({ css: { features: feats } });
  }
  _getWindowProperties() {
    return Promise.resolve({ windowAPIs: { webWorker: typeof Worker!=="undefined", webRTC: typeof RTCPeerConnection!=="undefined", webGL: typeof WebGLRenderingContext!=="undefined" } });
  }
  _getDOMProperties() {
    return Promise.resolve({ dom: { linksCount: document.links?.length||0, scriptsCount: document.scripts?.length||0 } });
  }
  async _getHeadersInfo() { return { headers: { fetched: false } }; }
  _generateHash(c){ const s=JSON.stringify(c); return { v4: this._toHex(this._murmurhash3(s,0xabcdef))+"-"+this._toHex(this._murmurhash3(s,0x12345678))+"-"+this._toHex(this._murmurhash3(s,0x87654321)), numeric: this._murmurhash3(s,0) }; }
  _murmurhash3(key, seed=0){ let h1=seed>>>0; const len=key.length; const nblocks=len>>2; const c1=0xcc9e2d51; const c2=0x1b873593; for(let i=0;i<nblocks;i++){ const i4=i*4; let k1=(key.charCodeAt(i4)&0xff)|((key.charCodeAt(i4+1)&0xff)<<8)|((key.charCodeAt(i4+2)&0xff)<<16)|((key.charCodeAt(i4+3)&0xff)<<24); k1=Math.imul(k1,c1); k1=(k1<<15)|(k1>>>17); k1=Math.imul(k1,c2); h1^=k1; h1=(h1<<13)|(h1>>>19); h1=Math.imul(h1,5)+0xe6546b64; } let k1=0; const tail=nblocks*4; switch(len&3){case 3:k1^=(key.charCodeAt(tail+2)&0xff)<<16; case 2:k1^=(key.charCodeAt(tail+1)&0xff)<<8; case 1:k1^=key.charCodeAt(tail)&0xff; k1=Math.imul(k1,c1); k1=(k1<<15)|(k1>>>17); k1=Math.imul(k1,c2); h1^=k1;} h1^=len; h1^=h1>>>16; h1=Math.imul(h1,0x85ebca6b); h1^=h1>>>13; h1=Math.imul(h1,0xc2b2ae35); h1^=h1>>>16; return h1>>>0; }
  _toHex(n){ return (n>>>0).toString(16).padStart(8,"0"); }
  _safeCheck(fn){ try{return fn();}catch(e){return undefined;} }
  async getStabilityTest(i=3){ const h=[]; for(let j=0;j<i;j++){ const r=await this.get(); h.push(r.hash.v4); } const u=[...new Set(h)]; return {iterations:i, hashes:h, uniqueCount:u.length, stable:u.length===1}; }
  static compare(a,b){ return null; }
  static toReadable(r){ return JSON.stringify(r,null,2); }
}
if (typeof module !== "undefined" && module.exports) module.exports = DeviceLib;
if (typeof window !== "undefined") window.DeviceLib = DeviceLib;
// BC-алиас для старых потребителей (шопы до обновления грузят fingerprint.js).
if (typeof window !== "undefined") window.FingerprintLib = DeviceLib;

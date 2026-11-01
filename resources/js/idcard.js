/*
 * ID card 3D bergaya "Vercel lanyard" — port vanilla dari template 3D_CARD
 * (react-three-fiber + rapier) ke Three.js + Rapier + meshline.
 *
 * - Model kartu: public/assets/kartu.glb (atlas tekstur: sisi depan di kiri, belakang di kanan)
 * - Tali: rantai rigid-body Rapier dengan rope joint, digambar sebagai meshline bermotif
 * - Wajah kartu & motif tali digambar dari desain ID Reno (Canvas 2D), bukan gambar bawaan template
 * Jika WebGL tidak tersedia, kartu HTML/CSS tetap tampil.
 */
import {
    WebGLRenderer, Scene, PerspectiveCamera, Vector2, Vector3, Raycaster, Group, Mesh,
    AmbientLight, MeshBasicMaterial, MeshPhysicalMaterial, PlaneGeometry, Color, CanvasTexture,
    SRGBColorSpace, RepeatWrapping, CatmullRomCurve3, PMREMGenerator, NoToneMapping, MathUtils,
} from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';
import { MeshLineGeometry, MeshLineMaterial } from 'meshline';

const host = document.getElementById('idcard3d');
const heroRight = host?.closest('.hero-right');

function webglAvailable() {
    try {
        const c = document.createElement('canvas');
        return !!(window.WebGLRenderingContext && (c.getContext('webgl2') || c.getContext('webgl')));
    } catch { return false; }
}

if (host && heroRight && webglAvailable()) {
    init(JSON.parse(host.dataset.card)).then(
        () => { heroRight.classList.add('has-3d'); heroRight.classList.remove('pending-3d'); }, // sembunyikan kartu HTML & nonaktifkan drag lama
        (err) => { console.warn('ID card 3D gagal dimuat, memakai kartu HTML.', err); heroRight.classList.remove('pending-3d'); },
    );
} else {
    heroRight?.classList.remove('pending-3d');
}

/* ───────────── Tekstur (Canvas 2D) ───────────── */
function loadImage(src) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => resolve(img);
        img.onerror = () => resolve(null);
        img.src = src;
    });
}

const roundRect = (ctx, x, y, w, h, r) => { ctx.beginPath(); ctx.roundRect(x, y, w, h, r); };

// Desain logis 716x1000 (rasio sama dengan kartu di model)
async function drawFront(text, photoSrc) {
    const W = 716, H = 1000;
    const c = document.createElement('canvas');
    c.width = W; c.height = H;
    const ctx = c.getContext('2d');

    const bg = ctx.createLinearGradient(0, 0, 0, H);
    bg.addColorStop(0, '#14171d'); bg.addColorStop(1, '#07090c');
    ctx.fillStyle = bg;
    ctx.fillRect(0, 0, W, H);

    const hd = ctx.createLinearGradient(0, 0, W, 0);
    hd.addColorStop(0, '#2b323f'); hd.addColorStop(1, '#1a1f29');
    ctx.fillStyle = hd; ctx.fillRect(0, 0, W, 150);
    ctx.fillStyle = 'rgba(255,255,255,0.22)'; ctx.fillRect(0, 148, W, 2);

    ctx.fillStyle = '#f2f5fa';
    ctx.textBaseline = 'middle';
    ctx.textAlign = 'left';
    // klip logam menutupi bagian tengah atas, jadi label dipecah per kata di sisi kiri
    ctx.font = '500 24px "DM Mono", monospace';
    const orgLines = text.org.split(' ');
    orgLines.forEach((word, i) => ctx.fillText(word, 44, 86 + (i - (orgLines.length - 1) / 2) * 32));
    ctx.textAlign = 'right';
    ctx.font = '500 36px "DM Mono", monospace';
    ctx.fillText('reno', W - 44, 86);

    // alur klip di tepi atas
    ctx.fillStyle = '#000000';
    roundRect(ctx, W / 2 - 50, 24, 100, 18, 9); ctx.fill();

    const cx = W / 2, cy = 330, r = 130;
    ctx.fillStyle = '#e8eef8';
    ctx.beginPath(); ctx.arc(cx, cy, r + 9, 0, Math.PI * 2); ctx.fill();
    ctx.save();
    ctx.beginPath(); ctx.arc(cx, cy, r, 0, Math.PI * 2); ctx.clip();
    const img = await loadImage(photoSrc);
    if (img) {
        const s = Math.max((r * 2) / img.width, (r * 2) / img.height);
        ctx.drawImage(img, cx - (img.width * s) / 2, cy - (img.height * s) / 2, img.width * s, img.height * s);
    } else {
        ctx.fillStyle = '#2b323f'; ctx.fillRect(cx - r, cy - r, r * 2, r * 2);
    }
    ctx.restore();

    ctx.textAlign = 'center';
    ctx.fillStyle = '#e8eef8';
    ctx.font = 'italic 70px "DM Serif Display", serif';
    ctx.fillText('Reno', cx, 540);
    ctx.fillStyle = '#a9b4c4';
    ctx.font = '500 28px "DM Mono", monospace';
    ctx.fillText('Indomaret Group', cx, 600);

    ctx.strokeStyle = 'rgba(255,255,255,0.16)'; ctx.lineWidth = 2;
    ctx.beginPath(); ctx.moveTo(70, 645); ctx.lineTo(W - 70, 645); ctx.stroke();

    const rows = [
        [text.status, text.available, '#22c97a'],
        [text.location, 'Jakarta, Indonesia', null],
        [text.role, text.roleValue, null],
    ];
    ctx.textAlign = 'left';
    rows.forEach(([label, value, dot], i) => {
        const y = 695 + i * 58;
        ctx.fillStyle = '#6f7a8a';
        ctx.font = '400 23px "DM Mono", monospace';
        ctx.fillText(label, 70, y);
        let x = 280;
        if (dot) { ctx.fillStyle = dot; ctx.beginPath(); ctx.arc(x + 8, y, 8, 0, Math.PI * 2); ctx.fill(); x += 28; }
        ctx.fillStyle = '#c7d4e6';
        ctx.font = '500 25px "DM Mono", monospace';
        ctx.fillText(value, x, y);
    });

    ctx.fillStyle = 'rgba(255,255,255,0.05)';
    roundRect(ctx, 70, 870, W - 140, 100, 18); ctx.fill();
    ctx.fillStyle = '#dbe6f5';
    let x = 190;
    const bars = [3, 1, 2, 1, 4, 1, 2, 3, 1, 2, 1, 3, 2, 1, 4, 2, 1, 3, 1, 2, 1, 4, 1, 2];
    for (const w of bars) { if (x > W - 190) break; ctx.fillRect(x, 886, w * 3, 46); x += w * 3 + 5; }
    ctx.fillStyle = '#6f7a8a';
    ctx.textAlign = 'center';
    ctx.font = '400 20px "DM Mono", monospace';
    ctx.fillText('RENO-DA-2026', W / 2, 950);
    return c;
}

function drawBack() {
    const W = 716, H = 1000;
    const c = document.createElement('canvas');
    c.width = W; c.height = H;
    const ctx = c.getContext('2d');
    const bg = ctx.createLinearGradient(0, 0, W, H);
    bg.addColorStop(0, '#222834'); bg.addColorStop(1, '#06080b');
    ctx.fillStyle = bg; ctx.fillRect(0, 0, W, H);
    ctx.strokeStyle = 'rgba(255,255,255,0.18)'; ctx.lineWidth = 3;
    roundRect(ctx, 28, 28, W - 56, H - 56, 28); ctx.stroke();
    ctx.fillStyle = 'rgba(255,255,255,0.95)';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.font = 'italic 190px "DM Serif Display", serif';
    ctx.fillText('RN', W / 2, H / 2 - 30);
    ctx.font = '500 28px "DM Mono", monospace';
    ctx.fillStyle = 'rgba(255,255,255,0.65)';
    ctx.fillText('DATA · BI · AUTOMATION', W / 2, H / 2 + 110);
    return c;
}

// Atlas seperti texture template: depan = u 0–0.5 / v 0–0.755, belakang = u 0.5–1 / v 0–0.755
async function buildCardAtlas(cfg) {
    const S = 2048;
    const atlas = document.createElement('canvas');
    atlas.width = S; atlas.height = S;
    const ctx = atlas.getContext('2d');
    ctx.fillStyle = '#0b1220'; ctx.fillRect(0, 0, S, S);
    const regionW = S * 0.5, regionH = S * 0.755;
    ctx.drawImage(await drawFront(cfg, cfg.photo), 0, 0, regionW, regionH);
    ctx.drawImage(drawBack(), regionW, 0, regionW, regionH);
    return atlas;
}

// Motif tali (pengganti bandd.png): teks putih berulang di atas pita biru
function buildStrapTexture() {
    const c = document.createElement('canvas');
    c.width = 1024; c.height = 247;
    const ctx = c.getContext('2d');
    const g = ctx.createLinearGradient(0, 0, 0, 247);
    g.addColorStop(0, '#222833'); g.addColorStop(1, '#0a0c10');
    ctx.fillStyle = g; ctx.fillRect(0, 0, 1024, 247);
    ctx.fillStyle = '#ffffff';
    ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
    ctx.font = '600 92px "DM Mono", monospace';
    ctx.fillText('RENO · DATA', 512, 128);
    return c;
}

/* ───────────── Scene, fisika (Rapier), render ───────────── */
async function init(cfg) {
    const [{ default: RAPIER }] = await Promise.all([
        import('@dimforge/rapier3d-compat'),
        document.fonts.ready,
    ]);
    await RAPIER.init();

    const renderer = new WebGLRenderer({ antialias: true, alpha: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.outputColorSpace = SRGBColorSpace;
    renderer.toneMapping = NoToneMapping; // ACES menurunkan saturasi warna foto
    host.appendChild(renderer.domElement);
    const el = renderer.domElement;
    el.style.display = 'block';
    el.style.touchAction = 'pan-y';

    const scene = new Scene();
    const camera = new PerspectiveCamera(25, 1, 0.1, 100);
    scene.add(new AmbientLight(0xffffff, Math.PI));

    // Pencahayaan studio ala <Environment><Lightformer/></Environment> (drei)
    const envScene = new Scene();
    envScene.background = new Color('black');
    const lightformer = (intensity, [px, py, pz], [rx, ry, rz], [sx, sy, sz]) => {
        const m = new Mesh(new PlaneGeometry(1, 1), new MeshBasicMaterial({ color: new Color().setScalar(intensity), toneMapped: false, side: 2 }));
        m.position.set(px, py, pz); m.rotation.set(rx, ry, rz); m.scale.set(sx, sy, sz);
        envScene.add(m);
    };
    const P3 = Math.PI / 3;
    lightformer(2, [0, -1, 5], [0, 0, P3], [100, 0.1, 1]);
    lightformer(3, [-1, -1, 1], [0, 0, P3], [100, 0.1, 1]);
    lightformer(3, [1, 1, 1], [0, 0, P3], [100, 0.1, 1]);
    lightformer(10, [-10, 0, 14], [0, Math.PI / 2, P3], [100, 10, 1]);
    const pmrem = new PMREMGenerator(renderer);
    scene.environment = pmrem.fromScene(envScene, 0.04).texture;

    // aset
    const [gltf, atlas] = await Promise.all([new GLTFLoader().loadAsync(cfg.model), buildCardAtlas(cfg)]);
    const cardTex = new CanvasTexture(atlas);
    cardTex.flipY = false; // konvensi glTF
    cardTex.colorSpace = SRGBColorSpace;
    cardTex.anisotropy = Math.min(16, renderer.capabilities.getMaxAnisotropy());
    // ganti bahasa tanpa reload: gambar ulang wajah kartu dengan teks baru
    window.addEventListener('localechange', async () => {
        cardTex.image = await buildCardAtlas(JSON.parse(host.dataset.card));
        cardTex.needsUpdate = true;
    });
    const strapTex = new CanvasTexture(buildStrapTexture());
    strapTex.colorSpace = SRGBColorSpace;
    strapTex.wrapS = strapTex.wrapT = RepeatWrapping;

    const nodes = Object.fromEntries(['card', 'clip', 'clamp'].map((n) => [n, gltf.scene.getObjectByName(n)]));
    const metal = gltf.scene.getObjectByName('clip').material;
    metal.roughness = 0.3;

    // visual kartu (skala & offset sama dengan template)
    const cardObj = new Group();
    const cardVisual = new Group();
    cardVisual.scale.setScalar(2.25);
    cardVisual.position.set(0, -1.2, -0.05);
    const cardMesh = new Mesh(nodes.card.geometry, new MeshPhysicalMaterial({
        // Warna wajah kartu dipancarkan apa adanya (emissive) agar tidak dipudarkan cahaya lingkungan;
        // kilap kaca tetap datang dari clearcoat.
        color: 0x000000, emissive: 0xffffff, emissiveMap: cardTex, emissiveIntensity: 1,
        clearcoat: 0.6, clearcoatRoughness: 0.2, roughness: 0.5, metalness: 0,
    }));
    cardVisual.add(cardMesh, new Mesh(nodes.clip.geometry, metal), new Mesh(nodes.clamp.geometry, metal));
    cardObj.add(cardVisual);
    scene.add(cardObj);

    // tali
    const strapMat = new MeshLineMaterial({
        color: new Color('white'), depthTest: false, resolution: new Vector2(1, 1),
        useMap: 1, map: strapTex, repeat: new Vector2(-4, 1), lineWidth: 1, sizeAttenuation: 1,
    });
    const strapGeo = new MeshLineGeometry();
    const strap = new Mesh(strapGeo, strapMat);
    strap.frustumCulled = false;
    scene.add(strap);
    strapMat.lineWidth = 0.5;

    /* fisika: fixed → j1 → j2 → j3 → kartu, dihubungkan rope joint & spherical joint */
    const world = new RAPIER.World({ x: 0, y: -40, z: 0 });
    world.timestep = 1 / 120;
    // Rapier terbaru lebih longgar dibanding versi di template: tambah iterasi agar rantai rope joint tidak putus
    world.integrationParameters.numSolverIterations = 24;
    const mkBody = (desc, x, y) => world.createRigidBody(desc.setTranslation(x, y, 0));
    const dyn = () => RAPIER.RigidBodyDesc.dynamic().setLinearDamping(4).setAngularDamping(4).setCanSleep(true);

    // Intro: seluruh rantai mulai di atas area terlihat, lalu titik gantung (kinematik) turun ke y=4
    // sehingga kartu "meluncur masuk dari atas" dan berayun alami mengikuti fisika.
    const DROP = 7, DROP_MS = 1400;
    const fixed = mkBody(RAPIER.RigidBodyDesc.kinematicPositionBased(), 0, 4 + DROP);
    const j1 = mkBody(dyn(), 0.5, 4 + DROP);
    const j2 = mkBody(dyn(), 1, 4 + DROP);
    const j3 = mkBody(dyn(), 1.5, 4 + DROP);
    const card = mkBody(dyn(), 2, 4 + DROP);
    let dropStart = 0;
    [j1, j2, j3].forEach((b) => world.createCollider(RAPIER.ColliderDesc.ball(0.1).setDensity(20), b));
    world.createCollider(RAPIER.ColliderDesc.cuboid(0.8, 1.125, 0.01), card);

    const zero = { x: 0, y: 0, z: 0 };
    const rope = (a, b) => world.createImpulseJoint(RAPIER.JointData.rope(1, zero, zero), a, b, true);
    rope(fixed, j1); rope(j1, j2); rope(j2, j3);
    world.createImpulseJoint(RAPIER.JointData.spherical(zero, { x: 0, y: 1.45, z: 0 }), j3, card, true);

    /* interaksi */
    const raycaster = new Raycaster();
    const pointer = new Vector2();
    const vec = new Vector3(), dir = new Vector3(), grab = new Vector3();
    let dragged = false;

    function setPointer(e) {
        const b = el.getBoundingClientRect();
        pointer.set(((e.clientX - b.left) / b.width) * 2 - 1, -((e.clientY - b.top) / b.height) * 2 + 1);
        raycaster.setFromCamera(pointer, camera);
    }
    const hit = () => raycaster.intersectObject(cardVisual, true)[0];

    // Canvas lebar dibuat tembus klik; interaksi lewat window agar tidak menutupi teks/scroll.
    el.style.pointerEvents = 'none';
    el.style.touchAction = 'auto';

    const grabAt = (h) => {
        const t = card.translation();
        grab.copy(h.point).sub(vec.set(t.x, t.y, t.z));
        dragged = true;
        card.setBodyType(RAPIER.RigidBodyType.KinematicPositionBased, true);
        document.body.style.cursor = 'grabbing';
    };
    const release = () => {
        if (!dragged) return;
        dragged = false;
        card.setBodyType(RAPIER.RigidBodyType.Dynamic, true);
        document.body.style.cursor = '';
    };

    // Mouse (desktop)
    window.addEventListener('pointerdown', (e) => {
        if (e.pointerType === 'touch') return;
        setPointer(e);
        const h = hit();
        if (!h) return;
        e.preventDefault();
        grabAt(h);
    });
    window.addEventListener('pointermove', (e) => {
        if (e.pointerType === 'touch') return;
        setPointer(e);
        if (!dragged) document.body.style.cursor = hit() ? 'grab' : '';
    });
    window.addEventListener('pointerup', (e) => { if (e.pointerType !== 'touch') release(); });
    window.addEventListener('pointercancel', (e) => { if (e.pointerType !== 'touch') release(); });

    // Sentuh (mobile): pakai touch event non-passive. Jika jari mendarat di kartu, preventDefault
    // mencegah browser men-scroll halaman sehingga kartu bisa ditarik ke segala arah (kiri/kanan/atas/bawah).
    // Jika mendarat di luar kartu, event dibiarkan sehingga halaman tetap bisa di-scroll.
    window.addEventListener('touchstart', (e) => {
        if (e.touches.length !== 1) return;
        setPointer(e.touches[0]);
        const h = hit();
        if (!h) return;
        e.preventDefault();
        grabAt(h);
    }, { passive: false });
    window.addEventListener('touchmove', (e) => {
        if (!dragged) return;
        e.preventDefault();
        setPointer(e.touches[0]);
    }, { passive: false });
    window.addEventListener('touchend', release);
    window.addEventListener('touchcancel', release);

    // ukuran responsif
    function resize() {
        const w = host.clientWidth, h = host.clientHeight;
        renderer.setSize(w, h, false);
        el.style.width = w + 'px'; el.style.height = h + 'px';
        strapMat.resolution.set(w, h);
        camera.aspect = w / h;
        const tan = Math.tan(MathUtils.degToRad(camera.fov / 2));
        // tinggi dunia terlihat ≈ 4.6; lebar minimal ≈ 3.4
        camera.position.set(0, 0.2, Math.max(2.3 / tan, 1.7 / (tan * camera.aspect)));
        camera.lookAt(0, 0.2, 0);
        camera.updateProjectionMatrix();
    }
    new ResizeObserver(resize).observe(host);
    resize();

    /* loop */
    const curve = new CatmullRomCurve3([new Vector3(), new Vector3(), new Vector3(), new Vector3()]);
    // centripetal: tidak menghasilkan lonjakan/ujung runcing saat titik-titik tali berdekatan (kartu diam)
    curve.curveType = 'centripetal';
    const lerped1 = new Vector3(), lerped2 = new Vector3();
    let lerpInit = false;
    const maxSpeed = 50, minSpeed = 10;
    const STEP = 1 / 60;
    let stepAcc = 0, calmFrames = 0;
    const toV = (t, v) => v.set(t.x, t.y, t.z);
    const tmp = new Vector3();

    function tick(delta) {
        let dropping = false;
        if (!dropStart) dropStart = performance.now();
        const dp = (performance.now() - dropStart) / DROP_MS;
        if (dp < 1) {
            dropping = true;
            const e = 1 - Math.pow(1 - dp, 3); // easeOutCubic
            fixed.setNextKinematicTranslation({ x: 0, y: 4 + DROP * (1 - e), z: 0 });
            [card, j1, j2, j3].forEach((b) => b.wakeUp());
        } else if (dp < 1.05) {
            fixed.setNextKinematicTranslation({ x: 0, y: 4, z: 0 });
        }
        if (dragged) {
            vec.set(pointer.x, pointer.y, 0.5).unproject(camera);
            dir.copy(vec).sub(camera.position).normalize();
            vec.add(dir.multiplyScalar(camera.position.length()));
            [card, j1, j2, j3, fixed].forEach((b) => b.wakeUp());
            card.setNextKinematicTranslation({ x: vec.x - grab.x, y: vec.y - grab.y, z: vec.z - grab.z });
        }
        // langkah waktu tetap 1/60 dtk, tak bergantung refresh rate layar (60/144 Hz sama cepat)
        stepAcc += delta;
        for (let n = 0; stepAcc >= STEP && n < 4; n++, stepAcc -= STEP) {
            // 2 sub-langkah (1/120 dtk) per frame: joint lebih kaku & stabil saat kartu ditarik
            world.step(); world.step();
            // redam putaran sumbu Y agar kartu tidak terus berputar
            const av = card.angvel(), r = card.rotation();
            // wakeUp=false: kalau true, badan yang sedang tidur dibangunkan terus tiap langkah → getaran mikro (berkedip)
            card.setAngvel({ x: av.x, y: av.y - r.y * 0.25, z: av.z }, false);
        }
        if (stepAcc > STEP) stepAcc = 0;

        // Saat sudah (hampir) diam, tidurkan rantai agar solver tidak menggetarkan tali/kartu terus-menerus
        const lv = card.linvel(), av2 = card.angvel();
        const speed = Math.hypot(lv.x, lv.y, lv.z) + Math.hypot(av2.x, av2.y, av2.z);
        if (!dragged && !dropping && speed < 0.05) {
            if (++calmFrames > 45) [card, j1, j2, j3].forEach((b) => b.sleep());
        } else calmFrames = 0;

        if (!lerpInit) { toV(j1.translation(), lerped1); toV(j2.translation(), lerped2); lerpInit = true; }
        [[j1, lerped1], [j2, lerped2]].forEach(([b, l]) => {
            const d = MathUtils.clamp(l.distanceTo(toV(b.translation(), tmp)), 0.1, 1);
            l.lerp(toV(b.translation(), tmp), delta * (minSpeed + d * (maxSpeed - minSpeed)));
        });
        // Ujung tali dikunci ke titik gantung di kartu (bukan ke j3). Saat kartu ditarik, joint bisa
        // melar sesaat sehingga j3 terpisah dari klip → ujung tali tampak berkedip/lepas.
        const ct = card.translation(), cq = card.rotation();
        cardObj.position.set(ct.x, ct.y, ct.z);
        cardObj.quaternion.set(cq.x, cq.y, cq.z, cq.w);
        curve.points[0].set(0, 1.45, 0).applyQuaternion(cardObj.quaternion).add(cardObj.position);
        curve.points[1].copy(lerped2);
        curve.points[2].copy(lerped1);
        curve.points[3].copy(toV(fixed.translation(), tmp));
        strapGeo.setPoints(curve.getPoints(32));
    }

    let visible = true, rafId = 0, last = performance.now();
    function frame(now) {
        rafId = requestAnimationFrame(frame);
        const delta = Math.min((now - last) / 1000, 1 / 20);
        last = now;
        tick(delta);
        renderer.render(scene, camera);
    }
    const start = () => { if (!rafId && visible && !document.hidden) { last = performance.now(); rafId = requestAnimationFrame(frame); } };
    const stop = () => { cancelAnimationFrame(rafId); rafId = 0; };
    new IntersectionObserver(([en]) => { visible = en.isIntersecting; visible ? start() : stop(); }).observe(host);
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
    start();
}

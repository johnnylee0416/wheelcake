<!DOCTYPE html>
<html lang="zh-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>購物車結帳</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Noto+Sans+TC:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
:root {
  --ink:     #1a1a1a;
  --ink-2:   #3a3a3a;
  --ink-3:   #6b6b6b;
  --ink-4:   #a0a0a0;
  --surface: #f5f4f1;
  --white:   #ffffff;
  --border:  rgba(0,0,0,0.09);
  --border2: rgba(0,0,0,0.15);
  --glass:   rgba(255,255,255,0.60);
  --blur:    blur(16px);
}
*,*::before,*::after { box-sizing:border-box; margin:0; padding:0; }

body {
  font-family:'Noto Sans TC',sans-serif;
  background:var(--surface);
  color:var(--ink);
  width:100vw; height:100vh;
  overflow:hidden;
  display:grid;
  grid-template-rows:auto 1fr auto;
}

/* ── Background ── */
#bg-video {
  position:fixed; inset:0;
  width:100%; height:100%;
  object-fit:cover;
  z-index:0;
  opacity:0.50;
  filter:grayscale(100%) contrast(1.1);
}
.overlay {
  position:fixed; inset:0; z-index:1;
  background:
    radial-gradient(ellipse 70% 60% at 30% 50%, rgba(200,200,195,0.14) 0%, transparent 65%),
    linear-gradient(160deg, rgba(245,244,241,0.10) 0%, rgba(245,244,241,0.55) 100%);
  pointer-events:none;
}

/* ── Header ── */
header {
  position:relative; z-index:10;
  padding:18px 44px;
  display:flex; align-items:center; gap:20px;
  border-bottom:1px solid var(--border);
  background:rgba(245,244,241,0.75);
  backdrop-filter:var(--blur);
  -webkit-backdrop-filter:var(--blur);
}

.back-btn {
  display:flex; align-items:center; justify-content:center;
  width:36px; height:36px;
  border-radius:50%;
  border:1px solid var(--border2);
  background:var(--glass);
  backdrop-filter:var(--blur); -webkit-backdrop-filter:var(--blur);
  cursor:pointer; text-decoration:none; color:var(--ink); flex-shrink:0;
  transition:background 0.2s, transform 0.2s;
}
.back-btn:hover { background:rgba(255,255,255,0.9); transform:translateX(-2px); }
.back-btn svg { width:15px; height:15px; stroke:var(--ink); fill:none; stroke-width:1.8; stroke-linecap:round; stroke-linejoin:round; }

.header-title { flex:1; display:flex; flex-direction:column; gap:2px; }
.header-title-main {
  font-family:'Playfair Display',serif;
  font-size:17px; font-weight:700; color:var(--ink);
}
.header-title-sub {
  font-size:10px; letter-spacing:0.2em;
  text-transform:uppercase; color:var(--ink-4);
}

.header-meta {
  display:flex; align-items:baseline; gap:5px;
  margin-right:4px;
}
.header-meta-count {
  font-family:'Playfair Display',serif;
  font-size:22px; font-weight:700; color:var(--ink);
}
.header-meta-label {
  font-size:12px; color:var(--ink-4);
}

.hbtn {
  padding:9px 22px; border-radius:50px;
  border:1px solid var(--border2);
  background:var(--glass);
  backdrop-filter:var(--blur); -webkit-backdrop-filter:var(--blur);
  color:var(--ink-2); font-size:13px; font-weight:500;
  font-family:inherit; cursor:pointer;
  transition:background 0.2s, transform 0.15s;
  letter-spacing:0.03em;
}
.hbtn:hover:not(:disabled) { background:rgba(255,255,255,0.9); transform:translateY(-1px); }
.hbtn:disabled { opacity:0.35; cursor:not-allowed; }
.hbtn-primary {
  background:var(--ink); border-color:var(--ink);
  color:var(--white); font-weight:700;
  box-shadow:0 3px 14px rgba(0,0,0,0.18);
}
.hbtn-primary:hover:not(:disabled) {
  background:var(--ink-2); border-color:var(--ink-2);
  color:var(--white); box-shadow:0 5px 20px rgba(0,0,0,0.22);
}

/* ── Success toast ── */
#success-msg {
  position:fixed; top:76px; left:50%;
  transform:translateX(-50%) translateY(-8px);
  background:rgba(255,255,255,0.95);
  border:1px solid rgba(0,0,0,0.10);
  backdrop-filter:var(--blur); color:var(--ink);
  padding:12px 32px; border-radius:50px;
  font-size:14px; font-weight:600;
  opacity:0; transition:opacity 0.4s, transform 0.4s;
  pointer-events:none; z-index:50; white-space:nowrap;
  box-shadow:0 4px 20px rgba(0,0,0,0.08);
}
#success-msg.show { opacity:1; transform:translateX(-50%) translateY(0); }

/* ── Canvas ── */
.canvas-wrap {
  position:relative; z-index:2; overflow:hidden;
}
canvas { display:block; width:100%!important; height:100%!important; }

.canvas-wrap::after {
  content:'';
  position:absolute; bottom:0; left:0; right:0; height:60px;
  background:linear-gradient(to top, rgba(245,244,241,0.20), transparent);
  pointer-events:none; z-index:3;
}

/* ── Bottom bar ── */
.bottom-bar {
  position:relative; z-index:10;
  border-top:1px solid var(--border);
  padding:14px 44px;
  display:flex; align-items:center; gap:10px;
  overflow-x:auto;
  background:rgba(245,244,241,0.82);
  backdrop-filter:var(--blur); -webkit-backdrop-filter:var(--blur);
}
.bottom-bar::-webkit-scrollbar { height:0; }

.item-chip {
  flex-shrink:0;
  display:flex; align-items:center; gap:7px;
  background:var(--white);
  border:1px solid var(--border2);
  border-radius:50px;
  padding:6px 14px 6px 10px;
  font-size:13px; font-weight:500;
  color:var(--ink-2); white-space:nowrap;
  box-shadow:0 1px 4px rgba(0,0,0,0.04);
}
.item-chip-icon { font-size:17px; }
.item-chip-qty {
  background:var(--ink-2); color:var(--white);
  font-size:10px; font-weight:700;
  min-width:18px; height:18px; border-radius:50px;
  display:flex; align-items:center; justify-content:center; padding:0 4px;
}

.bar-spacer { flex:1; }

.bar-total {
  flex-shrink:0;
  display:flex; align-items:baseline; gap:5px;
  padding-left:20px;
  border-left:1px solid var(--border2);
}
.bar-total-label {
  font-size:10px; font-weight:400;
  font-family:'Noto Sans TC',sans-serif;
  color:var(--ink-4); letter-spacing:0.1em;
}
.bar-total-num {
  font-family:'Playfair Display',serif;
  font-size:24px; font-weight:700; color:var(--ink);
  white-space:nowrap;
}
</style>
</head>
<body>

<video id="bg-video" autoplay muted loop playsinline>
  <source src="./bg.mp4" type="video/mp4">
</video>
<div class="overlay"></div>

<header>
  <a class="back-btn" href="./Shop.php" title="繼續選購">
    <svg viewBox="0 0 24 24">
      <line x1="19" y1="12" x2="5" y2="12"/>
      <polyline points="12 19 5 12 12 5"/>
    </svg>
  </a>

  <div class="header-title">
    <div class="header-title-main">購物車</div>
    <div class="header-title-sub">海陸紅豆餅 · 確認訂單</div>
  </div>

  <div class="header-meta" id="header-meta"></div>

  <div style="display:flex;gap:10px;">
    <button class="hbtn hbtn-primary" id="btn-checkout">確認結帳</button>
  </div>
</header>

<div id="success-msg">✓ 結帳成功，感謝您的購買！</div>

<div class="canvas-wrap" id="canvas-wrap">
  <canvas id="c"></canvas>
</div>

<div class="bottom-bar" id="item-bar">
  <span style="color:var(--ink-4);font-size:13px;">購物車是空的</span>
</div>

<script type="importmap">
{
  "imports": {
    "three": "https://unpkg.com/three@0.160.0/build/three.module.js",
    "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/"
  }
}
</script>

<script type="module">
import * as THREE from 'three';
import { GLTFLoader }    from 'three/addons/loaders/GLTFLoader.js';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

const CART_MODEL_URL = './cart.glb';

let cartItems = [];
try { cartItems = JSON.parse(localStorage.getItem('cartItems') || '[]'); } catch(e) {}
if (!cartItems.length) {
  cartItems = [
    { id:'p1', name:'大腸麵線', price:60,  qty:1, emoji:'🍜', modelUrl:'big.glb' },
    { id:'p3', name:'奶油',     price:50,  qty:2, emoji:'🧈', modelUrl:'keyboard.glb' },
    { id:'p5', name:'巧克力',   price:65,  qty:1, emoji:'🍫', modelUrl:'watch.glb' },
  ];
}

/* ── Header 件數 ── */
function renderHeaderMeta() {
  const el    = document.getElementById('header-meta');
  const count = cartItems.reduce((s, i) => s + i.qty, 0);
  el.innerHTML = `
    <span class="header-meta-count">${count}</span>
    <span class="header-meta-label">件商品</span>
  `;
}
renderHeaderMeta();

/* ── Bottom bar ── */
function renderItemBar() {
  const bar = document.getElementById('item-bar');
  if (!cartItems.length) return;
  const total = cartItems.reduce((s, i) => s + i.price * i.qty, 0);
  bar.innerHTML =
    cartItems.map(item => `
      <div class="item-chip">
        <span class="item-chip-icon">${item.emoji}</span>
        <span>${item.name}</span>
        <span class="item-chip-qty">×${item.qty}</span>
      </div>
    `).join('') +
    `<div class="bar-spacer"></div>
     <div class="bar-total">
       <span class="bar-total-label">合計</span>
       <span class="bar-total-num">NT$&nbsp;${total.toLocaleString()}</span>
     </div>`;
}
renderItemBar();

/* ── Three.js ── */
const canvas = document.getElementById('c');
const wrap   = document.getElementById('canvas-wrap');

const renderer = new THREE.WebGLRenderer({ canvas, antialias:true, alpha:true });
renderer.setPixelRatio(Math.min(devicePixelRatio, 2));
renderer.shadowMap.enabled   = true;
renderer.shadowMap.type      = THREE.PCFSoftShadowMap;
renderer.outputColorSpace    = THREE.SRGBColorSpace;
renderer.toneMapping         = THREE.ACESFilmicToneMapping;
renderer.toneMappingExposure = 1.1;
renderer.setClearColor(0x000000, 0);

function resize() {
  renderer.setSize(wrap.clientWidth, wrap.clientHeight);
  camera.aspect = wrap.clientWidth / wrap.clientHeight;
  camera.updateProjectionMatrix();
}

const scene = new THREE.Scene();

const camera = new THREE.PerspectiveCamera(38, 1, 0.001, 1000);
camera.position.set(0, 4.5, -4.5);
camera.lookAt(0, 0.8, 0);

/* 視角完全鎖定 */
const controls = new OrbitControls(camera, canvas);
controls.enableRotate  = false;
controls.enableZoom    = false;
controls.enablePan     = false;
controls.enableDamping = false;
controls.target.set(0, 0.8, 0);
controls.update();

/* 燈光 */
scene.add(new THREE.AmbientLight(0xffffff, 1.0));
const sun = new THREE.DirectionalLight(0xffffff, 2.0);
sun.position.set(5, 10, 7);
sun.castShadow = true;
sun.shadow.mapSize.set(2048, 2048);
sun.shadow.camera.left = sun.shadow.camera.bottom = -6;
sun.shadow.camera.right = sun.shadow.camera.top   =  6;
sun.shadow.bias = -0.001;
scene.add(sun);
const fillL = new THREE.DirectionalLight(0xe8e8e8, 0.5);
fillL.position.set(-4, 3, -3); scene.add(fillL);
const rimL = new THREE.DirectionalLight(0xd8d8d8, 0.3);
rimL.position.set(0, -2, 4); scene.add(rimL);

/* 地面 */
const floor = new THREE.Mesh(
  new THREE.PlaneGeometry(40, 40),
  new THREE.MeshStandardMaterial({ color:0xeeecea, roughness:0.95 })
);
floor.rotation.x = -Math.PI / 2;
floor.receiveShadow = true;
scene.add(floor);

const ITEM_COLORS = [0x9ca3af, 0x6b7280, 0xd1d5db, 0x4b5563, 0xe5e7eb, 0x374151];
const itemLoader  = new GLTFLoader();
const itemGroups  = [];

function dropIn(group, initWorldPos, delayMs) {
  const DROP_HEIGHT = 3.0, DURATION = 600;
  group.position.set(initWorldPos.x, initWorldPos.y + DROP_HEIGHT, initWorldPos.z);
  setTimeout(() => {
    const t0 = performance.now();
    function fall(now) {
      const p = Math.min((now - t0) / DURATION, 1);
      const e = 1 - Math.pow(1 - p, 3);
      group.position.y = initWorldPos.y + DROP_HEIGHT * (1 - e);
      if (p < 1) requestAnimationFrame(fall);
      else group.position.y = initWorldPos.y;
    }
    requestAnimationFrame(fall);
  }, delayMs);
}

function placeItems(cartModel) {
  const BASKET_Y = 0.18;
  const BW = 0.35, BH = 0.35, BD = 0.35;
  const slots = [
    [2.6,  -3.0],[2.15,-3.0],[1.72,-3.0],
    [2.6,  -3.5],[2.1, -3.5],[1.6, -3.5],
    [2.6,  -4.0],[2.1, -4.0],[1.1, -6.0],
  ];
  const slotStackH = {}, tasks = [];
  let si = 0;
  cartItems.forEach((item, ci) => {
    const sk = si % slots.length;
    const [lx, lz] = slots[sk]; si++;
    for (let q = 0; q < item.qty; q++) {
      const stackH = slotStackH[sk] || 0;
      slotStackH[sk] = stackH + BH - 0.25;
      tasks.push({ item, ci, lx, ly: BASKET_Y + stackH, lz, BW, BH, BD });
    }
  });
  tasks.forEach(({ item, ci, lx, ly, lz, BW, BH, BD }, idx) => {
    const addToScene = (obj) => {
      const group = new THREE.Group();
      group.add(obj); scene.add(group);
      const initWorldPos = new THREE.Vector3(lx, ly, lz).applyMatrix4(cartModel.matrixWorld);
      dropIn(group, initWorldPos, idx * 120);
      itemGroups.push({ group, initWorldPos });
    };
    if (item.modelUrl) {
      itemLoader.load(item.modelUrl, (gltf) => {
        const m = gltf.scene;
        const box   = new THREE.Box3().setFromObject(m);
        const size  = box.getSize(new THREE.Vector3());
        const scale = Math.min(BW/size.x, BH/size.y, BD/size.z);
        m.scale.setScalar(scale);
        const center = box.getCenter(new THREE.Vector3());
        m.position.set(-center.x*scale, (-box.min.y)*scale, -center.z*scale);
        m.rotation.y = ci * 0.6;
        m.traverse(o => { if(o.isMesh){ o.castShadow=true; o.receiveShadow=true; } });
        addToScene(m);
      }, null, () => {
        const mesh = new THREE.Mesh(
          new THREE.BoxGeometry(BW, BH, BD),
          new THREE.MeshStandardMaterial({ color:ITEM_COLORS[ci%ITEM_COLORS.length], roughness:0.55 })
        );
        mesh.castShadow=true; mesh.position.y=BH/2; addToScene(mesh);
      });
    } else {
      const mesh = new THREE.Mesh(
        new THREE.BoxGeometry(BW, BH, BD),
        new THREE.MeshStandardMaterial({ color:ITEM_COLORS[ci%ITEM_COLORS.length], roughness:0.55 })
      );
      mesh.castShadow=true; mesh.position.y=BH/2; addToScene(mesh);
    }
  });
}

let mixer=null, actions=[], animRunning=false;
let animNode=null;
const animNodeInitPos = new THREE.Vector3();
const animNodeCurPos  = new THREE.Vector3();

new GLTFLoader().load(CART_MODEL_URL, (gltf) => {
  const model = gltf.scene;
  const box    = new THREE.Box3().setFromObject(model);
  const size   = box.getSize(new THREE.Vector3());
  const center = box.getCenter(new THREE.Vector3());
  const scale  = 2.4 / Math.max(size.x, size.y, size.z);
  model.scale.setScalar(scale);
  model.position.sub(center.multiplyScalar(scale));
  model.position.y += (size.y * scale) / 2;
  model.traverse(obj => {
    if (obj.isMesh) { obj.castShadow=true; obj.receiveShadow=true; }
    if (obj.name === 'Cylinder002') animNode = obj;
  });
  if (gltf.animations?.length) {
    mixer = new THREE.AnimationMixer(model);
    gltf.animations.forEach(clip => {
      const a = mixer.clipAction(clip);
      a.clampWhenFinished=true; a.loop=THREE.LoopOnce;
      a.play(); a.paused=true; actions.push(a);
    });
  }
  scene.add(model);
  model.updateMatrixWorld(true);
  if (animNode) {
    animNode.updateWorldMatrix(true, false);
    animNodeInitPos.setFromMatrixPosition(animNode.matrixWorld);
  }
  placeItems(model);
}, null, err => console.error('❌', err));

/* ── 結帳 ── */
const btnCheckout = document.getElementById('btn-checkout');
const successMsg  = document.getElementById('success-msg');

btnCheckout.addEventListener('click', () => {
  if (animRunning) return;
  animRunning = true;
  btnCheckout.disabled = true;
  if (actions.length > 0) {
    actions.forEach(a => { a.reset(); a.paused=false; a.play(); });
    const dur = Math.max(...actions.map(a => a.getClip().duration)) * 1000;
    setTimeout(showSuccess, dur);
  } else {
    setTimeout(showSuccess, 800);
  }
});

function showSuccess() {
  animRunning = false;
  successMsg.classList.add('show');
  localStorage.removeItem('cartItems');
  setTimeout(() => {
    successMsg.classList.remove('show');
    btnCheckout.disabled = false;
  }, 2500);
}

/* ── Render loop ── */
const clock  = new THREE.Clock();
const _delta = new THREE.Vector3();

(function animate() {
  requestAnimationFrame(animate);
  const dt = Math.min(clock.getDelta(), 0.05);
  if (mixer) mixer.update(dt);
  controls.update();
  if (animNode && itemGroups.length > 0) {
    animNode.updateWorldMatrix(true, false);
    animNodeCurPos.setFromMatrixPosition(animNode.matrixWorld);
    _delta.subVectors(animNodeCurPos, animNodeInitPos);
    itemGroups.forEach(({ group, initWorldPos }) => {
      group.position.x = initWorldPos.x + _delta.x;
      group.position.y += _delta.y;
      group.position.z = initWorldPos.z + _delta.z;
    });
  }
  renderer.render(scene, camera);
})();

window.addEventListener('resize', resize);
resize();
</script>
</body>
</html>
/* 3D "мозг" — геометрические объекты, собранные в форму мозга, реагирующие на мышь и скролл */
(function () {
    'use strict';

    var canvas = document.getElementById('brainCanvas');
    if (!canvas || typeof THREE === 'undefined') return;

    /* ---------- Палитра ---------- */
    var PALETTE = {
        yellow: ['#FEF9C3', '#FEF08A', '#FDE047', '#FACC15', '#EAB308', '#CA8A04', '#A16207', '#854D0E'],
        orange: ['#FFF7ED', '#FFEDD5', '#FED7AA', '#FDBA74', '#FB923C', '#F97316', '#EA580C', '#C2410C', '#9A3412', '#7C2D12'],
        green:  ['#F0FDF4', '#DCFCE7', '#BBF7D0', '#86EFAC', '#4ADE80', '#22C55E', '#16A34A', '#15803D', '#166534', '#14532D'],
        purple: ['#FAF5FF', '#F3E8FF', '#E9D5FF', '#D8B4FE', '#C084FC', '#A855F7', '#9333EA', '#7E22CE', '#6B21A8', '#581C87'],
        pink:   ['#FDF2F8', '#FCE7F3', '#FBCFE8', '#F9A8D4', '#F472B6', '#EC4899', '#DB2777', '#BE185D', '#9D174D', '#831843']
    };
    var FAMILIES = Object.keys(PALETTE);
    var HOVER_COLOR = new THREE.Color('#FEF9C3').lerp(new THREE.Color('#FFFFFF'), 0.5);

    var isSmall = window.innerWidth < 480;
    var COUNT = isSmall ? 220 : 500;

    /* ---------- Сцена ---------- */
    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 0.1, 100);
    camera.position.set(0, 0, 5.2);

    var renderer = new THREE.WebGLRenderer({ canvas: canvas, alpha: true, antialias: true });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(window.innerWidth, window.innerHeight);
    if ('outputEncoding' in renderer) renderer.outputEncoding = THREE.sRGBEncoding;

    scene.add(new THREE.AmbientLight('#ffffff', 1.4));
    var keyLight = new THREE.DirectionalLight('#ffffff', 1.1);
    keyLight.position.set(3, 5, 5);
    scene.add(keyLight);
    var goldFill = new THREE.DirectionalLight('#D4A853', 0.7);
    goldFill.position.set(-4, -2, 3);
    scene.add(goldFill);
    var backLight = new THREE.DirectionalLight('#ffffff', 0.6);
    backLight.position.set(0, -3, -4);
    scene.add(backLight);

    var group = new THREE.Group();
    scene.add(group);

    /* ---------- Форма мозга: несколько «долей» ---------- */
    var lobes = [
        { c: [-0.55, 0.15, 0], r: [1.05, 0.95, 1.25] },
        { c: [0.55, 0.15, 0], r: [1.05, 0.95, 1.25] },
        { c: [0, -0.85, -0.5], r: [0.55, 0.4, 0.5] }
    ];

    function grooveValue(theta, phi) {
        return Math.sin(theta * 6 + phi * 3) * 0.5 + Math.sin(theta * 3 - phi * 5) * 0.5;
    }

    function samplePoint() {
        for (var attempt = 0; attempt < 40; attempt++) {
            var lobe = lobes[Math.floor(Math.random() * lobes.length)];
            var theta = Math.random() * Math.PI;
            var phi = Math.random() * Math.PI * 2;
            var g = grooveValue(theta, phi);
            if (g < -0.15) continue;
            var ux = Math.sin(theta) * Math.cos(phi);
            var uy = Math.cos(theta);
            var uz = Math.sin(theta) * Math.sin(phi);
            var bulge = 1 + g * 0.06;
            var x = lobe.c[0] + ux * lobe.r[0] * bulge;
            var y = lobe.c[1] + uy * lobe.r[1] * bulge;
            var z = lobe.c[2] + uz * lobe.r[2] * bulge;
            return { pos: new THREE.Vector3(x, y, z), groove: g, seamDist: Math.abs(x) };
        }
        var l0 = lobes[0];
        return { pos: new THREE.Vector3(l0.c[0], l0.c[1], l0.c[2]), groove: 0, seamDist: 0 };
    }

    /* ---------- Логотип «MngMent» — точки из canvas-текста ---------- */
    function buildLogoPoints(count) {
        var w = 900, h = 220;
        var c = document.createElement('canvas');
        c.width = w; c.height = h;
        var ctx = c.getContext('2d');
        ctx.fillStyle = '#fff';
        ctx.font = '900 130px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('MngMent', w / 2, h / 2);
        var data = ctx.getImageData(0, 0, w, h).data;
        var pts = [];
        for (var y = 0; y < h; y += 2) {
            for (var x = 0; x < w; x += 2) {
                var alpha = data[(y * w + x) * 4 + 3];
                if (alpha > 128) pts.push({ x: x, y: y });
            }
        }
        var result = [];
        for (var i = 0; i < count; i++) {
            var p = pts.length ? pts[Math.floor(Math.random() * pts.length)] : { x: w / 2, y: h / 2 };
            var nx = (p.x / w - 0.5) * 4.6;
            var ny = -(p.y / h - 0.5) * 1.2;
            result.push({ pos: new THREE.Vector3(nx, ny, (Math.random() - 0.5) * 0.3), x: p.x });
        }
        return result;
    }

    /* ---------- Геометрии и инстансы ---------- */
    var geoms = [
        new THREE.TetrahedronGeometry(0.13, 0),
        new THREE.BoxGeometry(0.16, 0.16, 0.16),
        new THREE.OctahedronGeometry(0.13, 0),
        new THREE.IcosahedronGeometry(0.12, 0)
    ];
    /* Нейтральный (белый) per-vertex цвет — иначе instanceColor умножается на 0 */
    geoms.forEach(function (g) {
        var n = g.attributes.position.count;
        g.setAttribute('color', new THREE.BufferAttribute(new Float32Array(n * 3).fill(1), 3));
    });

    var instances = [];
    var buckets = [[], [], [], []];

    var logoPoints = buildLogoPoints(COUNT);
    logoPoints.sort(function (a, b) { return a.x - b.x; });

    for (var i = 0; i < COUNT; i++) {
        var sample = samplePoint();
        var family = FAMILIES[Math.floor(Math.random() * FAMILIES.length)];
        var shades = PALETTE[family];
        var randIdx = Math.floor(Math.random() * shades.length);
        var bias = Math.round((sample.seamDist / 1.6) * 1.5 - sample.groove * 1.2);
        var idx = Math.min(shades.length - 1, Math.max(0, randIdx + bias));
        var colorHex = shades[idx];

        var scatterDir = new THREE.Vector3(
            (Math.random() - 0.5) * 2,
            (Math.random() - 0.5) * 2,
            (Math.random() - 0.5) * 2
        ).normalize();
        var scatterPos = sample.pos.clone().add(scatterDir.multiplyScalar(2.5 + Math.random() * 3));

        var shapeIndex = Math.floor(Math.random() * geoms.length);
        var inst = {
            family: family,
            basePos: sample.pos,
            scatterPos: scatterPos,
            logoPos: null,
            baseColor: new THREE.Color(colorHex),
            currentColor: new THREE.Color(colorHex),
            targetColor: new THREE.Color(colorHex),
            currentPos: sample.pos.clone(),
            targetPos: sample.pos,
            currentScale: 1,
            targetScale: 1,
            spin: new THREE.Vector3((Math.random() - 0.5) * 0.01, (Math.random() - 0.5) * 0.01, (Math.random() - 0.5) * 0.01),
            euler: new THREE.Euler(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI),
            shapeIndex: shapeIndex,
            mesh: null,
            localId: -1
        };
        instances.push(inst);
        buckets[shapeIndex].push(inst);
    }

    /* Группируем по семействам цветов для сборки в логотип полосами */
    var sortedByFamily = instances.slice().sort(function (a, b) {
        return FAMILIES.indexOf(a.family) - FAMILIES.indexOf(b.family);
    });
    for (var li = 0; li < sortedByFamily.length; li++) {
        sortedByFamily[li].logoPos = logoPoints[li] ? logoPoints[li].pos : new THREE.Vector3(0, 0, 0);
    }

    var meshes = [];
    for (var gI = 0; gI < geoms.length; gI++) {
        var bucket = buckets[gI];
        if (!bucket.length) continue;
        var material = new THREE.MeshLambertMaterial({ vertexColors: true, emissive: '#111111' });
        var mesh = new THREE.InstancedMesh(geoms[gI], material, bucket.length);
        for (var bI = 0; bI < bucket.length; bI++) {
            var inst2 = bucket[bI];
            inst2.mesh = mesh;
            inst2.localId = bI;
            var m = new THREE.Matrix4().compose(
                inst2.currentPos,
                new THREE.Quaternion().setFromEuler(inst2.euler),
                new THREE.Vector3(1, 1, 1)
            );
            mesh.setMatrixAt(bI, m);
            mesh.setColorAt(bI, inst2.currentColor);
        }
        mesh.instanceMatrix.needsUpdate = true;
        if (mesh.instanceColor) mesh.instanceColor.needsUpdate = true;
        group.add(mesh);
        meshes.push(mesh);
    }

    /* ---------- Состояния сцены по скроллу ---------- */
    var state = 'brain';
    function applyState(newState) {
        if (state === newState) return;
        state = newState;
        for (var i = 0; i < instances.length; i++) {
            var inst = instances[i];
            if (state === 'brain') inst.targetPos = inst.basePos;
            else if (state === 'scattered') inst.targetPos = inst.scatterPos;
            else if (state === 'logo') inst.targetPos = inst.logoPos;
        }
    }

    var scrollRotation = 0;
    function onScroll() {
        var scrollY = window.scrollY || window.pageYOffset;
        var heroEl = document.querySelector('.hero');
        var heroHeight = heroEl ? heroEl.offsetHeight : window.innerHeight;
        var docHeight = document.documentElement.scrollHeight - window.innerHeight;

        scrollRotation = scrollY * 0.0006;

        if (docHeight > 0 && scrollY > docHeight - 400) {
            applyState('logo');
        } else if (scrollY > heroHeight * 0.7) {
            applyState('scattered');
        } else {
            applyState('brain');
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    /* ---------- Мышь: параллакс + hover ---------- */
    var mouse = { x: 0, y: 0 };
    var raycaster = new THREE.Raycaster();
    var hovered = null;
    var needsRaycast = false;

    window.addEventListener('mousemove', function (e) {
        mouse.x = (e.clientX / window.innerWidth) * 2 - 1;
        mouse.y = -(e.clientY / window.innerHeight) * 2 + 1;
        needsRaycast = true;
    });

    function updateHover() {
        if (!needsRaycast || !meshes.length) return;
        needsRaycast = false;
        raycaster.setFromCamera(mouse, camera);
        var intersects = raycaster.intersectObjects(meshes);
        var newHovered = null;
        if (intersects.length) {
            var hit = intersects[0];
            for (var i = 0; i < instances.length; i++) {
                if (instances[i].mesh === hit.object && instances[i].localId === hit.instanceId) {
                    newHovered = instances[i];
                    break;
                }
            }
        }
        if (hovered && hovered !== newHovered) {
            hovered.targetColor = hovered.baseColor;
            hovered.targetScale = 1;
        }
        if (newHovered && newHovered !== hovered) {
            newHovered.targetColor = HOVER_COLOR;
            newHovered.targetScale = 1.5 + Math.random() * 0.5;
        }
        hovered = newHovered;
    }

    /* ---------- Resize ---------- */
    window.addEventListener('resize', function () {
        camera.aspect = window.innerWidth / window.innerHeight;
        camera.updateProjectionMatrix();
        renderer.setSize(window.innerWidth, window.innerHeight);
    });

    /* ---------- Анимация ---------- */
    var autoRotate = 0;
    var tmpMatrix = new THREE.Matrix4();
    var tmpQuat = new THREE.Quaternion();
    var tmpScaleVec = new THREE.Vector3();

    function animate() {
        requestAnimationFrame(animate);
        updateHover();

        autoRotate += 0.0009;
        group.rotation.y = autoRotate + scrollRotation + mouse.x * 0.2;
        group.rotation.x = mouse.y * 0.12;

        var meshesDirty = {};
        for (var i = 0; i < instances.length; i++) {
            var inst = instances[i];
            inst.currentPos.lerp(inst.targetPos, 0.05);
            inst.currentColor.lerp(inst.targetColor, 0.08);
            inst.currentScale += (inst.targetScale - inst.currentScale) * 0.15;
            inst.euler.x += inst.spin.x;
            inst.euler.y += inst.spin.y;
            inst.euler.z += inst.spin.z;

            tmpQuat.setFromEuler(inst.euler);
            tmpScaleVec.set(inst.currentScale, inst.currentScale, inst.currentScale);
            tmpMatrix.compose(inst.currentPos, tmpQuat, tmpScaleVec);
            inst.mesh.setMatrixAt(inst.localId, tmpMatrix);
            inst.mesh.setColorAt(inst.localId, inst.currentColor);
            meshesDirty[inst.mesh.uuid] = inst.mesh;
        }
        for (var key in meshesDirty) {
            meshesDirty[key].instanceMatrix.needsUpdate = true;
            if (meshesDirty[key].instanceColor) meshesDirty[key].instanceColor.needsUpdate = true;
        }

        renderer.render(scene, camera);
    }

    animate();
})();

/*
 * home.js — интерактивная 3D-композиция из частиц для главной страницы
 * Чистый JS + Canvas 2D, без внешних библиотек.
 * Подключается ТОЛЬКО в index.html.
 */
(function () {
    'use strict';

    var canvas = document.getElementById('heroCanvas');
    if (!canvas) return;
    var ctx = canvas.getContext('2d');

    /* ---------- Палитра частиц: по семействам, от насыщенного к светлому ---------- */
    /* Внутри семейства индексы 0-1 — самые яркие/сочные тона (им отдаём приоритет),
       последние — светлые акценты. Так палитра получается разнообразнее прежней
       и в среднем заметно насыщеннее (меньше блёклых пастельных частиц). */
    var PALETTE_FAMILIES = [
        ['#663AF3', '#7C52F5', '#8F6EF7', '#A98CF9'],   // void violet (акцент)
        ['#B6D9FC', '#9CC7F5', '#7FB0E8', '#D1E4FA'],   // blueprint blue
        ['#98C0EF', '#7FA8DD', '#6690C9', '#B6D9FC'],   // глубокий небесный
        ['#C7D3EA', '#A9B8D6', '#8A9BC2', '#9DA7BA'],   // moon mist / fog veil
        ['#D8ECF8', '#C7D3EA', '#E8F2FB', '#D1E4FA'],   // ice highlight
        ['#FFFFFF', '#F0F6FC', '#E3EDF7', '#D8ECF8']    // светлые акценты
    ];
    var LIGHT_FAMILY = PALETTE_FAMILIES[5];
    var HOVER_COLOR = '#FFFFFF';

    function pickColor(favorBright) {
        var family = PALETTE_FAMILIES[Math.floor(Math.random() * PALETTE_FAMILIES.length)];
        /* с небольшим перекосом в сторону первых (самых насыщенных) оттенков семейства */
        var idx = favorBright
            ? Math.floor(Math.pow(Math.random(), 1.6) * family.length)
            : Math.floor(Math.random() * family.length);
        return family[Math.min(idx, family.length - 1)];
    }

    var isSmall = window.innerWidth < 480;
    var COUNT = isSmall ? 900 : 2200;

    /* ---------- Геометрия формы: плотная сфера с лёгким рельефом ---------- */
    /* Вместо двух долей «мозга» — единое компактное ядро. Рельеф создаётся
       через несколько наложенных волн по широте/долготе, чтобы поверхность
       не была идеально гладкой (лёгкие «пояса» плотности, а не борозды). */
    var CORE_RADIUS = { x: 1, y: 0.98, z: 0.96 };

    function reliefValue(theta, phi) {
        return Math.sin(theta * 5 + phi * 2) * 0.5 + Math.sin(theta * 2.2 - phi * 4) * 0.5;
    }

    /* Подбираем точку на поверхности сферы. relief используется только для
       лёгкой рельефности (яркость/размер), частицы покрывают всю оболочку
       равномерно — никаких «пустых» зон, в отличие от прошлой версии. */
    function samplePoint() {
        var theta = Math.acos(2 * Math.random() - 1); // равномерное распределение по сфере
        var phi = Math.random() * Math.PI * 2;
        var relief = reliefValue(theta, phi);
        var bulge = 1 + relief * 0.04;

        var ux = Math.sin(theta) * Math.cos(phi);
        var uy = Math.cos(theta);
        var uz = Math.sin(theta) * Math.sin(phi);

        return {
            x: ux * CORE_RADIUS.x * bulge,
            y: uy * CORE_RADIUS.y * bulge,
            z: uz * CORE_RADIUS.z * bulge,
            groove: relief,
            seamDist: Math.abs(ux) * CORE_RADIUS.x
        };
    }

    /* ---------- Создание частиц ---------- */
    var particles = [];

    function createParticles() {
        particles = [];
        for (var i = 0; i < COUNT; i++) {
            var s = samplePoint();

            /* Цвет: на выпуклостях чаще светлые акценты, в остальном — насыщенные
               тона вперемешку по всем семействам палитры */
            var color = (s.groove > 0.15 && Math.random() < 0.4)
                ? LIGHT_FAMILY[Math.floor(Math.random() * LIGHT_FAMILY.length)]
                : pickColor(true);
            /* немного "искристых" частиц — случайно берём любой сочный тон без привязки к рельефу */
            if (Math.random() < 0.12) color = pickColor(false);

            var dir = { x: Math.random() - 0.5, y: Math.random() - 0.5, z: Math.random() - 0.5 };
            var dirLen = Math.sqrt(dir.x * dir.x + dir.y * dir.y + dir.z * dir.z) || 1;
            var mag = 0.55 + Math.random() * 0.45;

            /* Плотность/яркость: выпуклости плотнее и светлее, края — темнее и реже */
            var bulgeFactor = Math.max(0, Math.min(1, (s.groove + 0.4) / 1.2));
            var edgeFactor = Math.max(0, Math.min(1, 1 - s.seamDist / 1.4));

            /* Форма: упор на треугольники и ромбы, куб/октаэдр — реже, для разнообразия */
            var shapeRoll = Math.random();
            var shapeType = shapeRoll < 0.4 ? 0            // треугольник
                : shapeRoll < 0.75 ? 3                       // ромб
                : shapeRoll < 0.9 ? 1                         // куб
                : 2;                                           // октаэдр

            particles.push({
                shapeType: shapeType,
                color: color,
                baseX: s.x, baseY: s.y, baseZ: s.z,
                scatterDirX: dir.x / dirLen, scatterDirY: dir.y / dirLen, scatterDirZ: dir.z / dirLen,
                scatterMag: mag,
                sizeFactor: 0.4 + bulgeFactor * 0.3 + edgeFactor * 0.2 + Math.random() * 0.18,
                fillAlpha: 0.14 + Math.random() * 0.24,
                baseOpacity: 0.55 + bulgeFactor * 0.35 + edgeFactor * 0.2,
                lineWidth: Math.random() < 0.1 ? 1.8 + Math.random() * 0.6 : 0.9 + Math.random() * 0.4,
                selfRotation: Math.random() * Math.PI * 2,
                selfSpin: (Math.random() - 0.5) * 0.02,
                hoverT: 0,
                hoverScaleTarget: 1.5 + Math.random() * 0.5,
                /* заполняются в updateGeometry() */
                baseX_px: 0, baseY_px: 0, baseZ_px: 0,
                scatterX_px: 0, scatterY_px: 0, scatterZ_px: 0
            });
        }
    }

    /* ---------- Геометрия под текущий размер экрана ---------- */
    var W = 0, H = 0, DPR = 1;
    var centerX = 0, centerY = 0, brainRadius = 0, scatterRadius = 0, focalD = 0;

    function updateGeometry() {
        W = window.innerWidth;
        H = window.innerHeight;
        DPR = Math.min(window.devicePixelRatio || 1, 2);

        canvas.width = W * DPR;
        canvas.height = H * DPR;
        canvas.style.width = W + 'px';
        canvas.style.height = H + 'px';
        ctx.setTransform(DPR, 0, 0, DPR, 0, 0);

        centerX = W / 2;
        centerY = H / 2;
        brainRadius = Math.min(W, H) * 0.30;
        scatterRadius = Math.min(W, H) * 0.35;
        focalD = Math.min(W, H) * 0.9;

        for (var i = 0; i < particles.length; i++) {
            var p = particles[i];
            p.baseX_px = p.baseX * brainRadius;
            p.baseY_px = p.baseY * brainRadius;
            p.baseZ_px = p.baseZ * brainRadius;

            var scatterR = scatterRadius * p.scatterMag;
            p.scatterX_px = p.scatterDirX * scatterR;
            p.scatterY_px = p.scatterDirY * scatterR;
            p.scatterZ_px = p.scatterDirZ * scatterR;
        }
    }

    /* ---------- Состояние скролла (0 = собран в «мозг», 1 = разлетелся) ---------- */
    var scrollTargetT = 0;
    var scrollCurrentT = 0;

    function onScroll() {
        var scrollY = window.scrollY || window.pageYOffset;
        var heroEl = document.querySelector('.hero');
        var heroHeight = heroEl ? heroEl.offsetHeight : window.innerHeight;
        var raw = (scrollY - heroHeight * 0.15) / (heroHeight * 0.65);
        scrollTargetT = Math.max(0, Math.min(1, raw));
    }
    window.addEventListener('scroll', onScroll, { passive: true });

    /* ---------- Мышь: параллакс-поворот + hover ---------- */
    var mouse = { x: 0, y: 0 };
    var mouseScreenX = W / 2, mouseScreenY = H / 2;
    window.addEventListener('mousemove', function (e) {
        mouse.x = (e.clientX / W) * 2 - 1;
        mouse.y = (e.clientY / H) * 2 - 1;
        mouseScreenX = e.clientX;
        mouseScreenY = e.clientY;
    });

    /* ---------- Resize ---------- */
    window.addEventListener('resize', updateGeometry);

    /* ---------- Отрисовка форм (линии + лёгкая заливка) ---------- */
    function drawTriangle(x, y, size, rotation) {
        ctx.beginPath();
        for (var i = 0; i < 3; i++) {
            var angle = rotation + (i / 3) * Math.PI * 2 - Math.PI / 2;
            var px = x + Math.cos(angle) * size;
            var py = y + Math.sin(angle) * size;
            if (i === 0) ctx.moveTo(px, py); else ctx.lineTo(px, py);
        }
        ctx.closePath();
    }

    function drawCube(x, y, size, rotation) {
        var s = size * 0.8;
        var offset = size * 0.35;
        var ox = Math.cos(rotation + Math.PI / 4) * offset;
        var oy = Math.sin(rotation + Math.PI / 4) * offset;

        ctx.save();
        ctx.translate(x, y);
        ctx.rotate(rotation);
        ctx.beginPath();
        ctx.rect(-s / 2, -s / 2, s, s);
        ctx.closePath();
        ctx.restore();

        /* грани, намекающие на объём куба */
        ctx.moveTo(x - s / 2, y - s / 2);
        ctx.lineTo(x - s / 2 + ox, y - s / 2 + oy);
        ctx.moveTo(x + s / 2, y - s / 2);
        ctx.lineTo(x + s / 2 + ox, y - s / 2 + oy);
        ctx.moveTo(x + s / 2, y + s / 2);
        ctx.lineTo(x + s / 2 + ox, y + s / 2 + oy);
    }

    function drawOctahedron(x, y, size, rotation) {
        ctx.beginPath();
        var top = { x: x + Math.cos(rotation - Math.PI / 2) * size, y: y + Math.sin(rotation - Math.PI / 2) * size };
        var right = { x: x + Math.cos(rotation) * size * 0.7, y: y + Math.sin(rotation) * size * 0.7 };
        var bottom = { x: x + Math.cos(rotation + Math.PI / 2) * size, y: y + Math.sin(rotation + Math.PI / 2) * size };
        var left = { x: x + Math.cos(rotation + Math.PI) * size * 0.7, y: y + Math.sin(rotation + Math.PI) * size * 0.7 };
        ctx.moveTo(top.x, top.y);
        ctx.lineTo(right.x, right.y);
        ctx.lineTo(bottom.x, bottom.y);
        ctx.lineTo(left.x, left.y);
        ctx.closePath();
        ctx.moveTo(left.x, left.y);
        ctx.lineTo(right.x, right.y);
    }

    function drawDiamond(x, y, size, rotation) {
        ctx.beginPath();
        var top = { x: x + Math.cos(rotation - Math.PI / 2) * size * 1.2, y: y + Math.sin(rotation - Math.PI / 2) * size * 1.2 };
        var right = { x: x + Math.cos(rotation) * size * 0.55, y: y + Math.sin(rotation) * size * 0.55 };
        var bottom = { x: x + Math.cos(rotation + Math.PI / 2) * size * 1.2, y: y + Math.sin(rotation + Math.PI / 2) * size * 1.2 };
        var left = { x: x + Math.cos(rotation + Math.PI) * size * 0.55, y: y + Math.sin(rotation + Math.PI) * size * 0.55 };
        ctx.moveTo(top.x, top.y);
        ctx.lineTo(right.x, right.y);
        ctx.lineTo(bottom.x, bottom.y);
        ctx.lineTo(left.x, left.y);
        ctx.closePath();
        /* горизонтальная грань — намёк на огранку */
        ctx.moveTo(left.x, left.y);
        ctx.lineTo(right.x, right.y);
    }

    /* ---------- Анимация ---------- */
    var t = 0;
    var scatterSpin = 0;
    var hoveredIndex = -1;

    function hexToRgb(hex) {
        var v = parseInt(hex.slice(1), 16);
        return { r: (v >> 16) & 255, g: (v >> 8) & 255, b: v & 255 };
    }
    var colorCache = {};
    function getRgb(hex) {
        if (!colorCache[hex]) colorCache[hex] = hexToRgb(hex);
        return colorCache[hex];
    }

    function animate() {
        requestAnimationFrame(animate);
        t += 0.016;

        /* плавный переход между «мозгом» и «разлётом» с ease */
        scrollCurrentT += (scrollTargetT - scrollCurrentT) * 0.06;
        scatterSpin += 0.0025 * scrollCurrentT;

        /* лёгкое покачивание в покое + параллакс от мыши + поворот при разлёте */
        var rotY = Math.sin(t * 0.35) * 0.18 + mouse.x * 0.5 + scatterSpin;
        var rotX = Math.cos(t * 0.28) * 0.06 + mouse.y * 0.25;
        var cosY = Math.cos(rotY), sinY = Math.sin(rotY);
        var cosX = Math.cos(rotX), sinX = Math.sin(rotX);

        ctx.clearRect(0, 0, W, H);

        var nearestDist = Infinity;
        var nearestIdx = -1;
        var projected = [];

        for (var i = 0; i < particles.length; i++) {
            var p = particles[i];

            var lx = p.baseX_px + (p.scatterX_px - p.baseX_px) * scrollCurrentT;
            var ly = p.baseY_px + (p.scatterY_px - p.baseY_px) * scrollCurrentT;
            var lz = p.baseZ_px + (p.scatterZ_px - p.baseZ_px) * scrollCurrentT;

            /* поворот вокруг Y, затем вокруг X */
            var x1 = lx * cosY + lz * sinY;
            var z1 = -lx * sinY + lz * cosY;
            var y1 = ly * cosX - z1 * sinX;
            var z2 = ly * sinX + z1 * cosX;

            var scale = focalD / (focalD + z2);
            var sx = centerX + x1 * scale;
            var sy = centerY + y1 * scale;

            var depthT = Math.max(0, Math.min(1, (z2 + brainRadius) / (brainRadius * 2)));
            var opacity = p.baseOpacity * (0.4 + depthT * 0.6);
            var size = (p.sizeFactor * 4 + 1.4) * scale;

            p.hoverT += ((i === hoveredIndex ? 1 : 0) - p.hoverT) * 0.15;
            if (p.hoverT > 0.01) {
                size *= 1 + (p.hoverScaleTarget - 1) * p.hoverT;
                opacity = opacity + (1 - opacity) * p.hoverT;
            }

            projected.push({ p: p, sx: sx, sy: sy, size: size, opacity: opacity, scale: scale });

            var dx = sx - mouseScreenX;
            var dy = sy - mouseScreenY;
            var dist = Math.sqrt(dx * dx + dy * dy);
            if (dist < nearestDist) { nearestDist = dist; nearestIdx = i; }
        }

        hoveredIndex = nearestDist < 26 ? nearestIdx : -1;

        for (var j = 0; j < projected.length; j++) {
            var item = projected[j];
            var particle = item.p;
            particle.selfRotation += particle.selfSpin;

            var isHover = particle.hoverT > 0.01;
            var rgb = isHover ? getRgb(HOVER_COLOR) : getRgb(particle.color);
            if (isHover) {
                var base = getRgb(particle.color);
                rgb = {
                    r: base.r + (255 - base.r) * particle.hoverT,
                    g: base.g + (255 - base.g) * particle.hoverT,
                    b: base.b + (255 - base.b) * particle.hoverT
                };
            }

            ctx.beginPath();
            if (particle.shapeType === 0) drawTriangle(item.sx, item.sy, item.size, particle.selfRotation);
            else if (particle.shapeType === 1) drawCube(item.sx, item.sy, item.size, particle.selfRotation);
            else if (particle.shapeType === 3) drawDiamond(item.sx, item.sy, item.size, particle.selfRotation);
            else drawOctahedron(item.sx, item.sy, item.size, particle.selfRotation);

            ctx.fillStyle = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + (particle.fillAlpha * item.opacity) + ')';
            ctx.fill();
            ctx.strokeStyle = 'rgba(' + rgb.r + ',' + rgb.g + ',' + rgb.b + ',' + item.opacity + ')';
            ctx.lineWidth = particle.lineWidth;
            ctx.stroke();
        }
    }

    /* ---------- Инициализация ---------- */
    createParticles();
    updateGeometry();
    onScroll();
    animate();
})();

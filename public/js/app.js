// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  1.  GLOBALS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

let CAMPUS_NODES = [];
let map, markerLayer, routeLayer;
let markerMap = {};
let currentLocationCoords = null;
let currentLocationMarker = null;

// ─── Translation System ──────────────────────────────
function t(key) {
    const keys = key.split('.');
    let value = window.TRANSLATIONS || {};
    for (const k of keys) {
        if (value && value[k] !== undefined) {
            value = value[k];
        } else {
            return key; // Fallback to key if translation not found
        }
    }
    return value;
}

function getCategoryName(categoryKey) {
    const categories = {
        'buildings': t('categories.buildings'),
        'chill': t('categories.chill'),
        'dining': t('categories.dining'),
        'services': t('categories.services'),
        'residence': t('categories.residence'),
        'sports': t('categories.sports'),
    };
    return categories[categoryKey] || categoryKey || t('panel.category');
}

// ─── Custom Icons ──────────────────────────────

const startIcon = L.divIcon({
    className: 'custom-pin',
    html: `<div style="width:32px;height:32px;background:#ffb224;border:3px solid white;border-radius:50%;box-shadow:0 2px 8px rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;font-size:16px;color:white;transform:translate(-50%,-50%);">🚀</div>`,
    iconSize: [32, 32],
    iconAnchor: [16, 16],
    popupAnchor: [0, -16],
});

const endIcon = L.divIcon({
    className: 'custom-pin',
    html: `<div style="width:36px;height:36px;background:#c0392b;border:3px solid white;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 2px 8px rgba(0,0,0,0.4);display:flex;align-items:center;justify-content:center;"><span style="transform:rotate(45deg);font-size:16px;color:white;">📍</span></div>`,
    iconSize: [36, 36],
    iconAnchor: [18, 18],
    popupAnchor: [0, -18],
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  2.  RIGHT PANEL CONTROLS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function openRightPanel() {
    const panel = document.getElementById('right-panel');
    const overlay = document.getElementById('panel-overlay');
    if (panel) panel.classList.add('active');
    if (overlay) overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeRightPanel() {
    const panel = document.getElementById('right-panel');
    const overlay = document.getElementById('panel-overlay');
    if (panel) panel.classList.remove('active');
    if (overlay) overlay.classList.remove('active');
    document.body.style.overflow = '';
}

document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('panel-overlay');
    if (overlay) {
        overlay.addEventListener('click', closeRightPanel);
    }
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  3.  MAP INIT
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function initMap() {
    console.log('🗺️ Initialising map...');
    map = L.map('map', { zoomControl: false, center: [35.221, 33.4175], zoom: 16.2 });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19, attribution: '© OpenStreetMap' }).addTo(map);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    markerLayer = L.layerGroup().addTo(map);
    routeLayer = L.layerGroup().addTo(map);

    map.on('click', function () {
        closeRightPanel();
    });

    setTimeout(() => { if (map) map.invalidateSize(); }, 300);
    console.log('✅ Map initialised.');
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  4.  MARKERS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function getColor(node) {
    return node.accessible ? '#1fa86a' : '#d94a4a';
}

function addMarkers() {
    markerLayer.clearLayers();
    markerMap = {};
    for (const n of CAMPUS_NODES) {
        const color = getColor(n);
        const marker = L.circleMarker([n.lat, n.lng], {
            radius: 7,
            fillColor: color,
            color: '#fff',
            weight: 2,
            opacity: 1,
            fillOpacity: 0.85,
        }).addTo(markerLayer)
            .bindPopup(`<strong>${n.name}</strong><br><span class="sub">ID: ${n.id} · ${n.accessible ? '♿ Accessible' : '🚫 Not accessible'}</span>`);

        marker.on('click', function () { zoomToNode(n.id); });
        markerMap[n.id] = marker;
    }
    console.log(`✅ ${CAMPUS_NODES.length} markers added.`);
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  5.  DROPDOWNS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function populateDropdowns() {
    const startSelect = document.getElementById('start');
    const endSelect = document.getElementById('end');
    startSelect.innerHTML = '<option value="">' + t('sidebar.select_location') + '</option>';
    endSelect.innerHTML = '<option value="">' + t('sidebar.select_location') + '</option>';
    const sorted = [...CAMPUS_NODES].sort((a, b) => a.name.localeCompare(b.name));
    for (const n of sorted) {
        const opt1 = document.createElement('option');
        opt1.value = n.id;
        opt1.textContent = n.name;
        startSelect.appendChild(opt1);
        const opt2 = document.createElement('option');
        opt2.value = n.id;
        opt2.textContent = n.name;
        endSelect.appendChild(opt2);
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  6.  ZOOM & SIDEBAR
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function zoomToNode(id) {
    const node = CAMPUS_NODES.find(n => n.id === id);
    if (!node) return;
    const marker = markerMap[id];
    if (marker) { map.setView([node.lat, node.lng], 18); marker.openPopup(); }
    else { map.setView([node.lat, node.lng], 18); }
    showNodeDetails(id);
    document.querySelectorAll('.sidebar-item').forEach(el => el.classList.remove('active'));
    const activeItem = document.querySelector(`.sidebar-item[data-id="${id}"]`);
    if (activeItem) activeItem.classList.add('active');
}

function attachSidebarListeners() {
    document.querySelectorAll('.sidebar-item').forEach(item => {
        item.addEventListener('click', function () {
            const id = parseInt(this.dataset.id, 10);
            zoomToNode(id);
        });
    });
    console.log('✅ Sidebar listeners attached.');
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  7.  SHOW DETAILS (WITH TRANSLATIONS)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function showNodeDetails(id) {
    const node = CAMPUS_NODES.find(n => n.id === id);
    if (!node) return;
    const container = document.getElementById('details-content');

    const image = node.image || node.image_url || 'https://via.placeholder.com/400x200/2a3a5a/aaa?text=No+Image';

    let buttonHtml = '';
    if (node.link) {
        buttonHtml = `<a href="${node.link}" target="_blank" class="detail-btn">${node.button_text || t('panel.learn_more')}</a>`;
    }

    const statusMessage = node.accessible ? t('panel.status_open') : t('panel.status_closed');

    container.innerHTML = `
        <div class="detail-card">
            <img src="${image}" alt="${node.name}" onerror="this.src='https://via.placeholder.com/400x200/2a3a5a/aaa?text=Image+Not+Found'">
            <div class="detail-body">
                <h3>${node.name}</h3>
                <p>${node.description || 'No description available.'}</p>
                <span class="badge">${node.accessible ? '♿ Accessible' : '🚫 Not accessible'}</span>
                <div class="status-badge ${node.accessible ? 'open' : 'closed'}">${statusMessage}</div>
                ${buttonHtml}
            </div>
        </div>

        <div class="info-grid">
            <div>
                <div class="info-label">${t('panel.address')}</div>
                <div class="info-value">${node.address || 'CIU Campus, North Cyprus'}</div>
            </div>
            <div>
                <div class="info-label">${t('panel.category')}</div>
                <div class="info-value">${getCategoryName(node.category)}</div>
            </div>
            <div>
                <div class="info-label">${t('panel.hours')}</div>
                <div class="info-value">${node.opening_hours || '08:00 - 20:00'}</div>
            </div>
            <div>
                <div class="info-label">${t('panel.phone')}</div>
                <div class="info-value">${node.phone || '+90 392 671 1111'}</div>
            </div>
        </div>

        <div class="panel-actions">
            <button onclick="setAsStart(${node.id})">${t('panel.directions')}</button>
            <button onclick="setAsHome(${node.id})">${t('panel.set_home')}</button>
            <button onclick="saveLocation(${node.id})">${t('panel.save')}</button>
            <button onclick="shareLocation(${node.id})">${t('panel.share')}</button>
        </div>

        <div class="related-items">
            <h4>${t('panel.related_items')}</h4>
            <div class="related-item">
                <span class="item-icon">📚</span>
                <span>${t('panel.nearby_library')}</span>
            </div>
            <div class="related-item">
                <span class="item-icon">☕</span>
                <span>${t('panel.nearby_cafe')}</span>
            </div>
            <div class="related-item">
                <span class="item-icon">🏛️</span>
                <span>${t('panel.nearby_services')}</span>
            </div>
        </div>
    `;

    openRightPanel();
}

// ─── Action Functions ──────────────────────────────

function setAsStart(id) {
    const node = CAMPUS_NODES.find(n => n.id === id);
    if (!node) return;
    const startSelect = document.getElementById('start');
    let opt = startSelect.querySelector(`option[value="${id}"]`);
    if (!opt) {
        opt = document.createElement('option');
        opt.value = id;
        opt.textContent = node.name;
        startSelect.appendChild(opt);
    }
    startSelect.value = id;
    document.getElementById('status').textContent = t('status.start_set') + node.name + '"';
    document.getElementById('status').className = 'success';
    closeRightPanel();
}

function setAsHome(id) {
    const node = CAMPUS_NODES.find(n => n.id === id);
    alert(`🏠 "${node.name}" ` + (window.LOCALE === 'tr' ? 'evim olarak ayarlandı!' : 'set as your home location!'));
}

function saveLocation(id) {
    const node = CAMPUS_NODES.find(n => n.id === id);
    alert(`💾 "${node.name}" ` + (window.LOCALE === 'tr' ? 'favorilerinize kaydedildi!' : 'saved to your favorites!'));
}

function shareLocation(id) {
    const node = CAMPUS_NODES.find(n => n.id === id);
    if (navigator.share) {
        navigator.share({
            title: node.name,
            text: `${t('panel.share')}: ${node.name} - CIU Campus`,
            url: window.location.href,
        });
    } else {
        navigator.clipboard.writeText(`${node.name} - CIU Campus`);
        alert(t('panel.share') + ' ' + (window.LOCALE === 'tr' ? 'panoya kopyalandı!' : 'copied to clipboard!'));
    }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  8.  GPS LOCATION
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function addCurrentLocationMarker(lat, lng) {
    if (currentLocationMarker) map.removeLayer(currentLocationMarker);
    const pulseIcon = L.divIcon({
        className: 'pulse-marker',
        html: `<div style="width:20px;height:20px;background:#2a6df4;border:3px solid white;border-radius:50%;box-shadow:0 0 0 0 rgba(42,109,244,0.7);animation:pulse 1.5s infinite;"></div><style>@keyframes pulse{0%{box-shadow:0 0 0 0 rgba(42,109,244,0.7)}70%{box-shadow:0 0 0 15px rgba(42,109,244,0)}100%{box-shadow:0 0 0 0 rgba(42,109,244,0)}}</style>`,
        iconSize: [20, 20],
        iconAnchor: [10, 10],
    });
    currentLocationMarker = L.marker([lat, lng], { icon: pulseIcon }).addTo(map).bindPopup('📍 Your current location').openPopup();
    map.setView([lat, lng], 18);
}

document.getElementById('useMyLocation').addEventListener('click', function () {
    const status = document.getElementById('status');
    if (!navigator.geolocation) {
        status.textContent = '❌ ' + (window.LOCALE === 'tr' ? 'Konum desteklenmiyor.' : 'Geolocation is not supported.');
        status.className = 'error';
        return;
    }
    status.textContent = '⏳ ' + (window.LOCALE === 'tr' ? 'Konumunuz alınıyor…' : 'Getting your location…');
    status.className = '';
    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;
            currentLocationCoords = { lat, lng };
            addCurrentLocationMarker(lat, lng);
            const startSelect = document.getElementById('start');
            const existing = startSelect.querySelector('option[value="my-location"]');
            if (existing) existing.remove();
            const opt = document.createElement('option');
            opt.value = 'my-location';
            opt.textContent = t('sidebar.my_location');
            startSelect.appendChild(opt);
            startSelect.value = 'my-location';
            status.textContent = t('status.location_set');
            status.className = 'success';
        },
        (err) => {
            let msg = (window.LOCALE === 'tr' ? 'Konum alınamadı. ' : 'Could not get location. ');
            if (err.code === 1) msg += (window.LOCALE === 'tr' ? 'İzin verilmedi.' : 'Permission denied.');
            else if (err.code === 2) msg += (window.LOCALE === 'tr' ? 'Konum kullanılamıyor.' : 'Position unavailable.');
            else if (err.code === 3) msg += (window.LOCALE === 'tr' ? 'Zaman aşımı.' : 'Timeout.');
            else msg += (window.LOCALE === 'tr' ? 'Bilinmeyen hata.' : 'Unknown error.');
            status.textContent = '❌ ' + msg;
            status.className = 'error';
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  9.  ROUTING HELPERS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function haversine(lat1, lng1, lat2, lng2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLng = (lng2 - lng1) * Math.PI / 180;
    return R * 2 * Math.atan2(Math.sqrt(Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2), Math.sqrt(1 - (Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2)));
}

function buildGraph(nodes, maxDist = 120) {
    const edges = [];
    for (let i = 0; i < nodes.length; i++) {
        for (let j = i + 1; j < nodes.length; j++) {
            const d = haversine(nodes[i].lat, nodes[i].lng, nodes[j].lat, nodes[j].lng);
            if (d <= maxDist) edges.push({ from: nodes[i].id, to: nodes[j].id, distance: Math.round(d) });
        }
    }
    const spine = nodes.map(n => n.id).sort((a, b) => a - b);
    for (let i = 0; i < spine.length - 1; i++) {
        const a = nodes.find(n => n.id === spine[i]);
        const b = nodes.find(n => n.id === spine[i + 1]);
        if (a && b) {
            const d = haversine(a.lat, a.lng, b.lat, b.lng);
            if (d <= 500) edges.push({ from: a.id, to: b.id, distance: Math.round(d) });
        }
    }
    const mapEdges = new Map();
    for (const e of edges) {
        const key = Math.min(e.from, e.to) + '-' + Math.max(e.from, e.to);
        if (!mapEdges.has(key) || mapEdges.get(key).distance > e.distance) mapEdges.set(key, e);
    }
    return Array.from(mapEdges.values());
}

function dijkstra(graph, startId, endId) {
    const dist = new Map(), parent = new Map(), visited = new Set();
    const pq = new PriorityQueue();
    for (const n of graph.nodes) { dist.set(n.id, Infinity); parent.set(n.id, null); }
    dist.set(startId, 0);
    pq.enqueue(startId, 0);
    while (!pq.isEmpty()) {
        const cur = pq.dequeue().element;
        if (visited.has(cur)) continue;
        visited.add(cur);
        if (cur === endId) break;
        const neighbors = graph.adj.get(cur) || [];
        for (const nbr of neighbors) {
            if (visited.has(nbr.to)) continue;
            const alt = dist.get(cur) + nbr.weight;
            if (alt < dist.get(nbr.to)) {
                dist.set(nbr.to, alt);
                parent.set(nbr.to, cur);
                pq.enqueue(nbr.to, alt);
            }
        }
    }
    if (!parent.has(endId) || parent.get(endId) === null && startId !== endId) return null;
    const path = [];
    let cur = endId;
    while (cur !== null) { path.unshift(cur); cur = parent.get(cur); if (cur === startId) { path.unshift(startId); break; } }
    if (path.length > 1 && path[0] === path[1]) path.shift();
    if (path[0] !== startId || path[path.length - 1] !== endId) return null;
    return path;
}

class PriorityQueue {
    constructor() { this.heap = []; }
    enqueue(e, p) { this.heap.push({ element: e, priority: p }); this._bubbleUp(this.heap.length - 1); }
    dequeue() { const min = this.heap[0]; const end = this.heap.pop(); if (this.heap.length > 0) { this.heap[0] = end; this._sinkDown(0); } return min; }
    isEmpty() { return this.heap.length === 0; }
    _bubbleUp(i) { const el = this.heap[i]; while (i > 0) { const pIdx = Math.floor((i - 1) / 2), parent = this.heap[pIdx]; if (el.priority >= parent.priority) break; this.heap[i] = parent; this.heap[pIdx] = el; i = pIdx; } }
    _sinkDown(i) { const len = this.heap.length, el = this.heap[i]; while (true) { let lIdx = 2 * i + 1, rIdx = 2 * i + 2, swap = null; if (lIdx < len && this.heap[lIdx].priority < el.priority) swap = lIdx; if (rIdx < len && this.heap[rIdx].priority < (swap === null ? el.priority : this.heap[swap].priority)) swap = rIdx; if (swap === null) break; this.heap[i] = this.heap[swap]; this.heap[swap] = el; i = swap; } }
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  10. ROUTING (OSRM + FALLBACK)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

async function fetchOSRMRoute(lat1, lng1, lat2, lng2) {
    const url = `/api/route?start=${lng1},${lat1}&end=${lng2},${lat2}`;
    const response = await fetch(url);
    if (!response.ok) throw new Error('OSRM request failed');
    const data = await response.json();
    if (data.code !== 'Ok' || !data.routes || data.routes.length === 0) throw new Error('No route');
    const route = data.routes[0];
    const coords = route.geometry.coordinates.map(c => [c[1], c[0]]);
    return { coords, distance: route.distance, duration: route.duration };
}

function drawRoute(coords, distance, duration, startName, endName, method = 'OSRM') {
    routeLayer.clearLayers();
    L.polyline(coords, { color: '#2a6df4', weight: 5, opacity: 0.85, className: 'route-line', smoothFactor: 1.2 }).addTo(routeLayer);
    const bounds = L.latLngBounds(coords);
    map.fitBounds(bounds, { padding: [60, 60], maxZoom: 17 });
    const first = coords[0], last = coords[coords.length - 1];
    L.marker(first, { icon: startIcon }).addTo(routeLayer).bindPopup('🚀 Start: ' + startName);
    L.marker(last, { icon: endIcon }).addTo(routeLayer).bindPopup('🏁 End: ' + endName);
    const mins = Math.round(duration / 60);
    const distKm = (distance / 1000).toFixed(1);
    const statusText = (window.LOCALE === 'tr' ? '✅ ' : '✅ ') + startName + ' → ' + endName + '  ·  ' + distKm + ' km  ·  ~' + mins + ' min ' + (window.LOCALE === 'tr' ? 'yürüyüş' : 'walk') + '  ·  <em>' + method + '</em>';
    document.getElementById('status').innerHTML = statusText;
    document.getElementById('status').className = 'success';
}

function drawInternalRoute(pathIds, startName, endName) {
    const coords = pathIds.map(id => { const n = CAMPUS_NODES.find(n => n.id === id); return [n.lat, n.lng]; });
    const totalDist = coords.reduce((acc, cur, i) => i === 0 ? 0 : acc + haversine(coords[i - 1][0], coords[i - 1][1], cur[0], cur[1]), 0);
    drawRoute(coords, totalDist, totalDist / 1.3, startName, endName, window.LOCALE === 'tr' ? 'Dahili Grafik' : 'Internal Graph');
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  11. ROUTE HANDLER (WITH TRANSLATIONS)
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

document.getElementById('findRoute').addEventListener('click', async function () {
    const startSelect = document.getElementById('start');
    const endSelect = document.getElementById('end');
    const startVal = startSelect.value;
    const endVal = endSelect.value;
    const status = document.getElementById('status');

    if (!startVal || !endVal) {
        status.textContent = t('status.select_both');
        status.className = 'error';
        return;
    }
    if (startVal === endVal) {
        status.textContent = t('status.must_different');
        status.className = 'error';
        return;
    }

    let startLat, startLng, startName;
    if (startVal === 'my-location') {
        if (!currentLocationCoords) {
            status.textContent = t('status.location_not_set');
            status.className = 'error';
            return;
        }
        startLat = currentLocationCoords.lat;
        startLng = currentLocationCoords.lng;
        startName = t('sidebar.my_location');
    } else {
        const startNode = CAMPUS_NODES.find(n => n.id === parseInt(startVal, 10));
        if (!startNode) {
            status.textContent = t('status.invalid_start');
            status.className = 'error';
            return;
        }
        startLat = startNode.lat;
        startLng = startNode.lng;
        startName = startNode.name;
    }

    let endLat, endLng, endName;
    if (endVal === 'my-location') {
        if (!currentLocationCoords) {
            status.textContent = t('status.location_not_set');
            status.className = 'error';
            return;
        }
        endLat = currentLocationCoords.lat;
        endLng = currentLocationCoords.lng;
        endName = t('sidebar.my_location');
    } else {
        const endNode = CAMPUS_NODES.find(n => n.id === parseInt(endVal, 10));
        if (!endNode) {
            status.textContent = t('status.invalid_end');
            status.className = 'error';
            return;
        }
        endLat = endNode.lat;
        endLng = endNode.lng;
        endName = endNode.name;
    }

    status.textContent = t('status.fetch_route');
    status.className = '';

    try {
        const { coords, distance, duration } = await fetchOSRMRoute(startLat, startLng, endLat, endLng);
        const straightDist = haversine(startLat, startLng, endLat, endLng);
        if (distance > straightDist * 2.8) {
            status.textContent = t('status.osrm_detour');
            if (startVal !== 'my-location' && endVal !== 'my-location') {
                const edges = buildGraph(CAMPUS_NODES, 120);
                const graph = { nodes: CAMPUS_NODES, adj: new Map() };
                for (const n of CAMPUS_NODES) graph.adj.set(n.id, []);
                for (const e of edges) {
                    graph.adj.get(e.from).push({ to: e.to, weight: e.distance });
                    graph.adj.get(e.to).push({ to: e.from, weight: e.distance });
                }
                const path = dijkstra(graph, parseInt(startVal, 10), parseInt(endVal, 10));
                if (path && path.length > 1) { drawInternalRoute(path, startName, endName); return; }
            }
            drawRoute([coords[0], coords[coords.length - 1]], distance, duration, startName, endName, window.LOCALE === 'tr' ? 'Direkt' : 'Direct');
            return;
        }
        drawRoute(coords, distance, duration, startName, endName);
    } catch (err) {
        status.textContent = t('status.osrm_error');
        if (startVal !== 'my-location' && endVal !== 'my-location') {
            const edges = buildGraph(CAMPUS_NODES, 120);
            const graph = { nodes: CAMPUS_NODES, adj: new Map() };
            for (const n of CAMPUS_NODES) graph.adj.set(n.id, []);
            for (const e of edges) {
                graph.adj.get(e.from).push({ to: e.to, weight: e.distance });
                graph.adj.get(e.to).push({ to: e.from, weight: e.distance });
            }
            const path = dijkstra(graph, parseInt(startVal, 10), parseInt(endVal, 10));
            if (path && path.length > 1) { drawInternalRoute(path, startName, endName); return; }
        }
        drawRoute([[startLat, startLng], [endLat, endLng]], 0, 0, startName, endName, window.LOCALE === 'tr' ? 'Direkt' : 'Direct');
        status.textContent = t('status.no_route');
        status.className = 'error';
        console.error(err);
    }
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  12. SIDEBAR TOGGLE
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

window.toggleSidebar = function () {
    const sidebar = document.getElementById('left-sidebar');
    const hamburger = document.getElementById('menuToggle');
    sidebar.classList.toggle('sidebar-hidden');
    sidebar.classList.toggle('sidebar-visible');
    hamburger.classList.toggle('active');
    setTimeout(() => { if (map) map.invalidateSize(); }, 350);
};

window.toggleCategory = function (element) {
    element.classList.toggle('open');
    const items = element.nextElementSibling;
    if (items) items.classList.toggle('open');
};

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  13. DYNAMIC GREETING
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

function setGreeting() {
    const el = document.getElementById('greetingText');
    if (!el) return;
    const hour = new Date().getHours();
    let greeting;
    const isTR = window.LOCALE === 'tr';
    if (hour >= 5 && hour < 12) {
        greeting = '🌅 ' + (isTR ? 'Günaydın' : 'Good morning');
    } else if (hour >= 12 && hour < 17) {
        greeting = '☀️ ' + (isTR ? 'İyi günler' : 'Good afternoon');
    } else if (hour >= 17 && hour < 21) {
        greeting = '🌇 ' + (isTR ? 'İyi akşamlar' : 'Good evening');
    } else {
        greeting = '🌙 ' + (isTR ? 'İyi geceler' : 'Good night');
    }
    el.textContent = greeting;
}

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  14. KEYBOARD SHORTCUTS
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

document.addEventListener('keydown', function (e) {
    if (e.key === 'Enter' && e.target.tagName !== 'BUTTON') {
        document.getElementById('findRoute').click();
    }
    if (e.key === 'Escape') {
        const sidebar = document.getElementById('left-sidebar');
        const hamburger = document.getElementById('menuToggle');
        if (!sidebar.classList.contains('sidebar-hidden')) {
            sidebar.classList.add('sidebar-hidden');
            sidebar.classList.remove('sidebar-visible');
            hamburger.classList.remove('active');
            setTimeout(() => { if (map) map.invalidateSize(); }, 350);
        }
        closeRightPanel();
    }
});

// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
//  15. INIT – LOAD FROM API
// ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

document.addEventListener('DOMContentLoaded', function () {
    // Set greeting
    setGreeting();

    const overlay = document.getElementById('panel-overlay');
    if (overlay) {
        overlay.addEventListener('click', closeRightPanel);
    }

    // Open first category
    const firstToggle = document.querySelector('.category-toggle');
    if (firstToggle) {
        firstToggle.classList.add('open');
        const firstItems = firstToggle.nextElementSibling;
        if (firstItems) firstItems.classList.add('open');
    }

    // ─── LOAD FROM API ──────────────────────
    fetch('/api/v1/locations')
        .then(res => {
            if (!res.ok) throw new Error('API request failed');
            return res.json();
        })
        .then(response => {
            let data = response.data || response;
            if (data && typeof data === 'object' && !Array.isArray(data)) {
                data = data.data || data;
            }
            if (!Array.isArray(data)) {
                throw new Error('Invalid data format');
            }
            CAMPUS_NODES = data;
            initMap();
            populateDropdowns();
            addMarkers();
            attachSidebarListeners();
            console.log('✅ ' + CAMPUS_NODES.length + ' nodes loaded from API.');
        })
        .catch(err => {
            console.error('❌ Failed to load campus data from API:', err);
            document.getElementById('status').textContent = '⚠️ ' + (window.LOCALE === 'tr' ? 'Kampüs verileri yüklenemedi. Lütfen yenileyin.' : 'Could not load campus data. Please refresh.');
            document.getElementById('status').className = 'error';
        });
});
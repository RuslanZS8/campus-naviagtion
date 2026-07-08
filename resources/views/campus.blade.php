<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Smart Campus Navigation</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}" />
</head>

<body>
    <!-- ─── HEADER ─── -->
    <header class="ciu-header">
        <div class="ciu-container">
            <a href="#" class="ciu-brand">
                <img class="ciu-logo-main" src="https://www.ciu.edu.tr/map/logo_en.svg" alt="CIU">
            </a>
            <div class="ciu-lang-selector">
                <a href="?lang=en" class="ciu-lang-link {{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
                <span style="color: rgba(255,255,255,0.3); margin: 0 4px;">|</span>
                <a href="?lang=tr" class="ciu-lang-link {{ app()->getLocale() == 'tr' ? 'active' : '' }}">TR</a>
            </div>
        </div>
    </header>
    <!-- ─── MAIN APP ─── -->
    <div id="app">
        <!-- ─── HAMBURGER BUTTON ─── -->
        <button id="menuToggle" class="hamburger-btn" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- ─── LEFT SIDEBAR ─── -->
        <aside id="left-sidebar" class="sidebar-hidden">
            <!-- ─── Grouped Locations ─── -->
            <div class="sidebar-section">
                <h3>📍 Campus Locations</h3>

                @php
                $categories = [
                'buildings' => '🏛️ Academic Buildings',
                'chill' => '🌿 Chill Zone & Relaxation',
                'dining' => '🍽️ Dining & Cafés',
                'services' => '📋 Student Services',
                'residence' => '🏠 Residence Halls',
                'sports' => '⚽ Sports & Recreation',
                ];
                $grouped = collect($nodes)->groupBy('category');
                @endphp

                @foreach($categories as $key => $label)
                @if(isset($grouped[$key]) && $grouped[$key]->count() > 0)
                <div class="category-group">
                    <div class="category-toggle" onclick="toggleCategory(this)">
                        <span class="category-icon">▶</span>
                        <span class="category-label">{{ $label }}</span>
                        <span class="category-count">{{ $grouped[$key]->count() }}</span>
                    </div>
                    <ul class="category-items">
                        @foreach($grouped[$key] as $node)
                        <li class="sidebar-item" data-id="{{ $node['id'] }}">
                            <span class="icon">📍</span> {{ $node['name'] }}
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @endforeach
            </div>

            <!-- ─── Route Controls ─── -->
            <div class="sidebar-section route-section">
                <h3>🗺️ Plan Your Route</h3>
                <div class="route-controls-inline">
                    <div class="control-group">
                        <label for="start">From</label>
                        <div style="display: flex; gap: 6px; align-items: center;">
                            <select id="start" style="flex: 1;">
                                <option value="">— select —</option>
                            </select>
                            <button type="button" id="useMyLocation" style="
                                background: #2a6df4;
                                color: #fff;
                                border: none;
                                padding: 6px 12px;
                                border-radius: 6px;
                                cursor: pointer;
                                font-size: 0.8rem;
                                white-space: nowrap;
                            ">📍 My Location</button>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="end">To</label>
                        <select id="end">
                            <option value="">— select —</option>
                        </select>
                    </div>
                    <button id="findRoute" class="route-btn">🔄 Find Route</button>
                    <div id="status" class="route-status">Select start &amp; end, then click <strong>Find Route</strong></div>
                </div>
            </div>

            <!-- ─── Quick Links ─── -->
            <div class="sidebar-section">
                <h3>📚 Quick Links</h3>
                <ul>
                    <li>📚 Central Library</li>
                    <li>🏋️ Arena Gym</li>
                </ul>
            </div>

            <!-- ─── Bottom Actions ─── -->
            <div class="sidebar-actions">
                <button>📍 FIND MY LOCATION</button>
                <button>🗺️ MAP LAYERS</button>
                <button>📌 MY SAVED ROUTES</button>
                <button>🚌 FIND NEAREST SHUTTLE</button>
                <button>🎓 CAMPUS TOUR GUIDE</button>
                <button>⚠️ REPORT A PATH ISSUE</button>
            </div>

            <div class="sidebar-footer">Powered by CIU</div>
        </aside>

        <!-- ─── MAP CONTAINER ─── -->
        <main id="map-container">
            <div id="map"></div>
        </main>

        <!-- ─── OVERLAY ─── -->
        <div id="panel-overlay"></div>

        <!-- ─── RIGHT PANEL ─── -->
        <aside id="right-panel">
            <button class="panel-close-btn" onclick="closeRightPanel()">✕</button>
            <div class="panel-header">
                <h2>📍 Location Details</h2>
            </div>
            <div id="details-content">
                <div class="placeholder">
                    <p>Select a location from the map or sidebar to see details.</p>
                </div>
            </div>
        </aside>
    </div>

    <!-- ─── SCRIPTS ─── -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- ─── INLINE TOGGLE SCRIPT ─── -->
    <script>
        // Sidebar toggle function
        function toggleSidebar() {
            const sidebar = document.getElementById('left-sidebar');
            const hamburger = document.getElementById('menuToggle');
            sidebar.classList.toggle('sidebar-hidden');
            sidebar.classList.toggle('sidebar-visible');
            hamburger.classList.toggle('active');

            setTimeout(() => {
                if (typeof map !== 'undefined' && map) {
                    map.invalidateSize();
                }
            }, 350);
        }

        // Category toggle function
        function toggleCategory(element) {
            element.classList.toggle('open');
            const items = element.nextElementSibling;
            if (items) {
                items.classList.toggle('open');
            }
        }

        // Close sidebar with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('left-sidebar');
                const hamburger = document.getElementById('menuToggle');
                if (!sidebar.classList.contains('sidebar-hidden')) {
                    sidebar.classList.add('sidebar-hidden');
                    sidebar.classList.remove('sidebar-visible');
                    hamburger.classList.remove('active');
                    setTimeout(() => {
                        if (typeof map !== 'undefined' && map) {
                            map.invalidateSize();
                        }
                    }, 350);
                }
            }
        });

        // Open first category by default on load
        document.addEventListener('DOMContentLoaded', function() {
            const firstToggle = document.querySelector('.category-toggle');
            if (firstToggle) {
                firstToggle.classList.add('open');
                const firstItems = firstToggle.nextElementSibling;
                if (firstItems) firstItems.classList.add('open');
            }
        });
    </script>
    <script>
        // This is a JavaScript comment
        window.LOCALE = '{{ app()->getLocale() }}';
    </script>
</body>

</html>
import maplibregl from 'maplibre-gl';

export function initTeamZonePicker(containerId, options = {}) {
    const {
        styleUrl = 'https://tiles.openfreemap.org/styles/liberty',
        center = [121.0402, 14.5432],
        zoom = 11,
        zonesEndpoint = '/api/zones',
        zones = null,
        onZoneSelect = null,
        clickable = true,
        assignedZoneIds = [],
        assignEndpoint = null,
        onAssign = null,
    } = options;

    const map = new maplibregl.Map({
        container: containerId,
        style: styleUrl,
        center,
        zoom,
        attributionControl: false,
    });

    map.addControl(new maplibregl.NavigationControl(), 'top-right');
    map.addControl(new maplibregl.AttributionControl({ compact: true }), 'bottom-right');

    const state = { zones: [], markers: [], assigned: new Set(assignedZoneIds) };

    function isAssigned(zoneId) {
        return state.assigned.has(zoneId);
    }

    function setAssigned(zoneId, assigned) {
        if (assigned) state.assigned.add(zoneId);
        else state.assigned.delete(zoneId);
    }

    function loadZones() {
        if (zones) {
            state.zones = zones;
            renderZones();
        } else {
            fetch(zonesEndpoint)
                .then(res => res.json())
                .then(data => { state.zones = data.zones || data; renderZones(); })
                .catch(err => console.error('Failed to load zones:', err));
        }
    }

    function updateMarkerStyle(marker, zone) {
        const fill = isAssigned(zone.id) ? '#2563eb' : '#9ca3af';
        const opacity = isAssigned(zone.id) ? '1' : '0.5';
        marker.getElement().innerHTML = `<svg width="28" height="28" viewBox="0 0 24 24" fill="${fill}" stroke="white" stroke-width="2" opacity="${opacity}"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" fill="white" font-size="9" font-weight="bold">Z</text></svg>`;
        const teamName = zone.team_name || (zone.team?.name) || '';
        marker.setPopup(new maplibregl.Popup({ offset: 25, closeButton: false }).setHTML(`
            <div style="font-weight:600">${zone.name}</div>
            <div style="font-size:0.85rem;color:#666">${zone.radius_m} m radius</div>
            <div style="font-size:0.85rem;color:${isAssigned(zone.id) ? '#2563eb' : '#9ca3af'}">${isAssigned(zone.id) ? 'Assigned' : 'Click to assign'}</div>
        `));
    }

    function renderZones() {
        state.markers.forEach(m => m.remove());
        state.markers = [];

        state.zones.forEach(zone => {
            const lng = parseFloat(zone.center_longitude);
            const lat = parseFloat(zone.center_latitude);
            if (isNaN(lng) || isNaN(lat)) return;

            const el = document.createElement('div');
            el.className = 'zone-marker';
            el.style.cursor = 'pointer';
            el.style.width = '28px';
            el.style.height = '28px';

            const marker = new maplibregl.Marker({ element: el })
                .setLngLat([lng, lat])
                .addTo(map);

            updateMarkerStyle(marker, zone);

            if (clickable) {
                el.addEventListener('click', () => {
                    if (assignEndpoint) {
                        const currentlyAssigned = isAssigned(zone.id);
                        const newAssigned = !currentlyAssigned;
                        setAssigned(zone.id, newAssigned);
                        updateMarkerStyle(marker, zone);
                        fetch(assignEndpoint, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '', 'Content-Type': 'application/json' },
                            body: JSON.stringify({ zone_id: zone.id, assigned: newAssigned }),
                        }).then(r => r.json()).then(data => {
                            if (data.assigned !== undefined) {
                                setAssigned(zone.id, data.assigned);
                                updateMarkerStyle(marker, zone);
                            }
                            if (onAssign) onAssign(data);
                        }).catch(() => {
                            setAssigned(zone.id, currentlyAssigned);
                            updateMarkerStyle(marker, zone);
                        });
                    }
                    if (onZoneSelect) onZoneSelect(zone);
                    map.flyTo({ center: [lng, lat], zoom: 13, duration: 600 });
                });
            }

            state.markers.push(marker);
        });
    }

    map.on('load', loadZones);
    return map;
}

export function initZoneEditor(containerId, options = {}) {
    const {
        styleUrl = 'https://tiles.openfreemap.org/styles/liberty',
        center = [121.0402, 14.5432],
        zoom = 11,
        initialLat = null,
        initialLng = null,
        initialRadius = null,
        latInputId = null,
        lngInputId = null,
        radiusInputId = null,
        zones = null,
    } = options;

    const map = new maplibregl.Map({
        container: containerId,
        style: styleUrl,
        center,
        zoom,
        attributionControl: false,
    });

    map.addControl(new maplibregl.NavigationControl(), 'top-right');
    map.addControl(new maplibregl.AttributionControl({ compact: true }), 'bottom-right');

    const latInput = latInputId ? document.getElementById(latInputId) : null;
    const lngInput = lngInputId ? document.getElementById(lngInputId) : null;
    const radiusInput = radiusInputId ? document.getElementById(radiusInputId) : null;

    const state = {
        centerLat: initialLat ? parseFloat(initialLat) : null,
        centerLng: initialLng ? parseFloat(initialLng) : null,
        radiusM: initialRadius ? parseFloat(initialRadius) : 500,
        marker: null,
        circleSource: 'zone-circle',
    };

    function updateInputs() {
        if (latInput && state.centerLat !== null) latInput.value = state.centerLat.toFixed(7);
        if (lngInput && state.centerLng !== null) lngInput.value = state.centerLng.toFixed(7);
        if (radiusInput) radiusInput.value = Math.round(state.radiusM);
    }

    function renderCircle() {
        if (state.centerLat === null || state.centerLng === null) return;

        const existing = map.getSource(state.circleSource);
        if (existing) {
            map.removeLayer('circle-fill');
            map.removeLayer('circle-outline');
            map.removeSource(state.circleSource);
        }

        const centerPoint = [state.centerLng, state.centerLat];
        const radiusDeg = state.radiusM / 111320;

        const points = 64;
        const coords = [];
        for (let i = 0; i <= points; i++) {
            const angle = (i / points) * 2 * Math.PI;
            const dx = radiusDeg * Math.cos(angle) / Math.cos(state.centerLat * Math.PI / 180);
            const dy = radiusDeg * Math.sin(angle);
            coords.push([state.centerLng + dx, state.centerLat + dy]);
        }

        map.addSource(state.circleSource, {
            type: 'geojson',
            data: {
                type: 'Feature',
                geometry: { type: 'Polygon', coordinates: [coords] },
            },
        });

        map.addLayer({
            id: 'circle-fill',
            type: 'fill',
            source: state.circleSource,
            paint: { 'fill-color': 'rgba(37, 99, 235, 0.1)' },
        });

        map.addLayer({
            id: 'circle-outline',
            type: 'line',
            source: state.circleSource,
            paint: { 'line-color': '#2563eb', 'line-width': 2, 'line-dasharray': [4, 2] },
        });

        if (state.marker) state.marker.remove();
        const el = document.createElement('div');
        el.innerHTML = '<svg width="32" height="32" viewBox="0 0 24 24" fill="#dc2626" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" fill="white" font-size="10" font-weight="bold">C</text></svg>';
        el.style.width = '32px';
        el.style.height = '32px';
        state.marker = new maplibregl.Marker({ element: el })
            .setLngLat([state.centerLng, state.centerLat])
            .addTo(map);
    }

    map.on('load', () => {
        if (state.centerLat !== null && state.centerLng !== null) {
            renderCircle();
            map.flyTo({ center: [state.centerLng, state.centerLat], zoom: 13, duration: 500 });
        }

        if (zones) {
            zones.forEach(zone => {
                const lng = parseFloat(zone.center_longitude);
                const lat = parseFloat(zone.center_latitude);
                if (isNaN(lng) || isNaN(lat)) return;
                if (state.centerLat !== null && Math.abs(lat - state.centerLat) < 0.0001 && Math.abs(lng - state.centerLng) < 0.0001) return;

                const el = document.createElement('div');
                el.innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="#6b7280" stroke="white" stroke-width="2" opacity="0.6"><circle cx="12" cy="12" r="10"/><text x="12" y="16" text-anchor="middle" fill="white" font-size="8" font-weight="bold">Z</text></svg>';
                el.style.width = '24px';
                el.style.height = '24px';

                const popup = new maplibregl.Popup({ offset: 25, closeButton: false }).setHTML(`
                    <div style="font-weight:600">${zone.name}</div>
                    <div style="font-size:0.85rem;color:#666">${zone.radius_m} m radius</div>
                `);

                new maplibregl.Marker({ element: el })
                    .setLngLat([lng, lat])
                    .setPopup(popup)
                    .addTo(map);
            });
        }
    });

    map.on('click', (e) => {
        state.centerLat = e.lngLat.lat;
        state.centerLng = e.lngLat.lng;
        updateInputs();
        renderCircle();
    });

    if (radiusInput) {
        radiusInput.addEventListener('input', () => {
            const val = parseFloat(radiusInput.value);
            if (!isNaN(val) && val > 0) {
                state.radiusM = val;
                if (state.centerLat !== null && state.centerLng !== null) renderCircle();
            }
        });
    }

    updateInputs();
    return map;
}

window.__zonePicker = { initTeamZonePicker, initZoneEditor };

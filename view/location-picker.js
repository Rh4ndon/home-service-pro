/* HomeServicePro reusable Google Maps location picker */
(function () {
  var DEFAULT_CENTER = { lat: 14.5995, lng: 120.9842 }; // Manila
  var keyPromise = null;
  var mapsReadyPromise = null;
  var pickers = [];

  function getKey() {
    if (!keyPromise) {
      keyPromise = fetch('../../controllers/maps-config.php')
        .then(function (r) { return r.json(); })
        .then(function (d) {
          if (!d.key) throw new Error('Maps API key missing');
          return d.key;
        });
    }
    return keyPromise;
  }

  function loadMaps() {
    if (mapsReadyPromise) return mapsReadyPromise;
    mapsReadyPromise = new Promise(function (resolve, reject) {
      getKey().then(function (key) {
        window.__hsMapsReady = function () { resolve(window.google); };
        var s = document.createElement('script');
        s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(key) +
          '&libraries=places&callback=__hsMapsReady';
        s.async = true;
        s.defer = true;
        s.onerror = function () { reject(new Error('Failed to load Google Maps SDK')); };
        document.head.appendChild(s);
      }).catch(reject);
    });
    return mapsReadyPromise;
  }

  function showPickerError(opts, msg) {
    var mapEl = document.getElementById(opts.mapEl);
    if (mapEl) {
      mapEl.innerHTML = '<div style="height:100%;display:flex;align-items:center;justify-content:center;color:#ef4444;font-size:0.9rem;background:#fff5f5;">' +
        (msg || 'Unable to load the map. Please check your connection and API key.') +
        '</div>';
    }
    if (opts.onError) opts.onError(msg);
  }

  function reverseGeocode(geocoder, lat, lng, callback) {
    var request = { location: { lat: lat, lng: lng } };
    geocoder.geocode(request, function (results, status) {
      if (status === google.maps.GeocoderStatus.OK && results && results[0]) {
        callback(results[0].formatted_address);
      } else {
        callback(null);
      }
    });
  }

  function commitPosition(opts, google, map, marker, geocoder, lat, lng) {
    if (typeof lat !== 'number' || typeof lng !== 'number') return;
    lat = Number(lat.toFixed(7));
    lng = Number(lng.toFixed(7));
    var pos = { lat: lat, lng: lng };
    map.setCenter(pos);
    marker.setPosition(pos);
    setInput(opts.latInput, lat);
    setInput(opts.lngInput, lng);
    setInput(opts.addressInput, '');
    if (opts.onLocating) opts.onLocating(true);
    reverseGeocode(geocoder, lat, lng, function (address) {
      if (address) setInput(opts.addressInput, address);
      if (opts.onSelect) opts.onSelect({ lat: lat, lng: lng, address: address || readInput(opts.addressInput) });
      if (opts.onLocating) opts.onLocating(false);
    });
  }

  function readInput(id) {
    var el = document.getElementById(id);
    return el ? String(el.value || '').trim() : '';
  }

  function setInput(id, value) {
    var el = document.getElementById(id);
    if (el) {
      el.value = value === null || value === undefined ? '' : value;
      el.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  function createPicker(google, opts) {
    var mapEl = document.getElementById(opts.mapEl);
    if (!mapEl) {
      if (opts.onError) opts.onError('Map element "' + opts.mapEl + '" not found.');
      return;
    }
    // Ensure map element has actual dimensions before creating map.
    // If either dimension is zero, wait one paint cycle (CSS may still be computing).
    if (mapEl.offsetWidth === 0 || mapEl.offsetHeight === 0) {
      var rafTries = 0;
      function tryCreate() {
        if (mapEl.offsetWidth > 0 && mapEl.offsetHeight > 0) {
          createPicker(google, opts);
          return;
        }
        if (++rafTries < 10) {
          requestAnimationFrame(tryCreate);
          return;
        }
        // Fall back to MutationObserver for style/class changes (modals/tabs)
        var observer = new MutationObserver(function() {
          if (mapEl.offsetWidth > 0 && mapEl.offsetHeight > 0) {
            observer.disconnect();
            createPicker(google, opts);
          }
        });
        observer.observe(mapEl, { attributes: true, attributeFilter: ['style', 'class'] });
        var parent = mapEl.parentElement;
        while (parent && parent !== document.body) {
          if (window.getComputedStyle(parent).display === 'none' || window.getComputedStyle(parent).visibility === 'hidden') {
            var pObserver = new MutationObserver(function() {
              if (mapEl.offsetWidth > 0 && mapEl.offsetHeight > 0) {
                pObserver.disconnect();
                createPicker(google, opts);
              }
            });
            pObserver.observe(parent, { attributes: true, attributeFilter: ['style', 'class'] });
            break;
          }
          parent = mapEl.parentElement;
        }
      }
      requestAnimationFrame(tryCreate);
      return;
    }
    
    var latInput = document.getElementById(opts.latInput);
    var lngInput = document.getElementById(opts.lngInput);
    var addressInput = document.getElementById(opts.addressInput);
    var searchInput = document.getElementById(opts.searchInput);
    var deviceBtn = document.getElementById(opts.deviceBtn);

    var initialLat = parseFloat(readInput(opts.latInput)) || opts.initialLat;
    var initialLng = parseFloat(readInput(opts.lngInput)) || opts.initialLng;
    var center = (initialLat && initialLng) ? { lat: initialLat, lng: initialLng } : DEFAULT_CENTER;

    var map = new google.maps.Map(mapEl, {
      center: center,
      zoom: initialLat ? 15 : 12,
      mapTypeControl: false,
      fullscreenControl: true,
      streetViewControl: false
    });

    var marker = new google.maps.Marker({
      map: map,
      position: center,
      draggable: true,
      title: 'Your location'
    });
    if (!initialLat && !initialLng) {
      // If no location yet, drop marker on the Manila default
      marker.setPosition(center);
    }

    var geocoder = new google.maps.Geocoder();

    if (initialLat && initialLng) {
      setInput(opts.latInput, initialLat);
      setInput(opts.lngInput, initialLng);
    }

    // Click / drag to pin
    map.addListener('click', function (e) {
      commitPosition(opts, google, map, marker, geocoder, e.latLng.lat(), e.latLng.lng());
    });
    marker.addListener('dragend', function () {
      var p = marker.getPosition();
      commitPosition(opts, google, map, marker, geocoder, p.lat(), p.lng());
    });

    // Places autocomplete search
    if (searchInput && geometryAvailable(google)) {
      var autocomplete = new google.maps.places.Autocomplete(searchInput, {
        types: ['geocode'],
        componentRestrictions: { country: 'ph' }
      });
      autocomplete.bindTo('bounds', map);
      autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        if (!place || !place.geometry) return;
        if (place.geometry.viewport) map.fitBounds(place.geometry.viewport);
        map.setCenter(place.geometry.location);
        if (place.geometry.location) {
          commitPosition(opts, google, map, marker, geocoder, place.geometry.location.lat(), place.geometry.location.lng());
          if (place.formatted_address) setInput(opts.addressInput, place.formatted_address);
          if (opts.onSelect) opts.onSelect({
            lat: parseFloat(readInput(opts.latInput)) || place.geometry.location.lat(),
            lng: parseFloat(readInput(opts.lngInput)) || place.geometry.location.lng(),
            address: place.formatted_address || readInput(opts.addressInput)
          });
        }
      });
    }

    // Device location
    if (deviceBtn) {
      deviceBtn.addEventListener('click', function () {
        if (deviceBtn.disabled) return;
        deviceBtn.disabled = true;
        if (opts.onLocating) opts.onLocating(true);
        if (!navigator.geolocation) {
          showPickerError(opts, 'Geolocation is not supported by this browser.');
          deviceBtn.disabled = false;
          if (opts.onLocating) opts.onLocating(false);
          return;
        }
        navigator.geolocation.getCurrentPosition(
          function (pos) {
            deviceBtn.disabled = false;
            var lat = pos.coords.latitude;
            var lng = pos.coords.longitude;
            var accuracy = pos.coords.accuracy; // meters
            commitPosition(opts, google, map, marker, geocoder, lat, lng);
            // Show accuracy circle
            if (accuracy && map) {
              var circle = new google.maps.Circle({
                map: map,
                center: { lat: lat, lng: lng },
                radius: accuracy,
                fillColor: '#3b82f6',
                fillOpacity: 0.1,
                strokeColor: '#3b82f6',
                strokeOpacity: 0.5,
                strokeWeight: 1,
                clickable: false
              });
              // Fit map to circle bounds
              map.fitBounds(circle.getBounds());
              // Remove circle after 10 seconds
              setTimeout(function() { circle.setMap(null); }, 10000);
            }
            if (opts.onLocating) opts.onLocating(false);
          },
          function () {
            deviceBtn.disabled = false;
            if (opts.onLocating) opts.onLocating(false);
            showPickerError(opts, 'Unable to fetch your device location. Please allow location access or pin your address on the map.');
          },
          { enableHighAccuracy: true, timeout: 15000, maximumAge: 60000 }
        );
      });
    }

    if (typeof opts.onReady === 'function') opts.onReady(map, google);
  }

  function geometryAvailable(google) {
    return !!(google && google.maps && google.maps.places && google.maps.places.Autocomplete);
  }

  function initLocationPicker(opts) {
    opts = opts || {};
    if (!opts.mapEl || !opts.latInput || !opts.lngInput) {
      if (opts.onError) opts.onError('Location picker requires mapEl, latInput and lngInput.');
      return;
    }
    pickers.push(opts);
    loadMaps().then(function (google) {
      var idx = pickers.indexOf(opts);
      if (idx !== -1) {
        createPicker(google, opts);
        pickers.splice(idx, 1);
      }
    }).catch(function (err) {
      var idx = pickers.indexOf(opts);
      if (idx !== -1) pickers.splice(idx, 1);
      showPickerError(opts, (err && err.message) || 'Unable to load the map.');
    });
  }

  window.initLocationPicker = initLocationPicker;
  window.HSLocationPicker = { init: initLocationPicker, getKey: getKey };
})();
/**
 * @file
 * A JavaScript file for the theme.
 *
 * In order for this JavaScript to be loaded on pages, see the instructions in
 * the README.txt next to this file.
 */

// JavaScript should be made compatible with libraries other than jQuery by
// wrapping it with an "anonymous closure". See:
// - http://drupal.org/node/1446420
// - http://www.adequatelygood.com/2010/3/JavaScript-Module-Pattern-In-Depth
(function ($, Drupal, window, document, undefined) {

    var leaflet_map;
    var leaflet_features = [];
    var leaflet_layer_groups = {};
    var found_location_element = {};
    var current_zoom = 0;

    $(document).bind('leaflet.map', function (e, map, lMap) {
        leaflet_map = lMap;
        current_zoom = leaflet_map._zoom;
    });

    $(document).bind('leaflet.feature', function (e, lFeature, feature) {
        leaflet_features.push(lFeature);
    });

    $(document).bind('leaflet.layer_group', function (e, lGroup, feature) {
        leaflet_layer_groups[feature.group] = lGroup;
    });

    Drupal.behaviors.map = {
        attach: function (context, settings) {

            if (settings.map.override_zoom) {
                if (sessionStorage.getItem('map_zoom')) {
                    leaflet_map.setZoom(sessionStorage.getItem('map_zoom'));
                }
            }

            leaflet_features.forEach(function (element, index, array) {
                if (Drupal.opentourny.isMobile.any()) {
                    if (element.hasOwnProperty('_popup')) {
                        if (Drupal.opentourny.strpos(element._popup._content, 'node-' + settings.map.nid + '-popup') !== false) {

                            if (leaflet_layer_groups.hasOwnProperty('buses')) {
                                if (settings.map.type == 'bus_stop') {
                                    if (!Drupal.map.inBounds(leaflet_layer_groups['buses'], leaflet_map)) {
                                        element.setPopupContent(element._popup._content + settings.map.zoom_message);
                                    }
                                }
                            }

                            element.openPopup();
                        }
                    }
                }
            });

            $('.leaflet-control-layers-overlays').after(settings.map.control_links);

            $('.leaflet-bottom.leaflet-left').html('<div class="map-help-link leaflet-control"><a href="' + settings.map.help_link + '"><span class="glyphicon glyphicon-question-sign"></span></a></div>');

            $search_form = $('#route-point-search-form').detach();
            $('.leaflet-top.leaflet-right').html($search_form);

            $('#route-point-search-form').submit(function (e) {
                return false;
            })

            $('.leaflet-control-layers-toggle').html('<span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>');

            if (settings.map.start_filtered) {
                leaflet_map.removeLayer(leaflet_layer_groups['poi']);
                if (settings.map.type == 'route') {
                    for (var index in leaflet_layer_groups) {
                        if (leaflet_layer_groups.hasOwnProperty(index)) {
                            if (Drupal.opentourny.strpos(index, 'route', 0) !== false) {
                                if (index != settings.map.current_route) {
                                    leaflet_map.removeLayer(leaflet_layer_groups[index]);
                                }
                            }
                        }
                    }
                }
            }

            var i = 1;
            var blank_index = 0;
            $('.leaflet-control-layers-overlays label span').each(function () {
                if ($(this).html().trim() == '') {
                    blank_index = i;
                }
                i++;
            });

            if (blank_index != 0) {
                $('.leaflet-control-layers-overlays label:nth-child(' + blank_index + ')').remove();
            }

            var num_labels = $('.leaflet-control-layers-overlays label').length;
            var i = 1;
            $('.leaflet-control-layers-overlays label').each(function () {
                if (i + 3 == num_labels) {
                    $(this).after('<div class="leaflet-control-layers-separator" style="display:block;"></div>');
                }
                i++;
            });

            leaflet_map.locate();
            leaflet_map.on('locationfound', function (e) {
                var location_icon = L.icon({
                    iconUrl: settings.map.location_icon,
                    iconSize: [25, 25]
                });
                L.marker(e.latlng, {icon: location_icon}).addTo(leaflet_map);

                if (settings.map.location_search && 'bus_stop' in leaflet_layer_groups) {
                    var closest_bus_stop = leafletKnn(L.geoJson(leaflet_layer_groups['bus_stop'].toGeoJSON())).nearest(e.latlng, 1, settings.map.search_distance);
                    if (closest_bus_stop.length > 0) {
                        var found = false;
                        leaflet_layer_groups['bus_stop'].getLayers().forEach(function (element, index, array) {
                            if (!found) {
                                if (element._latlng.equals(closest_bus_stop[0].layer._latlng)) {
                                    element._popup._content = '<p class="closest-stop">' + settings.map.closest_stop_string + ':</p>' + element._popup._content;
                                    found_location_element = element;
                                    leaflet_map.setView(element._latlng, 15);
                                    found = true;
                                }
                            }
                        });
                    }
                }
            });

            sessionStorage.setItem('map_zoom', current_zoom);
            leaflet_map.on('zoomend', function (e) {
                if (current_zoom < 14 && e.target._zoom >= 14) {
                    if ('poi' in leaflet_layer_groups) {
                        Drupal.map.increase_marker_size(leaflet_layer_groups['poi']);
                    }
                    if ('bus_stop' in leaflet_layer_groups) {
                        Drupal.map.increase_marker_size(leaflet_layer_groups['bus_stop']);
                    }
                    if ('buses' in leaflet_layer_groups) {
                        Drupal.map.increase_marker_size(leaflet_layer_groups['buses']);
                    }
                }
                else if (current_zoom >= 14 && e.target._zoom < 14) {
                    if ('poi' in leaflet_layer_groups) {
                        Drupal.map.decrease_marker_size(leaflet_layer_groups['poi']);
                    }
                    if ('bus_stop' in leaflet_layer_groups) {
                        Drupal.map.decrease_marker_size(leaflet_layer_groups['bus_stop']);
                    }
                    if ('buses' in leaflet_layer_groups) {
                        Drupal.map.decrease_marker_size(leaflet_layer_groups['buses']);
                    }
                }
                current_zoom = e.target._zoom;
                sessionStorage.setItem('map_zoom', current_zoom);

                if (found_location_element.hasOwnProperty('_latlng')) {
                    found_location_element.openPopup();
                    found_location_element = {};
                }
            });

            Drupal.map.set_map_height(leaflet_map);
            $(window).resize(function () {
                $('.field-name-field-geojson').css('width', $('.leaflet-map-container').width());
                Drupal.map.set_map_height(leaflet_map);
            });

            $('.field-name-field-geojson').css('width', $('.leaflet-map-container').width());

            if (!Drupal.opentourny.isMobile.any() && $('.main-content').height() - $('.tabs--primary').height() > $('.field-name-field-geojson').height()) {
                $('.field-name-field-geojson').affix({
                    offset: {
                        top: function () {
                            return (this.top = $('.page-header-wrapper').outerHeight() + parseInt($('aside.leaflet-map-container').css('padding-top'), 10));
                        },
                        bottom: function () {
                            return (this.bottom = $('.footer-wrapper').outerHeight(true) + 50);
                        }
                    }
                });
            }

            if (leaflet_layer_groups.hasOwnProperty('buses')) {
                setInterval(function () {
                    Drupal.map.update_bus_locations(leaflet_layer_groups['buses'], leaflet_map);
                }, 1000 * 10);
            }
        }
    };

    Drupal.map = {
        set_map_height: function (leaflet_map) {
            var grid = Drupal.opentourny.get_current_bootstrap_grid();
            if (grid == 'xs') {
                $("#leaflet-map").height($(window).height() - $('.navbar-wrapper').outerHeight(true));
            }
            else if (grid == 'sm' || (grid == 'md' && $('html').hasClass('touch'))) {
                $("#leaflet-map").height($(window).height() - $('.navbar-wrapper').outerHeight(true) - $('.page-header-wrapper').outerHeight(true));
            }
            leaflet_map.invalidateSize();
        },

        increase_marker_size: function (leaflet_layer_group) {
            leaflet_layer_group.eachLayer(function (layer) {
                var icon = layer.options.icon;
                if (Drupal.opentourny.strpos(icon.options.iconUrl, 'spacer.gif') === false) {
                    if (icon.options.hasOwnProperty('iconAnchor')) {
                        icon.options.iconAnchor = new L.Point(icon.options.iconAnchor.x * 2, icon.options.iconAnchor.y * 2);
                    }
                    if (icon.options.hasOwnProperty('iconSize')) {
                        icon.options.iconSize = new L.Point(icon.options.iconSize.x * 2, icon.options.iconSize.y * 2);
                    }
                    if (icon.options.hasOwnProperty('popupAnchor')) {
                        icon.options.popupAnchor = new L.Point(icon.options.popupAnchor.x * 2, icon.options.popupAnchor.y * 2);
                    }
                    layer.setIcon(icon);
                }
            });
        },

        decrease_marker_size: function (leaflet_layer_group) {
            leaflet_layer_group.eachLayer(function (layer) {
                var icon = layer.options.icon;
                if (Drupal.opentourny.strpos(icon.options.iconUrl, 'spacer.gif') === false) {
                    if (icon.options.hasOwnProperty('iconAnchor')) {
                        icon.options.iconAnchor = new L.Point(icon.options.iconAnchor.x / 2, icon.options.iconAnchor.y / 2);
                    }
                    if (icon.options.hasOwnProperty('iconSize')) {
                        icon.options.iconSize = new L.Point(icon.options.iconSize.x / 2, icon.options.iconSize.y / 2);
                    }
                    if (icon.options.hasOwnProperty('popupAnchor')) {
                        icon.options.popupAnchor = new L.Point(icon.options.popupAnchor.x / 2, icon.options.popupAnchor.y / 2);
                    }
                    layer.setIcon(icon);
                }
            });
        },

        update_bus_locations: function (leaflet_layer_group, leaflet_map) {
            $.getJSON('/ajax/bus_locations').done(function (data) {
                leaflet_layer_group.clearLayers();
                data.forEach(function (element, index, array) {
                    leaflet_layer_group.addLayer(Drupal.leaflet.create_point(element, leaflet_map));
                });
                if (leaflet_map._zoom < 14) {
                    Drupal.map.decrease_marker_size(leaflet_layer_group);
                }
            });
        },

        inBounds: function (leaflet_layer_group, leaflet_map) {
            var bounds = leaflet_map.getBounds();
            var found = false;
            leaflet_layer_group.eachLayer(function (layer) {
                if (!found && bounds.contains(layer._latlng)) {
                    found = true;
                }
            });

            return found;
        }
    }

    Drupal.jsAC.prototype.select = function (node) {
        window.location.href = $('a a', node).attr('href');
    };


})(jQuery, Drupal, this, this.document);
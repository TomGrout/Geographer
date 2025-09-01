function loadMap() {
    if (window.currentChart) {
        window.currentChart.destroy();
    }
    mapboxgl.accessToken = 'pk.eyJ1IjoidGdyb3V0IiwiYSI6ImNtM3FkdGtqazBjcXYyanNkN2cyc3FibjAifQ.JNhp9xT8u8kLVUVIGmO2VA';
    const map = new mapboxgl.Map({
        container: 'map',

        style: 'mapbox://styles/mapbox/streets-v12',
        center: [30.0222, -1.9596],
        zoom: 7 
    });

    const loadMap = async () => {
        const populationData = await fetchPopulationData();
        const maxPopulation = Math.max(...Object.values(populationData));

        map.on('load', () => {
            map.addSource('countries', {
                'type': 'geojson',
                'data': 'https://raw.githubusercontent.com/datasets/geo-countries/master/data/countries.geojson',
            });

            map.addLayer({
                'id': 'countries-fill',
                'type': 'fill',
                'source': 'countries',
                'paint': {
                    'fill-color': [
                        'match',
                        ['get', 'ADMIN'],
                        ...countriesOfInterest.flatMap(country => [
                            country,
                            calculatePurpleShade(populationData[country] || 0, maxPopulation)
                        ]),
                        '#cccccc' 
                    ],
                    'fill-opacity': 0.7,
                },
            });

            map.addLayer({
                'id': 'countries-outline',
                'type': 'line',
                'source': 'countries',
                'paint': {
                    'line-color': '#000',
                    'line-width': 1,
                },
            });
        });
    };
}

<?php require "navbar.php"?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
<link href="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.css" rel="stylesheet">
<script src="https://api.mapbox.com/mapbox-gl-js/v3.8.0/mapbox-gl.js"></script>
<script id="search-js" defer src="https://api.mapbox.com/search-js/v1.0.0-beta.21/web.js"></script>

<style>
body { margin: 0; padding: 0; }
#map { position: absolute; top: 20%; bottom: 0; width: 100%; }
</style>
</head>
<body style="background-color: black">

    <form id="searchForm" style="padding-top:0.5%">
        <label for="country" style="color:grey">Enter Country:</label>
        <input type="text" id="country" name="country" required>
        <button type="submit" class="btn btn-info" style="height: 85%">Search</button>
    </form>

    <div id="result" class="result" style="color: white"></div>
    <script>
    document.getElementById('searchForm').addEventListener('submit', async (event) => {
            event.preventDefault();

            const countryName = document.getElementById('country').value.trim();
            const resultDiv = document.getElementById('result');

            if (!countryName) {
                resultDiv.innerHTML = '<p class="error">Please enter a country name.</p>';
                return;
            }

            // Fetch country data to match the country name
            fetch('https://countriesnow.space/api/v0.1/countries')
                .then(response => response.json())
                .then(countriesData => {
                    if (countriesData.error) {
                        resultDiv.innerHTML = '<p class="error">Error fetching country data. Please try again later.</p>';
                        return;
                    }

                    const countries = countriesData.data;
                    const matchedCountry = countries.find(country => 
                        country.country.toLowerCase() === countryName.toLowerCase()
                    );

                    if (!matchedCountry) {
                        resultDiv.innerHTML = '<p class="error">Country not found. Please check the spelling and try again.</p>';
                        return;
                    }

                    // Fetch population data for the matched country
                    fetch('https://countriesnow.space/api/v0.1/countries/population')
                        .then(response => response.json())
                        .then(populationData => {
                            if (populationData.error) {
                                resultDiv.innerHTML = '<p class="error">Error fetching population data. Please try again later.</p>';
                                return;
                            }

                            const populationInfo = populationData.data.find(country => 
                                country.country.toLowerCase() === countryName.toLowerCase()
                            );

                            if (!populationInfo || !populationInfo.populationCounts.length) {
                                resultDiv.innerHTML = `<p class="error">Population data for ${countryName} is unavailable.</p>`;
                                return;
                            }

                            // Get the most recent population data
                            const latestPopulation = populationInfo.populationCounts.at(-1);
                            resultDiv.innerHTML = `
                                <p>The population of <strong>${matchedCountry.country}</strong> 
                                (${matchedCountry.iso3}) is <strong>${latestPopulation.value.toLocaleString()}</strong> 
                                as of ${latestPopulation.year}.</p>
                            `;
                        })
                        .catch(error => {
                            console.error('Error fetching population data:', error);
                            resultDiv.innerHTML = '<p class="error">An error occurred while fetching population data.</p>';
                        });
                })
                .catch(error => {
                    console.error('Error fetching country data:', error);
                    resultDiv.innerHTML = '<p class="error">An error occurred while fetching country data.</p>';
                });
        });
       
    </script>


<div id="map" style="width: 100%; height: 92%; display: flex"></div>
<script>
document.getElementById('searchForm').addEventListener('submit', async (event) => {
    event.preventDefault(); // Prevent form submission

    const countryName = document.getElementById('country').value.trim(); // Get user input
    if (!countryName) {
        alert('Please enter a valid country name.');
        return;
    }

	mapboxgl.accessToken = 'pk.eyJ1IjoidGdyb3V0IiwiYSI6ImNtM3FkdGtqazBjcXYyanNkN2cyc3FibjAifQ.JNhp9xT8u8kLVUVIGmO2VA';      //free api no card details

    const map = new mapboxgl.Map({
            container: 'map',
            style: 'mapbox://styles/mapbox/streets-v12',
            center: [10, 50],
            zoom: 4,
        }); // map starts germanyish

            const countriesOfInterest = [countryName];


        const fetchPopulationData = async () => {
            try {
                const response = await fetch('https://countriesnow.space/api/v0.1/countries/population');
                const data = await response.json();
                const countryPopulations = {};

                countriesOfInterest.forEach(country => {
                    const countryData = data.data.find(c => c.country === country);
                    if (countryData) {
                        const latestPopulation = countryData.populationCounts[countryData.populationCounts.length - 1];
                        countryPopulations[country] = latestPopulation?.value || 0;
                    }
                });

                return countryPopulations;
            } catch (error) {
                console.error('Error fetching population data:', error);
                return {};
            }
        };

        const calculatePurpleShade = (population, maxPopulation) => {       // !fix
            const intensity = population / maxPopulation;
            const alpha = Math.min(Math.max(intensity, 0.5), 1); 
            return `rgba(128, 0, 128, ${alpha})`;
        };

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
                                calculatePurpleShade(populationData[country] || 1 , maxPopulation)
                            ]),
                            '#cccccc' //GREY
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

        loadMap();
    });
</script>

</body>
</html>
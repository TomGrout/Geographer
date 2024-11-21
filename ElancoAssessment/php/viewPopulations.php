<?php 
require "navbar.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
 
</head>
<body>

<script>
let graphType = 0;
updateHeader(graphType);
const pageTitle = document.getElementById('pageTitle'); // title

function showYears() {
  var x = document.getElementById("yearButtons");
    x.style.display = "flex";
}

function hideYears() {
  var x = document.getElementById("yearButtons");
    x.style.display = "none";
}

function updateHeader(graphType) {
    const pageTitle = document.getElementById('pageTitle');
    if (graphType === 0) {
        pageTitle.textContent = "Dashboard";

    } else if (graphType === 1) {
        pageTitle.textContent = "Population Data by Country";
        showYears();
        filterByYear(2018, 1);

    } else if (graphType === 2) {
        pageTitle.textContent = "Population Data by Country";
        showYears();
        filterByYear(2018, 2);

    } else if (graphType === 3) {
        pageTitle.textContent = "Population Data by Country";
        showYears();
        filterByYear(2018, 3);

    } else if (graphType === 4) {
        pageTitle.textContent = "Population Data by Region";
        hideYears();
        filterByYearRegional(2018, 1);

    } else {
        pageTitle.textContent = "Dashboard"; // if nothing clicked text 
        hideYears();
        console.log(graphType);
    }
}

</script>
<div style="padding-left: 15%; padding-top: 2%">
    <h3 id="pageTitle">Dashboard</h3>
</div>

<div id="yearButtons" style="display:flex; gap: 1px; justify-content: center; margin-bottom: 20px;">
    <button type="button" class="btn btn-secondary" onclick="filterByYear(1960, graphType)">1960</button>
    <button type="button" class="btn btn-secondary" onclick="filterByYear(1970, graphType)">1970</button>
    <button type="button" class="btn btn-secondary" onclick="filterByYear(1980, graphType)">1980</button>
    <button type="button" class="btn btn-secondary" onclick="filterByYear(1990, graphType)">1990</button>
    <button type="button" class="btn btn-secondary" onclick="filterByYear(2000, graphType)">2000</button>
    <button type="button" class="btn btn-secondary" onclick="filterByYear(2018, graphType)">2018</button>
</div>

<div class="row align-items-start">

    <div class="col-1" style="padding-left: 2%; padding-top: 2%">  

        <div class="dropdown">
            <div class="dropdown-content">
            <form method="post">

                <input type="button" class="btn btn-info" name="bar" value="Bar" style="background-color: #207da4" id="bar"></input>
                <input type="button" class="btn btn-info" name="line" value="Line" style="background-color: #207da4" id="line"></input>
                <input type="button" class="btn btn-info" name="pie" value="Pie" style="background-color: #207da4" id="pie"></input>
                <input type="button" class="btn btn-info" name="table" value="Regions" style="background-color: #207da4" id="tbl"></input>

            </form>
            <script>
                const barBtn = document.getElementById('bar');
                barBtn.addEventListener('click', () => {
                    event.preventDefault();
                    updateHeader(1); // set graphType to ... bar
                    graphType = 1;
                });
                const lineBtn = document.getElementById('line');
                lineBtn.addEventListener('click', () => {
                    event.preventDefault();
                    updateHeader(2); //line
                    graphType = 2;
                });
                const pieBtn = document.getElementById('pie');
                pieBtn.addEventListener('click', () => {
                    event.preventDefault();
                    updateHeader(3); // pie
                    graphType = 3;
                });
                const tblBtn = document.getElementById('tbl');
                tblBtn.addEventListener('click', () => {
                    event.preventDefault();
                    updateHeader(4); // pie
                });
            </script>
            </div>
            

        </div>
    </div>
    <div class="col-10">

    <div id="chartContainer">
        <canvas id="populationChart"></canvas>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    window.currentChart = null;

    async function filterByYear(year, graphType) {
        const ctx = document.getElementById('populationChart').getContext('2d');

        // Destroy previous chart
        if (window.currentChart) {
            window.currentChart.destroy();
        }

        try {
            // Fetch specific countries and population data
            const [countriesResponse, populationResponse] = await Promise.all([
                fetch('https://countriesnow.space/api/v0.1/countries', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                }),
                fetch('https://countriesnow.space/api/v0.1/countries/population', {
                    method: 'GET',
                    headers: { 'Content-Type': 'application/json' },
                }),
            ]);

            const countriesData = await countriesResponse.json();
            const populationData = await populationResponse.json();

            if (!countriesData.data || !populationData.data) {
                console.warn('No data available.');
                alert('No data available for selected countries or populations.');
                return;
            }

            // Select specific countries
            const selectedCountries = ['United States', 'United Kingdom', 'Italy', 'Spain', 'Germany', 'France', 'Japan', 'India', 'Nigeria' ]; //Nigeria
            const filteredCountries = countriesData.data.filter(country =>
                selectedCountries.includes(country.country)
            );

            // Match population data for the specified year
            const countryPopulations = filteredCountries.map(country => {
                const populationEntry = populationData.data.find(
                    popCountry => popCountry.country === country.country
                );
                const populationYear = populationEntry?.populationCounts.find(
                    entry => entry.year === year
                );
                return {
                    country: country.country,
                    population: populationYear ? populationYear.value : 0,
                };
            });

            // Prepare data for the chart
            const labels = countryPopulations.map(item => item.country);
            const data = countryPopulations.map(item => item.population);

            const colors = [
            'rgba(255, 99, 132, 0.6)',  // Red
            'rgba(54, 162, 235, 0.6)',  // Blue
            'rgba(75, 192, 192, 0.6)',  // Green
            'rgba(255, 206, 86, 0.6)',  // Yellow
            'rgba(153, 102, 255, 0.6)', // Purple
            'rgba(255, 159, 64, 0.6)',  // Orange
            'rgba(99, 255, 132, 0.6)',  // Light Green
            'rgba(235, 162, 54, 0.6)',  // Brown
        ];


            let chartConfig = {
                type: 'bar',
                data: {
                    labels: labels, // country names
                    datasets: [
                        {
                            label: `Population in ${year}`,
                            data: data, // pop values
                            backgroundColor: colors.slice(0, data.length),
                            borderColor: graphType === 2 ? 'rgba(0, 0, 0, 1)': 'rgba(256, 256, 256, 1)',
                            borderWidth: 1,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: true, position: 'top' },
                        tooltip: { mode: 'index', intersect: false },
                    },
                    scales: {
                        x: { title: { display: true, text: 'Countries' } },
                        y: {
                            title: { display: true, text: 'Population' },
                            beginAtZero: true,
                        },
                    },
                },
            };

            if (graphType === 1) {
                chartConfig.type = 'bar';
            } else if (graphType === 2) {
                chartConfig.type = 'line';
            } else if (graphType === 3) {
                chartConfig.type = 'pie';
            }

            // Render the chart
            window.currentChart = new Chart(ctx, chartConfig);
        } catch (error) {
            console.error('Error fetching data:', error);
            alert('An error occurred while fetching data. Please try again later.');
        }
    }
</script>

<script>
    window.currentChart = null;

    function filterByYearRegional(year, graphType) {
        const ctx = document.getElementById('populationChart').getContext('2d');

        // destroy prev chart
        if (window.currentChart) {
            window.currentChart.destroy();
        }

        // fetch countries population data
        fetch('https://countriesnow.space/api/v0.1/countries/population', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' },
    })
    .then(response => response.json())
    .then(data => {
        console.log('Full API response:', data); // print api data

        if (!data.data || data.data.length === 0) {
            console.warn('No data available.');
            alert('No data available for countries population.');
            return;
        }

        
        const countries = data.data || [];
        const countryNames = countries.map(country => country.country);
        const populations = countries.map(country => {

            const populationData = country.populationCounts.find(entry => entry.year === year);
            return populationData ? populationData.value : 0; // defualt 0
        });

        let chartConfig = {
            type: 'bar', 
            data: {
                labels: countryNames.slice(0, 20),
                datasets: [{
                    label: `Population in ${year}`,
                    data: populations.slice(0, 20), 
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                }],
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { mode: 'index', intersect: false },
                },
                scales: {
                    x: { title: { display: true, text: 'Countries' } },
                    y: { 
                        title: { display: true, text: 'Population' },
                        beginAtZero: true,
                    },
                },
            },
        };

        if (graphType === 1) {
            chartConfig.type = 'bar'; 
        } else if (graphType === 2) {
            chartConfig.type = 'line'; 
        } else if (graphType === 3) {
            chartConfig.type = 'pie'; 
        }
        window.currentChart = new Chart(ctx, chartConfig);
    })
        .catch(error => {
            console.error('Error fetching population data:', error);
            alert('An error occurred while fetching data. Please try again later.');
        });
}
//filter year
</script>
</div>
</body>
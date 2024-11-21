<?php require "navbar.php"?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
 
</head>

<body>
<div style="padding-left: 15%; padding-top: 2%">
    <h3 id="pageTitle">Countries</h3>


<div class="row align-items-start">
<div class="col-10">

<div id="table-container" style="align:center">
    <canvas id="populationChart"></canvas>
</div>
</div>

<script>
async function loadTable() {
    window.currentChart = null;
    const ctx = document.getElementById('populationChart').getContext('2d');


    const flagAPI = 'https://countriesnow.space/api/v0.1/countries/flag/images';
    const capitalAPI = 'https://countriesnow.space/api/v0.1/countries/capital';
    const tableContainer = document.getElementById('table-container');

    if (window.currentChart) {
        window.currentChart.destroy();
    }
    
    try {
        // Fetch data from both APIs
        const [flagResponse, capitalResponse] = await Promise.all([
            fetch(flagAPI),
            fetch(capitalAPI)
        ]);

        const flagData = await flagResponse.json();
        const capitalData = await capitalResponse.json();

        if (!flagData.data || !capitalData.data) {
            throw new Error('Error fetching data from APIs');
        }

        // Combine data based on country names
        const combinedData = capitalData.data.map(country => {
            const flagInfo = flagData.data.find(flag => flag.name === country.name);
            return {
                name: country.name,
                capital: country.capital,
                flag: flagInfo ? flagInfo.flag : null
            };
        });

        // Create the table
        let tableHTML = `
            <table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background-color: #f2f2f2;">
                        <th>Flag</th>
                        <th>Country</th>
                        <th>Capital</th>
                    </tr>
                </thead>
                <tbody>
        `;

        combinedData.forEach(country => {
            tableHTML += `
                <tr>
                    <td><img src="${country.flag}" alt="${country.name} Flag" style="width: 50px; height: auto;"></td>
                    <td>${country.name}</td>
                    <td>${country.capital || 'N/A'}</td>
                </tr>
            `;
        });

        tableHTML += `
                </tbody>
            </table>
        `;

        // Display the table
        tableContainer.innerHTML = tableHTML;
    } catch (error) {
        console.error('Error loading table:', error);
        tableContainer.innerHTML = '<p>Error loading table. Please try again later.</p>';
    }
}
loadTable();
</script>
</div>
</div>
</body>
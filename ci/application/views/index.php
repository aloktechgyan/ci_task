<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task: Latest Price From CoinMarketCap</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        .status {
            margin-bottom: 10px;
        }
        .price {
            font-size: 20px;
            margin-top: 20px;
        }
        .connected {
            color: green;
        }
        .disconnected {
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Pixelsoftwares Task</h1>
        <hr>
        <h4>Latest Price From CoinMarketCap</h4><br>
        <div class="status" id="connectionStatus">
            <strong>Connection Status:</strong> <span id="statusText">Checking...</span>
        </div>  
        <div class="price" id="btcPrice">
            <strong>Latest Price BTC:</strong> <span id="btcPriceValue" class="text-success">Loading...</span>
        </div>
    </div>
    <script>
        // Create flag to track API call status
        let apiCalled = false;
        const channel = new BroadcastChannel('btcPriceUpdate');

        // Function to fetch live BTC price
        function fetchLivePrice() {
            fetch('<?php echo base_url('liveprice/update_price/BTCUSDT'); ?>')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 1) {
                        const bidPrice = data.bidPrice;
                        const formattedPrice = `$${bidPrice}`;
                        document.getElementById('btcPriceValue').textContent = formattedPrice;
                        document.getElementById('statusText').textContent = 'Connected';
                        document.getElementById('connectionStatus').classList.add('connected');
                        
                        // Store bid price in localStorage
                        localStorage.setItem('btcBidPrice', bidPrice);
                        localStorage.setItem('lastUpdated', Date.now());

                        // Broadcast the update to other tabs
                        channel.postMessage({ bidPrice });
                    } else {
                        console.error('Error fetching live BTC price:', data.message);
                        document.getElementById('statusText').textContent = 'Disconnected';
                        document.getElementById('connectionStatus').classList.add('disconnected');
                    }
                })
                .catch(error => {
                    console.error('Error fetching live BTC price:', error);
                    document.getElementById('statusText').textContent = 'Disconnected';
                    document.getElementById('connectionStatus').classList.add('disconnected');
                });
        }

        // Function to update bid price periodically
        function updateBidPricePeriodically() {
            setInterval(() => {
                const lastUpdated = localStorage.getItem('lastUpdated');
                if (!lastUpdated || (Date.now() - lastUpdated) > 60000) {
                    fetchLivePrice();
                }
            }, 60000);
        }

        // When page loads, fetch BTC price if not called before
        document.addEventListener('DOMContentLoaded', function() {
            const lastUpdated = localStorage.getItem('lastUpdated');
            const bidPrice = localStorage.getItem('btcBidPrice');

            if (bidPrice) {
                const formattedPrice = `$${bidPrice}`;
                document.getElementById('btcPriceValue').textContent = formattedPrice;
                document.getElementById('statusText').textContent = 'Connected';
                document.getElementById('connectionStatus').classList.add('connected');
            }

            if (!apiCalled && (!lastUpdated || (Date.now() - lastUpdated) > 60000)) {
                fetchLivePrice();
                apiCalled = true;
            }

            updateBidPricePeriodically();
        });

        // Listen for price updates from other tabs
        channel.onmessage = (event) => {
            const { bidPrice } = event.data;
            if (bidPrice) {
                const formattedPrice = `$${bidPrice}`;
                document.getElementById('btcPriceValue').textContent = formattedPrice;
                document.getElementById('statusText').textContent = 'Connected';
                document.getElementById('connectionStatus').classList.add('connected');
            }
        };

    </script>
</body>
</html>

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
        <!-- <div class="status" id="lastUpdated">
            <strong>Last Updated:</strong> <span id="lastUpdatedTime">Never</span>
        </div> -->
        <div class="price" id="btcPrice">
            <strong>Latest Price BTC:</strong> <span id="btcPriceValue" class="text-success">Loading...</span>
        </div>
    </div>

    <script>
        function fetchLivePrice() {
            fetch('<?php echo base_url('liveprice/update_price/BTCUSDT'); ?>')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log(data);// working fine
                    if (data.status === 1) {
                      
                        const bidPrice = data.bidPrice;

                      // Update the HTML element with the bidPrice
                        document.getElementById('btcPriceValue').innerText = `$: ${bidPrice}`;
    
                        debugger;
                        const formattedPrice = `Latest BTC Price: ${bidPrice}`;
                        document.getElementById('btcPrice').innerHTML = formattedPrice;
                       // document.getElementById('lastUpdatedTime').innerText = new Date().toLocaleTimeString();
                    } else {
                        console.error('Error fetching live BTC price:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error fetching live BTC price:', error);
                });
        }

        // function every 1 minute  bid price
        function checkConnectionStatus() {
            const connectionStatus = Math.random() >= 0.5 ? 'Connected' : 'Disconnected'; // Simulated status
            document.getElementById('statusText').innerText = connectionStatus;
            if (connectionStatus === 'Connected') {
                document.getElementById('connectionStatus').classList.add('connected');
                fetchLivePrice(); // Fetch price if connected
            } else {
                document.getElementById('connectionStatus').classList.add('disconnected');
            }
        }

        // Initial setup
        checkConnectionStatus();

        // Refresh BTC price every minute
        setInterval(fetchLivePrice, 60000);

        // Refresh connection status every 10 seconds (for demo purposes)
        setInterval(checkConnectionStatus, 10000);
    </script>
</body>
</html>

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
        // create flag to track API has been called or not
        let apiCalled = false;

        // Function to fetch live BTC price
        function fetchLivePrice() {
            // console.log("price function call");
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
                        //console.log("response=>"+data);
                        const formattedPrice = `$${bidPrice}`;
                        document.getElementById('btcPriceValue').textContent = formattedPrice;
                        document.getElementById('statusText').textContent = 'Connected';
                        document.getElementById('connectionStatus').classList.add('connected');
                        
                        // Store bid price in localStorage
                        localStorage.setItem('btcBidPrice', bidPrice);
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

        // Function to update bid price every minute
        function updateBidPricePeriodically() {
            // Fetch price every 1 minute
            setInterval(fetchLivePrice, 60000); 
        }

        // When page load then function call
        document.addEventListener('DOMContentLoaded', function() {
            // If API has not been called before, fetch BTC price
            if (!apiCalled) {
                fetchLivePrice();
                apiCalled = true;
            } else {
                // Retrieve and display last fetched bid price from localStorage
                const lastBidPrice = localStorage.getItem('btcBidPrice');
        
                if (lastBidPrice) {
                    const formattedPrice = `$${lastBidPrice}`;
                    document.getElementById('btcPriceValue').textContent = formattedPrice;
					
					// To check connection is established
                    document.getElementById('statusText').textContent = 'Connected'; 
                    document.getElementById('connectionStatus').classList.add('connected');
                }
            }

            // call function and start updating bid price every minute
            updateBidPricePeriodically();
        });
    </script>
</body>
</html>

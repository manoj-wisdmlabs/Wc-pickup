<body>
    <div class="wrap">
        <h1>Add New Store</h1>
    
        <form id="store-form">
            <table class="form-table">
                <tr>
                    <th><label for="store_name">Store Name</label></th>
                    <td><input type="text" id="store_name" name="store_name" required></td>
                </tr>
                <tr>
                    <th><label for="store_address">Store Address</label></th>
                    <td><input type="text" id="store_address" name="store_address" required></td>
                </tr>
                <tr>
                    <th><label for="store_postcode">Postcode</label></th>
                    <td><input type="text" id="store_postcode" name="store_postcode" required></td>
                </tr>
                <tr>
                    <th><label for="store_latitude">Latitude</label></th>
                    <td><input type="text" id="store_latitude" name="store_latitude" required></td>
                </tr>
                <tr>
                    <th><label for="store_longitude">Longitude</label></th>
                    <td><input type="text" id="store_longitude" name="store_longitude" required></td>
                </tr>
            </table>
    
            <p class="submit">
                <button type="submit" name="submit_store" class="button button-primary">Save Store</button>
            </p>
        </form>
    </div>
    <script>

	document.getElementById("store-form").addEventListener("submit", async event => {
		event.preventDefault(); // Prevent page reload

		// Get form values
		let name = document.getElementById("store_name").value;
		let address = document.getElementById("store_address").value;
		let postcode = document.getElementById("store_postcode").value;
		let latitude = document.getElementById("store_latitude").value;
		let longitude = document.getElementById("store_longitude").value;

		let storeData = {
			name,
			address,
			postcode,
			latitude,
			longitude
		};
		console.log(storeData);
		try {
			let response = await fetch("http://woocom.local/wp-json/wc-pickup/v1/store", { 
				method: "POST",
				headers: {
					"Content-Type": "application/json"
				},
				body: JSON.stringify(storeData)
			});

			let result = await response.json();
			console.log("Response from API:", result);

			alert("Store added successfully!");

		} catch (error) {
			console.error("Error:", error);
			alert("Error submitting store data!");
		}
	});

    </script>
</body>
</html>
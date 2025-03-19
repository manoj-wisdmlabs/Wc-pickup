<body>
    <div class="wrap">
        <div class="head">
            <h1>Stores Page</h1>
            <a href="./admin.php?page=wc-pickup-add-new-store" class="button-primary">Add New Post</a>
        </div>

        <div class="info">
            <p>All</p>
            <p>|</p>
            <p>Live</p>
        </div>

        <div class="actions">
            <select name="action" class="action">
                <option value="1">Bulk Actions</option>
                <option value="2">Edit</option>
                <option value="3">Move to Trash</option>
            </select>
        
            <button>Apply</button>

            <select name="date-range" class="date">
                <option value="1">All dates</option>
                <option value="2">March 2025</option>
            </select>

            <select name="categories" class="categories">
                <option value="1">All Categories</option>
                <option value="2">Uncategorized</option>
            </select>

            <button>Filter</button>
        </div>

        <table id="store-table">
            <thead>
                <tr>
                    <th><input type="checkbox" name="" id=""></th>
                    <th>Store Name</th>
                    <th>Store Address</th>             
                </tr>
            </thead>

            <tbody id="store-table-body">

            </tbody>
            <tfoot>
                <tr>
                    <th><input type="checkbox" name="" id=""></th>
                    <th>Store Name</th>
                    <th>Store Address</th>             
                </tr>
            </tfoot>
        </table>



        <div class="actions">
            <select name="action" class="action">
                <option value="1">Bulk Actions</option>
                <option value="2">Edit</option>
                <option value="3">Move to Trash</option>
            </select>
        
            <button>Apply</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                let response = await fetch("http://woocom.local/wp-json/wc-pickup/v1/stores", { 
                    method: "GET",
                    headers: {
                        "Content-Type": "application/json"
                    }
                });

                let result = await response.json();
                console.log("Response from API:", result);

                const tableBody = document.getElementById("store-table-body")
                tableBody.innerHTML = "";
                result.forEach(store => {
                    let row = document.createElement("tr");

                    row.innerHTML = `
                        <td><input type="checkbox"></td>
                        <td>${store.name}</td>
                        <td>${store.address}</td>
                    `;

                    tableBody.appendChild(row);
                })

            } catch (error) {
                console.error("Error:", error);
                alert("Error fetching store data!");
            }
        })
    </script>
</body>
</html>
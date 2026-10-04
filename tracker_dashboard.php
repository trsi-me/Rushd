<?php require_once 'trackerdashboravd.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense And Income Tracker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <link rel="stylesheet" href="rushd.css">
</head>


<body>

    <div id="header">
        <span id="title">
            Expense and Income Tracker
        </span>
    </div>

    <div id="dashboard">
        <div class="data-panel" id="expense-panel"><i class="fas fa-shopping-cart"></i>
            <div>Expense: <?php echo '$'.number_format(abs($expenses), 2); ?></div>
        </div>
        <div class="data-panel" id="income-panel"><i class="fas fa-money-bill-wave"></i>
            <div>Income: <?php echo '$'.number_format(abs($incomes), 2); ?></div>
        </div>
        <div class="data-panel" id="total-panel"><i class="fas fa-chart-pie"></i>
            <div>Total: <?php echo $formattedTotal; ?></div>
        </div>

        <div id="buttons">
            <button class="button" id="add-transaction"><i class="fas fa-plus"></i> Add Transaction</button>
        </div>


        <table id="transaction-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Type</th>
                    <th>Description</th>
                    <th id="amount-header">Amount<i class="fas fa-sort"></i></th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="transaction-body">


               

<!--                 <tr>
                    <td>1</td>
                    <td><span class="expense-type">Expense</span></td>
                    <td>Gym</td>
                    <td>$150</td>
                    <td class="remove-icon">
                        <button class="delete-button"><i class="fas fa-trash"></i>Delete</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td><span class="income-type">Income</span></td>
                    <td>Freelance Work</td>
                    <td>$450</td>
                    <td class="remove-icon">
                        <button class="delete-button"><i class="fas fa-trash"></i>Delete</button>
                    </td>
                </tr> -->
            </tbody>
    </div>

    <div id="add-transaction-dialog">
        <div id="dialog-title">Add Transaction</div>
        <div class="input-group">
            <label for="transaction-type">Type:</label>
            <select id="transaction-type">
                <option value="Expense">Expense</option>
                <option value="Income">Income</option>
            </select>
        </div>
        <div class="input-group">
            <label for="transaction-description">Description:</label>
            <input type="text" id="transaction-description">
        </div>
        <div class="input-group">
            <label for="transaction-amount">Amount:</label>
            <input type="text" id="transaction-amount">
        </div>
        <div class="button-group">
            <button class="button" id="add-transaction-button">Add</button>
            <button class="button cancel" id="cancel-transaction-button">Cancel</button>
        </div>
    </div>


</body>


<script>
    // show dialog
    document.getElementById('add-transaction').addEventListener('click', function(){
        document.getElementById('add-transaction-dialog').style.display = 'block';
    });

    // hide dialog
    document.getElementById('cancel-transaction-button').addEventListener('click', function(){
        document.getElementById('add-transaction-dialog').style.display = 'none';
    });
</script>

<script>

    // add transaction
    document.getElementById('add-transaction-button').addEventListener('click', function(){

        // Retrieve input values
        var type = document.getElementById('transaction-type').value;
        var description = document.getElementById('transaction-description').value;
        var amount = document.getElementById('transaction-amount').value;

        // Create a new Transaction object
        var newTransaction = { type:type, description:description, amount:amount };

        // Send the data to the server using AJAX
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'process_transaction.php', true);
        xhr.setRequestHeader('Content-Type', 'application/json');

        xhr.onreadystatechange = function(){
            if(xhr.readyState == 4 && xhr.status == 200){
                location.reload();
                //console.log(xhr.responseText);
            }
        }

        // Convert the transaction object to JSON
        var jsonData = JSON.stringify(newTransaction);
        // Send the JSON data to the server
        xhr.send(jsonData);


    });

</script>


<script>

document.getElementById('amount-header').addEventListener('click', function(){

    // Get all table rows
    var tableRows = document.querySelectorAll('#transaction-table tbody tr');

    // Convert NodeList to an array for easier manipulation
    var rowsArray = Array.from(tableRows);

    // Check if the rows are already sorted in ascending order
    var ascending = this.classList.contains('asc');

    // Sort the rows based on the amount column
    rowsArray.sort(function(a,b){
        // Extract the amount values from the rows and convert them to numbers
        var amountA = parseFloat(a.children[3].textContent.replace('$',''));
        var amountB = parseFloat(b.children[3].textContent.replace('$',''));

        // Determine whether to sort in ascending or descending order
        if (ascending) { return amountA - amountB; /*Sort in ascending order*/ }

        else{ return amountB - amountA; /* Sort in descending order*/}

    });

    // Update the table with sorted rows
    var tbody= document.querySelector('#transaction-table tbody');
    // Clear the existing table body content
    tbody.innerHTML = '';

    rowsArray.forEach(function(row){ 
        // Append each row to the table body in the sorted order
        tbody.appendChild(row); 
    })

    // Toggle sorting direction class
    this.classList.toggle('asc');
    

});

</script>


<script>

// Add event listener for the "Delete" button
document.querySelectorAll('.delete-button').forEach(function(button){

    button.addEventListener('click', function(){

        // Get the transaction id
        var transactionId = this.getAttribute('data-transaction-id');

        // Confirm deletion (optional)
        var confirmDeletion = confirm('Are you sure you want to delete this transaction?');

        if(confirmDeletion){
            // Make AJAX request to remove the transaction
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'remove_transaction.php', true);
            xhr.setRequestHeader('Content-Type', 'application/json');

            xhr.onreadystatechange = function(){
                if(xhr.readyState == 4 && xhr.status == 200){
                    location.reload();
                    //console.log(xhr.responseText);
                }
            }

            // Convert the transaction object to JSON
            var jsonData = JSON.stringify({transactionId:transactionId});
            // Send the JSON data to the server
            xhr.send(jsonData);

        }

    });
});


</script>
</html>

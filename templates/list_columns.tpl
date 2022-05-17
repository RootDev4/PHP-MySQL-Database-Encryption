<form action="" method="post">
    
    <div class="alert alert-info">
        <strong>Please choose a strong encryption key:</strong>
        <input type="text" name="key" class="enckey" autocomplete="off" autofocus placeholder="at least 6 letters" />
        <img src="./images/encryption_key.png" id="pwgen" title="Generate encryption key" />
        <button type="submit" class="btn btn-success" name="encryptDB" disabled="disabled" onclick="return confirm('Are you sure?');">
            Start table encryption!
        </button>
    </div>
    
    <div class="alert alert-warning">
        {num_rows} affected. Assuming that nothing goes wrong, no data will be lost. To be on the safe side, better create a backup first.
    </div>
    
    <table>
        <thead>
            <tr>
                <td>Column name</td>
                <td>Column Type</td>
                <td></td>
            </tr>
        </thead>
        <tbody>
            {columns}
        </tbody>
    </table>
    
</form>
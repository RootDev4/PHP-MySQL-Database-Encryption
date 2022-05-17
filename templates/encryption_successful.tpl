<div class="alert alert-success">
    <strong>Your selected table has been encrypted successfully!</strong> Please save your encryption key <u>{key}</u>
</div>

Here's a sample PHP/PDO query for fetching encrypted data from your database:
<textarea readonly rows="8">$stmnt = $PDO->prepare("{sql}");
$stmnt->execute();
    
while ($row = $stmnt->fetch())
{
    var_dump($row);
}
</textarea>
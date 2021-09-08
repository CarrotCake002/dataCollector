<?php
require_once 'header.php';
?>

<table>
    <tr>
        <th>Iteration</th>
        <th>URL</th>
        <th>Depth</th>
        <th>Status</th>
        <th>Nb links found</th>
    </tr>
    <tr>
        <td>0</td>
        <td><a href="exampleURL1.php">url1</a></td>
        <td>0</td>
        <td>200</td>
        <td>2</td>
    </tr>
    <tr>
        <td>1</td>
        <td><a href="exampleURL2.php">url2</a></td>
        <td>1</td>
        <td>200</td>
        <td>2</td>
    </tr>
    <tr>
        <td>2</td>
        <td>url3</td>
        <td>1</td>
        <td>200</td>
        <td>1</td>
    </tr>
    <tr>
        <td>3</td>
        <td>url4</td>
        <td>2</td>
        <td>200</td>
        <td>0</td>
    </tr>
    <tr>
        <td>4</td>
        <td>url5</td>
        <td>2</td>
        <td>500</td>
        <td>0</td>
    </tr>
    <tr>
        <td>5</td>
        <td>url6</td>
        <td>2</td>
        <td>200</td>
        <td>0</td>
    </tr>
</table>

<?php
require_once 'footer.php';
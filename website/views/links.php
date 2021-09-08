<?php
require_once 'header.php';
?>

<table>
    <tr>
        <th>URL</th>
        <th>Depth</th>
        <th>Iteration</th>
        <th>Status</th>
        <th>Nb links found</th>
    </tr>
    <tr>
        <td><a href="exampleURL.php">url1</a></td>
        <td>0</td>
        <td>0</td>
        <td>200</td>
        <td>2</td>
    </tr>
    <tr>
        <td>url2</td>
        <td>1</td>
        <td>1</td>
        <td>200</td>
        <td>2</td>
    </tr>
    <tr>
        <td>url3</td>
        <td>1</td>
        <td>2</td>
        <td>200</td>
        <td>1</td>
    </tr>
    <tr>
        <td>url4</td>
        <td>2</td>
        <td>3</td>
        <td>200</td>
        <td>0</td>
    </tr>
    <tr>
        <td>url5</td>
        <td>2</td>
        <td>4</td>
        <td>500</td>
        <td>0</td>
    </tr>
    <tr>
        <td>url6</td>
        <td>2</td>
        <td>5</td>
        <td>200</td>
        <td>0</td>
    </tr>
</table>

<?php
require_once 'footer.php';
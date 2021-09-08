<?php
require_once 'header.php';
?>

<table>
    <tr>
        <th>Iteration</th>
        <th>URL</th>
        <th>Depth</th>
        <th>Predecessor</th>
        <th>Status</th>
        <th>Load time</th>
        <th>Title(size)</th>
        <th>
            (Nb)<br>
            Meta selectors(size)
        </th>
        <th>
            (Nb)<br>
            hreflang(size)
        </th>
        <th>
            (Nb)<br>
            Links(size)
        </th>
        <th>
            (Nb)<br>    
            Custom Selectors(size)
        </th>
    </tr>
    <tr>
        <td>1</td>
        <td><a>url2</a></td>
        <td>1</td>
        <td><a href="exampleURL1.php">url1</a></td>
        <td>200</td>
        <td>5.294s</td>
        <td>Second URL title(16)</td>
        <td>
            (2)<br>
            <\meta>meta content<\/meta> (27)<br>
            <\meta>more meta content<\/meta> (32)
        </td>
        <td>
            (5)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"es\> (79)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"cat\> (80)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"fr\> (79)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"ru\> (79)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"en\> (79)
        </td>
        <td>
            (2)<br>
            url3 (4)<br>
            url4 (4)
        </td>
        <td>
            -
        </td>
    </tr>
</table>

<?php
require_once 'footer.php';
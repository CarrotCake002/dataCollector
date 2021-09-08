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
        <td>0</td>
        <td><a>url1</a></td>
        <td>0</td>
        <td>-</td>
        <td>200</td>
        <td>3.568s</td>
        <td>URL Title(9)</td>
        <td>
            (1)<br>
            <\meta>meta content<\/meta> (27)</td>
        <td>
            (3)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"es\> (79)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"cat\> (80)<br>
            <\link rel=\"alternate\" href=\"https://help.steampowered.com/\" hreflang=\"fr\> (79)
        </td>
        <td>
            (1)<br>
            <a href="exampleURL2.php">url2</a> (4)
        </td>
        <td>
            -
        </td>
    </tr>
</table>

<?php
require_once 'footer.php';
<style>
    body{
        font-size:12px;
        font-family: Segoe UI;
    }
    #graph{
        height: 200px;
        width: 940px;
        border:none;
        background:transparent;
        background-color:#e7e7e7;
    }

</style>
<style type="text/css"  media="screen">
    @import "css/graph.css";
</style>
<script>
    var delay=2000;//in ms
    setTimeout(function(){
        //document.forms["test"].submit();
    },delay);
</script>
<form name='test' action='' method='POST'>

</form>
<?php
/**
 * Created by PhpStorm.
 * User: Karl Agunday
 * Date: 2/10/2017
 * Time: 7:43 AM
 */

require_once "modules/Graph.php";

$graph = new Graph(3);
$graph->draw();

for($x = 0; $x <= 3; $x++){
    $graph->addPoint($x, rand(0,999));
}
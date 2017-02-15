<html>
    <style type="text/css"  media="screen">
        @import "css/graph.css";
    </style>
    <script>
        var delay=1000;//in ms
        setTimeout(function(){
            document.forms["test"].submit();
        },delay);
    </script>
    <title>Usage Monitor</title>
    <body>
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

        ?>
        <div id='data-table'>
            <table class='reading'>
                <tr>
                    <td>Name</td>
                    <td>Current</td>
                    <td>Average</td>
                    <td>Peak</td>
                </tr>
                <?php ;

                for($x = 0; $x <= $_SESSION['graph_numbers'] - 1; $x++){
                    echo "<tr>";
                    echo "<td style='color:".$graph->getColor($x)."'>Client ".($x + 1)."</td>";
                    echo "<td>".$graph->getCurrent($x)." Kbps</td>";
                    echo "<td>".$graph->getAverage($x)." Kbps</td>";
                    echo "<td>".$graph->getPeak($x)." Kbps</td>";
                    echo "</tr>";
                }
                ?>
                <table>
        </div>
        <?php
        for($x = 0; $x <= 2; $x++) {
            $graph->addPoint($x, rand(0, 1000));
        }
        ?>
    </body>
</html>
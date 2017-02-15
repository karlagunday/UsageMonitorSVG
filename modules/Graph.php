<?php

/**
 * Created by PhpStorm.
 * User: Karl Agunday
 * Date: 2/10/2017
 * Time: 6:02 AM
 */
class Graph {
    private $_dimensions  = array(
        'start-axes' => array(
            'x' => 50,
            'y' => 14,
        ),
        'cell-size' => array(
            'x' => 30,
            'y' => 30
        ),
        'grid-size' => array(
            'x' => 40,
            'y' => 10,
        )
    );

    private $_scaleRanges = array('10','100','200','500','1000');

    private $_color = array('#8acc25','#00004c','red', 'gray');

    private $_utils = array(
        'unit_value' => 0,
        'x_origin' => 0,
        'y_origin' => 0,
        'graph_ceiling' => 0

    );

    private $_labels = array(
        'graph-x-axis' => 'Usage (Kbps)',
        'graph-y-axis' => 'Interval'
    );

    public function __construct($graphs) {
        session_start();
/*        $this->removePlots();
        die();*/

        if(!isset($_SESSION['graph_values']) && !isset($_SESSION['graph_numbers'])) {
            //set number of graphs
            $_SESSION['graph_numbers'] = $graphs;
            //populate points array
            $this->_populatePoints();
        }
    }

    private function _populatePoints() {
        //populate graph_values with array of graph points filled with zeros
        $_SESSION['graph_values'] = array_fill(0, $_SESSION['graph_numbers'], array_fill(0,41,0));
        return;
    }

    public function getDimension($of) {
        return (isset($this->_dimensions[$of])) ? $this->_dimensions[$of] : NULL;
    }

    public function getLabel($of) {
        return (isset($this->_labels[$of])) ? $this->_labels[$of] : NULL;
    }

    public function addPoint($graphNumber, $value, $leftToRight = TRUE) {
        //die(var_dump($_SESSION['graph_values'][$graphNumber]));

        if($leftToRight === TRUE){
            //insert at the beginning of array. graph directions from left to right
            //die(var_dump($_SESSION['graph_values']));
            array_unshift($_SESSION['graph_values'][$graphNumber], $value);

            //remove oldest value (end of array)
            array_pop($_SESSION['graph_values'][$graphNumber]);
        }
        else{
            //insert at end of array. graph directions from right to left
            array_push($value, $_SESSION['graph_values'][$graphNumber]);

            //remove oldest value (beginning of array)
            array_shift($_SESSION['graph_values'][$graphNumber]);
        }

        return;
    }

    public function removePlots($graphs = NULL) {
        if(isset($graphs)){
            unset($_SESSION['graph_values'][$graphs]);
        }
        else{
            unset($_SESSION['graph_values']);
        }

        //unset no of graphs as well
        unset($_SESSION['graph_numbers']);

    }

    public function getAverage($graphNumber) {
        return round(array_sum($_SESSION['graph_values'][$graphNumber]) / count ($_SESSION['graph_values'][$graphNumber]), 2);
    }

    public function getPeak($graphNumber = NULL) {
        if(!isset($graphNumber)){
            //get peak of peaks
            $peaks = array();
            for($x=0; $x<=$_SESSION['graph_numbers'] - 1; $x++){
                $peaks[] = max($_SESSION['graph_values'][$x]);
            }
            return max($peaks);
        }
        else{
            return max($_SESSION['graph_values'][$graphNumber]);
        }
    }

    public function getCurrent($graphNumber) {
        //if array_unshift is used in addPoint
        return $_SESSION['graph_values'][$graphNumber][0];
        //if array_push is used in addPoint
        //return end($_SESSION['graph_values'][$graphNumber]);
    }

    public function getColor($index){
        return $this->_color[$index];
    }

    public function draw() {
        //setup utility values
        $this->_setUtils();
        echo "<div id='data-graph'>";
            echo "<svg class='graph'>";
                echo "<text class='graph-axis-label' y='".($this->getDimension('start-axes')['x']-20)."' x='".(($this->getDimension('start-axes')['y']+(($this->getDimension('cell-size')['y']*$this->getDimension('grid-size')['y'])/20)))."' fill='black' transform='translate(-10,160)rotate(-90)'>".$this->getLabel('graph-x-axis')."</text>";
                echo "<text class='graph-axis-label' y='".(($this->getDimension('cell-size')['y']*$this->getDimension('grid-size')['y'])+40)."' x='".(($this->getDimension('start-axes')['x']+(($this->getDimension('cell-size')['x']*$this->getDimension('grid-size')['x']))/2))."'>".$this->getLabel('graph-y-axis')."</text>";
                echo "<rect x='".($this->getDimension('start-axes')['x']+($this->getDimension('cell-size')['x']/3))."' y='".$this->getDimension('start-axes')['y']."' width='".($this->getDimension('cell-size')['x']*$this->getDimension('grid-size')['x'])."' height='".($this->getDimension('cell-size')['y']*$this->getDimension('grid-size')['y'])."' style='fill:#fff' />";
                echo "<g class='grid x-grid' id='xGrid'>";
                        for($x=0;$x<=$this->getDimension('grid-size')['x'];$x++){
                            echo "<line x1='".(($this->getDimension('start-axes')['x']+($this->getDimension('cell-size')['x']/3))+($this->getDimension('cell-size')['x']*$x))."' x2='".(($this->getDimension('start-axes')['x']+($this->getDimension('cell-size')['x']/3))+($this->getDimension('cell-size')['x']*$x))."' y1='".$this->getDimension('start-axes')['y']."' y2='".(($this->getDimension('start-axes')['y']+($this->getDimension('cell-size')['y']*$this->getDimension('grid-size')['y']))+($this->getDimension('cell-size')['y']/3))."'></line>";
                        }
                echo "</g>";
                echo "<g class='grid y-grid' id='yGrid'>";

                for($y=0;$y<=$this->getDimension('grid-size')['y'];$y++){
                    echo "<line x1='".$this->getDimension('start-axes')['x']."' x2='".(($this->getDimension('start-axes')['x']+($this->getDimension('cell-size')['x']*$this->getDimension('grid-size')['x']))+($this->getDimension('cell-size')['x']/3))."' y1='".($this->getDimension('start-axes')['y']+($this->getDimension('cell-size')['y']*$y))."' y2='".($this->getDimension('start-axes')['y']+($this->getDimension('cell-size')['y']*$y))."'></line>";
                }

                echo "</g>";
                $this->_setScaleLabels();
                $this->plot();
                echo "<use class='grid overlay' xlink:href='#xGrid' style=''></use>";
                echo "<use class='grid overlay' xlink:href='#yGrid' style=''></use>";
            echo "</svg>";
        echo "</div>";
    }


    private function _setUtils(){
        $peak = $this->getPeak();
        foreach ($this->_scaleRanges as $key => $range) {
            if($peak <= $range){
                $this->_utils['unit_value'] = $range / $this->getDimension('grid-size')['y'];
                $this->_utils['graph_ceiling'] = $range;
                break;
            }
        }
        $this->_utils['x_origin'] = $this->getDimension('start-axes')['x'] + ($this->getDimension('cell-size')['x'] / 3); //figure out why 3. must be with the scaling
        $this->_utils['y_origin'] = $this->getDimension('start-axes')['y'] + ($this->getDimension('cell-size')['y'] * $this->getDimension('grid-size')['y']);

        return;
    }

    private function _setScaleLabels(){
        for($x=0;$x<=$this->_utils['graph_ceiling'];$x=$x+($this->_utils['graph_ceiling']/2)){
            echo "<text class='y-label graph-label 'x='".($this->getDimension('start-axes')['x']-5)."' y='".($this->_utils['y_origin']-($this->getDimension('cell-size')['y']*($x/$this->_utils['unit_value']))+2)."'>".$x."</text>";
        }
    }

    public function plot(){
        foreach($_SESSION['graph_values'] as $key => $graph_data){
            $y = 0;
            $line_points = "";
            echo "<g class='line'>";
            foreach ($graph_data as $points) {
                $xpos=$this->_utils['x_origin']+($y*$this->getDimension('cell-size')['x']);
                $ypos=$this->_utils['y_origin']-($points*$this->getDimension('cell-size')['x'])/$this->_utils['unit_value'];
                //for line graph's points
                echo "<circle cx='".$xpos."' cy='".$ypos."' data-value='".$points."' r='3.3' style='fill:".$this->_color[$key]."; cursor:pointer'>";
                    echo "<title>" . $points . " Kbps</title>";
                echo "</circle>";
                //for line graph's line points
                //create path element's d property
                $line_points=$line_points." ".$xpos.",".$ypos;
                $y++;
            }

            //remove prefix space
            $line_points=substr($line_points,1);
            //create polyline
            if(0){
                //filled line graph
                echo "<polyline class='set_line' points='".$this->_utils['x_origin'].",".$this->_utils['y_origin']." ".$line_points." ".(($this->getDimension('start-axes')['x']+($this->getDimension('cell-size')['x']*$this->getDimension('grid-size')['x']))+($this->getDimension('cell-size')['x']/3)).",".$this->_utils['y_origin']."' style=fill:".$this->_color[$key].">";
            }
            else{
                //just line graph
                echo "<polyline class='set_line' points='".$line_points."' style=fill:none;stroke:".$this->_color[$key].";stroke-width:0.8>";
            }
            echo "</g>";

        }
    }
}
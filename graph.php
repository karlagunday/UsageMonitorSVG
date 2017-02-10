<style type="text/css"  media="screen">
	@import "./css/graph.css";
</style>
<script>
	var delay=2000;//in ms
    setTimeout(function(){
		document.forms["test"].submit();
    },delay); 
</script>
<?php
	session_start();
	//start axes
	$xstart=50;
	$ystart=14;
	//cell size
	$xcell=14;
	$ycell=14;
	//grid size, in cells
	$xgrid=40;
	$ygrid=10;
	$xlabel="Interval (2s)";
	$ylabel="Usage (Kbps)";
?>
<div id='data-graph'>
	<svg class='graph'>
		<!--graph's grid--->
		<?php
			echo "<text class='graph-axis-label' y='".($xstart-20)."' x='".(($ystart+(($ycell*$ygrid)/2)))."' fill='black' transform='translate(-10,160)rotate(-90)'>".$ylabel."</text>";
			echo "<text class='graph-axis-label' y='".(($ycell*$ygrid)+40)."' x='".(($xstart+(($xcell*$xgrid))/2))."'>".$xlabel."</text>";
			echo "<rect x='".($xstart+($xcell/3))."' y='".$ystart."' width='".($xcell*$xgrid)."' height='".($ycell*$ygrid)."' style='fill:#fff' />";
		?>
		<g class="grid x-grid" id="xGrid">
			<?php
				for($x=0;$x<=$xgrid;$x++){
					echo "<line x1='".(($xstart+($xcell/3))+($xcell*$x))."' x2='".(($xstart+($xcell/3))+($xcell*$x))."' y1='".$ystart."' y2='".(($ystart+($ycell*$ygrid))+($ycell/3))."'></line>";
				}		
			?>
		</g>
		<g class="grid y-grid" id="yGrid">
			<?php
				for($y=0;$y<=$ygrid;$y++){
					echo "<line x1='".$xstart."' x2='".(($xstart+($xcell*$xgrid))+($xcell/3))."' y1='".($ystart+($ycell*$y))."' y2='".($ystart+($ycell*$y))."'></line>";
				}
			?>
		</g>	
		<?php
			$index_graph=0;
			$color=Array('#8acc25','#00004c','red');
			foreach ($color as $value){
				$round_to=Array('10','100','200','500','1000');
				if($_SERVER['REQUEST_METHOD'] == 'POST'){
					//remove value at the start of the array
					array_shift($_SESSION['points'][$index_graph]);
					//add new value to end of array
					array_push($_SESSION['points'][$index_graph],rand(0,999));
				}
				else{
					//fill array with zeros
					$_SESSION['points'][$index_graph]=array_fill(0,41,'0');
				}
				//determine highest value of all graphs
				$highest_data=max($_SESSION['points'][$index_graph]);
				foreach($round_to as $value){
					if($highest_data<$value){
						//get ceiling value
						$ceiling[$index_graph]=$value;
						break;
					}
				}
				$index_graph++;
			}
			$graph_ceiling=max($ceiling);
			$unit_value=$graph_ceiling/$ygrid;
			
			//get graph origin
			$x_origin=$xstart+($xcell/3);
			$y_origin=$ystart+($ycell*$ygrid);
			
			//for point-plotting
			for($x=0;$x<=$index_graph-1;$x++){
				$y=0;
				$line_points="";
				echo "<g class='line'>";				
					foreach($_SESSION['points'][$x] as $value){
						$xpos=$x_origin+($y*$xcell);
						$ypos=$y_origin-($value*$ycell)/$unit_value;
						//for line graph's points
						//echo "<circle cx='".$xpos."' cy='".$ypos."' data-value='".$value."' r='1' style='fill:".$color[$x]."'></circle>";
						
						//for line graph's line points
						//create path element's d property
						$line_points=$line_points." ".$xpos.",".$ypos;
						$y++;					
					}
					//remove prefix space
					$line_points=substr($line_points,1);
					//create polyline
					if($x==0){
						//filled line graph
						echo "<polyline class='set_line' points='".$x_origin.",".$y_origin." ".$line_points." ".(($xstart+($xcell*$xgrid))+($xcell/3)).",".$y_origin."' style=fill:".$color[$x].">";
					}
					else{
						//just line graph
						echo "<polyline class='set_line' points='".$line_points."' style=fill:none;stroke:".$color[$x].";stroke-width:0.8>";
					}					
					$current[$x]=end($_SESSION['points'][$x]);
					$peak[$x]=max($_SESSION['points'][$x]);
					$average[$x]=round((array_sum($_SESSION['points'][$x])/count($_SESSION['points'][$x])),2);
				echo "</g>";					
			}
			//for labels
			for($x=0;$x<=$graph_ceiling;$x=$x+($graph_ceiling/2)){
				echo "<text class='y-label graph-label 'x='".($xstart-5)."' y='".($y_origin-($ycell*($x/$unit_value))+2)."'>".$x."</text>";
			}
		?>
		<use class="grid overlay" xlink:href="#xGrid" style=""></use>
		<use class="grid overlay" xlink:href="#yGrid" style=""></use>		
	</svg>
</div>
<div id='data-table'>
	<table class='reading'>
		<tr>
			<td>Name</td>
			<td>Current</td>
			<td>Average</td>
			<td>Peak</td>
		</tr>
		<?php
			$x=0;
			$name=Array('Client A','Client B','Client C');
			foreach($_SESSION['points'] as $set){
				echo "<tr>";
					echo "<td style='color:".$color[$x]."'>".$name[$x]."</td>";
					echo "<td>".$current[$x]." Kbps</td>";
					echo "<td>".$average[$x]." Kbps</td>";
					echo "<td>".$peak[$x]." Kbps</td>";
				echo "</tr>";
				$x++;
			}
		?>
	<table>
</div>
<form name='test' action='' method='POST'>
	
</form>
<!DOCTYPE html>
<html lang="en">
<?php
// If you want to run this change 'root' 'root' and 'virus'
// user, password, database
$dbcon = mysqli_connect("localhost","root","root","virus");
function get_rows($best, $worst){
	global $dbcon;
	$best_nums = mysqli_query($dbcon, "SELECT COUNT(*) FROM `$best`");
	$best_nums = $best_nums->fetch_row()[0];
	
	$worst_nums = mysqli_query($dbcon, "SELECT COUNT(*) FROM `$worst`");
	$worst_nums = $worst_nums->fetch_row()[0];
	
	return  $best_nums + $worst_nums;
}
$bact_nums = get_rows('BACTERIA_best', 'BACTERIA_worst');
$env_nums = get_rows('ENVIRONMENTAL_best', 'ENVIRONMENTAL_worst');
$inv_nums = get_rows('INVERTEBRATE_best', 'INVERTEBRATE_worst');
$mam_nums = get_rows('MAMMAL_best', 'MAMMAL_worst');
$phage_nums = get_rows('PHAGE_best', 'PHAGE_worst');
$plant_nums = get_rows('PLANTS_best', 'PLANTS_worst');
$pri_nums = get_rows('PRIMATES_best', 'PRIMATES_worst');
$rod_nums = get_rows('RODENT_best', 'RODENT_worst');
$ver_nums = get_rows('VERTEBRATE_best', 'VERTEBRATE_worst');
?>

<html>
	<head>		
		<link rel="stylesheet" type="text/css" href="styles.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
		<script type="text/javascript" src="/media/js/site.js?_=5e8f232afab336abc1a1b65046a73460"></script>
		<script type="text/javascript" src="/media/js/dynamic.php?comments-page=examples%2Fdata_sources%2Fdom.html" async></script>
		<script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" language="javascript" src="../resources/demo.js"></script>
		<script type="text/javascript" class="init">		
			$(document).ready( function() {
				$('#testTable').DataTable({
					"scrollX": true,
					//"pageLength": 25
				});
			} );

		</script>				
				
		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	</head>
	<body>	
		<div class="titleDiv">
			<h2>HIVES Blast data.</h2>
			<p align="right">For help and source code click <a href="https://github.com/ZackBoyd123/hives_pipeline">here</a></p>
		</div>		
		<div class="chart">
			<canvas id="pie-chart"></canvas>
		</div>
		
		<script>
			var bact = <?php echo $bact_nums ?>;
			var env = <?php echo $env_nums ?>;
			var mam = <?php echo $mam_nums ?>;
			var phage = <?php echo $phage_nums ?>;
			var plant = <?php echo $plant_nums ?>;
			var pri = <?php echo $pri_nums ?>;
			var rod = <?php echo $rod_nums ?>;
			var ver = <?php echo $ver_nums ?>;
			var inv = <?php echo $inv_nums ?>;
			
			var ctx = document.getElementById("pie-chart").getContext('2d');
			//new Chart(document.getElementById("pie-chart"), {
			var myChart = new Chart(ctx, {
    			type: 'pie',
    			data: {
      			labels: ["Bacteria", "Environmental", "Invertebrate", "Mammal", "Phage",
      			"Plants", "Primates", "Rodent", "Vertebrate"],
      			datasets: [{      	  		
      	  		backgroundColor: ["#3e95cd", "#8e5ea2","#3cba9f","#e8c3b9","#c45850",
        			"#FF8C00", "#FF1493", "#FFFF00", "#AFEEEE"],
        			data: [bact,env,inv,mam,phage,plant,pri,rod,ver]
     			 }]
   		 		},
   		 		options: {
   		 			onClick:function(e){
    					var activePoints = myChart.getElementsAtEvent(e);
  			 			var selectedIndex = activePoints[0]._index;
  			 			var label = this.data.labels[selectedIndex];
  			 			label = label.toUpperCase();
  			 			//document.getElementById("script_log").innerHTML = label;
  			 			window.location.href="index.php?org="+label;
  			 			
					},
   	 				responsive: true,
      				maintainAspectRatio: false,
      				
      				legend: {
    	  				position: "right"
  	    			},
  	    			
     			title: {
     			   	display: true
      				}      			

   	 			} 			
   	 				   	 			   	 			
			});					
			
		</script>
		<script type="text/javascript">
			function view_bad(clicked_id){
				var host = document.location.href.split("=")[1];
				window.open("subject.php?host="+ host + "&quer="+ clicked_id, '_blank');
			}
		</script>
		<?php	
			function table_head(){
				echo "<div class=\"tablediv\">
						<table id=\"testTable\" width=\"100%\" class=\"display cell-border\">
						<thead>
							<tr>
								<th>Query</th>
								<th>Subject</th>
								<th>Percentage</th>
								<th>View all hits</th>
								<th>Query HTML</th>
								<th>Subject HTML</th>								
								<th>Alignment Length</th>
								<th>E-Value</th>
								<th>Query Taxid</th>
								<th>Subject Taxid</th>
								
							</tr>
						</thead>
						<tbody>";
			}		
  			$organism = $_GET["org"];
  			if (!isset($organism)){
  			} else {
  				echo "<div><h3>Now viewing blast results for: " . $organism . ".</h3>";
  				echo "<p>Showing best hits for each unique virus entry.</p></div>";
  				table_head();
				// Do some SQL stuff. 
				$query = "SELECT Query, Subject, Percentage, Alignment_Length, EValue, Query_Taxid, Subject_Taxid, Query_HTML, Subject_HTML FROM $organism" . "_best";
				$result = mysqli_query($dbcon, $query);
				while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
					echo "\r\n<tr>";
					echo "<td>" . $row['Query'] . "</td>";
					echo "<td>" . $row['Subject'] . "</td>";
					echo "<td>" . $row['Percentage'] . "</td>";
					echo "<td>" . "<button id=\"" . $row['Subject'] . "\" onClick=\"view_bad(this.id)\">View all hits</button>" . "</td>";
					echo "<td><a href=\"" . $row['Query_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Query_HTML'])[4]."</a></td>";
					echo "<td><a href=\"" . $row['Subject_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Subject_HTML'])[4] . "</a></td>";	
					echo "<td>" . $row['Alignment_Length'] . "</td>";
					echo "<td>" . $row['EValue'] . "</td>";
					echo "<td>" . $row['Query_Taxid'] . "</td>";
					echo "<td>" . $row['Subject_Taxid'] . "</td>";
					
					echo "</tr>";
				}							
				
				echo "</tbody>";
				echo "</table>";
  			}
  			
  			
			
  		?>
	</body>
</html>

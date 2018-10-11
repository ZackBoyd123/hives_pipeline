<!DOCTYPE html>
<html lang="en">
<?php
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
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
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
					"pageLength": 100,
					"order": [[6, "desc"]]
				});
			} );
			
			var table = $('#testTable').DataTable();
			$("#hide").click(function() {
    			$.fn.dataTable.ext.search.push(
       				function(settings, data, dataIndex) {
          				return $(table.row(dataIndex).node()).attr('retro') == 1;
       				}
    			);
   			 table.draw();
			});
			
		</script>						
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
	</head>
	<body>	
		<div class="container-fluid" style="border: 1px solid; background-color: #E4E4E4;">
			<div class="row">
				<div class="col-8">
					<h2>HIVES Blast data.</h2>
				</div>	
				<div class="col-4">
					<p align="right" style="padding-top:2%;">For help and source code click <a href="https://github.com/ZackBoyd123/hives_pipeline">here</a></p>
				</div>
			</div>
		</div>	
		<div class="container-fluid" style="border: 1px solid; border-top: none;">
			<div class="chart">
				<canvas id="pie-chart"></canvas>
			</div>
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
				window.open("subject.php" + document.location.href.split("php")[1] + "&quer="+ clicked_id, '_blank');
			}
		</script>
		<script type="text/javascript">
			function search_fam(){
				if((window.location.href).includes("&search=")){
					window.location.href = document.location.href.split("&search=")[0] + "&search=" + document.getElementById("search_quer").value;

				} else {
					window.location.href = document.location.href + "&search=" + document.getElementById("search_quer").value;
				}								
				
			}
		</script>
		<script>
			function reset_search(){
				if((window.location.href).includes("&search=")){
					window.location.href = document.location.href.split("&search=")[0]
				}
			}
		</script>
		
		<script>
			function search_correct(id){
				window.location.href = document.location.href.split("&search=")[0] + "&search=" + id
				//alert(document.location.href.split("&search=")[0] + "&search=" + id);
			}
		
		</script>
		<script>
			function deal_retro(id){				
				if(id == "retro"){
					if((window.location.href).includes("&retro=")){
						window.location.href = document.location.href.split("&retro=")[0] + "&retro=YES";
					} else {					
						window.location.href = document.location.href + "&retro=YES";
					}
				} else if (id == "noRetro"){
					if((window.location.href).includes("&retro=")){
						window.location.href = document.location.href.split("&retro=")[0] + "&retro=NO";
					} else {					
						window.location.href = document.location.href + "&retro=NO";
					}
				} else {
					window.location.href = document.location.href.split("&retro=")[0];
				}
			}
		</script>
	
		<?php	
			function table_head(){
				echo "<div class=\"container-fluid\"><div class=\"tablediv\">
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
								<th>Retro</th>
								
							</tr>
						</thead>
						<tbody>";
			}		
  			$organism = $_GET["org"];
  			if (!isset($organism)){
  			} else {
  				$retro = $_GET['retro'];
  				$search = $_GET["search"];
  				if(isset($search)){
  					echo "<div class=\"container-fluid\">\n<div class=\"row\">\n<div class=\"col-6\">\n<h3>". $organism . ": " . $search . "</h3></div>\n";

  				} else {
  					echo "<div class=\"container-fluid\">\n<div class=\"row\">\n<div class=\"col-6\">\n<h3>". $organism . ".</h3></div>\n";
  				
  				}
  				echo "</div>\n</div>\n";
  				echo "<div class=\"container-fluid\" style=\"padding-top: 5px;\">\n<div class=\"row\">";
  				echo "<div class=\"col-2\"><h5>Search subject: </h5></div>";
  				echo "<div class=\"col-3\"><input type=\"text\" id=\"search_quer\" style=\"width:100%;\"/></div>";
  				echo "<div class=\"col-2\"><button id=\"search_butt\" onClick=\"search_fam()\">Search.</button>";
  				echo "<button id=\"reset_butt\" onClick=\"reset_search()\">Reset.</button></div>";
  				echo "<div class=\"col-2\"><h5>Show / hide retrovirsues: </h5></div>";
  				echo "<div class=\"col-3\"><button id=\"retro\" onClick=\"deal_retro(this.id)\">Retro</button><button id=\"noRetro\" onClick=\"deal_retro(this.id)\">Non-Retro</button><button id=\"reset_retro\" onClick=\"deal_retro(this.id)\">Reset</div>";
  				echo "</div></div>";
  				
  				if(isset($search)){
  					$exists = mysqli_query($dbcon, "SELECT `organism` FROM `family` WHERE `organism` = \"$search\"");
  					if(mysqli_num_rows($exists) == 0){
  						echo "<div class=\"container-fluid\" style=\"background-color:#E4E4E4; border: solid 1px;\"><div class=\"row\"><div class=\"col-12\"><h3>$search not found in the \"$organism\" database: </h3>";
  						$query = mysqli_query($dbcon, "SELECT `organism` FROM `family` WHERE `organism` LIKE \"%$search%\" OR `organism` SOUNDS LIKE \"$search\"");
  						echo "<h5>Did you mean any of the following?</h5></div></div></div>";
  						//echo "SELECT `organism` FROM `family` WHERE `organism` LIKE \"%$search%\"";
  						echo "<div class=\"container-fluid\"><div class=\"row\">";
  						$count = 1;
  						while($row = mysqli_fetch_array($query, MYSQLI_ASSOC)){
  							if($count % 3 == 0){
  								echo "</div></div>";
  								echo "<div class=\"container-fluid\"><div class=\"row\">";
  							}
  							echo "<div class=\"col-2\">
  								<p>" . $row['organism'] . "</p></div>";
  							echo "<div class=\"col-2\">
  								<button id=\"" . $row['organism'] . "\" onClick=\"search_correct(this.id)\">Search</button></div>";
  						}
  						die;
  					}
  					$min = "SELECT `min_ort` FROM `family` WHERE `organism` = \"$search\"";
  					$min = mysqli_query($dbcon, $min);
  					$min = $min->fetch_row()[0];
  					$max = "SELECT `max_ort` FROM `family` WHERE `organism` = \"$search\"";
  					$max = mysqli_query($dbcon, $max);
  					$max = $max->fetch_row()[0];
  					
  					if(isset($retro)){
  						$query = "SELECT Query, Subject, Percentage, Alignment_Length, EValue, Query_Taxid, Subject_Taxid, Query_HTML, Subject_HTML, Retro FROM `$organism" . "_best` WHERE `Orto_id` BETWEEN $min and $max AND Retro = \"$retro\"";

  					} else {
  						$query = "SELECT Query, Subject, Percentage, Alignment_Length, EValue, Query_Taxid, Subject_Taxid, Query_HTML, Subject_HTML, Retro FROM `$organism" . "_best` WHERE `Orto_id` BETWEEN $min and $max";
  					}
  					$result = mysqli_query($dbcon, $query);
  					
  				} else {  					
					// Do some SQL stuff. 
					if(isset($retro)){
						$query = "SELECT Query, Subject, Percentage, Alignment_Length, EValue, Query_Taxid, Subject_Taxid, Query_HTML, Subject_HTML, Retro FROM $organism" . "_best WHERE Retro = \"$retro\"";
					
					} else {					
						$query = "SELECT Query, Subject, Percentage, Alignment_Length, EValue, Query_Taxid, Subject_Taxid, Query_HTML, Subject_HTML, Retro FROM $organism" . "_best";
					}
					$result = mysqli_query($dbcon, $query);											
				}
				if(mysqli_num_rows($result) >= 1){
					if(isset($retro)){
						echo "\n<div class=\"containter-fluid\" style=\"padding-left: 0.8%;\">\n<div class=\"row\">\n<div class=\"col-7\"><h5>Showing best hit for each unique virus</h5>\n</div><div class=\"col-4\"><h5>Retro: $retro</h5></div>\n</div>\n</div>";

					} else {
						echo "\n<div class=\"containter-fluid\" style=\"padding-left: 0.8%;\">\n<div class=\"row\">\n<div class=\"col-7\"><h5>Showing best hit for each unique virus</h5>\n</div><div class=\"col-4\"><h5>Retro: All</h5></div>\n</div>\n</div>";
					
					}
					table_head();
					while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
						
							echo "<td>" . str_replace("_", " ",explode("|", $row['Query'])[6]) . "</td>";
							echo "<td>" . str_replace("_", " ",explode("|", $row['Subject'])[6]) . "</td>";
							echo "<td>" . $row['Percentage'] . "</td>";
							echo "<td>" . "<button id=\"" . $row['Subject'] . "\" onClick=\"view_bad(this.id)\">View all hits</button>" . "</td>";
							echo "<td><a href=\"" . $row['Query_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Query_HTML'])[4]."</a></td>";
							echo "<td><a href=\"" . $row['Subject_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Subject_HTML'])[4] . "</a></td>";	
							echo "<td>" . $row['Alignment_Length'] . "</td>";
							echo "<td>" . $row['EValue'] . "</td>";
							echo "<td><a href=\"https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=" . $row['Query_Taxid'] . "\" target=\"_blank\">" . $row['Query_Taxid'] . "</td>";
							echo "<td><a href=\"https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=" . $row['Subject_Taxid'] . "\" target=\"_blank\">" . $row['Subject_Taxid'] . "</td>";
							echo "<td>" . $row['Retro'] . "</td>";					
							echo "</tr>";
					}					
					echo "</tbody>";
					echo "</table>";
					echo "</div></div>";
				} else {
					echo "<h6>No results found for $search in the $organism database</h6>";
				}
  			}
  			
  			
			
  		?>
	</body>
</html>

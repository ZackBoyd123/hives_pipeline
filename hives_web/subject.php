<!doctype html>
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
				$('#subjectTable').DataTable({
					"pageLength": 100,
					"scrollX": true
				});
			} );

		</script>

</head>

<body>
	<?php
		$dbcon = mysqli_connect("localhost","root","root","virus");
		function table_head(){
				echo "<div class=\"container-fluid\"><div class=\"tablediv\">
						<table id=\"subjectTable\" width=\"100%\" class=\"display cell-border\">
						<thead>
							<tr>
								<th>Query</th>
								<th>Query HTML</th>								
								<th>Subject</th>
								<th>Subject HTML</th>								
								<th>Percentage</th>
								<th>Alignment Length</th>
								<th>E-Value</th>
								<th>Query Taxid</th>
								<th>Subject Taxid</th>
							</tr>
						</thead>
						<tbody>";
			}
		$host = $_GET['org'];
		$subject = $_GET['quer'];
		echo "<div class=\"container-fluid\"><div class=\"row\">";
		echo "<div class=\"col-3\"><h5>Showing all hits for subject: </h5></div>";
		echo "<div class=\"col-9\"><p>$subject</p></div>";
		echo "</div></div>";
		table_head();
		$query = "SELECT Query, Subject, Percentage, Alignment_Length, EValue, Query_Taxid, Subject_Taxid, Query_HTML, Subject_HTML FROM " . $host . "_worst WHERE Subject = \"$subject\"";
		//echo $query;
		$result = mysqli_query($dbcon, $query);
		if(mysqli_num_rows($result) == 0){
			echo "<h3>No bad hits found for specified subject</h3>";
		}
		while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
			echo "\r\n<tr>";
					echo "<td>" . str_replace("_", " ",explode("|", $row['Query'])[6]) . "</td>";
					echo "<td><a href=\"" . $row['Query_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Query_HTML'])[4]."</a></td>";
					echo "<td>" . str_replace("_", " ",explode("|", $row['Subject'])[6]) . "</td>";
					echo "<td><a href=\"" . $row['Subject_HTML'] . "\" target=\"_blank\">" . explode("/", $row['Subject_HTML'])[4]."</a></td>";						
					echo "<td>" . $row['Percentage'] . "</td>";
					echo "<td>" . $row['Alignment_Length'] . "</td>";
					echo "<td>" . $row['EValue'] . "</td>";
					echo "<td><a href=\"https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=" . $row['Query_Taxid'] . "\" target=\"_blank\">" . $row['Query_Taxid'] . "</td>";
					echo "<td><a href=\"https://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=" . $row['Subject_Taxid'] . "\" target=\"_blank\">" . $row['Subject_Taxid'] . "</td>";
					echo "</tr>";
		}
		echo "</tbody>";
		echo "</table>";
		echo "</div></div>";
	?>
</body>


</html>
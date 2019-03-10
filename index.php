<?php
if (isset($_POST["import"])) {
    
    $fileName = $_FILES["file"]["tmp_name"];
 	//class 
	class CSVUpload {
		
		//Field 
		protected $file;
		protected $rows = '';
		//parameterized Constructor
		public function __construct($file) {
			// echo $file;exit;
			$this->file = $file;
		}
		//Non static Method
		public function getArrayFromCSV() {
		    $csv = Array();
		    $rowcount = 0;
		    if (($handle = fopen($this->file, "r")) !== FALSE) {
		        $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
		        $header = fgetcsv($handle, $max_line_length);
		        $header_colcount = count($header);
		        while (($row = fgetcsv($handle, $max_line_length)) !== FALSE) {
		            $row_colcount = count($row);
		            if ($row_colcount == $header_colcount) {
		                $entry = array_combine($header, $row);
		                $csv[] = $entry;
		            }
		            else {
		                error_log("csvreader: Invalid number of columns at line " . ($rowcount + 2) . " (row " . ($rowcount + 1) . "). Expected=$header_colcount Got=$row_colcount");
		                return null;
		            }
		            $rowcount++;
		        }
		        //echo "Totally $rowcount rows found\n";
		        fclose($handle);
		    }
		    else {
		        error_log("csvreader: Could not read CSV \"$this->file\"");
		        return null;
		    }
		    return $csv;
		}
		//Static method or class method 
		public static function getHeaderRow($records) {
			// print_r($records);exit;
			$header = [];
			foreach($records[0] as $key => $value) {
			  $header[] = $key;
			}

			return $header;
		}
	}
}
?>
<!DOCTYPE html>
<html>

<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    $("#frmCSVImport").on("submit", function () {

	    $("#response").attr("class", "");
        $("#response").html("");
        var fileType = ".csv";
        var regex = new RegExp("([a-zA-Z0-9\s_\\.\-:])+(" + fileType + ")$");
        if (!regex.test($("#file").val().toLowerCase())) {
        	    $("#response").addClass("error");
        	    $("#response").addClass("display-block");
            $("#response").html("Invalid Or CSV File not in Proper Format File. Upload : <b>" + fileType + "</b> Files. Go to <a target='_blank' href='http://convertcsv.com/csv-viewer-editor.htm'>Link</a> and convert it into Proper format , then try again !!!.");
            return false;
        }
        return true;
    });
});
</script>
<style>
#response {
    padding: 10px;
    margin-bottom: 10px;
    border-radius: 2px;
    display:none;
}
.error {
    background: #fbcfcf;
    border: #f3c6c7 1px solid;
}

div#response.display-block {
    display: block;
}
</style>

</head>

<body>
    
    <div class="container">
  <div class="jumbotron">
    <h2>Import CSV file and Generate HTML Table</h2>
   
</div>

    <div  id="response" class="<?php if(!empty($type)) { echo $type . " display-block"; } ?>"><?php if(!empty($message)) { echo $message; } ?></div>
</div>
    <div class="container">
        <div class="row">

            <form  action="" method="post"
                name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data">
                <div class="input-row">
                    <label class="btn btn-primary">Choose CSV
                        File</label> </br></br><input type="file" name="file"
                        id="file" accept=".csv">
						</br>
                    <button type="submit" id="submit" name="import"
                        class="btn btn-primary">Generate HTML Table</button>
                    <br />
					<?php
					if (@$_FILES["file"]["size"] > 0) {  
		$file = fopen($fileName, "r");
		
		//instance of class is created 
    	$csvObj = new CSVUpload($_FILES["file"]["tmp_name"]); 
		//Invoking method via Object
        $arr = $csvObj->getArrayFromCSV();

?>
        <table id='userTable' class="table table-striped">
        <thead>
			<?php if(!empty($arr)) {?>
            <tr>
            	<?php 
				//Invoking static method via class
            	$headers = CSVUpload::getHeaderRow($arr);
            	foreach ($headers as $key => $header) { ?>
            		<th><?php echo $header;?></th>
            	<?php } ?>
                
            </tr>
		
			<?php } ?>
        </thead>
                    
                <tbody>
			
                <?php if(!empty($arr)) {?>
		            
		            	<?php 
		            	foreach ($arr as $key => $val) { ?>
		            		<tr>
			            	<?php foreach ($headers as $h_key => $header) { ?>
			            		<td><?php echo $val[$header];?></td>
			            	<?php } ?>
			            	</tr> 
			            <?php } ?>
		                
		            
				
					<?php } ?>
			
                </tbody>
        </table>
	<?php }?>
                </div>

            </form>

        </div>

    </div>

</body>

</html>
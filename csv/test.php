<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html><head>
<title>cvs exampe https://github.com/code2code/php-csv </title>
<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
<style>
	textarea{
		width:80%;
		min-width:800px;
		height:200px;
		font-size:1em;
	}
</style>
</head><body>
<?
include "csv_lib.php";

$con=file_get_contents("book.csv");
if($con===false){
	die("load file:book.cvs error");
}
echo "<h2>source data:</h2>";
echo '<textarea>',$con, '</textarea>';
?>


<?
$csv=new ZyCsv();
list($titles, $data)=$csv->unserialize($con);
echo "<h2>unserialize:</h2>";
echo "<textarea>";
print_r($titles);
print_r($data);
echo "</textarea>";
?>


<?
$mdata=$csv->map($titles, $data);
echo "<h2>map:</h2>";
echo "<textarea>";
print_r($mdata);
echo "</textarea>";
?>

<?
$titles=Array("author", "title");
$data=$csv->unmap($titles, $mdata);
echo "<h2>unmap:</h2>";
echo "<textarea>";
print_r($mdata);
echo "</textarea>";
?>

<?
$str=$csv->serialize($titles, $data);
echo "<h2>serialize:</h2>";
echo "<textarea>";
echo $str;
echo "</textarea>";
?>


</body>
</html>

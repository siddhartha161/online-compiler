


<?php


$sample_ip="";
$sample_code="";
$output=array();
$error=array();

function test($i,$ques)
{
		$name="../../sample/".$ques.$i."out.txt";
		$op="op.txt";
		$ah = fopen($name, 'r');
		 $bh = fopen("op.txt", 'r');

		$result = true;
		while(!feof($ah)&&!feof($bh))
		{	
			if(trim(fgets($ah))!= trim(fgets($bh)))
			{
			echo fgets($bh);
			$result = false;
			break;
			}
		}
		//echo "test".$i;
		fclose($ah);
		fclose($bh);
		
		return $result;
		
		
		



}
function fun($run,$ques)
{

		$i=1;
		$result=true;
		for($i=1;$i<=3;$i++)
		{	//echo "Test".$i;
			$name="../../sample/".$ques.$i."in.txt";
			//echo $name;
			system($run.'< '.$name.'>op.txt 2>&1');
			if(test($i,$ques)==false)
			{	$result=false;
				$output="Failed for Test Cases please try again";
				echo "<script>alert(\"$output\");</script>";
				break;
			}
		
		}
		
		if($result)
		{		
				$id=$_SESSION['SESS_MEMBER_ID'];
				$query=mysql_query("select * from register where id='$id'");
				$row=mysql_fetch_array($query);
				if($row)
				{
					$score=$row['score'];
						$time=$row['login_time'];
						$time1=$row['time'];
				
					$i=1;
					for($i;$i<=4;$i++)
					{
						if($ques==$i)
						{
							$question="question".$i;
							$stat=$row[$question];
						}
					
					}
				
				}
				if($stat==0)
				{	$i=0;
					for($i;$i<=4;$i++)
					{
						if($ques==$i)
						{
							$newscr=$i*50;
						}
					
					}
					$score=$score+$newscr;
					$tp=date('Y-m-d H:i:s');
					
						/*if(strtotime($time1)==0)
							$tp=date('Y-m-d H:i:s',strtotime($tp)-strtotime($time));
						else*/
							$tp=strtotime($tp)-strtotime($time);
						//echo $tp;
							
					$query=mysql_query("update register set ".$question."='1',score='$score',time='$tp' where id='$id'") or die(mysql_error());
				
					if($query)
					{
					$output="Succesfull for Test Cases";
					echo "<script>alert(\"$output\");</script>";
					}//my sql statement
					
				}
				else echo "<script>alert(\"You have already submitted correct answer for this question try another\");</script>";
				
		
		}


}


if(isset($_POST['submit']))
{
     $stat=$_POST['submit'];
	 $ques=$_POST['ques'];
     $ip=mysql_real_escape_string($_POST['ip']);

	if(isset($ip))
	{
	$ip=str_replace('\r',"",$ip);

   $ip=str_replace('\"','"',$ip);
	$f1=fopen("inp.txt",'w');


        
	$fi_str1=str_replace('\n',"\n",$ip);
	

	$fi_str1=$fi_str1;
	fwrite($f1,$fi_str1,strlen($fi_str1));
	
	
    $sample_ip=str_replace("\n",'&#13;&#10;',$fi_str1);
	
	
	}

   $code=mysql_real_escape_string($_POST['code']);
//$sample_code=$code;
  $ext=$_POST['extension'];
  $allowedExts = array("c", "c++", "java");
 //echo "hello in main";
		
if( isset($_FILES['file']['name']) && ($_FILES["file"]["size"] / 1024)>0) 
{     //echo "hello in file";
    $file_name=$_FILES["file"]["name"];
    $temp = explode(".", $_FILES["file"]["name"]);

      $extension = end($temp);
   if(($_FILES["file"]["size"] / 1024)>0)
    {
   //echo "hello";
     if (in_array($extension, $allowedExts))
      {
       if ($_FILES["file"]["error"] > 0)
       {
      echo "Error in file " . $_FILES["file"]["error"] . "<br>";
       }

       else
       {
    
	$info=pathinfo($_FILES['file']['name']);
    
	
	  $name=$_FILES['file']['name'];
	  $target = $name;
	  move_uploaded_file($_FILES["file"]["tmp_name"],
      $target);
      }
	  $codename=$temp[0];
	  $code1=$codename.".".$extension;
	  
     
     if($extension=="java")//if java
	 {
	 
	     
	     $cmd="javac"." ".$code1;

	    system($cmd.'>error.txt 2>&1');

       $error = file_get_contents("error.txt");
       if(empty($error))
        {
			
        $run="java"." ".$codename;
					if($stat=="test")
					{
						system($run.'< inp.txt >err1.txt 2>&1');
	
						$output=explode("\n",file_get_contents("err1.txt"));

						unlink($codename."."."class");
					}
					else if($stat=="submit")
					fun($run,$ques);
        }
       else
       {
        unset($output);

         $error = explode("\n",file_get_contents("error.txt"));
       }
	   
	  
	   
     }
	 else if($extension=="c")
	 {
	   $cmd="cc "." ".$code1;
	 
	   system($cmd.'>error.txt 2>&1');

	   $error = file_get_contents("error.txt");
		if(empty($error))
		{

					$run="./a.out";
					if($stat=="test")
					{
					system($run.'< inp.txt >err1.txt 2>&1');
					$output=explode("\n",file_get_contents("err1.txt"));
					unlink($run);
					}
					else if($stat=="submit")
					fun($run,$ques);
					
					
		}
	   else
	    {
	    unset($output);

	    $error = explode("\n",file_get_contents("error.txt"));
	    }
	 
	 
	 
	 }
	 else if($extension=="c++")
	 {
		
		$cmd="g++ "." ".$code1;
	 
	   system($cmd.'>error.txt 2>&1');

	   $error = file_get_contents("error.txt");
		if(empty($error))
		{

		$run="./a.out";
					if($stat=="test")
					{
					system($run.'< inp.txt >err1.txt 2>&1');
					$output=explode("\n",file_get_contents("err1.txt"));
					unlink($run);
					}
					else if($stat=="submit")
					fun($run,$ques);
		}
	   else
	    {
	    unset($output);

	    $error = explode("\n",file_get_contents("error.txt"));
	    }
	 
	 
	 
	 }
	 
	}
 }
  
  
}//end for file 
  
  

  
  

else if($code!="")// if  file not uploaded
{
//echo "hello";
  if($ext=='java')
  {

  $code=str_replace('\r',"",$code);

  $code=str_replace('\"','"',$code);

  $code=str_replace("\'","'",$code);

//get class name
  $class_pos=strpos($code,"class");

  $class_brac=strpos($code,"{");

  $start=$class_pos+6;
  $len=$class_brac-$start;


  $file=substr($code,$start,$len);
  $file2=explode(" ",$file);
  $file=str_replace('\n',"",$file2[0]);
	$file=trim($file);
//end of get class name



  $file_name=$file.".".$ext;


  $fp=fopen($file_name,'w');
  $i=0; 

        
	$fi_str=str_replace('\n',"\n",$code);
	

	$fi_str=$fi_str;
	fwrite($fp,$fi_str,strlen($fi_str));
	
	
  $sample_code=str_replace("\n",'&#13;&#10;',$fi_str);
	$sample_code=str_replace("\\","",$sample_code);

  $cmd="javac"." ".$file_name;

  system($cmd.'>error.txt 2>&1');

   $error = file_get_contents("error.txt");
   if(empty($error))
   {

    $run="java"." ".$file;
			if($stat=="test")
			{
				system($run.'< inp.txt >err1.txt 2>&1');
				$output=explode("\n",file_get_contents("err1.txt"));
				unlink($file."."."class");
			}
			else if($stat=="submit")
					{fun($run,$ques);}
  }
  else
  {
  unset($output);

  $error = explode("\n",file_get_contents("error.txt"));
  }
//else echo $error;



 //unlink($file_name);



}
  else if($ext=='c')
  {
   $code=str_replace('\r',"",$code);

  $code=str_replace('\"','"',$code);
	$code=str_replace("\'","'",$code);

    $file="file";
    $file_name=$file.".".$ext;


    $fp=fopen($file_name,'w');
    $i=0; 

       $fi_str=str_replace('\n',"\n",$code);
	

	$fi_str=$fi_str;
	fwrite($fp,$fi_str,strlen($fi_str));

   $sample_code=str_replace("\n",'&#13;&#10;',$fi_str);
	$sample_code=str_replace("\\","",$sample_code);
   $cmd="cc "." ".$file_name;
    //echo $cmd;
    system($cmd.'>error.txt 2>&1');

    $error = file_get_contents("error.txt");
    if(empty($error))
   {

   $run="./a.out";
				if($stat="test")
				{
				system($run.'< inp.txt >err1.txt 2>&1');
				$output=explode("\n",file_get_contents("err1.txt"));
				unlink($run);
				}
				else if($stat=="submit")
					fun($run,$ques);
  }
 
  else
  {
   unset($output);

   $error = explode("\n",file_get_contents("error.txt"));
  }

//unlink($file_name);
//unlink($run);


  }
  else if($ext=='c++')
  {
     $code=str_replace('\r',"",$code);

  $code=str_replace('\"','"',$code);
$code=str_replace("\'","'",$code);
    $file="file";
	$ext="cpp";
    $file_name=$file.".".$ext;


    $fp=fopen($file_name,'w');
    $i=0; 

       $fi_str=str_replace('\n',"\n",$code);
	
    $sample_code=str_replace("\n",'&#13;&#10;',$fi_str);
	$sample_code=str_replace("\\","",$sample_code);
	$fi_str=$fi_str;
	fwrite($fp,$fi_str,strlen($fi_str));

     
     $cmd="g++"." ".$file_name;

    system($cmd.'>error.txt 2>&1');

     $error = file_get_contents("error.txt");
    if(empty($error))
    {

    $run="./a.out";
				if($stat=="test")
				{
				system($run.'< inp.txt >err1.txt 2>&1');
				$output=explode("\n",file_get_contents("err1.txt"));
				unlink($run);
				}
				else if($stat=="submit")
					fun($run,$ques);
    }
 
   else
   {
   unset($output);

   $error = explode("\n",file_get_contents("error.txt"));
   }
	
  
  
  }

}

else  // either of cases  ot choosen
{

   //echo "hello";
   echo "<script>alert(\"please upload a file or write a code in given text area\");</script>";
}

}

?>

<div align="right"><a href="../../index.php" align="right">LOGOUT</a></div>
<script>
function Fun() {
str=document.getElementById('ques').value;

  if (str=="") {
    document.getElementById("txtHint").innerHTML="";
    return;
  } 
  if (window.XMLHttpRequest) {
    // code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else { // code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
    }
  }
  xmlhttp.open("GET","../../getdat.php?q="+str,true);
  xmlhttp.send();
}


</script>
<form name="" id="" method="post" action="" enctype="multipart/form-data" >
Select question number<select name="ques" id="ques" title="question" onchange="Fun()">
<option value="1">-</option>
<option value="1">Question 1</option>
<option value="2">Question 2</option>
<option value="3">Question 3</option>
<option value="4">Question 4</option>
</select>
<br />
Question:<br />
<div id="txtHint" >
</div>

<select name="extension" id="extension" >
<option value="java">JAVA</option>
<option value="c">C</option>
<option value="c++">C++</option>
</select>

<table>
<tr><td><div name="ans" id="ans" ><textarea name="code" id="code" rows="30" cols="140"><?php echo $sample_code; ?></textarea></div>
</td><td><div name="lead" id="lead"><?php 
$query9=mysql_query("select * from register order by score desc,time asc LIMIT 5");
echo "<table border=1 height=450 width=300><th>name/team name</th><th>score</th>";
while($row9=mysql_fetch_array($query9))
{
echo "<tr><td>".$row9['username']."</td><td>".$row9['score']."</td></tr>";
}
echo "</table>";
?></div></td></tr>
</table>
<div align="right" ><a href="../../lead.php" target="_blank">more...</a></div>
Inputs:
<textarea name="ip" id="ip" cols="184" rows="10" ><?php echo $sample_ip; ?></textarea><br />


<input type="file"  name="file" id="file"  /> 


<center><input type="submit" value="submit" id="submit" name="submit" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" value="test" id="submit" name="submit" /></center>

</table>
</form>

<div name="output" align="left" >
output:<br />
<?php if(isset($output)){$output=str_replace("\n","<br >",$output); echo implode("<br >",$output);}else{echo "compilation error <br />";$error=str_replace("\n","<br >",$error); echo implode("<br >",$error);} ?></div>

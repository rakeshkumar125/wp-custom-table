<?php
/*
	Example of how to user the class starts here
	
	$config['query'] = "SELECT * FROM issue_comments WHERE id = '".$_REQUEST["issue_id"]."'";
	$config['rows_per_page'] = 3;
	$config['page_no_variable']="page_no";
	$rec_page = new pagination($config);
	$row_array  = $rec_page->get_array();
	
	// show links functions - call where you want to display the pagination links
	$rec_page->show_links()
	
*/
//print_r($_REQUEST);

class pagination
	{
	var $query="";
	var $rows_per_page=10;
	var $page_no_variable="page_no";
	var $cur_page=1;
	var $total_rows=0;
	
	var $maxs=0;
	var $mins=0;
	
	var $total_pages=0;
	var $limit="";
	var $link_page_start="<li>";
	var $link_page_end="</li>";
	var $link_class="pagination";
	var $link_class_current="active";
	
	// The following Icons appears in front of the labels
	var $icon_asc="";
	var $icon_desc="";
	var $page_name="";
	
	
	// INITIALIZER
	function initialize($params = array())
	{
		if (count($params) > 0)
		{
			foreach ($params as $key => $val)
			{
				if (isset($this->$key))
				{
					$this->$key = $val;
				}
			}
		}
	}
	
	
		// RECORDS
	function perpage_selector()
	{
		?>
		<form name="per_page2" action="" method="get">
			Show Per page
			<?php
			if($_REQUEST['per_page']==100)
			{
				$per_page1="selected='selected'";
			}
			else if($_REQUEST['per_page']==50)
			{
				$per_page2="selected='selected'";
			}
			else if($_REQUEST['per_page']==20)
			{
				$per_page3="selected='selected'";
			}
			else
			{
				$per_page4="selected='selected'";
			}
			
			?>
			<select name="per_page" id="per_page" onChange='location.replace("<?php
			$data= $_REQUEST;
			if(isset($data["per_page"]))
			{
				unset($data["per_page"]);
			}
			echo $_SERVER['PHP_SELF']."?".http_build_query($data); ?>&<?php echo "per_page";?>="+$("#per_page").val() )'>
			
				<option <?php echo $per_page4;?>>10</option>
				<option <?php echo $per_page3;?>>20</option>
				<option <?php echo $per_page2;?>>50</option>
				<option <?php echo $per_page1;?>>100</option>
			</select>
			
		</form>
		
		<?php
	}
	
	// CONSTRUCTOR
	function pagination($params=array())
	{
		if (count($params) > 0)
		{
			$this->initialize($params);
		
		}	
		if(isset($_REQUEST['per_page'])){
			$this->rows_per_page = $_REQUEST['per_page'];
			
		}
		$this->get_current_page();
		$this->query_limit();
		$this->get_pagename();
		
	}
	function get_current_page()
	{
		if(isset($_REQUEST[$this->page_no_variable]))
		{
		$this->cur_page=$_REQUEST[$this->page_no_variable];
		}
		
	}
	
	function sortable_label($table_field_name,$label)
	{
	//echo $config['query'];
		$data = $_REQUEST;
		
		// REMOVING THE PREVIOUS sort_by COLUMN. 
		if(isset($data['sort_by']))
		{
			unset($data['sort_by']);
		}
		
		// CHECKING TO SEE IF THE LABLE VALUE IS EQUAL TO THE CURRENT SELECTION
		$sort_img ="";
		if($_REQUEST['sort_by']==$table_field_name && ($_REQUEST['sort_direction']=="" || $_REQUEST['sort_direction']=="asc"))
		{
			$data['sort_by'] = $table_field_name;
			$data['sort_direction']='desc';
			$sort_img="<img src='".$this->icon_asc."' />";
			
		}
		else
		{
			$data['sort_by'] = $table_field_name;
			$data['sort_direction']='asc';
			if($_REQUEST['sort_by']==$table_field_name)
			{
				$sort_img="<img src='".$this->icon_desc."' />";
			}
			
			
		}
		echo "<a href='".$_SERVER['PHP_SELF']."?".http_build_query($data)."' >"."".$sort_img." ".$label."</a>";
	}
	
	 
	// GET ARRAY 
	function query_limit()
	{
		//echo $this->query;
		//exit;
		// $data = mysql_query($this->query) or die("123".mysql_error()); 
		 global $wpdb;
		 $data = $wpdb->get_results($this->query);

		 $rows = count($data);
		 $this->total_rows 	=  $rows ;
		if($rows>0)
		 {
		 $last = ceil($rows/$this->rows_per_page); 
		 $this->total_pages=$last;
		 if ($this->cur_page < 1) 
 		{ 
			 $this->cur_page = 1; 
		} 
		 elseif ($this->cur_page > $last) 
		 { 
			 $this->cur_page = $last; 
		 } 
		 $this->limit = ' limit ' .($this->cur_page - 1) * $this->rows_per_page .',' .$this->rows_per_page; 
		 }
		 else
		 {
			  $this->limit = '';
			  $this->total_pages=0;
		 }
	}
	
	function get_pagename() 
	{
 		$this->page_name = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	}
	
	function get_array()
	{
	
	//echo '<pre>';
	//	print_r($_REQUEST);
			$result_array=array();
			
			if(isset($_REQUEST['sort_by']) && $_REQUEST['sort_by']!="" )
			//if(($_REQUEST['sort_by']) && ($_REQUEST['sort_by']!=""))
			{
				$order_by = " order by ".$_REQUEST['sort_by']." ".$_REQUEST['sort_direction']. " ";
				//$filter_by = " and select_option ="."'".$_REQUEST['filter_by']."'";
			//	echo $order_by;
				//order by question_answer asc
				
			}
			else
			{
				$order_by = "";
			}
			//echo $this->query.$order_by.$this->limit;
			//exit();
			//echo $this->query.$order_by.$this->limit;
			global $wpdb;
			//$result=mysql_query($this->query.$order_by.$this->limit) or die("dsadsad".mysql_error());
			$result_array = $wpdb->get_results($this->query.$order_by.$this->limit);
			/*if(mysql_num_rows($result)>0)
			{
				while($row=mysql_fetch_array($result))
				{
						$result_array[]=$row;
						
				}
			}*/
		/*	echo "<pre>";
			print_r($result_array);
			echo "</pre>";*/
			return $result_array;
	}
	
	function pagebreak($cur,$tot)
	{
		
		if($cur >= 7)
		{
			$this->mins = $cur;
			$this->maxs = (($cur+10) >= $tot)?$tot:$cur+10;
		}
		else
		{
			$this->mins = 1;
			$this->maxs = (10 > $tot)?$tot:10;
		}
		
	
		
	}
	function show_links($query_string="")
	{
		//var $temp_link_start="";
		$this->pagebreak($_REQUEST['page_no'],$this->total_pages);

		if($this->mins  > 1)
		{
			//$link = "<span class='".$this->link_class."'><a  href='".$this->page_name."?".$this->page_no_variable."=".(($this->mins)-1)."".$query_string."'>&laquo; Previous</a></span>&nbsp;&nbsp;";
			
			$link = "<li class='".$this->link_class."'><a  href=".admin_url('admin.php?page=custompage')."&".$this->page_no_variable."=".(($this->mins)-1)."".$query_string.">&laquo; Previous</a></li>&nbsp;&nbsp;";
			
			echo $link;
		}

		for($i=$this->mins;$i<=$this->maxs;$i++){
			
			// Adding Classes to the Start TAG
			if($i==$this->cur_page){
				$temp_link_start= str_replace(">", " class='".$this->link_class_current."'>",$this->link_page_start);
			} else {
				$temp_link_start= str_replace(">", " class='".$this->link_class."'>",$this->link_page_start);
			}
			$link = "<a href='".admin_url('admin.php?page=custompage')."&".$this->page_no_variable."=".$i.$query_string."'>".$i."</a>";
			echo $temp_link_start.$link.$this->link_page_end;
			
		}
		if($this->maxs  < $this->total_pages)
		{

		$link = " <li class='".$this->link_class."'><a class='".$this->link_class_current."' href='".admin_url('admin.php?page=custompage')."&".$this->page_no_variable."=".(($this->maxs)+1).$query_string."'>Next &raquo;</a></li>";
		echo $link;
		}
	
	}

// Function to fix the link displays

	function showlinks_n($query_string=""){
	// If the Page number is below 7
	
	}

	
	
// Old function 

	function pagebreak_google_type($cur,$tot)
	{
		
		if($cur >= 7)
		{
			$this->mins = $cur-5;
			$this->maxs = (($cur+5) >= $tot)?$tot:$cur+5;
		}
		else
		{
			$this->mins = 1;
			$this->maxs = (10 > $tot)?$tot:10;
		}
		
	
		
	}

function show_links_google_type($hash_string="")
	{
		$cur_page_number=1;
		$link="";
		$querystring_array = $_REQUEST;
		

		if(isset($_REQUEST[$this->page_no_variable]))
		{
			$cur_page_number=$_REQUEST[$this->page_no_variable];
		}
		
		$querystring_array[$this->page_no_variable]=$cur_page_number;
		
		
		
		
			
			
		$this->pagebreak_google_type($cur_page_number,$this->total_pages);

		
		
		if($cur_page_number!=1)
		{
		$querystring_array[$this->page_no_variable] = 1;
		$query_string = http_build_query($querystring_array);	
		$link .= "<span class='".$this->link_class."' ><a href='".$this->page_name."?".$query_string.$hash_string."'>First</a></span>";
		
		
		$prev_link=	$cur_page_number-1;
		$querystring_array[$this->page_no_variable] = $prev_link;
		$query_string = http_build_query($querystring_array);	
		$link .= "<span class='".$this->link_class."'><a href='".$this->page_name."?".$query_string.$hash_string."'>&laquo; Prev</a></span>&nbsp;";
		echo $link;
		}



			for($i=$this->mins;$i<=$this->maxs;$i++)
		{
			
			
			// Adding Classes to the Start TAG
			if($i==$this->cur_page)
			{
			//exit();
					$temp_link_start= str_replace(">", " class='".$this->link_class_current."'>",$this->link_page_start);
			}
			else
			{
					$temp_link_start= str_replace(">", " class='".$this->link_class."'>",$this->link_page_start);
			}
			$querystring_array[$this->page_no_variable] = $i;
			$query_string = http_build_query($querystring_array);	
			
			
			if($cur_page_number==$i)
			{
				$current_class = "class='".$this->link_class_current."'";
			}
			
			if($i==$this->cur_page)
			{
				$linkclass = $this->link_class_current;
			}
			else
			{
				$linkclass = $this->link_class;
			}
			
			$link = "<span class='".$linkclass."' ><a ".$current_class." href='".$this->page_name."?".$query_string.$hash_string."'>".$i."</a></span>";
			echo $temp_link_start.$link.$this->link_page_end;
			$current_class="";
		}
		
		$link="";

		if($cur_page_number!=$this->total_pages)
		{
		$prev_link=	$cur_page_number+1;
		$querystring_array[$this->page_no_variable] = $prev_link;
		$query_string = http_build_query($querystring_array);	
		
		
		$link .= "&nbsp;<span class='".$this->link_class."' ><a href='".$this->page_name."?".$query_string.$hash_string."'>Next&nbsp;&raquo;</a></span>";
		
		$querystring_array[$this->page_no_variable] = $this->total_pages;
		$query_string = http_build_query($querystring_array);	
		$link .= "<span class='".$this->link_class."' ><a href='".$this->page_name."?".$query_string.$hash_string."'>Last </a></span>";
		echo $link;

		
		}


		
			
	}

// Old function  ends here 
	function show_total_records()
	{
		return $this->total_rows;
	
	}
	
}// Class Ends here 
	


?>
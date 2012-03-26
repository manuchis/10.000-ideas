<?php
//-=-=-=-= the variables =-=-=-=-
$total_pages = ceil($total / $per_page); //the total pages
$first_page = 0;
$last_page = $total_pages - 1;

$prev_page = $page - 1;
if($prev_page < 0)
	$prev_page = 0;

$next_page = $page + 1;
if($next_page > $total_pages - 1)
	$next_page = $total_pages - 1;
	
$display = $total_pages;
$start_count = $page - floor($display / 2);
$end_count = $start_count + $display;

if($end_count > $total_pages)
{
	$start_count = $total_pages - $display;
	$end_count = $start_count + $display;
}

if($start_count < 0)
{
	$start_count = 0;
	$end_count = $start_count + $display;
}

if($end_count > $total_pages)
{
	$end_count = $total_pages;
}
?>

<?php if($total_pages > 1){ ?>
<div id="pager">	
<ul>
<!--	<li>P&aacute;gina <?php echo $page + 1; ?> de <?php echo $total_pages; ?></li>
	<li><a href="<?php echo $link; ?>&page=<?php echo $first_page; ?>" title="Primera p&aacute;gina">&laquo;</a></li>
	<li><a href="<?php echo $link; ?>&page=<?php echo $prev_page; ?>" title="P&aacute;gina anterior">&lsaquo;</a></li>

	
	<?php if($end_count > $first_page + $display){?>
	<li class="dots">...</li>
	<?php } ?>
-->	
	<?php
		for($i = $start_count; $i < $end_count; $i++){
			$p = $i + 1 ;
			$class = "text";
			if($i == $page)
				$class = "active";	
	?>
	<li><a class="<?php echo $class; ?>" href="<?php echo $link; ?>&page=<?php echo $i; ?>" title="P&aacute;gina <?php echo $p; ?>"><?php echo $p; ?></a></li>
	<?php } ?>
	
	<?php if($start_count < $last_page - $display + 1){ ?>
	<li class="dots">...</li>
	<?php } ?>
	
<!--	<li><a href="<?php echo $link; ?>&page=<?php echo $next_page; ?>" title="P&aacute;gina siguiente">&rsaquo;</a></li>
	<li><a href="<?php echo $link; ?>&page=<?php echo $last_page; ?>" title="Ultima p&aacute;gina">&raquo;</a></li>
-->
<div class="clearfix"><!-- --></div>

</ul>
</div>
<?php } ?>
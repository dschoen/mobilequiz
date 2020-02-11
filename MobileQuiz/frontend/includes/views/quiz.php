<?php render('_header',array('title'=>$title, 'latex_active'=>$latex_active))?>

<form id="form1" action="index.php" method="post">
	<fieldset>
		<ul data-role="listview" data-inset="true" data-theme="a" data-dividertheme="c">
			
			<?php render($questions) ?>
			
			<li class="question">
				<input type="hidden" id="round_id" name="round_id" value="<?php echo $_GET['round_id'];?>" />
				<input type="hidden" id="quiz_id" name="quiz_id" value="<?php echo $_GET['quiz_id'];?>" />
				<button type="submit" data-theme="a" name="submit" value="submitAnswers">Submit</button>
			</li>
		</ul>
	</fieldset>
</form>

<?php render('_footer')?>
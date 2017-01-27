<li class="question">
    <?php
    // If you want to add new question types, you must modify this file here.
    
    echo polishText($question->text);

    $type_of_question = array( "type_of_question" => $question->type);
    // This variable is neccesary for the generation of choices.

    switch($question->type) {
            case "1":
	            // multiple choice
	            ?>
				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup" id="question<?php echo $question->question_id;?>">
						<?php render($question->choices,$type_of_question);?>
					</fieldset>
				</div>
	            <?php
				break;

            case "2":
            	// single choice
				?>
				<div data-role="fieldcontain">
					<fieldset data-role="controlgroup" id="question<?php echo $question->question_id;?>">
						<?php render($question->choices,$type_of_question);?>
					</fieldset>
				</div>
            	<?php
				break;

            case "3":
            	// numeric
				?>
				<div data-role="fieldcontain">
					<?php render($question->choices,$type_of_question);?>
				</div>
            	<?php
                break;
               
            case "4":
               	// text
               	?>
               	<div data-role="fieldcontain">
	      			<fieldset data-role="controlgroup" id="question<?php echo $question->question_id;?>">
	        			<?php render($question->choices,$type_of_question);?>
	        		</fieldset>
        		</div>
               	<?php
                break;
    }
    ?>
</li>
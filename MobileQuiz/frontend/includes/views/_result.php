<li class="question">
    <?php
    // If you want to add new question types, you must modify this file here.
    echo $question->text;

    $answer = array();
    $type_of_question = Array( "type_of_question" => $question->type, "result" => $result);
    // This variable is neccesary for the generation of choices.

    switch($question->type) {
        case "1":
            // multiple choice
            $type_for_choice = "1";
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
            $type_for_choice = "2";
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
            $type_for_choice = "3";
            ?>
            <div data-role="fieldcontain">
                <?php render($question->choices,$type_of_question);?>
            </div>
            <?php
            break;
    }
    ?>
    <div>
    <?php if (!empty($question->solution)) { ?>
    	<a id="button-solution-<?php echo $question->question_id;?>" 
    		class="button-optional"
    		href="#"
    		>Solution</a>
    		    
	    <div id="text-button-solution-<?php echo $question->question_id;?>" class="solution text-info">
	    	 Solution: <?php echo $question->solution;?>
	    </div>
    <?php } ?>
    
    <?php if (!empty($question->furthermore)) { ?>
	    <a id="button-furthermore-<?php echo $question->question_id;?>" 
	    	class="button-optional"
	    	href="#"
	    	>Further Information</a>
	    
	    <div id="text-button-furthermore-<?php echo $question->question_id;?>" class="furthermore text-info">
	    	 Further Information: <?php echo $question->furthermore;?>
	    </div>
     <?php } ?>
    </div>
</li>

<!-- JAVASCRIPT ---------------------------------------------------------------- -->

<script language="javascript" type="text/javascript">

//bind additional info buttons
$( ".button-optional" ).click(function(event) {
	$("#"+event.target.id).hide();
	$("#text-"+event.target.id).show();	  
});

</script>
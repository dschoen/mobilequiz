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
</li>
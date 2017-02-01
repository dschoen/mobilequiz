<?php

// Feedback ermitteln
$symbole = "";
$color = "";

if ( !empty( $result ) ) {
    $correct_value = $choice->correct_value;    

    if ( $correct_value == "0" && $result[$choice->choice_id] == "1" ) {
    	// Falsch und gewählt
        $symbole = "incorrect";
        $color = "red";
    }
    if ( $correct_value == "0" && $result[$choice->choice_id] == "0" ) {
    	// Falsch und nicht gewählt
        $symbole = "incorrect";
        $color = "";
    }
    if ( $correct_value == "1" && $result[$choice->choice_id] == "1" ) {
    	// Richtig
    	$symbole = "correct";
    	$color = "green";
    }
    if ( $correct_value == "1" && $result[$choice->choice_id] == "0" ) {
    	// Richtig aber nicht gewählt
    	$symbole = "correct";
    	$color = "";
    }
    if ( $correct_value == "2" && $result[$choice->choice_id] == "1" ) {
    	// Richtig
    	$symbole = "neutral";
    	$color = "blue";
    }
    if ( $correct_value == "2" && $result[$choice->choice_id] == "0" ) {
    	// Richtig aber nicht gewählt
    	$symbole = "neutral";
    	$color = "";
    }
}

// If you want to add new question types, you must modify this file here.
switch($type_of_question) {
    case "1":
        // multiple choice
        ?>
        <?php
        if ( !empty( $result ) ) {
            ?>
            <ul style="margin-bottom: 10px;">
                <li>
                    <div style="width: 19px; float: left;"><?php if ( !empty( $symbole ) ) : ?>
                    <img src="assets/img/<?php echo $symbole; ?>.png" alt="" title="" width="16" />
                    <?php else: ?>&nbsp;<?php endif; ?></div>
                    <span>&nbsp;-&nbsp;</span> 
                    <span style="color:<?php echo $color; ?>"><?php echo $choice->text; ?></span>
                </li>
            </ul>
            <div style="clear: both;"></div>
        <?php
        } else {
            ?>
            <input type="checkbox" name="checkbox-choice-<?php echo $choice->question_id.$choice->choice_id ?>" id="checkbox-choice-<?php echo $choice->question_id.$choice->choice_id ?>" value="<?php echo $choice->choice_id ?>" />
            <label for="checkbox-choice-<?php echo $choice->question_id.$choice->choice_id ?>">
                <?php echo polishText($choice->text); ?>
            </label>
        <?php
        }
        break;

    case "2":
        // single choice
        if ( !empty( $result ) ) {
            ?>
            <ul style="margin-bottom: 10px;">
                <li>
                    <div style="width: 19px; float: left;"><?php if ( !empty( $symbole ) ) : ?><img src="assets/img/<?php echo $symbole; ?>.png" alt="" title="" width="16" /><?php else: ?>&nbsp;<?php endif; ?></div> <span style="color:<?php echo $color; ?>"><?php echo $choice->text; ?></span>
                </li>
            </ul>
            <div style="clear: both;"></div>
        <?php
        } else {
            ?>
            <input type="radio" name="radio-choice-<?php echo $choice->question_id ?>" id="radio-choice-<?php echo $choice->question_id.$choice->choice_id ?>" value="<?php echo $choice->choice_id ?>" />
            <label for="radio-choice-<?php echo $choice->question_id.$choice->choice_id ?>">
                <?php echo polishText($choice->text); ?>
            </label>
        <?php
        }
        break;

    // numeric question
    case "3":
        $slider_values = (explode(';',$choice->text));
    
        // $slider[0] = minimum
        // $slider[1] = maximum
        // $slider[2] = step range
        // $slider[3] = correct value
        if ( !empty( $result ) ) {
			$incorrectSymbol = "incorrect";
			$correctSymbol = "correct";

           	$userChoice = $result[$choice->choice_id];
           	$correctNumericValue = $slider_values[3];

 			if ($userChoice!=$correctNumericValue) {
            	
				?>
				<ul style="margin-bottom: 10px; list-style-type:none">
					<li>
						<div style="width: 19px; float: left;">
							<?php if ( !empty( $incorrectSymbol ) && $correctNumericValue!="" ) : 
							$colorUserChoice = "red"; ?>
							
							<img src="assets/img/<?php echo $incorrectSymbol; ?>.png" alt="" title="" width="16" />
							<?php else: 
							$colorUserChoice = "";
							?>&nbsp;<?php endif; ?>
						</div>
						<span style="color:<?php echo $colorUserChoice; ?>">
					            	<?php echo $result[$choice->choice_id];  ?></span>
					</li>
				</ul>
				<div style="clear: both;"></div>    	
				    			
				            
			<?php } else {
			
				$colorCorrectValue = "green";
			} 
			
			if ($correctNumericValue!="") {	?>				
					
            <ul style="margin-bottom: 10px; list-style-type:none">
            	<li>
	            	<div style="width: 19px; float: left;"><?php if ( !empty( $correctSymbol ) ) : ?><img src="assets/img/<?php echo $correctSymbol; ?>.png" alt="" title="" width="16" /><?php else: ?>&nbsp;<?php endif; ?></div>
	            	<span style="color: <?php echo $colorCorrectValue; ?>"><?php echo $slider_values[3];  ?></span>
				</li>
			</ul>
            <div style="clear: both;"></div>
        	<?php }
        } else {
            ?>
            <input type="range" name="numeric-<?php echo $choice->choice_id ?>" id="numeric-<?php echo $choice->choice_id ?>"
                   value="<?php echo $slider_values[0] ?>"
                   min="<?php echo $slider_values[0] ?>"
                   max="<?php echo $slider_values[1] ?>"
                   step="<?php echo $slider_values[2] ?>" />
        	<?php
        }
        break;
        
    // text question
	case "4":
		if ( !empty( $result ) ) {  ?>
        	<ul>
            	<li>
            		<div class="result-textfield"><?php echo $result[$choice->choice_id]?> </div>

	        		<?php if (!empty($choice->text)) { ?>
	        			<div class="result-textfield solution-textfield"><?php echo $choice->text; ?></div>
	        		<?php } ?>   		
	        	</li>
	        </ul>
	        <div style="clear: both;"></div>
        
        <?php } else { ?>   
                    
        	<textarea 
				class="text-choice-textarea"
        		name="textual-choice-<?php echo $choice->choice_id ?>" 
        		id="textual-choice-<?php echo $choice->question_id."-".$choice->choice_id ?>" 
        		></textarea>

        <?php }
        break;
}
?>

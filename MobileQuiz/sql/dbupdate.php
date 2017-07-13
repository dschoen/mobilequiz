<#1>
<?php
$definition_quizzes = array(
    'quiz_id' => array(
            'type'      => 'integer',
            'length'    => 8,
),
    'name' => array(
            'type'      => 'text',
            'length'    => 4000,
),
    'type' => array(
            'type'      => 'text',
            'length'    => 25,
            'default'   => 'normal'
    )

);
$ilDB->createTable("rep_robj_xuiz_quizzes", $definition_quizzes);
$ilDB->addPrimaryKey("rep_robj_xuiz_quizzes", array("quiz_id"));
$ilDB->createSequence("rep_robj_xuiz_quizzes");

$definition_questions = array(
    'question_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'quiz_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'type' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'text' => array(
            'type'     => 'text',
            'length'   => 4000
),
    'question_order' => array(
            'type'     => 'integer',
            'length'   => 8
),
);
$ilDB->createTable("rep_robj_xuiz_qs", $definition_questions);
$ilDB->addPrimaryKey("rep_robj_xuiz_qs", array("question_id"));
$ilDB->createSequence("rep_robj_xuiz_qs");


$definition_choices = array(
    'choice_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'choice_order' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'question_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'correct_value' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'text' => array(
            'type'     => 'text',
            'length'   => 4000
)
);
$ilDB->createTable("rep_robj_xuiz_choices", $definition_choices);
$ilDB->addPrimaryKey("rep_robj_xuiz_choices", array("choice_id"));
$ilDB->createSequence("rep_robj_xuiz_choices");

$definition_rounds = array(
    'round_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'quiz_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'start_date' => array(
            'type'     => 'timestamp'
),
    'end_date' => array(
            'type'     => 'timestamp'
),
    'tiny_url' => array(
            'type'     => 'text',
            'length'   => 155,
),
    'type' => array(
            'type'     => 'text',
            'length'   => 155,
            'default' => 'normal'
    )
);
$ilDB->createTable("rep_robj_xuiz_rounds", $definition_rounds);
$ilDB->addPrimaryKey("rep_robj_xuiz_rounds", array("round_id"));
$ilDB->createSequence("rep_robj_xuiz_rounds");

$definition_answers = array(
    'answer_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'round_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'choice_id' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'value' => array(
            'type'     => 'integer',
            'length'   => 8,
),
    'user_string' => array(
            'type'     => 'text',
            'length'   => 100,
)
);
$ilDB->createTable("rep_robj_xuiz_answers", $definition_answers);
$ilDB->addPrimaryKey("rep_robj_xuiz_answers", array("answer_id"));
$ilDB->createSequence("rep_robj_xuiz_answers");
?>

<#2>
ALTER TABLE rep_robj_xuiz_qs ADD COLUMN solution VARCHAR(4000);
ALTER TABLE rep_robj_xuiz_qs ADD COLUMN furthermore VARCHAR(2000);
<#3>
ALTER TABLE rep_robj_xuiz_answers MODIFY value VARCHAR(500);
<#4>
ALTER TABLE rep_robj_xuiz_answers ADD INDEX (user_string);
<#5>
<?php
$definition_config = array(
    'item' => array(
            'type'      => 'text',
            'length'    => 40,
		),
    'value' => array(
            'type'      => 'text',
            'length'    => 100,
		),
);
$ilDB->createTable("rep_robj_xuiz_config", $definition_config);
?>
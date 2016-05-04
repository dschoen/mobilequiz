<?php render('_header',array('title'=>$title))?>

    <center><img src="assets/img/ok.png" /></center>
<?php
// Passiver Modus
if ( $type == "passive" ) {
    ?>
    <ul data-role="listview" data-inset="true" data-theme="c" data-dividertheme="c">
        <?php
        foreach ( $result as $r ) {
                $answer[$r->choice_id] = $r->value;
            }
        render($questions, array("templateFile" => "result", "result" => $answer));
        ?>
    </ul>
    <?php
}
?>

<?php render('_footer')?>
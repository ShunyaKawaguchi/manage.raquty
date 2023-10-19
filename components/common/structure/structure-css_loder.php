<?php //structureのcss読み込み 
 $structures = array('sidebar','tournament_edit_menu');
?>
<?php foreach($structures as $single_structure):?>
    <link rel="stylesheet" href="/components/common/structure/<?php echo $single_structure ?>/<?php echo $single_structure ?>.min.css">
<?php endforeach?>
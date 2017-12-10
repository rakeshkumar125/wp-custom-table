<?php 

if(isset($atts['id'])) { 
$post_id =  $atts['id'];
$table_name = get_post_meta($post_id, 'wpc_table_name', true );
    ?>
<h2 style="margin-top: 40px; margin-bottom: 40px;"><?php echo get_the_title($post_id); ?></h2>

<table id="example" class="display" cellspacing="0" width="99%">
    <thead>
        <tr>
        <?php $wpc_columns_name = get_post_meta($post_id, 'wpc_columns_name', true );
        $table_columns = array_keys($wpc_columns_name);
        $table_title = array_values($wpc_columns_name);
         ?>
            <?php 
            $i=0;
            foreach ($table_title as $np) { ?>
            <th><?php if(strlen($np)>0){ echo $np; } else { echo $table_columns[$i]; } ?></th>
            <?php } ?>
          </tr>
    </thead>
    
</table>
<script type="text/javascript" language="javascript" class="init">
jQuery(document).ready(function() {
      jQuery('#example').DataTable( {
        "processing": true,
        "serverSide": true,
        "ajax": "admin-ajax.php?action=get_wp_custom_table&table_name=<?php echo $table_name; ?>&post_id=<?php echo $post_id; ?>"   
         } );
} );
    </script>
    <?php } else { echo "<h1>Table name my be incorrect or not exist ! </h1>"; } ?>
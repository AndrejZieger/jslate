<?php 
	// If I want more secure ajax request
	// I can set csrfUseOnce=false and use this one token
	//echo $this->Form->create('sectokn',array('id'=>'sectoken'));
    //echo $this->Form->end();
?>
<?php
	$zid=1;
    foreach ($dashboard['Dbview'] as $dbview){
		$zid++;
        $id = $dbview['id'];
        $code = $dbview['code'];
        echo "<textarea id='code$id' style='display: none;'>$code</textarea>";
        echo "<div class='dragbox' id='dragbox_$id'
        style=' overflow: hidden; position: absolute; z-index: $zid; left: ".$dbview['left']."px; top: ".$dbview['top']."px; width: ".($dbview['width'])."px; height: ".($dbview['height'])."px;'>
            <div class='header'>
                <span>&nbsp;";
                echo $this->Html->link('x', "/dbviews/delete/$id", array('style' =>'float: right; margin-left: 10px;'));
                echo $this->Html->link('edit', "/dbviews/edit/$id", array('style' =>'float: right;'));
                echo "</span>";
            echo "</div>
            <div class='dragbox-content' id='view$id' style='clear: both; width: ".($dbview['width']-10)."px; height: ".($dbview['height']-30)."px;'>".
            str_replace($replacements['search'], $replacements['replace'], $code)
            ."</div>
        </div>";
    }
    ?>
    <script type='text/javascript'>
    	(function( $ ){
		$.fn.serializeJSON=function() {
		var json = {};
		jQuery.map($(this).serializeArray(), function(n, i){
		json[n['name']] = n['value'];
		});
		return json;
		};
		})( jQuery );
        $(function(){

            $(".dragbox").draggable({
                handle: ".header",
                grid: [10, 10],
                stop: function(event, ui){
                    console.log(ui);
                    var id = ui.helper.context.id.split('_')[1];
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->Html->url('/dbviews/update/')?>"+id,
                        data: {data:{left: ui.position.left, top: ui.position.top}},
                        success: function(msg){
                            console.log(msg);
                        }
                    });
                }
            });
            $(".dragbox").resizable({
                grid: [10, 10],
                stop: function(event, ui){
                    
                    var id = ui.helper.context.id.split('_')[1];
                    $.ajax({
                        type: "POST",
                        url: "<?php echo $this->Html->url('/dbviews/update/')?>"+id,
                        data: {data:{width: ui.size.width, height: ui.size.height}},
                        success: function(msg){
                            $('#view'+id).height((ui.size.height-30)+'px')
                            $('#view'+id).width((ui.size.width-10)+'px')
                            $('#view'+id).html($('#code'+id).val())
                            console.log(msg);
                        }
                    });
                }
            });
        });
    </script>

<div id="HyperComments_Box">
<?php if(!hc_searchbot_detect($_SERVER['HTTP_USER_AGENT'])):?>
	<?php echo hc_show_script();?>	
<?php else:?>	
	<?php $comments = hc_get_comments_post();?>		
	<?php for($i=0;$i<count($comments);$i++):?>	
	<div style='position:relative;padding:5px;font-size:12px;'>
		<div  style='position:absolute;'><?php echo get_avatar($comments[$i]->user_id)?></div>
		<div style='margin-left:50px;'>
			<div style='float:left;margin-right:5px;color:#3B5998;font-size: 11px;font-family: tahoma,verdana,arial,sans-serif;font-weight: bold;'><?php echo $comments[$i]->comment_author;?></div>
			<div style='color: gray;font-size:10px;'><?php echo $comments[$i]->comment_date;?></div>
			<div style='padding:5px;'><?php echo $comments[$i]->comment_content;?></div>
		</div>
	</div>
	<?php endfor;?>
<?php endif;?>
</div>
<?php $posts = $cms->get_post(); if($posts) foreach($posts as $post){ ?>
	<article class="content-box">
		<h2>
			<a class="ajax-link" href="/<?php echo $post['Path']; ?>"><?php echo $post['Title']; ?></a>
			<?php if($login->is_admin()){ ?>
				<form method="post">
					<input type="hidden" name="post_id" value="<?php echo $post['ID']; ?>" />
					<input type="submit" name="delete_post" value="x" title="Delete this post." />
				</form>
			<?php } ?>
		</h2>
		<h3 class="timestamp">
			Posted on: <?php echo date('F j', $post['Timestamp']).'<sup>'.date('S', $post['Timestamp']).'</sup>'.date(', Y', $post['Timestamp']). ' / '.date('h:i A', $post['Timestamp']); ?>
			<span class="author">By: <span class="lime"><?php echo $post['Author']; ?></span></span>
		</h3>
		<?php echo $post['Content']; ?>
	</article>
<?php } ?>

<?php echo $cms->editor(); ?>

<?php $children = $cms->get_children(); if($children) foreach($children as $child){ ?>
	<article class="content-box">
		<h2>
			<a class="ajax-link" href="/<?php echo $child['Path']; ?>"><?php echo $child['Title']; ?></a>
			<?php if($login->is_admin()){ ?>
				<form method="post">
					<input type="hidden" name="post_id" value="<?php echo $child['ID']; ?>" />
					<input type="submit" name="delete_post" value="x" title="Delete this post." />
				</form>
			<?php } ?>
		</h2>
		<h3 class="timestamp">
			Posted on: <?php echo date('F j', $child['Timestamp']).'<sup>'.date('S', $child['Timestamp']).'</sup>'.date(', Y', $child['Timestamp']). ' / '.date('h:i A', $child['Timestamp']); ?>
			<span class="author">By: <span class="lime"><?php echo $child['Author']; ?></span></span>
		</h3>
		<?php echo $child['Content']; ?>
	</article>
<?php } ?>
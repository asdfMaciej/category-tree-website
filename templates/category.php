<div class="menu-category">
	<a href="?category=<?=$id?>" class="menu-category__name">
		<?=$name?> (<?=count($items)?>)
	</a>
	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="menu-category__delete">
		<input type="hidden" name="action" value="delete_category">
		<input type="hidden" name="id" value="<?=$id?>">
		<input type="submit" value="X">
	</form>

	<div class="menu-category__subcategories">
		<?=$categories?>
	</div>
</div>
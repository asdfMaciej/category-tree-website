<?php if (isset($category)): ?>
	<h2><?=$category['name']?></h2>
	<h4>Zmień nazwe kategorii:</h4>
	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
		<input placeholder="Nazwa kategorii" name="name">
		<input type="hidden" name="action" value="edit_category">
		<input type="hidden" name="id" value="<?=$category['id']?>">
		<input type="submit">
	</form><br>

	<h4>Dodaj podkategorię:</h4>
	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
		<input placeholder="Nazwa kategorii" name="name">
		<input type="hidden" name="action" value="add_category">
		<input type="hidden" name="parent_id" value="<?=$category['id']?>">
		<input type="submit">
	</form><br>

	<h4>Dodaj zwierzaka:</h4>
	<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
		<input placeholder="Nazwa kategorii" name="name">
		<input type="hidden" name="action" value="add_item">
		<input type="hidden" name="category_id" value="<?=$category['id']?>">
		<input type="submit">
	</form>

<?php endif; ?>

<?php if (isset($search) && $search): ?>
	<h2>Wyniki wyszukiwania: </h2>
<?php endif; ?>

<?php foreach($items as $i): ?>
	<div class='item'>
		<li>
			<?=$i['name'];?>
			<form action="<?=$_SERVER['REQUEST_URI']?>" method="post" class="menu-category__delete">
				<input type="hidden" name="action" value="delete_item">
				<input type="hidden" name="id" value="<?=$i['id']?>">
				<input type="submit" value="X">
			</form>
		</li>
	</div>
<?php endforeach; ?>
<?php if (empty($items)): ?>
	Brak zwierzaków w tej kategorii.
<?php endif; ?>


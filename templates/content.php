<div class="top">
	Strona o zwierzaczkach
	<?php if (isset($_SESSION["user"])): ?>
		<br>Zalogowano <?=$_SESSION["user"]["admin"] ? "jako administrator" : ""?> - 
		<?=$_SESSION["user"]["login"]?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
			<input type="hidden" name="action" value="logout">
			<input type="submit" value="Wyloguj">
		</form>

	<?php else: ?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
			<input placeholder="Login" name="login">
			<input type="password" placeholder="Hasło" name="password">
			<input type="hidden" name="action" value="login">
			<input type="submit">
		</form><br>

		<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
			<input placeholder="Login" name="login">
			<input type="password" placeholder="Hasło" name="password">
			<input type="hidden" name="action" value="register">
			<input type="submit">
		</form>
	<?php endif; ?>
</div>


<?php if ($popup): ?>
	<h3> <?=$popup?> </h3>
<?php endif; ?>

<?php 
if (!isset($_SESSION["user"])) {
	echo "<div class=\"container\">Zaloguj się!</div>";
	return;
}
?>

<div class="container">
	<div class="sidebar">
		<?=$categories?>
		<form action="<?=$_SERVER['REQUEST_URI']?>" method="post">
			<input placeholder="Nazwa kategorii" name="name">
			<input type="hidden" name="action" value="add_category">
			<input type="hidden" name="parent_id" value="0">
			<input type="submit">
		</form>

		<form action="." method="get">
			<input placeholder="Szukaj zwierzaka" name="search">
			<input type="submit">
		</form>
	</div>
	<div class="content">
		<?=$items?>
	</div>
	
</div>

<?php
require_once 'db/database.php';
require_once 'parse/parser_functions.php';

createTable();

fillingDatabase();

$data = readingDatabase();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<link rel="icon" href="/favicon.ico" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="theme-color" content="#000000" />
	<meta name="description" content="Web site created using create-react-app" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
		integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
		integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
		crossorigin="anonymous"></script>
	<title>Parsing sites</title>
</head>

<body>
	<div class="container">
		<nav class="navbar navbar-expand-lg navbar-light bg-light">
			<div class="container-fluid">
				<a class="navbar-brand" href="/">Parsing</a>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav me-auto mb-2 mb-lg-0">
						<li class="nav-item">
							<a class="nav-link active" aria-current="page" href="/">Home</a>
						</li>
					</ul>
					<form class="d-flex">
						<input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
						<button class="btn btn-outline-success" type="submit">Search</button>
					</form>
				</div>
			</div>
		</nav>
		<table class="table table-striped">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">№ извещения</th>
					<th scope="col">№ извещения ООС</th>
					<th scope="col">Ссылка на страницу</th>
					<th scope="col">Почта</th>
					<th scope="col">Документы</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data as $key):?>
				<tr>
					<td><?php echo $key['id_procedure'];?></td>
					<td><?php echo $key['procedure_number'];?></td>
					<td><?php echo $key['oos_procedure_number'];?></td>
					<td><?php echo $key['link_procedure'];?></td>
					<td><?php echo $key['email'];?></td>
					<td>
						<div class="dropdown">
							<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton1"
								data-bs-toggle="dropdown" aria-expanded="false">
								Документы
							</button>
							<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
								<?php foreach ($key['attachment'] as $key):?>
								<li><a class="dropdown-item" href="<?php echo $key['link_to_file'];?>"><?php echo $key['title'];?></a></li>
								<?php endforeach;?>
							</ul>
						</div>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
</body>
</html>
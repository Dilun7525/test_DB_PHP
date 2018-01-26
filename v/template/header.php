<?php
/**загрузка заголовка*/
require_once $_SERVER['DOCUMENT_ROOT'] . PATH_TEMPLATE . 'setup_head.php'; ?>

<body>
<header class="container">
	<div class="row">
		<div class="col">
			<div class="row">
				<a href="/">Home</a>
			</div>
		</div>
		<div class="col">
			<div class="row justify-content-end align-items-center">
				<a href="/home/show_profile/<?= $id ?>">
					<?= $login ?>
				</a>

				<a href="/auth/exit_user">
					<img src="/<?= PATH_IMG ?>user_exit.png" alt="exitUser">
				</a>
			</div>
		</div>
	</div>

</header>

<!--Content-->
<div class="container">



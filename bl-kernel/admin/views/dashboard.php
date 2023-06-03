<div id="dashboard" class="container">
	<div class="row">
		<div class="col-md-12">

			<!-- Good message -->
			<div>
			<h1><svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-car-front bg-dark rounded p-1 text-white" viewBox="0 0 16 16">
  <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2H6ZM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17 1.247 0 2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276Z"/>
  <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679c.033.161.049.325.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.807.807 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.807.807 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155 1.806 0 4.037-.084 5.592-.155A1.479 1.479 0 0 0 15 9.611v-.413c0-.099-.01-.197-.03-.294l-.335-1.68a.807.807 0 0 0-.43-.563 1.807 1.807 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3H4.82Z"/>
</svg> <strong>OTO CMS</strong></h1>
			<h2>Built fast website without database with flatfile cms</h2>
			<h3 id="hello-message" class="pt-0">
				<?php
					$username = $login->username();
					$user = new User($username);
					$name = '';
					if ($user->nickname()) {
						$name = $user->nickname();
					} elseif ($user->firstName()) {
						$name = $user->firstName();
					}
				?>
				<span class="fa fa-hand-spock-o"></span><span><?php echo $L->g('hello').' '.$name ?></span>
			</h3>
			<script>
			$( document ).ready(function() {
				$("#hello-message").fadeOut(2400, function() {
					var date = new Date()
					var hours = date.getHours()
					if (hours > 6 && hours < 12) {
						$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-morning') ?>');
					} else if (hours > 12 && hours < 18) {
						$(this).html('<span class="fa fa-sun-o"></span><?php echo $L->g('good-afternoon') ?>');
					} else if (hours > 18 && hours < 22) {
						$(this).html('<span class="fa fa-moon-o"></span><?php echo $L->g('good-evening') ?>');
					} else {
						$(this).html('<span class="fa fa-moon-o"></span><span><?php echo $L->g('good-night') ?></span>');
					}
				}).fadeIn(1000);
			});
			</script>
			</div>

			<!-- Quick Links -->
			<div class="container" id="jsclippyContainer">

				<div class="row">
					<div class="col">
						<div class="form-group">
						<select id="jsclippy" class="clippy" name="state"></select>
						</div>
					</div>
				</div>

			<script>
			$(document).ready(function() {

				var clippy = $("#jsclippy").select2({
					placeholder: "<?php $L->p('Start typing to see a list of suggestions') ?>",
					allowClear: true,
					width: "100%",
					theme: "bootstrap4",
					minimumInputLength: 2,
					dropdownParent: "#jsclippyContainer",
					language: {
						inputTooShort: function () { return ''; }
					},
					ajax: {
						url: HTML_PATH_ADMIN_ROOT+"ajax/clippy",
						data: function (params) {
							var query = { query: params.term }
							return query;
						},
						processResults: function (data) {
							return data;
						}
					},
					templateResult: function(data) {
						// console.log(data);
						var html = '';
						if (data.type=='menu') {
							html += '<a href="'+data.url+'"><div class="search-suggestion">';
							html += '<span class="fa fa-'+data.icon+'"></span>'+data.text+'</div></a>';
						} else {
							if (typeof data.id === 'undefined') {
								return '';
							}
							html += '<div class="search-suggestion">';
							html += '<div class="search-suggestion-item">'+data.text+' <span class="badge badge-pill badge-light">'+data.type+'</span></div>';
							html += '<div class="search-suggestion-options">';
							html += '<a target="_blank" href="'+DOMAIN_PAGES+data.id+'"><?php $L->p('view') ?></a>';
							html += '<a class="ml-2" href="'+DOMAIN_ADMIN+'edit-content/'+data.id+'"><?php $L->p('edit') ?></a>';
							html += '</div></div>';
						}

						return html;
					},
					escapeMarkup: function(markup) {
						return markup;
					}
				}).on("select2:closing", function(e) {
					e.preventDefault();
				}).on("select2:closed", function(e) {
					clippy.select2("open");
				});
				clippy.select2("open");

			});
			</script>
			<ul class="list-group list-group-striped p-3">
			<li class="list-group-item pt-0"><h4 class="m-0"><?php $L->p('Notifications') ?></h4></li>
			<?php
			$logs = array_slice($syslog->db, 0, NOTIFICATIONS_AMOUNT);
			foreach ($logs as $log) {
				$phrase = $L->g($log['dictionaryKey']);
				echo '<li class="list-group-item">';
				echo $phrase;
				if (!empty($log['notes'])) {
					echo ' « <b>'.$log['notes'].'</b> »';
				}
				echo '<br><span class="notification-date"><small>';
				echo Date::format($log['date'], DB_DATE_FORMAT, NOTIFICATIONS_DATE_FORMAT);
				echo ' [ '.$log['username'] .' ]';
				echo '</small></span>';
				echo '</li>';
			}
			?>
			</ul>
			</div>

			<?php Theme::plugins('dashboard') ?>
		</div>
		
	</div>
</div>

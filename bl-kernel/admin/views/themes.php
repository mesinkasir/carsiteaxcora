<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('Themes'), 'icon'=>'desktop'));

echo '
<table class="table  mt-3">
	<thead>
		<tr>
			<th class="border-bottom-0 w-25" scope="col">'.$L->g('Name').'</th>
			<th class="border-bottom-0 d-none d-sm-table-cell" scope="col">'.$L->g('Description').'</th>
		</tr>
	</thead>
	<tbody>
';

foreach ($themes as $theme) {
	echo '
	<tr '.($theme['dirname']==$site->theme()?'class="bg-light"':'').'>
		<td class="align-middle pt-3 pb-3">
			<div>'.$theme['name'].'</div>
			<div class="mt-1">
	';

	if ($theme['dirname']!=$site->theme()) {
		echo '<a href="'.HTML_PATH_ADMIN_ROOT.'install-theme/'.$theme['dirname'].'">'.$L->g('Activate').'</a>';
	}

	echo '
			</div>
		</td>
	';

	echo '<td class="align-middle d-none d-sm-table-cell">';
	echo $theme['description'];
	echo '</td>';


	echo '</tr>';
}

echo '
	</tbody>
</table>
';
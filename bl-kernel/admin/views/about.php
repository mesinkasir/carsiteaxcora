<?php

echo Bootstrap::pageTitle(array('title'=>$L->g('About'), 'icon'=>'info-circle'));

echo '
<h3>Bluger Resto CMS</h3>
<p>Free and open source code restaurant flatfile cms for built simple fast resto website app .
present by <a href="https://www.axcora.com/" class="text-dark" target="_blank"> axcora technology</a> <a href="https://www.bludit.com/" class="text-dark" target="_blank">bludit</a>
Hire developer and built your modern fast website app with flatfile cms 
<a href="https://www.fiverr.com/creativitas/design-your-website-with-phyton-django" target="_blank" class="btn btn-dark">Built Web Now </a></p>
<table class="table table-striped mt-3">
	<tbody>
';

echo '<tr>';
echo '<td>Edition</td>';
if (defined('BLUDIT_PRO')) {
	echo '<td>PRO - '.$L->g('Thanks for supporting Bludit').' <span class="fa fa-heart" style="color: #ffc107"></span></td>';
} else {
	echo '<td>Standard - <a target="_blank" href="https://www.fiverr.com/creativitas/design-your-website-with-phyton-django">'.$L->g('Upgrade to PRO Version').'</a></td>';
}
echo '</tr>';

echo '<tr>';
echo '<td>Version</td>';
echo '<td>'.BLUDIT_VERSION.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>Bluger Release</td>';
echo '<td>'.BLUDIT_BUILD.'</td>';
echo '</tr>';

echo '<tr>';
echo '<td>Disk usage</td>';
echo '<td>'.Filesystem::bytesToHumanFileSize(Filesystem::getSize(PATH_ROOT)).'</td>';
echo '</tr>';

echo '<tr>';
echo '<td><a href="'.HTML_PATH_ADMIN_ROOT.'developers'.'">Developers Area</a></td>';
echo '<td></td>';
echo '</tr>';

echo '
	</tbody>
</table>
';

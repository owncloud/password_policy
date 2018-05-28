<?php
/**
 * @author Jörn Friedrich Dreyer <jfd@butonic.de>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license GPL-2.0
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

script('password_policy', 'password');
style('password_policy', 'styles');

/**
 * @var array $_
 * @var \OCP\IL10N $l
 * @var \OC_Defaults $theme
 */
?>

<form id="password_policy" method="post">
	<fieldset>
	<h1 class="warning"><?php p($l->t('Password changed required'));?></h1>

	<?php if (isset($_['error'])) { ?><div id="error" class="warning"><?php p($_['error']) ?></div> <?php } ?>

	<p>

	<input type="password" id="current_password" name="current_password"
		   value="" autofocus placeholder="<?php p($l->t('current password'));?>"
	/>
	<label for="current_password" class="infield"><?php p($l->t('current password'));?></label>

	<input type="password" id="new_password" name="new_password"
		   value="" placeholder="<?php p($l->t('new password'));?>"
	/>
	<label for="new_password" class="infield"><?php p($l->t('new password'));?></label>

	<input type="password" id="confirm_password" name="confirm_password"
		   value="" placeholder="<?php p($l->t('confirm new password'));?>"
	/>
	<label for="confirm_password" class="infield"><?php p($l->t('confirm new password'));?></label>

	<input type="hidden" name="redirect_url" value="<?php p($_['redirect_url']) ?>" />
	<input type="hidden" name="app" value="oca-password-policy" />
	<input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" id="requesttoken">
	<input type="submit" id="submit" value="<?php p($l->t('Save'));?>" title="<?php p($l->t('Save'));?>"/>
	</p>
	</fieldset>
</form>

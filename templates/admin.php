<?php
/**
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

/** @var array $_ */

style('password_policy', 'styles');
script('password_policy', 'ajax');

?>

<div class="section" id="password-policy">
	<form id="password_policy" method="post">
		<h2><?php p($l->t('Password and public link expiration policies'));?></h2>

		<br>
		<p><?php p($l->t('Minimum password requirements for user accounts and public links:'));?></p>

		<ul>
			<li><label><input type="checkbox" name="spv_password_history_checked"
						<?php if ($_['spv_password_history_checked']): ?> checked="checked"<?php endif; ?>>
					<input type="number" name="spv_password_history_value" min="0" max="255" value="<?php p($_['spv_password_history_value']) ?>"> <?php p($l->t('different than last passwords'));?></label></li>
			<li><label><input type="checkbox" name="spv_min_chars_checked"
						<?php if ($_['spv_min_chars_checked']): ?> checked="checked"<?php endif; ?>>
					<input type="number" name="spv_min_chars_value" min="0" max="255" value="<?php p($_['spv_min_chars_value']) ?>"> <?php p($l->t('minimum characters'));?></label></li>
			<li><label><input type="checkbox" name="spv_lowercase_checked"
						<?php if ($_['spv_lowercase_checked']): ?> checked="checked"<?php endif; ?>>
					<input type="number" name="spv_lowercase_value" min="0" max="255" value="<?php p($_['spv_lowercase_value']) ?>"> <?php p($l->t('lowercase letters'));?></label></li>
			<li><label><input type="checkbox" name="spv_uppercase_checked"
						<?php if ($_['spv_uppercase_checked']): ?> checked="checked"<?php endif; ?>>
					<input type="number" name="spv_uppercase_value" min="0" max="255" value="<?php p($_['spv_uppercase_value']) ?>"> <?php p($l->t('uppercase letters'));?></label></li>
			<li><label><input type="checkbox" name="spv_numbers_checked"
						<?php if ($_['spv_numbers_checked']): ?> checked="checked"<?php endif; ?>>
					<input type="number" name="spv_numbers_value" min="0" max="255" value="<?php p($_['spv_numbers_value']) ?>"> <?php p($l->t('numbers'));?></label></li>
			<li><label><input type="checkbox" name="spv_special_chars_checked"
						<?php if ($_['spv_special_chars_checked']): ?> checked="checked"<?php endif; ?>>
					<input type="number" name="spv_special_chars_value" min="0" max="255" value="<?php p($_['spv_special_chars_value']) ?>"> <?php p($l->t('special characters'));?></label></li>

			<li class="indented"><label><input type="checkbox" name="spv_def_special_chars_checked"
						<?php if ($_['spv_def_special_chars_checked']): ?> checked="checked"<?php endif; ?>> <?php p($l->t('Define special characters:'));?></label>
				<input type="text" name="spv_def_special_chars_value" value="<?php p($_['spv_def_special_chars_value']) ?>"></li>
		</ul>
		<input type="hidden" name="app" value="oca-password-policy" />

		<br>
		<p><?php p($l->t('Public link expiration policy:'));?></p>

		<ul>
			<li><label><input type="checkbox" name="spv_expiration_password_checked"
					<?php if ($_['spv_expiration_password_checked']): ?> checked="checked"<?php endif; ?>>
				<input type="number" name="spv_expiration_password_value"  min="0" max="255" value="<?php p($_['spv_expiration_password_value']) ?>" placeholder="7">
				<?php p($l->t('days until link expires if password is set'));?></label>
			</li>
			<li><label><input type="checkbox" name="spv_expiration_nopassword_checked"
					<?php if ($_['spv_expiration_nopassword_checked']): ?> checked="checked"<?php endif; ?>>
				<input type="number" name="spv_expiration_nopassword_value"  min="0" max="255" value="<?php p($_['spv_expiration_nopassword_value']) ?>" placeholder="7">
				<?php p($l->t('days until link expires if password is not set'));?></label>
			</li>
		</ul>
		<br>

		<input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" id="requesttoken">
		<input type="submit" value="<?php p($l->t('Save'));?>" />
		<span class="msg"></span>
	</form>
</div>

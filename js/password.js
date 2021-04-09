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
$(document).ready(function() {
	var $newPassword = $("#password_policy #new_password"),
		$confirmPassword = $("#password_policy #confirm_password"),
		$submit = $("#password_policy #submit"),
		check = function() {
		if ($confirmPassword.val() !== '' && $newPassword.val() !== $confirmPassword.val()) {
			$newPassword.addClass('password-mismatch');
			$confirmPassword.addClass('password-mismatch');
			$submit.attr('disabled', 'disabled');
		} else {
			$newPassword.removeClass('password-mismatch');
			$confirmPassword.removeClass('password-mismatch');
			$submit.removeAttr('disabled');
		}
	};
	$newPassword.keyup(check);
	$confirmPassword.keyup(check);

	$('#send-mail').click(resetLink);

	function resetLink(){
			var user = $("#send-mail").data('user');
			if (OC.config['lost_password_link']) {
				window.location = OC.config['lost_password_link'];
			} else {
				$.post(
					OC.generateUrl('/lostpassword/email'),
					{
						user : user,
					},
					sendLinkDone
				);
			}

	}

	function sendLinkDone(result){
		var message;

		if (result && result.status === 'success'){
			message = ( t('core', 'The link to reset your password has been sent to your email. If you do not receive it within a reasonable amount of time, check your spam/junk folders.<br><br>If it is not there please contact us via servicedesk@pagani.com.'));
		} else {
			if (result && result.msg){
				message = result.msg;
			} else {
				message = t('core', 'Couldn\'t send reset email. Please contact us via servicedesk@pagani.com.');
			}
		}

		$("#send-mail-message").html(message).css("color", "white");
	}
});

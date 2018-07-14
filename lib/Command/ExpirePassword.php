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

namespace OCA\PasswordPolicy\Command;

use OCP\IConfig;
use OCP\IUserManager;
use OCA\PasswordPolicy\Db\OldPasswordMapper;
use OCA\PasswordPolicy\Db\OldPassword;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use OCP\AppFramework\Utility\ITimeFactory;


class ExpirePassword extends Command {
	/** @var \OCP\IConfig */
	private $config;

	/** @var \OCP\IUserManager */
	protected $userManager;

	/** @var ITimeFactory */
	private $timeFactory;

	/** @var OldPasswordMapper */
	private $mapper;

	/**
	 * @param IConfig $config
	 * @param IUserManager $userManager
	 * @param ITimeFactory $timeFactory
	 * @param OldPasswordMapper $mapper
	 */
	public function __construct(
		IConfig $config,
		IUserManager $userManager,
		ITimeFactory $timeFactory,
		OldPasswordMapper $mapper
	) {
		parent::__construct();
		$this->config = $config;
		$this->userManager = $userManager;
		$this->timeFactory = $timeFactory;
		$this->mapper = $mapper;
	}

	protected function configure() {
		$this
			->setName('user:expire-password')
			->setDescription('Expire a user\'s password')
			->addArgument(
					'uid',
					InputArgument::REQUIRED,
					'The user\'s ownCloud uid (username).'
				     )
			->addArgument(
					'expiredate',
					InputArgument::OPTIONAL,
					'The date and time when a password expires, e.g. "2019-01-01 14:00:00 CET".',
					'-1 days'		// base.php sets timezone to utc, so
										// make sure date is in the past
				     )
		;
	}

	/**
	 * sets a user value as a flag in the user config that will be checked by
	 * the AccountModule on login.
	 *
	 * @param InputInterface $input
	 * @param OutputInterface $output
	 * @return int
	 * @throws \OCP\PreConditionNotMetException
	 */
	protected function execute(InputInterface $input, OutputInterface $output) {
		$uid = $input->getArgument('uid');

		/** @var $user \OCP\IUser */
		$user = $this->userManager->get($uid);

		if ($user === null) {
			$output->writeln("<error>Unknown user: $uid</error>");
			/**
			 * return EX_NOUSER from /usr/include/sysexits.h
			 * @see http://tldp.org/LDP/abs/html/exitcodes.html#FTN.AEN23647
			 */
			return 67;
		}

		if (!$user->canChangePassword()) {
			$output->writeln("<error>The user's backend doesn't support password changes. The password cannot be expired for user: $uid</error>");
			return 1;
		}

		$expireDate = new \DateTime();
		$expireDate->setTimezone(new \DateTimeZone('UTC'));
		$expireDate->setTimestamp($this->timeFactory->getTime());
		$expireDate->modify($input->getArgument('expiredate'));

		$oldDate = new \DateTime();
		$oldDate->setTimezone(new \DateTimeZone('UTC'));
		$oldDate->setTimestamp($this->timeFactory->getTime());
		$oldDate->modify($input->getArgument('expiredate'));

		if ($this->config->getAppValue('password_policy', 'spv_user_password_expiration_checked', false) === 'on') {
			$delta = $this->config->getAppValue('password_policy', 'spv_user_password_expiration_value', 90);
			$oldDate->modify("-$delta days");
		}

		$this->config->deleteUserValue(
			$uid,
			'password_policy',
			'forcePasswordChange'
		);

		// add a dummy password in the user_password_history so the cron job
		// can notify about the expiration of the password.
		$oldPassword = new OldPassword();
		$oldPassword->setUid($uid);
		$oldPassword->setPassword(OldPassword::EXPIRED);
		$oldPassword->setChangeTime($oldDate->getTimestamp());
		$this->mapper->insert($oldPassword);

		// show expire date if it was given
		if ($input->hasArgument('expiredate')) {
			$output->writeln("The password for $uid is set to expire on ". $expireDate->format('Y-m-d H:i:s T').'.');
		}
		return 0;
	}
}
